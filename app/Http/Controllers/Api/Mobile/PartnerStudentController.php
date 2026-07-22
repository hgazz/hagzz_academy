<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Academies;
use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\AcademyStudentSubscription;
use App\Support\MembershipCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerStudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $academyId = $this->academyId($request);
        $data = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,inactive,suspended'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $students = AcademyStudent::query()
            ->with('groups:id,name')
            ->where('academy_id', $academyId)
            ->when($data['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($data['search'] ?? null, function ($query, $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('guardian_phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => collect($students->items())->map(fn (AcademyStudent $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'phone' => $student->phone ?: $student->guardian_phone,
                'status' => $student->status,
                'image' => $student->avatarUrl(),
                'groups' => $student->groups->pluck('name')->values(),
            ]),
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'total' => $students->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $academyId = $this->academyId($request);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'group_id' => ['nullable', 'integer', 'exists:academy_groups,id'],
        ]);

        $student = AcademyStudent::create([
            'academy_id' => $academyId,
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'guardian_name' => $validated['guardian_name'] ?? null,
            'guardian_phone' => $validated['guardian_phone'] ?? null,
            'status' => 'active',
        ]);

        if (!empty($validated['group_id'])) {
            $student->groups()->attach($validated['group_id']);
        }

        return response()->json([
            'message' => 'تمت إضافة الطالب بنجاح',
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'phone' => $student->phone,
                'status' => $student->status,
            ],
        ], 201);
    }

    public function show(Request $request, int $student): JsonResponse
    {
        $model = AcademyStudent::query()
            ->where('academy_id', $this->academyId($request))
            ->with(['groups', 'subscriptions.group', 'subscriptions.payments', 'attendanceRecords.session.group'])
            ->findOrFail($student);
        $subscription = $model->subscriptions->sortByDesc('starts_on')->first();
        $attendance = $model->attendanceRecords->groupBy('status')->map->count();

        return response()->json(['student' => [
            'id' => $model->id,
            'name' => $model->name,
            'phone' => $model->phone,
            'email' => $model->email,
            'guardian_name' => $model->guardian_name,
            'guardian_phone' => $model->guardian_phone,
            'status' => $model->status,
            'image' => $model->avatarUrl(),
            'membership_code' => MembershipCode::make($model),
            'groups' => $model->groups->pluck('name')->values(),
            'subscription' => $subscription ? [
                'group' => $subscription->group?->name,
                'starts_on' => $subscription->starts_on?->format('Y-m-d'),
                'ends_on' => $subscription->ends_on?->format('Y-m-d'),
                'status' => $subscription->status,
                'payment_status' => $subscription->payment_status,
                'amount' => (float) $subscription->amount,
                'remaining' => $subscription->remaining_amount,
            ] : null,
            'attendance' => [
                'present' => (int) $attendance->get('present', 0),
                'late' => (int) $attendance->get('late', 0),
                'absent' => (int) $attendance->get('absent', 0),
                'excused' => (int) $attendance->get('excused', 0),
                'total' => $model->attendanceRecords->count(),
            ],
        ]]);
    }

    public function subscriptions(Request $request): JsonResponse
    {
        $academyId = $this->academyId($request);
        $subscriptions = AcademyStudentSubscription::query()
            ->whereHas('student', fn ($query) => $query->where('academy_id', $academyId))
            ->with(['student:id,name,phone', 'group:id,name'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'subscriptions' => collect($subscriptions->items())->map(fn ($sub) => [
                'id' => $sub->id,
                'student_name' => $sub->student?->name ?? 'طالب غير محدد',
                'group_name' => $sub->group?->name ?? 'عام',
                'amount' => (float) $sub->amount,
                'status' => $sub->status,
                'payment_status' => $sub->payment_status ?? 'paid',
                'starts_on' => $sub->starts_on?->format('Y-m-d'),
                'ends_on' => $sub->ends_on?->format('Y-m-d'),
            ]),
        ]);
    }

    public function notifications(Request $request): JsonResponse
    {
        return response()->json([
            'notifications' => [
                [
                    'id' => 1,
                    'title' => 'اشتراك ينتهي قريباً',
                    'body' => 'اشتراك الطالب أحمد اللاعب ينتهي خلال 3 أيام.',
                    'created_at' => now()->subHours(2)->diffForHumans(),
                    'read' => false,
                ],
                [
                    'id' => 2,
                    'title' => 'تم دفع قسط جديد',
                    'body' => 'تم استلام مبلغ 500 ريال مقابل اشتراك مجموعة الناشئين.',
                    'created_at' => now()->subDay()->diffForHumans(),
                    'read' => true,
                ],
            ],
        ]);
    }

    public function messages(Request $request): JsonResponse
    {
        return response()->json([
            'conversations' => [
                [
                    'id' => 1,
                    'name' => 'محمد ولي الأمر (والد أحمد)',
                    'last_message' => 'هل تمرين الغد في موعده الساعة 5 مساءً؟',
                    'time' => '10:30 م',
                    'unread' => 1,
                ],
                [
                    'id' => 2,
                    'name' => 'الكابتن عبد الرحمن (مدرب الناشئين)',
                    'last_message' => 'تم إتمام تسجيل الحضور لحصة اليوم بنجاح.',
                    'time' => '06:15 م',
                    'unread' => 0,
                ],
            ],
        ]);
    }

    private function academyId(Request $request): int
    {
        abort_unless($request->user() instanceof Academies, 403);

        return $request->user()->id;
    }
}
