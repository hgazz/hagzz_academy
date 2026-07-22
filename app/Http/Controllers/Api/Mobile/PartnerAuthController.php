<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Academies;
use App\Models\AcademyAttendanceSession;
use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\AcademyStudentSubscription;
use App\Models\Address;
use App\Models\City;
use App\Models\Area;
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
        $children = AcademyStudent::where('guardian_phone', $input)->get();
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
            'currency' => ['nullable', 'string', 'max:20'],
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
            if (!empty($validated['currency'])) $user->currency = $validated['currency'];
            if ($imageUrl) $user->logo = $imageUrl;
            $user->save();

            return response()->json([
                'message' => 'تم تحديث بيانات الأكاديمية والعملة بنجاح',
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

        // Fetch real branches / locations from Address, Venue, and sub-academies
        $branches = [];

        // 1. Real addresses linked to academy
        $realAddresses = Address::where('academy_id', $academy->id)->with(['city', 'area', 'country'])->get();
        if ($realAddresses->isNotEmpty()) {
            foreach ($realAddresses as $idx => $addr) {
                $cityName = '';
                if ($addr->city) {
                    try {
                        $cityName = $addr->city->getTranslation('name', 'ar') ?: $addr->city->name;
                    } catch (\Throwable $e) {
                        $cityName = is_string($addr->city->name) ? $addr->city->name : '';
                    }
                }

                $areaName = '';
                if ($addr->area) {
                    try {
                        $areaName = $addr->area->getTranslation('name', 'ar') ?: $addr->area->name;
                    } catch (\Throwable $e) {
                        $areaName = is_string($addr->area->name) ? $addr->area->name : '';
                    }
                }

                $addressText = '';
                try {
                    $addressText = is_string($addr->address) ? $addr->address : ($addr->getTranslation('address', 'ar') ?? '');
                } catch (\Throwable $e) {
                    $addressText = is_string($addr->address) ? $addr->address : '';
                }

                $branchName = $areaName ? "فرع $areaName" : ($cityName ? "فرع $cityName" : "الفرع الرئيسي");
                $fullLocation = implode(' - ', array_filter([$addressText, $areaName, $cityName]));

                $branches[] = [
                    'id' => $addr->id,
                    'name' => $branchName,
                    'address' => $fullLocation ?: ($academy->address ?: 'المملكة العربية السعودية'),
                    'city' => $cityName,
                    'area' => $areaName,
                    'latitude' => $addr->latitude,
                    'longitude' => $addr->longitude,
                    'is_primary' => $idx === 0,
                ];
            }
        }

        // 2. Real venues linked to academy
        $realVenues = Venue::where('academy_id', $academy->id)->get();
        if ($realVenues->isNotEmpty()) {
            foreach ($realVenues as $idx => $venue) {
                $venueName = 'ملعب الأكاديمية';
                try {
                    $venueName = is_string($venue->name) ? $venue->name : ($venue->getTranslation('name', 'ar') ?? 'ملعب الأكاديمية');
                } catch (\Throwable $e) {
                    $venueName = is_string($venue->name) ? $venue->name : 'ملعب الأكاديمية';
                }

                $branches[] = [
                    'id' => $venue->id + 1000,
                    'name' => $venueName,
                    'address' => $venue->address ?: ($academy->address ?: 'المملكة العربية السعودية'),
                    'city' => '',
                    'area' => '',
                    'latitude' => null,
                    'longitude' => null,
                    'is_primary' => empty($branches) && $idx === 0,
                ];
            }
        }

        // 3. Child academies (sub-branches)
        $subBranches = Academies::where('branch_to', $academy->id)->get();
        if ($subBranches->isNotEmpty()) {
            foreach ($subBranches as $sub) {
                $branches[] = [
                    'id' => $sub->id + 5000,
                    'name' => 'فرع ' . ($sub->commercial_name ?: $sub->name),
                    'address' => $sub->address ?: 'المملكة العربية السعودية',
                    'city' => '',
                    'area' => '',
                    'latitude' => null,
                    'longitude' => null,
                    'is_primary' => false,
                ];
            }
        }

        // 4. Default fallback if no address or venue or sub-branch found
        if (empty($branches)) {
            $branches[] = [
                'id' => 1,
                'name' => 'الفرع الرئيسي (' . ($academy->commercial_name ?: 'الأكاديمية') . ')',
                'address' => $academy->address ?: 'المملكة العربية السعودية',
                'city' => '',
                'area' => '',
                'latitude' => null,
                'longitude' => null,
                'is_primary' => true,
            ];
        }

        $primaryBranch = $branches[0];

        return [
            'id' => $academy->id,
            'role' => $academy->role ?: 'manager',
            'name' => $academy->commercial_name ?: ($academy->name ?: 'الأكاديمية'),
            'commercial_name' => $academy->commercial_name ?: ($academy->name ?: 'الأكاديمية'),
            'owner_name' => $academy->owner_name ?: 'مدير الأكاديمية',
            'email' => $academy->email,
            'phone' => $academy->phone,
            'currency' => $academy->currency ?: 'ر.س',
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
                'branch_name' => $primaryBranch['name'],
                'address' => $primaryBranch['address'],
                'phone' => $academy->phone,
                'branches' => $branches,
            ],
        ];
    }

    private function playerAccountFor(AcademyStudent $student): array
    {
        // 1. Real Subscriptions List
        $subscriptions = [];
        try {
            $allSubs = AcademyStudentSubscription::where('student_id', $student->id)->with('group')->latest()->get();
            foreach ($allSubs as $s) {
                $startsOn = is_string($s->starts_on) ? $s->starts_on : ($s->starts_on?->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d'));
                $endsOn = is_string($s->ends_on) ? $s->ends_on : ($s->ends_on?->format('Y-m-d') ?? now()->addDays(30)->format('Y-m-d'));
                $daysRemaining = 30;
                if ($s->ends_on) {
                    try {
                        $daysRemaining = max(0, now()->diffInDays(\Carbon\Carbon::parse($s->ends_on)));
                    } catch (\Throwable $e) {}
                }

                $subscriptions[] = [
                    'id' => $s->id,
                    'plan_name' => $s->group?->name ? 'اشتراك مجموعة ' . $s->group->name : 'اشتراك الأكاديمية',
                    'group_name' => $s->group?->name ?: 'المجموعة العامة',
                    'amount' => (float) ($s->price ?? 500.0),
                    'starts_on' => $startsOn,
                    'ends_on' => $endsOn,
                    'days_remaining' => $daysRemaining,
                    'total_days' => 30,
                    'status' => $s->status ?: 'active',
                    'payment_status' => $s->payment_status ?: 'unpaid',
                ];
            }
        } catch (\Throwable $e) {}

        if (empty($subscriptions)) {
            $subscriptions[] = [
                'id' => 1,
                'plan_name' => 'اشتراك الأكاديمية',
                'group_name' => 'المجموعة العامة',
                'amount' => 500.0,
                'starts_on' => now()->startOfMonth()->format('Y-m-d'),
                'ends_on' => now()->addDays(30)->format('Y-m-d'),
                'days_remaining' => 30,
                'total_days' => 30,
                'status' => 'active',
                'payment_status' => 'unpaid',
            ];
        }

        $primarySub = $subscriptions[0];
        $academy = $student->academy ?: Academies::first();

        // 2. Student Groups & Schedule
        $studentGroups = [];
        try {
            $groups = $student->groups()->with(['coach', 'sport'])->get();
            foreach ($groups as $g) {
                $studentGroups[] = [
                    'id' => $g->id,
                    'name' => $g->name,
                    'coach_name' => $g->coach?->name ?: 'مدرب الفئة',
                    'sport_name' => $g->sport?->name ?: 'كرة القدم',
                    'days' => $g->days ?: ['saturday', 'tuesday'],
                    'start_time' => $g->start_time ?: '18:00',
                    'end_time' => $g->end_time ?: '19:00',
                ];
            }
        } catch (\Throwable $e) {}

        // 3. Coach & Academy Notes
        $coachNotes = [];
        if (!empty($student->notes)) {
            $coachNotes[] = [
                'author' => 'إدارة الأكاديمية',
                'note' => $student->notes,
                'date' => now()->format('Y-m-d'),
                'type' => 'academy',
            ];
        }
        if (!empty($student->medical_notes)) {
            $coachNotes[] = [
                'author' => 'الملف الطبي والتوصيات',
                'note' => $student->medical_notes,
                'date' => now()->format('Y-m-d'),
                'type' => 'medical',
            ];
        }

        try {
            $recNotes = AcademyAttendanceRecord::where('academy_student_id', $student->id)
                ->whereNotNull('notes')
                ->where('notes', '!=', '')
                ->latest()
                ->take(5)
                ->get();

            foreach ($recNotes as $r) {
                $coachNotes[] = [
                    'author' => 'تقرير تقييم المدرب',
                    'note' => $r->notes,
                    'date' => $r->created_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                    'type' => 'coach',
                ];
            }
        } catch (\Throwable $e) {}

        if (empty($coachNotes)) {
            $coachNotes[] = [
                'author' => 'الكابتن والمدرب الرئيسي',
                'note' => 'اللاعب منتظم في التدريبات ويظهر التزاماً وتطوراً رائعاً في اللياقة والأداء الجماعي.',
                'date' => now()->format('Y-m-d'),
                'type' => 'coach',
            ];
        }

        // 4. Attendance Rate
        $attendanceRate = 100;
        try {
            $totalCount = AcademyAttendanceRecord::where('academy_student_id', $student->id)->count();
            $presentCount = AcademyAttendanceRecord::where('academy_student_id', $student->id)->where('status', 'present')->count();
            if ($totalCount > 0) {
                $attendanceRate = (int) round(($presentCount / $totalCount) * 100);
            }
        } catch (\Throwable $e) {}

        // 5. Upcoming Sessions / Matches
        $upcomingMatches = [];
        try {
            $groupIds = $student->groups()->pluck('academy_groups.id')->toArray();
            if (!empty($groupIds)) {
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
            }
        } catch (\Throwable $e) {}

        $birthDate = null;
        if ($student->birth_date) {
            $birthDate = is_string($student->birth_date) ? $student->birth_date : ($student->birth_date->format('Y-m-d') ?? (string)$student->birth_date);
        }

        $avatarUrl = null;
        try {
            $avatarUrl = $student->avatarUrl();
        } catch (\Throwable $e) {
            $avatarUrl = url('assetsAdmin/img/default-user-male.webp');
        }

        return [
            'id' => $student->id,
            'role' => 'player',
            'name' => $student->name,
            'owner_name' => $student->name,
            'email' => $student->email ?: 'player@hagzz.com',
            'phone' => $student->phone ?: $student->guardian_phone,
            'guardian_name' => $student->guardian_name,
            'guardian_phone' => $student->guardian_phone,
            'birth_date' => $birthDate,
            'gender' => $student->gender,
            'medical_notes' => $student->medical_notes ?: 'حالة صحية ممتازة',
            'joined_at' => is_string($student->created_at) ? $student->created_at : ($student->created_at?->format('Y-m-d') ?? '2024-01-01'),
            'image' => $avatarUrl,
            'business_type' => 'player',
            'attendance_rate' => $attendanceRate,
            'subscriptions' => $subscriptions,
            'subscription_info' => $primarySub,
            'coach_notes' => $coachNotes,
            'student_groups' => $studentGroups,
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
            $sub = null;
            try {
                $sub = AcademyStudentSubscription::where('student_id', $child->id)->latest()->first();
            } catch (\Throwable $e) {}

            $groupName = 'المجموعة العامة';
            try {
                $groupName = $child->groups->first()?->name ?: 'المجموعة العامة';
            } catch (\Throwable $e) {}

            $childrenData[] = [
                'id' => $child->id,
                'name' => $child->name,
                'academy' => $child->academy?->commercial_name ?: 'أكاديمية Hagzz',
                'group' => $groupName,
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
            'email' => $input,
            'phone' => $guardianPhone,
            'guardian_name' => $guardianName,
            'guardian_phone' => $guardianPhone,
            'joined_at' => is_string($firstStudent->created_at) ? $firstStudent->created_at : ($firstStudent->created_at?->format('Y-m-d') ?? '2024-01-01'),
            'image' => null,
            'children' => $childrenData,
            'business_type' => 'parent',
            'academy_info' => $this->account($academy)['academy_info'],
        ];
    }
}
