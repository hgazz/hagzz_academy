<?php

namespace App\Http\Controllers;

use App\Models\AcademyCampParticipant;
use App\Models\AcademyStudentSubscription;
use App\Models\AcademyStudentPayment;
use App\Models\Address;
use App\Models\Coach;
use App\Models\Invoice;
use App\Models\PartnerExpense;
use App\Models\Sport;
use App\Models\Training;
use App\Models\VenueBooking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademyFinancialReportController extends Controller
{
    private function getAcademyId(): int
    {
        /** @var \App\Models\PartnerUser $user */
        $user = auth('academy')->user();
        return (int) ($user?->academy_id ?? auth('academy')->id());
    }

    public function index(Request $request)
    {
        $academyId = $this->getAcademyId();
        $filters = $this->filters($request);

        // Fetch Branches & Sports for Filters & Reports
        $branches = Address::where('academy_id', $academyId)->get();
        $sports = Sport::whereHas('academies', fn ($q) => $q->where('academy_id', $academyId))
            ->orWhereHas('trainings', fn ($q) => $q->where('academy_id', $academyId))
            ->distinct()
            ->get();

        [$subscriptions, $trainingBookings, $venueBookings, $campParticipants] = $this->queries($filters);

        $subscriptionFinancial = (clone $subscriptions)->where('status', '!=', 'cancelled');
        $trainingFinancial = (clone $trainingBookings)->where('is_canceled', false);
        $venueFinancial = (clone $venueBookings)->where('status', '!=', 'cancelled');
        $campFinancial = (clone $campParticipants)->where('status', '!=', 'cancelled');

        $breakdown = [
            'subscriptions' => $this->subscriptionTotals($subscriptionFinancial),
            'training' => $this->invoiceTotals($trainingFinancial),
            'venues' => $this->venueTotals($venueFinancial),
            'camps' => $this->campTotals($campFinancial),
        ];
        $breakdown['subscriptions']['cancelled'] = (clone $subscriptions)->where('status', 'cancelled')->count();
        $breakdown['training']['cancelled'] = (clone $trainingBookings)->where('is_canceled', true)->count();
        $breakdown['venues']['cancelled'] = (clone $venueBookings)->where('status', 'cancelled')->count();
        $breakdown['camps']['cancelled'] = (clone $campParticipants)->where('status', 'cancelled')->count();

        $summarySources = $filters['source'] === 'all'
            ? $breakdown
            : [$filters['source'] => $breakdown[$filters['source']] ?? ['billed' => 0, 'collected' => 0, 'remaining' => 0, 'records' => 0, 'cancelled' => 0]];

        $summary = [
            'billed' => collect($summarySources)->sum('billed'),
            'collected' => collect($summarySources)->sum('collected'),
            'remaining' => collect($summarySources)->sum('remaining'),
            'records' => collect($summarySources)->sum('records'),
            'cancelled' => collect($summarySources)->sum('cancelled'),
        ];
        $summary['collection_rate'] = $summary['billed'] > 0
            ? round(($summary['collected'] / $summary['billed']) * 100, 1)
            : 0;

        // Custom Detailed Reports
        $branchReportData = $this->getBranchReportData($academyId, $branches, $filters);
        $coachReportData = $this->getCoachReportData($academyId, $filters);
        $studentDuesReportData = $this->getStudentDuesReportData($academyId, $filters);
        $sportsReportData = $this->getSportsReportData($academyId, $sports, $filters);
        $paymentMethodReportData = $this->getPaymentMethodReportData($academyId, $filters);
        $groupsReportData = $this->getGroupsReportData($academyId, $filters);
        $campsReportData = $this->getCampsReportData($academyId, $filters);
        $competitionsReportData = $this->getCompetitionsReportData($academyId, $filters);

        return view('Academy.pages.reports.financial', [
            'filters' => $filters,
            'summary' => $summary,
            'breakdown' => $breakdown,
            'branches' => $branches,
            'sports' => $sports,
            'branchReportData' => $branchReportData,
            'coachReportData' => $coachReportData,
            'studentDuesReportData' => $studentDuesReportData,
            'sportsReportData' => $sportsReportData,
            'paymentMethodReportData' => $paymentMethodReportData,
            'groupsReportData' => $groupsReportData,
            'campsReportData' => $campsReportData,
            'competitionsReportData' => $competitionsReportData,
            'subscriptions' => (clone $subscriptions)->latest()->paginate(15, ['*'], 'subscription_page')->withQueryString(),
            'trainingBookings' => (clone $trainingBookings)->latest()->paginate(15, ['*'], 'training_page')->withQueryString(),
            'venueBookings' => (clone $venueBookings)->latest('starts_at')->paginate(15, ['*'], 'venue_page')->withQueryString(),
            'campParticipants' => (clone $campParticipants)->latest()->paginate(15, ['*'], 'camp_page')->withQueryString(),
        ]);
    }

    public function export(Request $request, string $type)
    {
        abort_unless(in_array($type, ['subscriptions', 'training', 'venues', 'camps', 'student_dues', 'coaches', 'groups', 'camps_report', 'competitions'], true), 404);
        $filters = $this->filters($request);
        $academyId = $this->getAcademyId();

        if ($type === 'camps_report') {
            $campsData = $this->getCampsReportData($academyId, $filters);
            $rows = collect($campsData['items'])->map(fn ($row) => [
                $row['title'],
                $row['sport_name'],
                $row['location'],
                $row['dates'],
                $row['enrolled'] . ' / ' . $row['capacity'],
                (float) $row['billed'],
                (float) $row['collected'],
                (float) $row['expenses'],
                (float) $row['net_profit'],
            ]);
            $fileName = 'hagzz-camps-report-' . now()->format('Y-m-d-His') . '.csv';

            return response()->streamDownload(function () use ($rows) {
                $output = fopen('php://output', 'w');
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, ['عنوان المعسكر', 'الرياضة', 'المكان', 'الفترة', 'المشتركين', 'المستحق', 'المحصل', 'المصروفات', 'صافي الأرباح']);
                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
            }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        if ($type === 'competitions') {
            $compData = $this->getCompetitionsReportData($academyId, $filters);
            $rows = collect($compData['items'])->map(fn ($row) => [
                $row['date'],
                $row['sport_name'],
                $row['home_team'],
                $row['opponent'],
                $row['venue'],
                $row['score'],
                $row['status'],
                $row['players_count'],
                $row['notes'],
            ]);
            $fileName = 'hagzz-competitions-report-' . now()->format('Y-m-d-His') . '.csv';

            return response()->streamDownload(function () use ($rows) {
                $output = fopen('php://output', 'w');
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, ['التاريخ', 'الرياضة', 'فريق الأكاديمية', 'المنافس', 'الملعب/المكان', 'النتيجة', 'الحالة', 'عدد اللاعبين', 'ملاحظات/المركز']);
                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
            }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }
        $filters = $this->filters($request);
        $academyId = $this->getAcademyId();

        if ($type === 'groups') {
            $groupsData = $this->getGroupsReportData($academyId, $filters);
            $rows = collect($groupsData['items'])->map(fn ($row) => [
                $row['name'],
                $row['sport_name'],
                $row['branch_name'],
                $row['coach_name'],
                $row['enrolled'] . ' / ' . $row['capacity'],
                $row['fill_rate'] . '%',
                (float) $row['billed'],
                (float) $row['collected'],
                (float) $row['remaining'],
            ]);
            $fileName = 'hagzz-groups-financial-report-' . now()->format('Y-m-d-His') . '.csv';

            return response()->streamDownload(function () use ($rows) {
                $output = fopen('php://output', 'w');
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, ['اسم المجموعة', 'الرياضة', 'الفرع / المقر', 'المدرب', 'الطلاب / السعة', 'نسبة الإشغال', 'المستحق', 'المحصل', 'المتبقي']);
                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
            }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }
        $filters = $this->filters($request);
        $academyId = $this->getAcademyId();

        if ($type === 'student_dues') {
            $duesData = $this->getStudentDuesReportData($academyId, $filters);
            $rows = collect($duesData['items'])->map(fn ($row) => [
                $row['source_label'],
                $row['reference'],
                $row['student_name'],
                $row['phone'],
                $row['service_name'],
                $row['date'],
                $row['payment_status'],
                (float) $row['amount'],
                (float) $row['paid_amount'],
                (float) $row['remaining_amount'],
            ]);
            $fileName = 'hagzz-student-dues-' . now()->format('Y-m-d-His') . '.csv';

            return response()->streamDownload(function () use ($rows) {
                $output = fopen('php://output', 'w');
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, ['المصدر', 'المرجع', 'اسم الطالب', 'الهاتف', 'الخدمة / المجموعة', 'التاريخ', 'حالة السداد', 'المستحق', 'المحصل', 'المتبقي']);
                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
            }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        if ($type === 'coaches') {
            $coachData = $this->getCoachReportData($academyId, $filters);
            $rows = collect($coachData['items'])->map(fn ($row) => [
                $row['name'],
                $row['phone'],
                $row['sports_list'],
                $row['assigned_groups_count'],
                $row['capacity_sum'],
                $row['enrolled_students_count'],
                $row['utilization_rate'] . '%',
                (float) $row['total_collected'],
                $row['compensation_label'],
                (float) $row['coach_cost'],
                (float) $row['net_revenue'],
            ]);
            $fileName = 'hagzz-coach-report-' . now()->format('Y-m-d-His') . '.csv';

            return response()->streamDownload(function () use ($rows) {
                $output = fopen('php://output', 'w');
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, ['اسم المدرب', 'الهاتف', 'الرياضات', 'عدد المجموعات', 'السعة', 'الطلاب المسجلين', 'نسبة التشغيل', 'إجمالي الإيراد', 'نظام الاستحقاق', 'تكلفة المدرب', 'صافي الدخل المحقق']);
                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
            }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        [$subscriptions, $trainingBookings, $venueBookings, $campParticipants] = $this->queries($filters);

        $rows = match ($type) {
            'subscriptions' => (clone $subscriptions)->latest()->get()->map(fn ($row) => [
                'subscriptions', $row->id, $row->student?->name, $row->group?->name,
                $row->created_at?->format('Y-m-d'), $row->payment_status, '-',
                (float) $row->amount, (float) ($row->payments_sum_amount ?? 0),
                max(0, (float) $row->amount - (float) ($row->payments_sum_amount ?? 0)),
            ]),
            'training' => (clone $trainingBookings)->latest()->get()->map(fn ($row) => [
                'training', $row->order_number, $row->user?->name, $row->training?->name,
                $row->created_at?->format('Y-m-d'), $row->payment_state, $row->payment_method_label,
                (float) $row->amount, $row->collected_amount, $row->remaining_amount,
            ]),
            'venues' => (clone $venueBookings)->latest('starts_at')->get()->map(fn ($row) => [
                'venues', $row->reference, $row->customer?->name,
                trim(($row->space?->venue?->name ?? '') . ' - ' . ($row->space?->name ?? ''), ' -'),
                $row->starts_at?->format('Y-m-d H:i'), $row->payment_status, $row->payment_method,
                (float) $row->total_amount, (float) $row->paid_amount, $row->remaining_amount,
            ]),
            'camps' => (clone $campParticipants)->latest()->get()->map(fn ($row) => [
                'camps', $row->id, $row->name,
                $row->camp?->title_ar ?: 'معسكر تدريبي',
                $row->created_at?->format('Y-m-d'), $row->status, '-',
                (float) $row->total_fee, (float) $row->paid_amount,
                max(0, (float) $row->total_fee - (float) $row->paid_amount),
            ]),
        };

        $fileName = 'hagzz-' . $type . '-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['source', 'reference', 'customer', 'service', 'date', 'payment_status', 'payment_method', 'amount', 'paid', 'remaining']);
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'source' => ['nullable', 'in:all,subscriptions,training,venues,camps'],
            'payment_status' => ['nullable', 'in:all,paid,partial,unpaid'],
            'branch_id' => ['nullable', 'integer'],
            'sport_id' => ['nullable', 'integer'],
            'payment_method' => ['nullable', 'string'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        return array_merge([
            'start_date' => null,
            'end_date' => null,
            'source' => 'all',
            'payment_status' => 'all',
            'branch_id' => null,
            'sport_id' => null,
            'payment_method' => null,
            'search' => null,
        ], $validated);
    }

    private function queries(array $filters): array
    {
        $academyId = $this->getAcademyId();
        $search = trim((string) ($filters['search'] ?? ''));

        $subscriptions = AcademyStudentSubscription::query()
            ->with(['student', 'group.training.address', 'group.sport'])
            ->withSum('payments', 'amount')
            ->whereHas('student', fn (Builder $query) => $query->where('academy_id', $academyId))
            ->when($filters['start_date'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['payment_status'] !== 'all', fn (Builder $query) => $query->where('payment_status', $filters['payment_status']))
            ->when($filters['branch_id'], function (Builder $query, $branchId) {
                $query->whereHas('group.training', fn (Builder $tr) => $tr->where('address_id', $branchId));
            })
            ->when($filters['sport_id'], function (Builder $query, $sportId) {
                $query->whereHas('group', function (Builder $g) use ($sportId) {
                    $g->where('sport_id', $sportId)->orWhereHas('training', fn ($tr) => $tr->where('sport_id', $sportId));
                });
            })
            ->when($filters['payment_method'], function (Builder $query, $method) {
                $query->whereHas('payments', fn (Builder $p) => $p->where('method', $method));
            })
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->whereHas('student', fn (Builder $student) => $student
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%"))
                    ->orWhereHas('group', fn (Builder $group) => $group->where('name', 'like', "%{$search}%"));
            }));

        $trainingBookings = Invoice::query()
            ->with(['user', 'training.address', 'training.sport'])
            ->whereHas('training', fn (Builder $query) => $query->where('academy_id', $academyId))
            ->when($filters['start_date'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['payment_status'] === 'paid', fn (Builder $query) => $query->whereRaw('COALESCE(paid_amount, amount) >= amount'))
            ->when($filters['payment_status'] === 'partial', fn (Builder $query) => $query->whereRaw('COALESCE(paid_amount, amount) > 0 AND COALESCE(paid_amount, amount) < amount'))
            ->when($filters['payment_status'] === 'unpaid', fn (Builder $query) => $query->whereRaw('COALESCE(paid_amount, amount) <= 0'))
            ->when($filters['branch_id'], function (Builder $query, $branchId) {
                $query->whereHas('training', fn (Builder $tr) => $tr->where('address_id', $branchId));
            })
            ->when($filters['sport_id'], function (Builder $query, $sportId) {
                $query->whereHas('training', fn (Builder $tr) => $tr->where('sport_id', $sportId));
            })
            ->when($filters['payment_method'], function (Builder $query, $method) {
                $query->where('payment_method', $method);
            })
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn (Builder $user) => $user
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%"));
            }));

        $venueBookings = VenueBooking::query()
            ->with(['customer', 'space.venue'])
            ->where('academy_id', $academyId)
            ->when($filters['start_date'], fn (Builder $query, $date) => $query->whereDate('starts_at', '>=', $date))
            ->when($filters['end_date'], fn (Builder $query, $date) => $query->whereDate('starts_at', '<=', $date))
            ->when($filters['payment_status'] === 'paid', fn (Builder $query) => $query->whereColumn('paid_amount', '>=', 'total_amount'))
            ->when($filters['payment_status'] === 'partial', fn (Builder $query) => $query->where('paid_amount', '>', 0)->whereColumn('paid_amount', '<', 'total_amount'))
            ->when($filters['payment_status'] === 'unpaid', fn (Builder $query) => $query->where('paid_amount', '<=', 0))
            ->when($filters['payment_method'], function (Builder $query, $method) {
                $query->where('payment_method', $method);
            })
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn (Builder $customer) => $customer
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%"));
            }));

        $campParticipants = AcademyCampParticipant::query()
            ->with(['camp', 'student'])
            ->whereHas('camp', fn (Builder $query) => $query->where('academy_id', $academyId))
            ->when($filters['start_date'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['payment_status'] === 'paid', fn (Builder $query) => $query->whereColumn('paid_amount', '>=', 'total_fee'))
            ->when($filters['payment_status'] === 'partial', fn (Builder $query) => $query->where('paid_amount', '>', 0)->whereColumn('paid_amount', '<', 'total_fee'))
            ->when($filters['payment_status'] === 'unpaid', fn (Builder $query) => $query->where('paid_amount', '<=', 0))
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('camp', fn (Builder $camp) => $camp->where('title_ar', 'like', "%{$search}%"));
            }));

        return [$subscriptions, $trainingBookings, $venueBookings, $campParticipants];
    }

    private function subscriptionTotals(Builder $query): array
    {
        $filteredSubscriptions = (clone $query)->reorder()->toBase();
        $totals = DB::query()->fromSub($filteredSubscriptions, 'filtered_subscriptions')
            ->selectRaw('COALESCE(SUM(amount), 0) AS billed, COALESCE(SUM(payments_sum_amount), 0) AS collected')
            ->first();
        $billed = (float) ($totals->billed ?? 0);
        $collected = (float) ($totals->collected ?? 0);

        return ['billed' => $billed, 'collected' => $collected, 'remaining' => max(0, $billed - $collected), 'records' => (clone $query)->count()];
    }

    private function invoiceTotals(Builder $query): array
    {
        $totals = (clone $query)->reorder()->toBase()->selectRaw(
            'COALESCE(SUM(amount), 0) AS billed, COALESCE(SUM(COALESCE(paid_amount, amount)), 0) AS collected'
        )->first();
        $billed = (float) ($totals->billed ?? 0);
        $collected = (float) ($totals->collected ?? 0);

        return ['billed' => $billed, 'collected' => $collected, 'remaining' => max(0, $billed - $collected), 'records' => (clone $query)->count()];
    }

    private function venueTotals(Builder $query): array
    {
        $totals = (clone $query)->reorder()->toBase()->selectRaw(
            'COALESCE(SUM(total_amount), 0) AS billed, COALESCE(SUM(paid_amount), 0) AS collected'
        )->first();
        $billed = (float) ($totals->billed ?? 0);
        $collected = (float) ($totals->collected ?? 0);

        return ['billed' => $billed, 'collected' => $collected, 'remaining' => max(0, $billed - $collected), 'records' => (clone $query)->count()];
    }

    private function campTotals(Builder $query): array
    {
        $totals = (clone $query)->reorder()->toBase()->selectRaw(
            'COALESCE(SUM(total_fee), 0) AS billed, COALESCE(SUM(paid_amount), 0) AS collected'
        )->first();
        $billed = (float) ($totals->billed ?? 0);
        $collected = (float) ($totals->collected ?? 0);

        return ['billed' => $billed, 'collected' => $collected, 'remaining' => max(0, $billed - $collected), 'records' => (clone $query)->count()];
    }

    /**
     * 1. Branch Financial Reports (per branch + combined all branches)
     */
    private function getBranchReportData(int $academyId, $branches, array $filters): array
    {
        $branchItems = [];
        $combinedBilled = 0;
        $combinedCollected = 0;
        $combinedRemaining = 0;
        $combinedExpenses = 0;

        foreach ($branches as $branch) {
            $branchFilters = array_merge($filters, ['branch_id' => $branch->id]);
            [$subQ, $trQ] = $this->queries($branchFilters);

            $subTotals = $this->subscriptionTotals($subQ->where('status', '!=', 'cancelled'));
            $trTotals = $this->invoiceTotals($trQ->where('is_canceled', false));

            $billed = $subTotals['billed'] + $trTotals['billed'];
            $collected = $subTotals['collected'] + $trTotals['collected'];
            $remaining = $subTotals['remaining'] + $trTotals['remaining'];

            // Expenses allocated if applicable or general branch expenses
            $expenses = (float) PartnerExpense::where('academy_id', $academyId)->sum('base_amount');

            $netIncome = $collected - $expenses;

            $branchItems[] = [
                'id' => $branch->id,
                'name' => $branch->address ?: ('فرع #' . $branch->id),
                'city' => $branch->city?->name ?? '',
                'area' => $branch->area?->name ?? '',
                'billed' => $billed,
                'collected' => $collected,
                'remaining' => $remaining,
                'expenses' => $expenses,
                'net_income' => $netIncome,
            ];

            $combinedBilled += $billed;
            $combinedCollected += $collected;
            $combinedRemaining += $remaining;
            $combinedExpenses += $expenses;
        }

        return [
            'items' => $branchItems,
            'combined' => [
                'billed' => $combinedBilled,
                'collected' => $combinedCollected,
                'remaining' => $combinedRemaining,
                'expenses' => $combinedExpenses,
                'net_income' => $combinedCollected - $combinedExpenses,
            ],
        ];
    }

    /**
     * 2. Coach Operation / Utilization Rate Report
     */
    private function getCoachReportData(int $academyId, array $filters): array
    {
        $coaches = Coach::where('academy_id', $academyId)
            ->with(['sports', 'trainings.joins'])
            ->get();

        $items = [];
        $totalCoachExpenses = 0;

        foreach ($coaches as $coach) {
            $assignedTrainings = Training::where('coach_id', $coach->id)->with('joins')->get();
            $assignedGroups = \App\Models\AcademyGroup::where('coach_id', $coach->id)
                ->with(['subscriptions' => fn ($q) => $q->where('status', '!=', 'cancelled')->with('payments')])
                ->get();

            $capacitySum = $assignedTrainings->sum('max_players') + $assignedGroups->sum('capacity');
            $enrolledCount = 0;
            $totalCollected = 0;
            $totalBilled = 0;

            foreach ($assignedTrainings as $tr) {
                $joins = $tr->joins->where('is_canceled', false);
                $enrolledCount += $joins->count();
                $totalBilled += (float) $joins->sum('price');
                $totalCollected += (float) $joins->sum('net_amount');
            }

            foreach ($assignedGroups as $group) {
                foreach ($group->subscriptions as $sub) {
                    $enrolledCount++;
                    $totalBilled += (float) $sub->amount;
                    $totalCollected += (float) $sub->payments->sum('amount');
                }
            }

            $utilizationRate = $capacitySum > 0 ? round(($enrolledCount / $capacitySum) * 100, 1) : 0;

            // Calculate Coach Expense based on compensation_type & compensation_value
            $compType = $coach->compensation_type ?? 'salary';
            $compVal = (float) ($coach->compensation_value ?? 0);
            $coachCost = 0;

            if ($compType === 'percentage') {
                $coachCost = ($totalCollected * $compVal) / 100;
            } else {
                $coachCost = $compVal; // fixed salary
            }

            $netRevenue = $totalCollected - $coachCost;
            $totalCoachExpenses += $coachCost;

            $isAr = app()->getLocale() === 'ar';
            $items[] = [
                'id' => $coach->id,
                'name' => $coach->name,
                'phone' => $coach->phone,
                'sports_list' => $coach->sports->pluck('name')->implode(', '),
                'assigned_groups_count' => $assignedGroups->count() + $assignedTrainings->count(),
                'capacity_sum' => $capacitySum,
                'enrolled_students_count' => $enrolledCount,
                'utilization_rate' => $utilizationRate,
                'total_billed' => $totalBilled,
                'total_collected' => $totalCollected,
                'compensation_type' => $compType,
                'compensation_value' => $compVal,
                'compensation_label' => $compType === 'percentage' 
                    ? ($compVal . ($isAr ? '% من الإيراد' : '% of Revenue')) 
                    : (number_format($compVal, 2) . ($isAr ? ' ج.م مرتب' : ' EGP Salary')),
                'coach_cost' => $coachCost,
                'net_revenue' => $netRevenue,
            ];
        }

        return [
            'items' => $items,
            'total_coach_expenses' => $totalCoachExpenses,
        ];
    }

    /**
     * 3. Student Outstanding Dues Report (Partial & Unpaid payments)
     */
    private function getStudentDuesReportData(int $academyId, array $filters): array
    {
        $isAr = app()->getLocale() === 'ar';
        $items = [];
        $totalBilledDues = 0;
        $totalPaidDues = 0;
        $totalRemainingDues = 0;

        // 1. Subscriptions with remaining balance
        $subscriptions = AcademyStudentSubscription::with(['student', 'group.training'])
            ->withSum('payments', 'amount')
            ->whereHas('student', fn ($q) => $q->where('academy_id', $academyId))
            ->where(function ($q) {
                $q->whereIn('payment_status', ['partial', 'unpaid'])
                    ->orWhereRaw('amount > COALESCE((SELECT SUM(amount) FROM academy_student_payments WHERE academy_student_subscription_id = academy_student_subscriptions.id), 0)');
            })
            ->latest()
            ->get();

        foreach ($subscriptions as $sub) {
            $paid = (float) ($sub->payments_sum_amount ?? 0);
            $amount = (float) $sub->amount;
            $remaining = max(0, $amount - $paid);

            if ($remaining <= 0 && $sub->payment_status === 'paid') {
                continue;
            }

            $items[] = [
                'type' => 'subscription',
                'source_label' => $isAr ? 'اشتراك طالب' : 'Subscription',
                'reference' => '#' . $sub->id,
                'student_name' => $sub->student?->name ?? ($isAr ? 'غير محدد' : 'N/A'),
                'phone' => $sub->student?->phone ?? '-',
                'service_name' => $sub->group?->name ?: ($sub->group?->training?->name ?: ($isAr ? 'تدريب أكاديمي' : 'Academy Training')),
                'date' => $sub->created_at?->format('Y-m-d'),
                'payment_status' => $sub->payment_status === 'partial' ? ($isAr ? 'مدفوع جزئياً' : 'Partially paid') : ($isAr ? 'غير مدفوع' : 'Unpaid'),
                'amount' => $amount,
                'paid_amount' => $paid,
                'remaining_amount' => $remaining,
            ];

            $totalBilledDues += $amount;
            $totalPaidDues += $paid;
            $totalRemainingDues += $remaining;
        }

        // 2. Training Invoices with remaining balance
        $invoices = Invoice::with(['user', 'training'])
            ->whereHas('training', fn ($q) => $q->where('academy_id', $academyId))
            ->where('is_canceled', false)
            ->whereRaw('COALESCE(paid_amount, amount) < amount')
            ->latest()
            ->get();

        foreach ($invoices as $inv) {
            $amount = (float) $inv->amount;
            $paid = (float) ($inv->paid_amount ?? 0);
            $remaining = max(0, $amount - $paid);

            $items[] = [
                'type' => 'invoice',
                'source_label' => $isAr ? 'حجز تدريب' : 'Booking',
                'reference' => $inv->order_number,
                'student_name' => $inv->user?->name ?? ($isAr ? 'عميل/طالب' : 'Customer/Student'),
                'phone' => $inv->user?->phone ?? '-',
                'service_name' => $inv->training?->name ?? ($isAr ? 'حجز تدريب' : 'Training Booking'),
                'date' => $inv->created_at?->format('Y-m-d'),
                'payment_status' => $paid > 0 ? ($isAr ? 'مدفوع جزئياً' : 'Partially paid') : ($isAr ? 'غير مدفوع' : 'Unpaid'),
                'amount' => $amount,
                'paid_amount' => $paid,
                'remaining_amount' => $remaining,
            ];

            $totalBilledDues += $amount;
            $totalPaidDues += $paid;
            $totalRemainingDues += $remaining;
        }

        return [
            'items' => $items,
            'total_billed' => $totalBilledDues,
            'total_paid' => $totalPaidDues,
            'total_remaining' => $totalRemainingDues,
            'count' => count($items),
        ];
    }

    /**
     * 4. Sports & Branch Financial Breakdown
     */
    private function getSportsReportData(int $academyId, $sports, array $filters): array
    {
        $items = [];

        foreach ($sports as $sport) {
            $trainings = Training::where('academy_id', $academyId)
                ->where('sport_id', $sport->id)
                ->with('joins')
                ->get();

            $trainingsCount = $trainings->count();
            $totalBilled = 0;
            $totalCollected = 0;
            $studentsCount = 0;

            foreach ($trainings as $tr) {
                $joins = $tr->joins->where('is_canceled', false);
                $studentsCount += $joins->count();
                $totalBilled += (float) $joins->sum('price');
                $totalCollected += (float) $joins->sum('net_amount');
            }

            // Include Student Subscriptions in this sport
            $groups = \App\Models\AcademyGroup::where('academy_id', $academyId)
                ->where(function ($q) use ($sport) {
                    $q->where('sport_id', $sport->id)
                        ->orWhereHas('training', fn ($tr) => $tr->where('sport_id', $sport->id));
                })
                ->with(['subscriptions' => fn ($q) => $q->where('status', '!=', 'cancelled')->with('payments')])
                ->get();

            foreach ($groups as $group) {
                foreach ($group->subscriptions as $sub) {
                    $studentsCount++;
                    $totalBilled += (float) $sub->amount;
                    $totalCollected += (float) $sub->payments->sum('amount');
                }
            }

            $items[] = [
                'id' => $sport->id,
                'name' => $sport->name,
                'trainings_count' => $trainingsCount + $groups->count(),
                'students_count' => $studentsCount,
                'total_billed' => $totalBilled,
                'total_collected' => $totalCollected,
                'total_remaining' => max(0, $totalBilled - $totalCollected),
            ];
        }

        return [
            'items' => $items,
        ];
    }

    /**
     * 5. Payment Methods Report Data
     */
    private function getPaymentMethodReportData(int $academyId, array $filters): array
    {
        $isAr = app()->getLocale() === 'ar';
        $methods = [
            'cash' => [
                'label' => $isAr ? 'نقداً (كاش)' : 'Cash',
                'icon' => 'fa-money-bill-wave',
                'color' => '#10b981',
                'keys' => ['cash'],
            ],
            'card' => [
                'label' => $isAr ? 'بطاقة بنكية / فيزا / مدى' : 'Card / POS / Mada',
                'icon' => 'fa-credit-card',
                'color' => '#3b82f6',
                'keys' => ['card', 'visa', 'mastercard', 'online', 'app_online', 'mada'],
            ],
            'instapay' => [
                'label' => $isAr ? 'إنستا باي (InstaPay)' : 'InstaPay',
                'icon' => 'fa-bolt',
                'color' => '#8b5cf6',
                'keys' => ['instapay'],
            ],
            'fawry' => [
                'label' => $isAr ? 'فوري (Fawry)' : 'Fawry',
                'icon' => 'fa-receipt',
                'color' => '#f59e0b',
                'keys' => ['fawry'],
            ],
            'sadad' => [
                'label' => $isAr ? 'سداد / STC Pay / NAPS' : 'Sadad / STC Pay / NAPS',
                'icon' => 'fa-mobile-screen-button',
                'color' => '#7c3aed',
                'keys' => ['sadad', 'stc_pay', 'apple_pay', 'naps'],
            ],
            'bank_transfer' => [
                'label' => $isAr ? 'تحويل بنكي' : 'Bank Transfer',
                'icon' => 'fa-building-columns',
                'color' => '#06b6d4',
                'keys' => ['bank_transfer', 'bank'],
            ],
            'other' => [
                'label' => $isAr ? 'وسائل أخرى' : 'Other Methods',
                'icon' => 'fa-wallet',
                'color' => '#6b7280',
                'keys' => ['other'],
            ],
        ];

        $breakdown = [];

        foreach ($methods as $methodKey => $meta) {
            $keys = $meta['keys'];

            // Subscription Payments
            $subPaid = (float) DB::table('academy_student_payments')
                ->join('academy_student_subscriptions', 'academy_student_payments.academy_student_subscription_id', '=', 'academy_student_subscriptions.id')
                ->join('academy_students', 'academy_student_subscriptions.academy_student_id', '=', 'academy_students.id')
                ->where('academy_students.academy_id', $academyId)
                ->whereIn('academy_student_payments.method', $keys)
                ->sum('academy_student_payments.amount');

            // Training Invoices
            $invPaid = (float) DB::table('invoices')
                ->join('trainings', 'invoices.training_id', '=', 'trainings.id')
                ->where('trainings.academy_id', $academyId)
                ->where('invoices.is_canceled', false)
                ->whereIn('invoices.payment_method', $keys)
                ->sum(DB::raw('COALESCE(paid_amount, amount)'));

            // Venue Bookings
            $venuePaid = (float) DB::table('venue_bookings')
                ->where('academy_id', $academyId)
                ->where('status', '!=', 'cancelled')
                ->whereIn('payment_method', $keys)
                ->sum('paid_amount');

            $totalCollected = $subPaid + $invPaid + $venuePaid;

            $breakdown[$methodKey] = array_merge($meta, [
                'sub_paid' => $subPaid,
                'inv_paid' => $invPaid,
                'venue_paid' => $venuePaid,
                'total_collected' => $totalCollected,
            ]);
        }

        return $breakdown;
    }

    /**
     * 6. Training Groups Financial & Operational Report (per group, sport & branch)
     */
    private function getGroupsReportData(int $academyId, array $filters): array
    {
        $groupsQuery = \App\Models\AcademyGroup::where('academy_id', $academyId)
            ->with(['sport', 'training.address', 'coach', 'subscriptions.payments'])
            ->when($filters['branch_id'], function ($q, $branchId) {
                $q->whereHas('training', fn ($tr) => $tr->where('address_id', $branchId));
            })
            ->when($filters['sport_id'], function ($q, $sportId) {
                $q->where('sport_id', $sportId)->orWhereHas('training', fn ($tr) => $tr->where('sport_id', $sportId));
            });

        $groups = $groupsQuery->get();
        $items = [];
        $totalBilledAll = 0;
        $totalCollectedAll = 0;
        $totalRemainingAll = 0;

        foreach ($groups as $group) {
            $subs = $group->subscriptions->where('status', '!=', 'cancelled');
            $billed = (float) $subs->sum('amount');
            $collected = 0;

            foreach ($subs as $sub) {
                $collected += (float) $sub->payments->sum('amount');
            }

            $remaining = max(0, $billed - $collected);
            $enrolled = $subs->count();
            $capacity = (int) ($group->capacity ?: ($group->training?->max_players ?: 0));
            $fillRate = $capacity > 0 ? round(($enrolled / $capacity) * 100, 1) : 0;

            $items[] = [
                'id' => $group->id,
                'name' => $group->name,
                'sport_name' => $group->sport?->name ?: ($group->training?->sport?->name ?: 'غير محدد'),
                'branch_name' => $group->training?->address?->address ?: 'المقر الرئيسي',
                'coach_name' => $group->coach?->name ?: ($group->training?->coach?->name ?: 'غير محدد'),
                'days' => is_array($group->days) ? implode(', ', $group->days) : ($group->days ?: '-'),
                'enrolled' => $enrolled,
                'capacity' => $capacity,
                'fill_rate' => $fillRate,
                'billed' => $billed,
                'collected' => $collected,
                'remaining' => $remaining,
            ];

            $totalBilledAll += $billed;
            $totalCollectedAll += $collected;
            $totalRemainingAll += $remaining;
        }

        return [
            'items' => $items,
            'total_billed' => $totalBilledAll,
            'total_collected' => $totalCollectedAll,
            'total_remaining' => $totalRemainingAll,
        ];
    }

    /**
     * 7. Camps Financial & Operational Report
     */
    private function getCampsReportData(int $academyId, array $filters): array
    {
        $campsQuery = \App\Models\AcademyCamp::where('academy_id', $academyId)
            ->with(['sport', 'country', 'participants', 'expenses'])
            ->when($filters['sport_id'], fn ($q, $sId) => $q->where('sport_id', $sId));

        $camps = $campsQuery->get();
        $items = [];
        $totalBilledAll = 0;
        $totalCollectedAll = 0;
        $totalExpensesAll = 0;
        $totalNetProfitAll = 0;

        foreach ($camps as $camp) {
            $participants = $camp->participants->where('status', '!=', 'cancelled');
            $billed = (float) $participants->sum('total_fee');
            $collected = (float) $participants->sum('paid_amount');
            $expenses = (float) $camp->expenses->sum('amount');
            $netProfit = $collected - $expenses;
            $enrolled = $participants->count();

            $items[] = [
                'id' => $camp->id,
                'title' => $camp->title,
                'sport_name' => $camp->sport?->name ?? 'عام',
                'location' => trim(($camp->country?->name ?? '') . ' ' . ($camp->city_name ?? '') . ' - ' . ($camp->venue_name ?? ''), ' -'),
                'dates' => ($camp->starts_on?->format('Y-m-d') ?? '-') . ' إلى ' . ($camp->ends_on?->format('Y-m-d') ?? '-'),
                'enrolled' => $enrolled,
                'capacity' => (int) $camp->capacity,
                'billed' => $billed,
                'collected' => $collected,
                'expenses' => $expenses,
                'net_profit' => $netProfit,
            ];

            $totalBilledAll += $billed;
            $totalCollectedAll += $collected;
            $totalExpensesAll += $expenses;
            $totalNetProfitAll += $netProfit;
        }

        return [
            'items' => $items,
            'total_billed' => $totalBilledAll,
            'total_collected' => $totalCollectedAll,
            'total_expenses' => $totalExpensesAll,
            'total_net_profit' => $totalNetProfitAll,
        ];
    }

    /**
     * 8. Competitions & Matches Report
     */
    private function getCompetitionsReportData(int $academyId, array $filters): array
    {
        $query = \App\Models\AcademyCompetition::where('academy_id', $academyId)
            ->with(['sport', 'students'])
            ->when($filters['start_date'], fn ($q, $d) => $q->whereDate('competition_date', '>=', $d))
            ->when($filters['end_date'], fn ($q, $d) => $q->whereDate('competition_date', '<=', $d))
            ->when($filters['sport_id'], fn ($q, $sId) => $q->where('sport_id', $sId))
            ->latest('competition_date');

        $competitions = $query->get();
        $items = [];

        foreach ($competitions as $comp) {
            $statusLabel = match ($comp->status) {
                'completed' => 'مكتملة',
                'scheduled' => 'مجدولة',
                'cancelled' => 'ملغاة',
                default => $comp->status ?: 'غير محدد',
            };

            $scoreText = ($comp->home_score !== null && $comp->opponent_score !== null)
                ? ($comp->home_score . ' - ' . $comp->opponent_score)
                : 'لم تحدد النتيجة';

            $items[] = [
                'id' => $comp->id,
                'date' => $comp->competition_date?->format('Y-m-d'),
                'time' => $comp->starts_at ?: '-',
                'sport_name' => $comp->sport?->name ?? 'غير محدد',
                'home_team' => $comp->home_team_name ?: 'فريق الأكاديمية',
                'opponent' => $comp->opponent_name ?: 'المنافس',
                'venue' => $comp->venue ?: 'الملعب الرئيسي',
                'score' => $scoreText,
                'status' => $statusLabel,
                'players_count' => $comp->students->count(),
                'notes' => $comp->result_notes ?: ($comp->notes ?: '-'),
            ];
        }

        return [
            'items' => $items,
            'total_matches' => count($items),
        ];
    }
}
