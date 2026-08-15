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
            'discount_amount' => ['required', 'numeric', 'min:0.01', 'max:' . max(0.01, $maxDiscount)],
            'discount_reason' => ['required', 'string', 'max:255'],
            'discount_approved_by' => ['nullable', 'string', 'max:255'],
        ]);

        $approver = $data['discount_approved_by'] ?: (auth('academy')->user()?->name ?: 'الإدارة');

        $notes = $subscription->notes;
        $noteEntry = 'خصم معتمد بقيمة: ' . number_format($data['discount_amount'], 2) . ' ج.م (السبب: ' . $data['discount_reason'] . ' - اعتماد: ' . $approver . ')';
        $notes = trim(($notes ? $notes . ' | ' : '') . $noteEntry);

        $subscription->update([
            'discount_amount' => $data['discount_amount'],
            'discount_reason' => $data['discount_reason'],
            'discount_approved_by' => $approver,
            'discount_approved_at' => now(),
            'notes' => $notes,
            'payment_status' => ($paid + (float) $data['discount_amount'] >= (float) $subscription->amount) ? 'paid' : ($paid > 0 || (float) $data['discount_amount'] > 0 ? 'partial' : 'unpaid'),
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
        $notes = trim(($notes ? $notes . ' | ' : '') . 'تم استرداد وإلغاء خصم سابق بقيمة: ' . $prevDiscount . ' ج.م بواسطة: ' . $reverser);

        $paid = (float) $subscription->payments()->sum('amount');
        $subscription->update([
            'discount_amount' => 0,
            'discount_reason' => null,
            'discount_approved_by' => null,
            'discount_approved_at' => null,
            'notes' => $notes,
            'payment_status' => $paid >= (float) $subscription->amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
        ]);

        session()->flash('success', 'تم إلغاء واسترداد الخصم وإعادة المبلغ لرصيد المتبقي.');
        return back();
    }

    private function formData(): array
    {
        $academyId = $this->getAcademyId();

        return [
            'students' => AcademyStudent::where('academy_id', $academyId)->orderBy('name')->get(),
            'groups' => AcademyGroup::where('academy_id', $academyId)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'academy_student_id' => ['required', 'integer', 'exists:academy_students,id'],
            'academy_group_id' => ['nullable', 'integer', 'exists:academy_groups,id'],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,active,expired,cancelled'],
            'payment_status' => ['required', 'in:unpaid,partial,paid'],
            'notes' => ['nullable', 'string'],
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
