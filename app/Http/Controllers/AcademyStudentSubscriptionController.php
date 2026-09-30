<?php

namespace App\Http\Controllers;

use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\AcademyStudentSubscription;
use App\Models\PartnerUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademyStudentSubscriptionController extends Controller
{
    private function getAcademyId(): int
    {
        $user = auth('academy')->user();
        if ($user instanceof PartnerUser) {
            return (int) $user->academy_id;
        }
        return (int) ($user?->id ?? auth('academy')->id());
    }

    public function index()
    {
        $academyId = $this->getAcademyId();
        $subscriptions = AcademyStudentSubscription::with(['student', 'group', 'payments'])
            ->whereHas('student', fn($query) => $query->where('academy_id', $academyId))
            ->latest()
            ->paginate(20);

        return view('Academy.pages.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        return view('Academy.pages.subscriptions.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->authorizeStudent($data['academy_student_id']);
        $this->authorizeGroup($data['academy_group_id'] ?? null);

        AcademyStudentSubscription::create($data);

        session()->flash('success', trans('admin.student_management.subscription_created'));
        return to_route('academy.subscriptions.index');
    }

    public function edit(AcademyStudentSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        return view('Academy.pages.subscriptions.edit', array_merge($this->formData(), compact('subscription')));
    }

    public function update(Request $request, AcademyStudentSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);
        $data = $this->validated($request);
        $this->authorizeStudent($data['academy_student_id']);
        $this->authorizeGroup($data['academy_group_id'] ?? null);
        $subscription->update($data);

        session()->flash('success', trans('admin.student_management.subscription_updated'));
        return to_route('academy.subscriptions.index');
    }

    public function destroy(AcademyStudentSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);
        $subscription->delete();

        session()->flash('success', trans('admin.student_management.subscription_deleted'));
        return to_route('academy.subscriptions.index');
    }

    public function storePayment(Request $request, AcademyStudentSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'method' => ['required', 'in:cash,card,instapay,fawry,bank_transfer,sadad,stc_pay,app_online,other'],
            'method_other' => ['required_if:method,other', 'nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if (($data['method'] ?? null) !== 'other') {
            $data['method_other'] = null;
        }

        DB::transaction(function () use ($subscription, $data) {
            $subscription->payments()->create($data);

            $paid = (float) $subscription->payments()->sum('amount');
            $subscription->update([
                'payment_status' => $paid >= (float) $subscription->amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
            ]);
        });

        session()->flash('success', trans('admin.student_management.payment_recorded') ?: 'تم تسجيل تحصيل الدفعة بنجاح وتحديث الاشتراك.');
        return back();
    }

    public function applyDiscount(Request $request, AcademyStudentSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        $paid = (float) $subscription->payments()->sum('amount');
        $maxDiscount = (float) $subscription->amount - $paid;

        $data = $request->validate([
            'discount_amount'      => ['required', 'numeric', 'min:0.01', 'max:' . max(0.01, $maxDiscount)],
            'discount_reason'      => ['required', 'string', 'max:255'],
            'discount_approved_by' => ['nullable', 'string', 'max:255'],
        ]);

        $approver = $data['discount_approved_by'] ?: (auth('academy')->user()?->name ?: 'الإدارة');

        $notes    = $subscription->notes;
        $noteEntry = 'خصم معتمد بقيمة: ' . number_format($data['discount_amount'], 2) . ' (السبب: ' . $data['discount_reason'] . ' - اعتماد: ' . $approver . ')';
        $notes    = trim(($notes ? $notes . ' | ' : '') . $noteEntry);

        $subscription->update([
            'discount_amount'      => $data['discount_amount'],
            'discount_reason'      => $data['discount_reason'],
            'discount_approved_by' => $approver,
            'discount_approved_at' => now(),
            'notes'                => $notes,
            'payment_status'       => ($paid + (float) $data['discount_amount'] >= (float) $subscription->amount) ? 'paid' : ($paid > 0 || (float) $data['discount_amount'] > 0 ? 'partial' : 'unpaid'),
        ]);

        session()->flash('success', 'تم اعتماد وتطبيق الخصم بنجاح وتحديث الاشتراك.');
        return back();
    }

    public function removeDiscount(Request $request, AcademyStudentSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        if ((float) $subscription->discount_amount <= 0) {
            return back()->with('info', 'لا يوجد خصم مسجل على هذا الاشتراك.');
        }

        $prevDiscount = number_format((float) $subscription->discount_amount, 2);
        $reverser = auth('academy')->user()?->name ?: 'الإدارة';
        $notes = $subscription->notes;
        $notes = trim(($notes ? $notes . ' | ' : '') . 'تم استرداد وإلغاء خصم سابق بقيمة: ' . $prevDiscount . ' بواسطة: ' . $reverser);

        $paid = (float) $subscription->payments()->sum('amount');
        $subscription->update([
            'discount_amount'      => 0,
            'discount_reason'      => null,
            'discount_approved_by' => null,
            'discount_approved_at' => null,
            'notes'                => $notes,
            'payment_status'       => $paid >= (float) $subscription->amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
        ]);

        session()->flash('success', 'تم إلغاء واسترداد الخصم وإعادة المبلغ لرصيد المتبقي.');
        return back();
    }

    // ════════════════════════════════════════════════════════════════════
    // تجميد / إلغاء تجميد الاشتراك (للجيم والمراكز الصحية)
    // ════════════════════════════════════════════════════════════════════

    /**
     * تجميد الاشتراك مع تمديد تاريخ الانتهاء تلقائياً
     */
    public function freeze(Request $request, AcademyStudentSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        if ($subscription->status === 'frozen') {
            return back()->with('info', app()->getLocale() === 'ar'
                ? 'الاشتراك مجمد بالفعل.'
                : 'Subscription is already frozen.');
        }

        $data = $request->validate([
            'freeze_reason' => ['required', 'string', 'max:500'],
            'freeze_days'   => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $subscription->freeze(
            reason:   $data['freeze_reason'],
            days:     (int) $data['freeze_days'],
            frozenBy: auth('academy')->user()?->name
        );

        $msg = app()->getLocale() === 'ar'
            ? 'تم تجميد الاشتراك لمدة ' . $data['freeze_days'] . ' يوماً وتمديد تاريخ الانتهاء تلقائياً إلى ' . $subscription->ends_on->format('Y-m-d') . '.'
            : 'Subscription frozen for ' . $data['freeze_days'] . ' days. End date extended to ' . $subscription->ends_on->format('Y-m-d') . '.';

        $sPhone = preg_replace('/\D+/', '', (string) ($subscription->student?->phone ?: $subscription->student?->guardian_phone));
        if ($sPhone && str_starts_with($sPhone, '0')) $sPhone = '2' . $sPhone;
        if ($sPhone) {
            $academyName = $subscription->student?->academy?->name ?: 'إدارة المنشأة';
            $waText = "مرحباً بك المشترك العزيز " . ($subscription->student?->name ?: '') . " 👋\n"
                . "تم تجميد اشتراكك لدى ({$academyName}) بنجاح بناءً على طلبكم ❄️\n"
                . "📅 مدة التجميد: " . $data['freeze_days'] . " يوماً\n"
                . "📌 سبب التجميد: " . $data['freeze_reason'] . "\n"
                . "⏳ تاريخ نهاية الاشتراك الجديد: " . $subscription->ends_on->format('Y-m-d') . "\n\n"
                . "نتمنى لكم عودة موفقة وقريبة لمواصلة النشاط والتمارين! 🌟";
            session()->flash('freeze_whatsapp_url', 'https://api.whatsapp.com/send?phone=' . $sPhone . '&text=' . urlencode($waText));
        }

        session()->flash('success', $msg);
        return back();
    }

    /**
     * إلغاء تجميد الاشتراك وإعادته للحالة النشطة
     */
    public function unfreeze(AcademyStudentSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        if ($subscription->status !== 'frozen') {
            return back()->with('info', app()->getLocale() === 'ar'
                ? 'الاشتراك غير مجمد.'
                : 'Subscription is not frozen.');
        }

        $subscription->unfreeze();

        session()->flash('success', app()->getLocale() === 'ar'
            ? 'تم إلغاء التجميد وإعادة الاشتراك للحالة النشطة.'
            : 'Subscription unfrozen and reactivated.');
        return back();
    }

    /**
     * استهلاك جلسة علاجية (للمراكز الصحية)
     */
    public function consumeSession(AcademyStudentSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        if ($subscription->remaining_sessions === 0) {
            return back()->with('error', app()->getLocale() === 'ar'
                ? 'لا توجد جلسات متبقية في هذه الباقة.'
                : 'No sessions remaining in this package.');
        }

        $subscription->consumeSession();

        $remaining = $subscription->fresh()->remaining_sessions;
        $msg = app()->getLocale() === 'ar'
            ? 'تم تسجيل الجلسة. المتبقي: ' . ($remaining !== null ? $remaining . ' جلسة' : 'غير محدود')
            : 'Session recorded. Remaining: ' . ($remaining !== null ? $remaining : 'Unlimited');

        session()->flash('success', $msg);
        return back();
    }

    private function formData(): array
    {
        $academyId = $this->getAcademyId();

        return [
            'students' => AcademyStudent::where('academy_id', $academyId)->orderBy('name')->get(),
            'groups'   => AcademyGroup::where('academy_id', $academyId)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'academy_student_id' => ['required', 'integer', 'exists:academy_students,id'],
            'academy_group_id'   => ['nullable', 'integer', 'exists:academy_groups,id'],
            'starts_on'          => ['required', 'date'],
            'ends_on'            => ['required', 'date', 'after_or_equal:starts_on'],
            'amount'             => ['required', 'numeric', 'min:0'],
            'status'             => ['required', 'in:pending,active,frozen,expired,cancelled'],
            'payment_status'     => ['required', 'in:unpaid,partial,paid'],
            'sessions_total'     => ['nullable', 'integer', 'min:1'],
            'notes'              => ['nullable', 'string'],
        ]);
    }

    private function authorizeSubscription(AcademyStudentSubscription $subscription): void
    {
        abort_unless($subscription->student?->academy_id === $this->getAcademyId(), 404);
    }

    private function authorizeStudent(int $studentId): void
    {
        abort_unless(
            AcademyStudent::where('academy_id', $this->getAcademyId())->whereKey($studentId)->exists(),
            404
        );
    }

    private function authorizeGroup(?int $groupId): void
    {
        if ($groupId === null) {
            return;
        }

        abort_unless(
            AcademyGroup::where('academy_id', $this->getAcademyId())->whereKey($groupId)->exists(),
            404
        );
    }
}

