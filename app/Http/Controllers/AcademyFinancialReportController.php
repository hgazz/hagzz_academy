<?php

namespace App\Http\Controllers;

use App\Models\AcademyStudentSubscription;
use App\Models\Invoice;
use App\Models\VenueBooking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademyFinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        [$subscriptions, $trainingBookings, $venueBookings] = $this->queries($filters);

        $subscriptionFinancial = (clone $subscriptions)->where('status', '!=', 'cancelled');
        $trainingFinancial = (clone $trainingBookings)->where('is_canceled', false);
        $venueFinancial = (clone $venueBookings)->where('status', '!=', 'cancelled');

        $breakdown = [
            'subscriptions' => $this->subscriptionTotals($subscriptionFinancial),
            'training' => $this->invoiceTotals($trainingFinancial),
            'venues' => $this->venueTotals($venueFinancial),
        ];
        $breakdown['subscriptions']['cancelled'] = (clone $subscriptions)->where('status', 'cancelled')->count();
        $breakdown['training']['cancelled'] = (clone $trainingBookings)->where('is_canceled', true)->count();
        $breakdown['venues']['cancelled'] = (clone $venueBookings)->where('status', 'cancelled')->count();
        $summarySources = $filters['source'] === 'all'
            ? $breakdown
            : [$filters['source'] => $breakdown[$filters['source']]];

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

        return view('Academy.pages.reports.financial', [
            'filters' => $filters,
            'summary' => $summary,
            'breakdown' => $breakdown,
            'subscriptions' => (clone $subscriptions)->latest()->paginate(15, ['*'], 'subscription_page')->withQueryString(),
            'trainingBookings' => (clone $trainingBookings)->latest()->paginate(15, ['*'], 'training_page')->withQueryString(),
            'venueBookings' => (clone $venueBookings)->latest('starts_at')->paginate(15, ['*'], 'venue_page')->withQueryString(),
        ]);
    }

    public function export(Request $request, string $type)
    {
        abort_unless(in_array($type, ['subscriptions', 'training', 'venues'], true), 404);
        $filters = $this->filters($request);
        [$subscriptions, $trainingBookings, $venueBookings] = $this->queries($filters);

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
            'source' => ['nullable', 'in:all,subscriptions,training,venues'],
            'payment_status' => ['nullable', 'in:all,paid,partial,unpaid'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        return array_merge([
            'start_date' => null,
            'end_date' => null,
            'source' => 'all',
            'payment_status' => 'all',
            'search' => null,
        ], $validated);
    }

    private function queries(array $filters): array
    {
        $academyId = auth('academy')->id();
        $search = trim((string) $filters['search']);

        $subscriptions = AcademyStudentSubscription::query()
            ->with(['student', 'group'])
            ->withSum('payments', 'amount')
            ->whereHas('student', fn (Builder $query) => $query->where('academy_id', $academyId))
            ->when($filters['start_date'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['payment_status'] !== 'all', fn (Builder $query) => $query->where('payment_status', $filters['payment_status']))
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->whereHas('student', fn (Builder $student) => $student
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%"))
                    ->orWhereHas('group', fn (Builder $group) => $group->where('name', 'like', "%{$search}%"));
            }));

        $trainingBookings = Invoice::query()
            ->with(['user', 'training'])
            ->whereHas('training', fn (Builder $query) => $query->where('academy_id', $academyId))
            ->when($filters['start_date'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['payment_status'] === 'paid', fn (Builder $query) => $query->whereRaw('COALESCE(paid_amount, amount) >= amount'))
            ->when($filters['payment_status'] === 'partial', fn (Builder $query) => $query->whereRaw('COALESCE(paid_amount, amount) > 0 AND COALESCE(paid_amount, amount) < amount'))
            ->when($filters['payment_status'] === 'unpaid', fn (Builder $query) => $query->whereRaw('COALESCE(paid_amount, amount) <= 0'))
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
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn (Builder $customer) => $customer
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%"));
            }));

        return [$subscriptions, $trainingBookings, $venueBookings];
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
        $totals = (clone $query)->selectRaw(
            'COALESCE(SUM(amount), 0) AS billed, COALESCE(SUM(COALESCE(paid_amount, amount)), 0) AS collected'
        )->first();
        $billed = (float) $totals->billed;
        $collected = (float) $totals->collected;

        return ['billed' => $billed, 'collected' => $collected, 'remaining' => max(0, $billed - $collected), 'records' => (clone $query)->count()];
    }

    private function venueTotals(Builder $query): array
    {
        $totals = (clone $query)->selectRaw(
            'COALESCE(SUM(total_amount), 0) AS billed, COALESCE(SUM(paid_amount), 0) AS collected'
        )->first();
        $billed = (float) $totals->billed;
        $collected = (float) $totals->collected;

        return ['billed' => $billed, 'collected' => $collected, 'remaining' => max(0, $billed - $collected), 'records' => (clone $query)->count()];
    }
}
