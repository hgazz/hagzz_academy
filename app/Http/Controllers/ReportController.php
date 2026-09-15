<?php

namespace App\Http\Controllers;

use App\DataTables\InvoiceDataTable;
use App\DataTables\JoinDataTable;
use App\DataTables\OfflineJoinDataTable;
use App\DataTables\SettlementDataTable;
use App\Exports\BookingOfflineExport;
use App\Exports\InvoiceExport;
use App\Exports\JoinExport;
use App\Exports\SettlementExport;
use App\Models\Join;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected $settlementData = [];
    public function settlement(SettlementDataTable $dataTable )
    {

        return $dataTable->render('Academy.pages.settlements.index');
    }

    public function filter(Request $request, SettlementDataTable $dataTable)
    {
        $query = Settlement::query();

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $query->where('partner_id', auth('academy')->id())
               ->whereBetween('created_at', [$startDate, $endDate]);
           $settlement = $query->get();
            session(['settlementData' => $settlement]);
        }

        return $dataTable->with('query', $query)->render('Academy.pages.settlements.index');
    }

    public function export()
    {

        return Excel::download(new SettlementExport(),'settlement.xlsx');
    }

    public function transaction(InvoiceDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.booking.index');
    }
    public function invoice(Request $request)
    {
        return redirect()->route('academy.report.transaction.index', $request->query());
    }

    public function bookingExport()
    {
         return Excel::download(new InvoiceExport(), 'invoice.xlsx');
    }

    public function joins(JoinDataTable $dataTable)
    {
        $metrics = $this->getJoinMetrics();
        return $dataTable->render('Academy.pages.joins.index', compact('metrics'));
    }

    public function offlineJoins(OfflineJoinDataTable $dataTable)
    {
        $metrics = $this->getOfflineJoinMetrics();
        return $dataTable->render('Academy.pages.joinsOffline.index', compact('metrics'));
    }

    public function joinFilter(Request $request , JoinDataTable $dataTable)
    {
        $metrics = $this->getJoinMetrics();
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $joins = Join::with([
                'invoice',
                'training'=>function($model){
                    $model->where('academy_id',auth('academy')->id())->get();
                }
            ])->whereHas('training', function ($q) {
                $q->where('academy_id', auth('academy')->id());
            })->whereBetween('created_at', [$startDate, $endDate]);

            $joinsData = $joins->get();
            session(['joinsData' => $joinsData]);
            return $dataTable->with('query', $joins)->render('Academy.pages.joins.index', compact('metrics'));
        }

        return $dataTable->render('Academy.pages.joins.index', compact('metrics'));
    }

    public function offlineJoinFilter(Request $request , OfflineJoinDataTable $dataTable)
    {
        $metrics = $this->getOfflineJoinMetrics();
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $joins = Join::with(['invoice', 'user', 'training'])
                ->whereHas('training', function ($q) {
                    $q->where('academy_id', auth('academy')->id());
                })
                ->whereHas('invoice', function ($query) {
                    $query->where('user_type', 'offline');
                })
                ->whereBetween('created_at', [$startDate, $endDate]);

            $joinsData = $joins->get();

            session(['joinsData' => $joinsData]);
            return $dataTable->with('query', $joins)->render('Academy.pages.joinsOffline.index', compact('metrics'));
        }

        return $dataTable->render('Academy.pages.joinsOffline.index', compact('metrics'));
    }

    private function getJoinMetrics(): array
    {
        $academyId = auth('academy')->id();
        $base = Join::whereHas('training', fn($q) => $q->where('academy_id', $academyId));
        return [
            'total' => (clone $base)->count(),
            'today' => (clone $base)->whereDate('created_at', today())->count(),
            'online' => (clone $base)->whereDoesntHave('invoice', fn($q) => $q->where('user_type', 'offline'))->count(),
            'offline' => (clone $base)->whereHas('invoice', fn($q) => $q->where('user_type', 'offline'))->count(),
        ];
    }

    private function getOfflineJoinMetrics(): array
    {
        $academyId = auth('academy')->id();
        $base = Join::whereHas('training', fn($q) => $q->where('academy_id', $academyId))
            ->whereHas('invoice', fn($q) => $q->where('user_type', 'offline'));
        return [
            'total' => (clone $base)->count(),
            'today' => (clone $base)->whereDate('created_at', today())->count(),
            'with_user' => (clone $base)->whereNotNull('user_id')->count(),
            'guest' => (clone $base)->whereNull('user_id')->count(),
        ];
    }

    public function joinExport()
    {


        return Excel::download(new JoinExport(), 'booking.xlsx');
    }


    public function coach()
    {
        return redirect()->route('academy.coach');
    }


    public function coachFilter(Request $request)
    {
        return redirect()->route('academy.coach.filter', $request->query());
    }

    public function coachExport()
    {
        return redirect()->route('academy.coach.export');
    }

    public function viewBookingDetails(Join $join)
    {
        return view('Academy.pages.joins.details', compact('join'));
    }

    public function exportBookingFile($join)
    {
        return  Excel::download(new BookingOfflineExport($join), 'booking.xlsx');
    }

}
