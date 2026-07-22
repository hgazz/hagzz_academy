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
        $user = $request->user();
        $academyId = $this->academyId($request);
        $currency = $this->getAcademyCurrency($academyId);
        $subscriptionsList = [];

        $tokenName = null;
        try {
            $tokenName = $user->currentAccessToken()?->name;
        } catch (\Throwable $e) {}

        if ($tokenName === 'player-token' || $user instanceof AcademyStudent) {
            // Player Role: filter strictly by student_id
            try {
                $student = ($user instanceof AcademyStudent)
                    ? $user
                    : AcademyStudent::where('email', $user->email)->orWhere('phone', $user->phone)->first();

                if ($student) {
                    $subs = AcademyStudentSubscription::where('student_id', $student->id)->with(['student', 'group'])->latest()->get();
                    foreach ($subs as $s) {
                        $originalPrice = (float) ($s->price ?: $s->amount);
                        $discount = (float) ($s->discount_amount ?: 0.0);
                        $finalAmount = (float) $s->amount;
                        $paid = $s->payment_status === 'unpaid' ? 0.0 : $finalAmount;

                        $subscriptionsList[] = [
                            'id' => $s->id,
                            'student_id' => $student->id,
                            'invoice_no' => 'INV-' . date('Y') . '-' . str_pad($s->id, 5, '0', STR_PAD_LEFT),
                            'invoice_date' => $s->created_at?->format('Y-m-d') ?: date('Y-m-d'),
                            'student_name' => $student->name,
                            'guardian_name' => $student->guardian_name ?: '',
                            'group_name' => $s->group?->name ?: '',
                            'package_name' => $s->group?->name ? 'باقة ' . $s->group->name : 'اشتراك الأكاديمية',
                            'original_price' => $originalPrice,
                            'discount' => $discount,
                            'amount' => $finalAmount,
                            'paid_amount' => $paid,
                            'remaining_amount' => max(0, $finalAmount - $paid),
                            'currency' => $currency,
                            'payment_method' => $s->payment_method ?: 'الدفع الإلكتروني',
                            'status' => $s->status ?: 'active',
                            'payment_status' => $s->payment_status ?: 'paid',
                            'starts_on' => is_string($s->starts_on) ? $s->starts_on : ($s->starts_on?->format('Y-m-d') ?: null),
                            'ends_on' => is_string($s->ends_on) ? $s->ends_on : ($s->ends_on?->format('Y-m-d') ?: null),
                        ];
                    }
                }
            } catch (\Throwable $e) {}
        } else if ($tokenName === 'parent-token') {
            // Parent Role: filter strictly by children's student_ids
            try {
                $students = AcademyStudent::where('guardian_phone', $user->phone)->orWhere('phone', $user->phone)->pluck('id')->toArray();
                if (!empty($students)) {
                    $subs = AcademyStudentSubscription::whereIn('student_id', $students)->with(['student', 'group'])->latest()->get();
                    foreach ($subs as $s) {
                        $originalPrice = (float) ($s->price ?: $s->amount);
                        $discount = (float) ($s->discount_amount ?: 0.0);
                        $finalAmount = (float) $s->amount;
                        $paid = $s->payment_status === 'unpaid' ? 0.0 : $finalAmount;

                        $subscriptionsList[] = [
                            'id' => $s->id,
                            'student_id' => $s->student_id,
                            'invoice_no' => 'INV-' . date('Y') . '-' . str_pad($s->id, 5, '0', STR_PAD_LEFT),
                            'invoice_date' => $s->created_at?->format('Y-m-d') ?: date('Y-m-d'),
                            'student_name' => $s->student?->name ?: 'الابن المسجل',
                            'guardian_name' => $user->name,
                            'group_name' => $s->group?->name ?: '',
                            'package_name' => $s->group?->name ? 'باقة ' . $s->group->name : 'اشتراك باقة الأبناء',
                            'original_price' => $originalPrice,
                            'discount' => $discount,
                            'amount' => $finalAmount,
                            'paid_amount' => $paid,
                            'remaining_amount' => max(0, $finalAmount - $paid),
                            'currency' => $currency,
                            'payment_method' => $s->payment_method ?: 'الدفع الإلكتروني',
                            'status' => $s->status ?: 'active',
                            'payment_status' => $s->payment_status ?: 'paid',
                            'starts_on' => is_string($s->starts_on) ? $s->starts_on : ($s->starts_on?->format('Y-m-d') ?: null),
                            'ends_on' => is_string($s->ends_on) ? $s->ends_on : ($s->ends_on?->format('Y-m-d') ?: null),
                        ];
                    }
                }
            } catch (\Throwable $e) {}
        } else {
            // Academy Manager / Coach Role: full academy subscriptions
            try {
                $subs = AcademyStudentSubscription::query()
                    ->whereHas('student', fn ($q) => $q->where('academy_id', $academyId))
                    ->with(['student:id,name,phone,guardian_name', 'group:id,name'])
                    ->latest()
                    ->paginate(30);

                $subscriptionsList = collect($subs->items())->map(function ($s) use ($currency) {
                    $originalPrice = (float) ($s->price ?: $s->amount);
                    $discount = (float) ($s->discount_amount ?: 0.0);
                    $finalAmount = (float) $s->amount;
                    $paid = $s->payment_status === 'unpaid' ? 0.0 : $finalAmount;
                    $remaining = max(0, $finalAmount - $paid);

                    return [
                        'id' => $s->id,
                        'student_id' => $s->student_id,
                        'invoice_no' => 'INV-' . date('Y') . '-' . str_pad($s->id, 5, '0', STR_PAD_LEFT),
                        'invoice_date' => $s->created_at?->format('Y-m-d') ?: date('Y-m-d'),
                        'student_name' => $s->student?->name ?: 'طالب',
                        'guardian_name' => $s->student?->guardian_name ?: '',
                        'group_name' => $s->group?->name ?: '',
                        'package_name' => $s->group?->name ? 'باقة ' . $s->group->name : 'اشتراك الأكاديمية',
                        'original_price' => $originalPrice,
                        'discount' => $discount,
                        'amount' => $finalAmount,
                        'paid_amount' => $paid,
                        'remaining_amount' => $remaining,
                        'currency' => $currency,
                        'payment_method' => $s->payment_method ?: 'الدفع الإلكتروني',
                        'status' => $s->status ?: 'active',
                        'payment_status' => $s->payment_status ?: 'paid',
                        'starts_on' => is_string($s->starts_on) ? $s->starts_on : ($s->starts_on?->format('Y-m-d') ?: null),
                        'ends_on' => is_string($s->ends_on) ? $s->ends_on : ($s->ends_on?->format('Y-m-d') ?: null),
                    ];
                })->toArray();
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'currency' => $currency,
            'subscriptions' => $subscriptionsList,
        ]);
    }

    public function financialReport(Request $request): JsonResponse
    {
        $user = $request->user();
        $academyId = $this->academyId($request);
        $currency = $this->getAcademyCurrency($academyId);

        $subsQuery = AcademyStudentSubscription::query()
            ->whereHas('student', fn ($q) => $q->where('academy_id', $academyId));
        
        $subsBilled = (float) $subsQuery->sum('amount');
        $subsPaid = (float) $subsQuery->where('payment_status', 'paid')->sum('amount');
        $subsRemaining = max(0, $subsBilled - $subsPaid);
        $subsCount = $subsQuery->count();

        $trainingQuery = \App\Models\Invoice::query()
            ->whereHas('training', fn ($q) => $q->where('academy_id', $academyId));
        
        $trainingBilled = (float) $trainingQuery->sum('amount');
        $trainingPaid = (float) $trainingQuery->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(paid_amount, amount)'));
        $trainingRemaining = max(0, $trainingBilled - $trainingPaid);
        $trainingCount = $trainingQuery->count();

        $venueQuery = \App\Models\VenueBooking::query()->where('academy_id', $academyId);
        $venueBilled = (float) $venueQuery->sum('total_amount');
        $venuePaid = (float) $venueQuery->sum('paid_amount');
        $venueRemaining = max(0, $venueBilled - $venuePaid);
        $venueCount = $venueQuery->count();

        $totalBilled = $subsBilled + $trainingBilled + $venueBilled;
        $totalCollected = $subsPaid + $trainingPaid + $venuePaid;
        $totalRemaining = max(0, $totalBilled - $totalCollected);
        $totalRecords = $subsCount + $trainingCount + $venueCount;
        $collectionRate = $totalBilled > 0 ? round(($totalCollected / $totalBilled) * 100, 1) : 100.0;

        return response()->json([
            'currency' => $currency,
            'summary' => [
                'billed' => (float) $totalBilled,
                'collected' => (float) $totalCollected,
                'remaining' => (float) $totalRemaining,
                'records' => (int) $totalRecords,
                'collection_rate' => $totalBilled > 0 ? $collectionRate : 0.0,
            ],
            'breakdown' => [
                'subscriptions' => [
                    'name' => 'اشتراكات الطلاب واللاعبين',
                    'billed' => (float) $subsBilled,
                    'collected' => (float) $subsPaid,
                    'remaining' => (float) $subsRemaining,
                    'records' => (int) $subsCount,
                ],
                'training' => [
                    'name' => 'حجوزات التمارين الخاصة',
                    'billed' => (float) $trainingBilled,
                    'collected' => (float) $trainingPaid,
                    'remaining' => (float) $trainingRemaining,
                    'records' => (int) $trainingCount,
                ],
                'venues' => [
                    'name' => 'حجوزات وإيجارات الملاعب',
                    'billed' => (float) $venueBilled,
                    'collected' => (float) $venuePaid,
                    'remaining' => (float) $venueRemaining,
                    'records' => (int) $venueCount,
                ],
            ],
        ]);
    }

    public function notifications(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = [];

        if ($user instanceof Academies) {
            // Manager / Academy Notifications
            try {
                $expiring = AcademyStudentSubscription::query()
                    ->whereHas('student', fn ($q) => $q->where('academy_id', $user->id))
                    ->with('student:id,name')
                    ->whereDate('ends_on', '<=', now()->addDays(7))
                    ->latest()
                    ->take(5)
                    ->get();

                foreach ($expiring as $sub) {
                    $notifications[] = [
                        'id' => 'exp_' . $sub->id,
                        'title' => 'اشتراك ينتهي قريباً ⏳',
                        'body' => 'اشتراك الطالب (' . ($sub->student?->name ?: 'طالب') . ') ينتهي بتاريخ ' . ($sub->ends_on?->format('Y-m-d') ?: 'قريباً') . '.',
                        'created_at' => 'منذ ساعة',
                        'read' => false,
                    ];
                }

                $newStudents = AcademyStudent::where('academy_id', $user->id)->latest()->take(5)->get();
                foreach ($newStudents as $st) {
                    $notifications[] = [
                        'id' => 'st_' . $st->id,
                        'title' => 'طالب جديد مسجل ⚽',
                        'body' => 'تم تسجيل الطالب (' . $st->name . ') بنجاح في الأكاديمية.',
                        'created_at' => $st->created_at?->diffForHumans() ?? 'اليوم',
                        'read' => true,
                    ];
                }
            } catch (\Throwable $e) {}
        } else if ($user instanceof AcademyStudent) {
            // Player / Student Notifications (Live Check-in, Check-out & Subscriptions)
            try {
                $records = \App\Models\AcademyAttendanceRecord::where('academy_student_id', $user->id)
                    ->with('session.group:id,name')
                    ->latest()
                    ->take(5)
                    ->get();

                foreach ($records as $att) {
                    $groupName = $att->session?->group?->name ?: 'التدريبية';
                    if ($att->check_in_at) {
                        $notifications[] = [
                            'id' => 'att_in_' . $att->id,
                            'title' => 'تسجيل حضور الحصة التدريبية ⚽',
                            'body' => 'تم تسجيل حضورك بنجاح في تمارين مجموعة (' . $groupName . ') الساعة ' . substr($att->check_in_at, 0, 5) . '.',
                            'created_at' => $att->updated_at?->diffForHumans() ?? 'اليوم',
                            'read' => false,
                        ];
                    }
                    if ($att->check_out_at) {
                        $notifications[] = [
                            'id' => 'att_out_' . $att->id,
                            'title' => 'تسجيل انصراف بعد الحصة 🏃‍♂️',
                            'body' => 'تم تسجيل انصرافك ومغادرتك بعد انتهاء الحصة التدريبية الساعة ' . substr($att->check_out_at, 0, 5) . '.',
                            'created_at' => $att->updated_at?->diffForHumans() ?? 'اليوم',
                            'read' => true,
                        ];
                    }
                }

                $sub = AcademyStudentSubscription::where('student_id', $user->id)->latest()->first();
                if ($sub) {
                    $notifications[] = [
                        'id' => 'sub_' . $sub->id,
                        'title' => 'حالة الاشتراك وسريان العضوية 💳',
                        'body' => 'اشتراكك الحالي بالأكاديمية ساري وحالتك نشطة حتى تاريخ ' . ($sub->ends_on ? (is_string($sub->ends_on) ? $sub->ends_on : $sub->ends_on->format('Y-m-d')) : 'نهاية الشهر') . '.',
                        'created_at' => 'الآن',
                        'read' => true,
                    ];
                }
            } catch (\Throwable $e) {}
        } else {
            // Parent Account Notifications (Notifications for all children of this parent)
            try {
                $studentIds = AcademyStudent::where('guardian_phone', $user->phone)->orWhere('phone', $user->phone)->pluck('id')->toArray();
                if (!empty($studentIds)) {
                    $records = \App\Models\AcademyAttendanceRecord::whereIn('academy_student_id', $studentIds)
                        ->with(['student:id,name', 'session.group:id,name'])
                        ->latest()
                        ->take(8)
                        ->get();

                    foreach ($records as $att) {
                        $studentName = $att->student?->name ?: 'ابنكم';
                        $groupName = $att->session?->group?->name ?: 'التدريبية';
                        if ($att->check_in_at) {
                            $notifications[] = [
                                'id' => 'parent_in_' . $att->id,
                                'title' => "تسجيل حضور ابنكم ($studentName) ⚽",
                                'body' => "تنبيه: تم تسجيل دخول وحضور الطالب ($studentName) في الأكاديمية بمجموعة ($groupName) الساعة " . substr($att->check_in_at, 0, 5) . '.',
                                'created_at' => $att->updated_at?->diffForHumans() ?? 'اليوم',
                                'read' => false,
                            ];
                        }
                        if ($att->check_out_at) {
                            $notifications[] = [
                                'id' => 'parent_out_' . $att->id,
                                'title' => "تسجيل انصراف ابنكم ($studentName) 🏃‍♂️",
                                'body' => "تنبيه: تم تسجيل مغادرة وانصراف الطالب ($studentName) بأمان بعد انتهاء الحصة التدريبية الساعة " . substr($att->check_out_at, 0, 5) . '.',
                                'created_at' => $att->updated_at?->diffForHumans() ?? 'اليوم',
                                'read' => true,
                            ];
                        }
                    }

                    $subs = AcademyStudentSubscription::whereIn('student_id', $studentIds)->with('student:id,name')->latest()->take(4)->get();
                    foreach ($subs as $sub) {
                        $notifications[] = [
                            'id' => 'parent_sub_' . $sub->id,
                            'title' => 'تحديث اشتراك الابن (' . ($sub->student?->name ?: 'الابن') . ') 💳',
                            'body' => 'اشتراك الابن (' . ($sub->student?->name ?: 'الابن') . ') ساري بالأكاديمية حتى تاريخ ' . ($sub->ends_on ? (is_string($sub->ends_on) ? $sub->ends_on : $sub->ends_on->format('Y-m-d')) : 'نهاية الشهر') . '.',
                            'created_at' => 'اليوم',
                            'read' => true,
                        ];
                    }
                }
            } catch (\Throwable $e) {}
        }

        if (empty($notifications)) {
            $notifications[] = [
                'id' => 99,
                'title' => 'أهلاً بك في منصة Hagzz ⚽',
                'body' => 'جميع التنبيهات والإشعارات الخاصة بالحضور والانصراف والاشتراكات ستظهر هنا فور صدورها.',
                'created_at' => 'الآن',
                'read' => true,
            ];
        }

        return response()->json(['notifications' => array_values($notifications)]);
    }

    public function messages(Request $request): JsonResponse
    {
        $user = $request->user();
        $conversations = [];

        if ($user instanceof Academies) {
            // Manager: get real students / parents list
            try {
                $students = AcademyStudent::where('academy_id', $user->id)->latest()->take(6)->get();
                foreach ($students as $st) {
                    $conversations[] = [
                        'id' => $st->id,
                        'name' => ($st->guardian_name ?: $st->name) . ' (ولي أمر ' . $st->name . ')',
                        'last_message' => 'استفسار بخصوص مواعيد التمارين والاشتراك.',
                        'time' => '10:30 م',
                        'unread' => rand(0, 1),
                    ];
                }
            } catch (\Throwable $e) {}
        } else if ($user instanceof AcademyStudent) {
            // Player Conversations
            $conversations = [
                [
                    'id' => 1,
                    'name' => 'الكابتن والمدرب الرئيسي ⚽',
                    'last_message' => 'أحسنت يا ' . $user->name . '، أداء رائع في تمرين اللياقة اليوم!',
                    'time' => '06:15 م',
                    'unread' => 1,
                ],
                [
                    'id' => 2,
                    'name' => 'إدارة الأكاديمية (الدعم الفني)',
                    'last_message' => 'أهلاً بك! يمكنك الاستفسار عن أي تفاصيل يخص باقتك هنا.',
                    'time' => 'أمس',
                    'unread' => 0,
                ],
            ];
        } else {
            // Parent Conversations
            $conversations = [
                [
                    'id' => 1,
                    'name' => 'إدارة الأكاديمية الرئيسية ⚽',
                    'last_message' => 'أهلاً بك ولي الأمر المحترم، يسعدنا تواصلك معنا دائماً.',
                    'time' => '05:40 م',
                    'unread' => 1,
                ],
                [
                    'id' => 2,
                    'name' => 'مدرب الفئة البراعم والناشئين',
                    'last_message' => 'تم تسجيل تقييم وحضور الأبناء بحالة ممتازة.',
                    'time' => 'أمس',
                    'unread' => 0,
                ],
            ];
        }

        return response()->json(['conversations' => $conversations]);
    }

    private function academyId(Request $request): int
    {
        $user = $request->user();
        if ($user instanceof Academies) {
            return $user->id;
        }
        if ($user instanceof AcademyStudent) {
            return $user->academy_id;
        }

        $student = AcademyStudent::where('phone', $user->phone)->orWhere('guardian_phone', $user->phone)->first();
        return $student ? $student->academy_id : 1;
    }

    private function getAcademyCurrency(int $academyId): string
    {
        try {
            $academy = Academies::find($academyId);
            if (!empty($academy?->currency)) {
                return $academy->currency;
            }

            $addr = \App\Models\Address::where('academy_id', $academyId)->with('country')->first();
            if (!empty($addr?->country?->currency)) {
                return $addr->country->currency;
            }

            $venueCurr = \App\Models\Venue::where('academy_id', $academyId)->whereNotNull('currency')->value('currency');
            if (!empty($venueCurr)) {
                return $venueCurr;
            }

            $invCurr = \App\Models\Invoice::whereHas('training', fn ($q) => $q->where('academy_id', $academyId))->whereNotNull('currency')->value('currency');
            if (!empty($invCurr)) {
                return $invCurr;
            }
        } catch (\Throwable $e) {}

        return 'ر.س';
    }
}
