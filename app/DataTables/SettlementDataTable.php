<?php

namespace App\DataTables;

use App\Models\Settlement;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SettlementDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Settlement $model): QueryBuilder
    {
        return $model->newQuery()->where('partner_id', auth('academy')->id());
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('settlement-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            ['name' => 'id', 'data' => 'id', 'title' => trans('admin.id')],
            ['name' => 'total_amount', 'data' => 'total_amount', 'title' => trans('admin.settlement.total_amount')],
            ['name' => 'settlement_date', 'data' => 'settlement_date', 'title' => trans('admin.settlement.settlement_date')],
            ['name' => 'status', 'data' => 'status', 'title' => trans('admin.settlement.status'), 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Settlement_' . date('YmdHis');
    }
}
