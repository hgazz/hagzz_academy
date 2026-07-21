<?php

namespace App\DataTables;

use App\Http\Traits\DataTablesTrait;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class InvoiceDataTable extends DataTable
{
    use DataTablesTrait;
    protected $query;

    /**
     * Set a custom query.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return static
     */
    public function with(array|string $key, mixed $value = null): static
    {
        if (is_string($key) && $key === 'query') {
            $this->query = $value;
        }

        return $this;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', function ($data) {
                return $data->created_at->format('Y-m-d');
            })
            ->editColumn('user_id', function ($row) {
                return $row->user->name;
            })
            ->editColumn('training_id', function ($row) {
                return $row->training->name;
            })
            ->editColumn('is_canceled', function ($row) {
                return $row->is_canceled ? trans('admin.bookings.cancelled') : 'N/A';
            })
            ->addColumn('payment_method_label', function ($row) {
                return $row->payment_method_label;
            })
            ->addColumn('paid_amount', fn($row) => number_format($row->collected_amount, 2))
            ->addColumn('remaining_amount', fn($row) => number_format($row->remaining_amount, 2))
            ->addColumn('payment_state', fn($row) => $row->payment_state_label)
            ->addColumn('partner', function ($row) {
                return $row->training->academy->commercial_name;
            })
            ->addColumn('action', function ($row) {
                return '<a class="btn btn-sm btn-outline-primary" target="_blank" href="'.route('academy.invoices.bookings.print', ['invoice' => $row, 'paper' => 'a4']).'" title="'.(app()->getLocale() === 'ar' ? 'طباعة الفاتورة' : 'Print invoice').'"><i class="fa-solid fa-print"></i></a>';
            })
            ->filterColumn('training.name', function ($query, $keyword) {
                $query->whereHas('training',function ($q) use($keyword){
                    $q->whereRaw("JSON_SEARCH(lower(name), 'one', lower(?)) IS NOT NULL", ["%{$keyword}%"]);
                });
            })
            ->filterColumn('partner', function ($query, $keyword) {
                $query->whereHas('training',function ($q) use($keyword){
                    $q->whereHas('academy',function ($q) use($keyword){
                        $q->whereRaw("JSON_SEARCH(lower(commercial_name), 'one', lower(?)) IS NOT NULL", ["%{$keyword}%"]);
                    });
                });
            })
            ->setRowId('id')
            ->rawColumns(['created_at', 'user_id', 'training_id', 'is_canceled', 'partner', 'payment_method_label', 'paid_amount', 'remaining_amount', 'payment_state', 'action']);
    }
    /**
     * Get the query source of dataTable.
     */
    public function query(Invoice $model): QueryBuilder
    {
        if ($this->query) {
            return $this->query;
        }

        return $model->newQuery()->with(['training' => ['academy'], 'user'])
            ->whereHas('training', function ($q) {
            $q->where('academy_id', auth('academy')->id());
        });
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $hideButtonsArray = array_column($this->getColumns(), 'title');
        $hideButtonsArray = $this->makeHideButtons($hideButtonsArray);
        return $this->builder()
            ->setTableId('invoice-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfltip')
            ->selectStyleSingle()
            ->parameters([
                'scrollX' => true,
                'scrollY' => true,
                'autoWidth' => false,
                'lengthMenu' => [[10, 25, 50, -1], [10, 25, 50, 'All records']],
                'buttons' => [
                    $hideButtonsArray,
                ],
                'order' => [
                    0, 'desc'
                ],
                'language' =>
                    (app()->getLocale() === 'ar') ?
                        [
                            'url' => asset('datatableAr.json')
                        ] :
                        [
                            'url' => asset('datatableEn.json')
                        ]

            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            ['name' => 'id', 'data' => 'id', 'title' => trans('admin.id')],
            ['name' => 'order_number', 'data' => 'order_number', 'title' => trans('admin.order_number')],
            ['name' => 'training.academy.commercial_name', 'data' => 'partner', 'title' => trans('admin.academy')],
            ['name' => 'user.name', 'data' => 'user_id', 'title' => trans('admin.bookings.user')],
            ['name' => 'training.name', 'data' => 'training_id', 'title' => trans('admin.bookings.training')],
            ['name' => 'amount', 'data' => 'amount', 'title' => trans('admin.bookings.amount')],
            ['name' => 'paid_amount', 'data' => 'paid_amount', 'title' => trans('admin.bookings.paid_amount'), 'orderable' => false, 'searchable' => false],
            ['name' => 'remaining_amount', 'data' => 'remaining_amount', 'title' => trans('admin.bookings.remaining_amount'), 'orderable' => false, 'searchable' => false],
            ['name' => 'payment_state', 'data' => 'payment_state', 'title' => trans('admin.bookings.payment_state'), 'orderable' => false, 'searchable' => false],
            ['name' => 'net_amount', 'data' => 'net_amount', 'title' => trans('admin.bookings.net_amount')],
            ['name' => 'payment_method', 'data' => 'payment_method_label', 'title' => trans('admin.payment_method')],
            ['name' => 'status', 'data' => 'status', 'title' => trans('admin.bookings.status')],
            ['name' => 'user_type', 'data' => 'user_type', 'title' => trans('admin.bookings.user_type')],
            ['name' => 'is_canceled', 'data' => 'is_canceled', 'title' => trans('admin.bookings.is_canceled')],
            ['name' => 'action', 'data' => 'action', 'title' => trans('admin.actions'), 'exportable' => false, 'printable' => false, 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Invoice_' . date('YmdHis');
    }
}
