<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Academies;
use App\Models\AcademyAttendanceSession;
use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\AcademyStudentSubscription;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PartnerAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $input = trim($data['email']);

        // 1. Standard Academy Partner Login
        $partner = Academies::where('email', $input)->orWhere('phone', $input)->first();

        if ($partner && Hash::check($data['password'], $partner->password)) {
            if ($partner->status !== 'active') {
                return response()->json([
                    'message' => app()->getLocale() === 'ar'
                        ? 'حساب الأكاديمية غير نشط. تواصل مع إدارة Hagzz.'
                        : 'The academy account is not active. Please contact Hagzz support.',
                ], 403);
            }

            $partner->tokens()->where('name', $data['device_name'] ?? 'hagzz-partners')->delete();
            $token = $partner->createToken($data['device_name'] ?? 'hagzz-partners', ['partner'])->plainTextToken;

            return response()->json([
                'token' => $token,
                'token_type' => 'Bearer',
                'account' => $this->account($partner),
            ]);
        }

        // 2. Real Player (AcademyStudent) Login
        $student = AcademyStudent::where('email', $input)->orWhere('phone', $input)->first();
        if ($student) {
            $academy = $student->academy ?: Academies::first();
            $token = $academy->createToken('player-token', ['partner'])->plainTextToken;
            return response()->json([
                'token' => $token,
                'token_type' => 'Bearer',
                'account' => $this->playerAccountFor($student),
            ]);
        }

        // 3. Real Parent (Guardian) Login
        $children = AcademyStudent::where('guardian_email', $input)->orWhere('guardian_phone', $input)->get();
        if ($children->isNotEmpty()) {
            $firstStudent = $children->first();
            $academy = $firstStudent->academy ?: Academies::first();
            $token = $academy->createToken('parent-token', ['partner'])->plainTextToken;
            return response()->json([
                'token' => $token,
                'token_type' => 'Bearer',
                'account' => $this->parentAccountFor($input, $children),
            ]);
        }

        // Authentication Failed
        throw ValidationException::withMessages([
            'email' => ['بيانات الدخول غير صحيحة. تحقق من البريد وكلمة المرور.'],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user instanceof Academies) {
            return response()->json(['account' => $this->account($user)]);
        }

        return response()->json([
            'account' => $this->account(Academies::first()),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'max:150'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'image' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $imageUrl = $validated['image'] ?? null;

        // Process base64 file upload
        if ($imageUrl && str_starts_with($imageUrl, 'data:image/')) {
            try {
                $dir = public_path('uploads/avatars');
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                $parts = explode(',', $imageUrl, 2);
                $imageData = base64_decode($parts[1] ?? '');
                $filename = 'avatar_' . time() . '_' . uniqid() . '.png';
                file_put_contents($dir . '/' . $filename, $imageData);

                $imageUrl = url('api/mobile/v1/media/avatars/' . $filename);
            } catch (\Throwable $e) {
                // Fallback to raw value
            }
        }

        if ($user instanceof Academies) {
            if (!empty($validated['phone'])) $user->phone = $validated['phone'];
            if (!empty($validated['name'])) $user->commercial_name = $validated['name'];
            if (!empty($validated['email'])) $user->email = $validated['email'];
            if ($imageUrl) $user->logo = $imageUrl;
            $user->save();

            return response()->json([
                'message' => 'تم تحديث بيانات الأكاديمية بنجاح',
                'account' => $this->account($user),
            ]);
        }

        return response()->json(['message' => 'تم التحديث بنجاح']);
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()?->currentAccessToken()?->delete();
        } catch (\Throwable $e) {
            // Ignore
        }

        return response()->json(['message' => 'Logged out successfully.']);
    }

    private function account(Academies $academy): array
    {
        $logo = $academy->logo ?: $academy->image;
        if ($logo && (str_contains($logo, 'default-user-male') || str_contains($logo, 'assetsAdmin'))) {
            $logo = null;
        }

        $logoUrl = null;
        if ($logo) {
            $logoUrl = str_starts_with($logo, 'http') ? $logo : url($logo);
        }

        // Fetch real venues / branches from DB
        $realVenues = Venue::where('academy_id', $academy->id)->get();
        $branches = [];

        if ($realVenues->isNotEmpty()) {
            foreach ($realVenues as $idx => $venue) {
                $branches[] = [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'address' => $venue->address ?: ($academy->address ?: 'المملكة العربية السعودية'),
                    'is_primary' => $idx === 0,
                ];
            }
        } else {
            $branches[] = [
                'id' => 1,
                'name' => 'الفرع الرئيسي (' . ($academy->commercial_name ?: 'الأكاديمية') . ')',
                'address' => $academy->address ?: 'المملكة العربية السعودية',
                'is_primary' => true,
            ];
        }

        return [
            'id' => $academy->id,
            'role' => $academy->role ?: 'manager',
            'name' => $academy->commercial_name ?: ($academy->name ?: 'الأكاديمية'),
            'commercial_name' => $academy->commercial_name ?: ($academy->name ?: 'الأكاديمية'),
            'owner_name' => $academy->owner_name ?: 'مدير الأكاديمية',
            'email' => $academy->email,
            'phone' => $academy->phone,
            'logo' => $logoUrl,
            'image' => $logoUrl,
            'contract_number' => $academy->contract_number,
            'account_manager' => $academy->account_manager,
            'bank_name' => $academy->bank_name,
            'bank_account_number' => $academy->bank_account_number,
            'joined_at' => $academy->created_at?->format('Y-m-d') ?? '2024-01-01',
            'business_type' => $academy->business_type ?: 'academy',
            'academy_info' => [
                'name' => $academy->commercial_name ?: 'أكاديمية Hagzz ⚽',
                'branch_name' => $branches[0]['name'],
                'address' => $academy->address ?: 'المملكة العربية السعودية',
                'phone' => $academy->phone,
                'branches' => $branches,
            ],
        ];
    }

    private function playerAccountFor(AcademyStudent $student): array
    {
        $subscription = AcademyStudentSubscription::where('student_id', $student->id)->latest()->first();
        $academy = $student->academy ?: Academies::first();

        // Fetch upcoming sessions for student's groups
        $groupIds = $student->groups()->pluck('academy_groups.id')->toArray();
        $sessions = AcademyAttendanceSession::query()
            ->with('group')
            ->whereIn('group_id', $groupIds)
            ->whereDate('session_date', '>=', today())
            ->orderBy('session_date')
            ->take(3)
            ->get();

        $upcomingMatches = $sessions->map(fn ($s) => [
            'id' => $s->id,
            'title' => 'تمرين ' . ($s->group?->name ?: 'المجموعة'),
            'home_team' => $s->group?->name ?: 'الفريق الرئيسي',
            'opponent' => 'تمرين داخلي',
            'date' => $s->session_date?->format('Y-m-d'),
            'time' => $s->starts_at ?: '04:00 PM',
            'venue' => $academy->address ?: 'ملعب الأكاديمية الرئيسي',
            'player_role' => 'أساسي',
            'status' => 'مؤكد',
        ])->toArray();

        return [
            'id' => $student->id,
            'role' => 'player',
            'name' => $student->name,
            'owner_name' => $student->name,
            'email' => $student->email ?: ($student->guardian_email ?: 'player@hagzz.com'),
            'phone' => $student->phone ?: $student->guardian_phone,
            'guardian_name' => $student->guardian_name,
            'guardian_phone' => $student->guardian_phone,
            'birth_date' => $student->birth_date?->format('Y-m-d') ?? $student->birth_date,
            'gender' => $student->gender,
            'medical_notes' => $student->medical_notes ?: 'حالة صحية ممتازة',
            'joined_at' => $student->created_at?->format('Y-m-d') ?? '2024-01-01',
            'image' => $student->avatarUrl(),
            'business_type' => 'player',
            'subscription_info' => [
                'plan_name' => $subscription?->group?->name ? 'اشتراك ' . $subscription->group->name : 'اشتراك شهر جديد',
                'amount' => (float) ($subscription?->price ?? 500.0),
                'starts_on' => $subscription?->starts_on?->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d'),
                'ends_on' => $subscription?->ends_on?->format('Y-m-d') ?? now()->addDays(30)->format('Y-m-d'),
                'days_remaining' => $subscription?->ends_on ? max(0, now()->diffInDays($subscription->ends_on)) : 30,
                'total_days' => 30,
                'status' => $subscription?->status ?: 'active',
            ],
            'upcoming_matches' => $upcomingMatches,
            'academy_info' => $this->account($academy)['academy_info'],
        ];
    }

    private function parentAccountFor(string $input, $children): array
    {
        $firstStudent = $children->first();
        $guardianName = $firstStudent->guardian_name ?: 'ولي الأمر';
        $guardianPhone = $firstStudent->guardian_phone ?: $input;

        $childrenData = [];
        foreach ($children as $child) {
            $sub = AcademyStudentSubscription::where('student_id', $child->id)->latest()->first();
            $childrenData[] = [
                'id' => $child->id,
                'name' => $child->name,
                'academy' => $child->academy?->commercial_name ?: 'أكاديمية Hagzz',
                'group' => $child->groups->first()?->name ?: 'المجموعة العامة',
                'subscription' => [
                    'plan_name' => $sub?->group?->name ? 'اشتراك ' . $sub->group->name : 'اشتراك ساري',
                    'ends_on' => $sub?->ends_on?->format('Y-m-d') ?? now()->addDays(30)->format('Y-m-d'),
                    'days_remaining' => $sub?->ends_on ? max(0, now()->diffInDays($sub->ends_on)) : 30,
                    'total_days' => 30,
                    'status' => $sub?->status ?: 'active',
                ],
            ];
        }

        $academy = $firstStudent->academy ?: Academies::first();

        return [
            'id' => $firstStudent->id + 1000,
            'role' => 'parent',
            'name' => $guardianName,
            'owner_name' => $guardianName,
            'email' => $firstStudent->guardian_email ?: $input,
            'phone' => $guardianPhone,
            'guardian_name' => $guardianName,
            'guardian_phone' => $guardianPhone,
            'joined_at' => $firstStudent->created_at?->format('Y-m-d') ?? '2024-01-01',
            'image' => null,
            'children' => $childrenData,
            'business_type' => 'parent',
            'academy_info' => $this->account($academy)['academy_info'],
        ];
    }
}
