<?php

namespace App\DataTables;

use App\Http\Traits\DataTablesTrait;
use App\Models\Join;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class OfflineJoinDataTable extends DataTable
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
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('training_name', fn($join) => $join->training->name)
            ->addColumn('user_name', fn($join) => $join->user->name)
            ->addColumn('phone', fn($join) => $join->user->phone)
            ->addColumn('referral_source', fn($join) => $join->user->referral_source)
            ->addColumn('start_date', fn($join) => $join->user->start_date)
            ->addColumn('price', fn($join) => $join?->training?->price)
            ->addColumn('user_type', fn($join) => $join?->invoice?->user_type)
            ->addColumn('training.created_at', fn($join) => Carbon::parse($join->training->created_at)->format('Y-m-d H:i:s'))
            ->addColumn('actions', fn($join)=> view('Academy.pages.joins.datatables.action', compact('join')))
            ->rawColumns([
                'user_name',
                'training',
                'start_date',
                'price',
                'training.created_at',
                'user_type'
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Join $model): QueryBuilder
    {
        if ($this->query) {
            return $this->query;
        }

        return $model->newQuery()->with([
            'user',
            'training' => function ($query) {
                $query->with(['academy', 'coach'])
                    ->where('academy_id', auth('academy')->id())
                    ->withoutTrashed();
            }
        ])->whereHas('training', function ($query) {
            $query->where('academy_id', auth('academy')->id())
                ->withoutTrashed();
        })->whereHas('invoice', function ($query) {
            $query->where('user_type', 'offline');
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
                    ->setTableId('join-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfltip')
            ->parameters([
                'scrollX' => true,
                'scrollY' => true,
                'autoWidth' => false,
                'lengthMenu' => [[10, 25, 50, -1], [10, 25, 50, 'All records']],
                'buttons' => [
                    $hideButtonsArray
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
                            'url' => url('//cdn.datatables.net/plug-ins/1.13.8/i18n/English.json')
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
            ['name' => 'training_name', 'data' => 'training_name', 'title' => trans('admin.training.training_name')],
            ['name' => 'price', 'data' => 'price', 'title' => trans('admin.training.price')],
            ['name' => 'user_name', 'data' => 'user_name', 'title' => trans('admin.bookings.user')],
            ['name' => 'phone', 'data' => 'phone', 'title' => trans('admin.academies.phone')],
            ['name' => 'referral_source', 'data' => 'referral_source', 'title' => trans('admin.academies.phone')],
            ['name' => 'user_type', 'data' => 'user_type', 'title' => trans('admin.academies.user_type')],
            ['name' => 'start_date', 'data' => 'start_date', 'title' => trans('admin.training.start_date')],
            ['name' => 'training.created_at', 'data' => 'training.created_at', 'title' => trans('admin.notifications.created_at'), 'exportable' => true, 'printable' => true, 'orderable' => true, 'searchable' => false],
            ['name' => 'actions', 'data' => 'actions', 'title' => trans('admin.actions'), 'exportable' => false, 'printable' => false, 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Join_' . date('YmdHis');
    }
}
