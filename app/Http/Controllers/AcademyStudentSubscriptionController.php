<?php

namespace App\Http\Controllers;

use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\AcademyStudentSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademyStudentSubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = AcademyStudentSubscription::with(['student', 'group', 'payments'])
            ->whereHas('student', fn($query) => $query->where('academy_id', auth('academy')->id()))
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

        session()->flash('success', 'Subscription created successfully');
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

        session()->flash('success', 'Subscription updated successfully');
        return to_route('academy.subscriptions.index');
    }

    public function destroy(AcademyStudentSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);
        $subscription->delete();

        session()->flash('success', 'Subscription deleted successfully');
        return to_route('academy.subscriptions.index');
    }

    public function storePayment(Request $request, AcademyStudentSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'method' => ['required', 'in:cash,bank_transfer,card,online,other'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($subscription, $data) {
            $subscription->payments()->create($data);

            $paid = $subscription->payments()->sum('amount');
            $subscription->update([
                'payment_status' => $paid >= $subscription->amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
            ]);
        });

        session()->flash('success', 'Payment recorded successfully');
        return back();
    }

    private function formData(): array
    {
        $academyId = auth('academy')->id();

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
        abort_unless($subscription->student?->academy_id === auth('academy')->id(), 404);
    }

    private function authorizeStudent(int $studentId): void
    {
        abort_unless(
            AcademyStudent::where('academy_id', auth('academy')->id())->whereKey($studentId)->exists(),
            404
        );
    }

    private function authorizeGroup(?int $groupId): void
    {
        if ($groupId === null) {
            return;
        }

        abort_unless(
            AcademyGroup::where('academy_id', auth('academy')->id())->whereKey($groupId)->exists(),
            404
        );
    }
}
