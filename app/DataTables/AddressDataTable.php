<?php

namespace App\DataTables;

use App\Models\Address;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AddressDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('address', fn($raw) => $raw->address)
            ->addColumn('city_id', function (Address $address) {
                return $address->city->name;
            })
            ->addColumn('area_id', function (Address $address) {
                return $address->area->name;
            })
            ->addColumn('academy_id', function (Address $address) {
                return $address->academy->name;
            })
            ->addColumn('action', function (Address $address) {
                return view('Academy.pages.address._action', compact('address'))->render();
            })
            ->rawColumns(['action', 'city_id','area_id','academy_id']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Address $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('address-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(1)
                    ->scrollX()
                    ->scrollY()
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
            ['name' => 'address', 'data' => 'address', 'title' => trans('admin.address.address')],
            ['name' => 'city.name', 'data' => 'city_id', 'title' => trans('admin.address.city')],
            ['name' => 'area.name', 'data' => 'area_id', 'title' => trans('admin.address.area')],
            ['name' => 'academy.name', 'data' => 'academy_id', 'title' => trans('admin.address.academy')],
            ['name' => 'longitude', 'data' => 'longitude', 'title' => trans('admin.address.longitude')],
            ['name' => 'latitude', 'data' => 'latitude', 'title' => trans('admin.address.latitude')],

            ['name' => 'action', 'data' => 'action', 'title' => trans('admin.actions'), 'exportable' => false, 'printable' => false, 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Address_' . date('YmdHis');
    }
}
