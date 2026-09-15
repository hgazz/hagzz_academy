<?php

namespace App\Services;

use App\Models\AcademyCampParticipant;
use App\Models\AcademyStudentSubscription;
use App\Models\Invoice;
use App\Models\PartnerExpense;
use App\Models\VenueBooking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AcademyFinancialEngine
{
    /**
     * Unified Revenue Calculation across all 4 academy revenue streams:
     * 1. Student Subscriptions (AcademyStudentSubscription & Payments)
     * 2. Direct Training Bookings (Invoice)
     * 3. Venue & Court Rentals (VenueBooking)
     * 4. Camps & Clinics (AcademyCampParticipant)
     */
    public function getRevenueSummary(int $academyId, array $filters = []): array
    {
        $startDate = $filters['from_date'] ?? $filters['start_date'] ?? null;
        $endDate = $filters['to_date'] ?? $filters['end_date'] ?? null;
        $branchId = $filters['branch_id'] ?? null;

        // 1. Subscriptions
        $subQuery = AcademyStudentSubscription::query()
            ->whereHas('student', fn (Builder $q) => $q->where('academy_id', $academyId))
            ->where('status', '!=', 'cancelled')
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->when($branchId, fn (Builder $q) => $q->whereHas('group.training', fn (Builder $tr) => $tr->where('address_id', $branchId)));

        $subBilled = (float) (clone $subQuery)->sum('amount');
        $subCollected = (float) DB::table('academy_student_payments')
            ->whereIn('academy_student_subscription_id', (clone $subQuery)->select('id'))
            ->when($startDate, fn ($q) => $q->whereDate('payment_date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('payment_date', '<=', $endDate))
            ->sum('amount');
        $subRemaining = max(0, $subBilled - $subCollected);

        // 2. Training Invoices
        $invoiceQuery = Invoice::query()
            ->whereHas('training', fn (Builder $q) => $q->where('academy_id', $academyId))
            ->where('is_canceled', false)
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->when($branchId, fn (Builder $q) => $q->whereHas('training', fn (Builder $tr) => $tr->where('address_id', $branchId)));

        $invBilled = (float) (clone $invoiceQuery)->sum('amount');
        $invCollected = (float) (clone $invoiceQuery)->sum(DB::raw('COALESCE(paid_amount, amount)'));
        $invRemaining = max(0, $invBilled - $invCollected);

        // 3. Venue Bookings
        $venueQuery = VenueBooking::query()
            ->where('academy_id', $academyId)
            ->where('status', '!=', 'cancelled')
            ->when($startDate, fn (Builder $q) => $q->whereDate('starts_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('starts_at', '<=', $endDate))
            ->when($branchId, fn (Builder $q) => $q->whereHas('space.venue', fn (Builder $v) => $v->where('address_id', $branchId)));

        $venueBilled = (float) (clone $venueQuery)->sum('total_amount');
        $venueCollected = (float) (clone $venueQuery)->sum('paid_amount');
        $venueRemaining = max(0, $venueBilled - $venueCollected);

        // 4. Camps
        $campQuery = AcademyCampParticipant::query()
            ->whereHas('camp', fn (Builder $q) => $q->where('academy_id', $academyId))
            ->where('status', '!=', 'cancelled')
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate));

        $campBilled = (float) (clone $campQuery)->sum('total_fee');
        $campCollected = (float) (clone $campQuery)->sum('paid_amount');
        $campRemaining = max(0, $campBilled - $campCollected);

        $totalBilled = $subBilled + $invBilled + $venueBilled + $campBilled;
        $totalCollected = $subCollected + $invCollected + $venueCollected + $campCollected;
        $totalRemaining = $subRemaining + $invRemaining + $venueRemaining + $campRemaining;

        return [
            'total_billed' => round($totalBilled, 2),
            'total_collected' => round($totalCollected, 2),
            'total_remaining' => round($totalRemaining, 2),
            'streams' => [
                'subscriptions' => ['billed' => round($subBilled, 2), 'collected' => round($subCollected, 2), 'remaining' => round($subRemaining, 2)],
                'trainings' => ['billed' => round($invBilled, 2), 'collected' => round($invCollected, 2), 'remaining' => round($invRemaining, 2)],
                'venues' => ['billed' => round($venueBilled, 2), 'collected' => round($venueCollected, 2), 'remaining' => round($venueRemaining, 2)],
                'camps' => ['billed' => round($campBilled, 2), 'collected' => round($campCollected, 2), 'remaining' => round($campRemaining, 2)],
            ],
        ];
    }

    /**
     * Unified Expenses Calculation
     */
    public function getExpenseSummary(int $academyId, array $filters = []): array
    {
        $startDate = $filters['from_date'] ?? $filters['start_date'] ?? null;
        $endDate = $filters['to_date'] ?? $filters['end_date'] ?? null;
        $branchId = $filters['branch_id'] ?? null;
        $coachId = $filters['coach_id'] ?? null;
        $categoryId = $filters['category_id'] ?? null;
        $expenseType = $filters['expense_type'] ?? null;

        $query = PartnerExpense::where('academy_id', $academyId)
            ->when($startDate, fn (Builder $q) => $q->whereDate('expense_date', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('expense_date', '<=', $endDate))
            ->when($branchId, fn (Builder $q) => $q->where('branch_id', $branchId))
            ->when($coachId, fn (Builder $q) => $q->where('coach_id', $coachId))
            ->when($categoryId, fn (Builder $q) => $q->where('category_id', $categoryId))
            ->when($expenseType, fn (Builder $q) => $q->where('expense_type', $expenseType));

        $totalExpenses = (float) (clone $query)->sum(DB::raw('COALESCE(base_amount, amount)'));

        // Coach specific expenses
        $coachExpenses = (float) (clone $query)->whereNotNull('coach_id')->sum(DB::raw('COALESCE(base_amount, amount)'));

        // Branch specific expenses
        $branchExpenses = (float) (clone $query)->whereNotNull('branch_id')->sum(DB::raw('COALESCE(base_amount, amount)'));

        return [
            'total_expenses' => round($totalExpenses, 2),
            'coach_expenses' => round($coachExpenses, 2),
            'branch_expenses' => round($branchExpenses, 2),
            'count' => (clone $query)->count(),
        ];
    }

    /**
     * Net Profit Summary combining true revenue collected and total expenses
     */
    public function getNetProfitSummary(int $academyId, array $filters = []): array
    {
        $revenue = $this->getRevenueSummary($academyId, $filters);
        $expenses = $this->getExpenseSummary($academyId, $filters);

        $collected = $revenue['total_collected'];
        $billed = $revenue['total_billed'];
        $totalExp = $expenses['total_expenses'];
        $netProfit = $collected - $totalExp;
        $profitMargin = $collected > 0 ? round(($netProfit / $collected) * 100, 1) : 0;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'total_billed' => $billed,
            'total_revenue' => $collected,
            'total_expenses' => $totalExp,
            'net_profit' => round($netProfit, 2),
            'profit_margin' => $profitMargin,
        ];
    }

    /**
     * Accounting Journal Entry structure (قيد محاسبي) for an expense
     */
    public static function buildJournalEntry(PartnerExpense $expense): array
    {
        $isAr = app()->getLocale() === 'ar';
        $entryNumber = 'JV-' . str_pad((string) $expense->id, 6, '0', STR_PAD_LEFT);

        // Debit Account (المدين: حساب المصروف أو المدرب أو الفرع)
        $debitAccount = $isAr ? 'مصروفات عامة' : 'General Expenses';
        if ($expense->coach_id && $expense->coach) {
            $debitAccount = ($isAr ? 'مستحقات مدرب: ' : 'Coach Payable: ') . $expense->coach->name;
        } elseif ($expense->is_external_coach) {
            $debitAccount = $isAr ? 'أتعاب مدرب خارجي / زائر' : 'External / Guest Coach Fee';
        } elseif ($expense->category) {
            $debitAccount = $isAr ? ($expense->category->name_ar ?: $expense->category->name_en) : ($expense->category->name_en ?: $expense->category->name_ar);
        }

        // Credit Account (الدائن: الصندوق أو البنك حسب وسيلة الدفع)
        $creditAccount = match ($expense->payment_method) {
            'bank_transfer' => $isAr ? 'البنك / تحويل بنكي' : 'Bank Transfer',
            'card' => $isAr ? 'شبكة / نقاط بيع / بطاقة' : 'POS / Card',
            'online' => $isAr ? 'بوابة الدفع الإلكتروني' : 'Online Gateway',
            default => $isAr ? 'الصندوق / النقدية (خزينة الأكاديمية)' : 'Cash / Academy Treasury',
        };

        return [
            'entry_number' => $entryNumber,
            'date' => $expense->expense_date?->format('Y-m-d') ?: date('Y-m-d'),
            'debit_account' => $debitAccount,
            'credit_account' => $creditAccount,
            'amount' => (float) ($expense->base_amount ?: $expense->amount),
            'currency' => $expense->base_currency ?: 'SAR',
            'notes' => $expense->notes,
            'created_by' => $expense->creator?->name ?: ($expense->approved_by ?: '-'),
        ];
    }
}
