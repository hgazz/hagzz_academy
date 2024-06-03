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
            ->addColumn('country_id', function (Address $address) {
                return $address->country->name;
            })
            ->addColumn('city_id', function (Address $address) {
                return $address->city->name;
            })
            ->addColumn('area_id', function (Address $address) {
                return $address->area->name;
            })
            ->editColumn('academy_id', function (Address $address) {
                return $address->academy->commercial_name;
            })
            ->editColumn('location_owned', function (Address $address) {
                return $address->location_owned ? trans('admin.yes') : trans('admin.no');
            })
            ->addColumn('action', function (Address $address) {
                return view('Academy.pages.address._action', compact('address'))->render();
            })
            ->filterColumn('academy.commercial_name', function ($query, $keyword) {
                $query->whereHas('academy',function ($q) use($keyword){
                    $q->whereRaw("JSON_SEARCH(lower(commercial_name), 'one', lower(?)) IS NOT NULL", ["%{$keyword}%"]);
                });
            })
            ->filterColumn('city.name', function ($query, $keyword) {
                $query->whereHas('city',function ($q) use($keyword){
                    $q->whereRaw("JSON_SEARCH(lower(name), 'one', lower(?)) IS NOT NULL", ["%{$keyword}%"]);
                });
            })
            ->filterColumn('area.name', function ($query, $keyword) {
                $query->whereHas('area',function ($q) use($keyword){
                    $q->whereRaw("JSON_SEARCH(lower(name), 'one', lower(?)) IS NOT NULL", ["%{$keyword}%"]);
                });
            })
            ->filterColumn('country.name', function ($query, $keyword) {
                $query->whereHas('country',function ($q) use($keyword){
                    $q->whereRaw("JSON_SEARCH(lower(name), 'one', lower(?)) IS NOT NULL", ["%{$keyword}%"]);
                });
            })
            ->rawColumns(['action', 'country_id','city_id','area_id','academy_id', 'location_owned']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Address $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['country', 'city', 'area', 'academy:id,commercial_name'])
            ->whereBelongsTo(auth('academy')->user(),'academy');

        $country = request()->input('country.name');
        if ($country) {
            $query->whereHas('city', function ($q) use ($country) {
                // Use JSON_SEARCH to find any occurrence of $city within the JSON column, regardless of the key (locale)
                $q->whereRaw("JSON_SEARCH(lower(name), 'one', lower(?)) IS NOT NULL", ["%{$country}%"]);
            });
        }

        $academy = request()->input('academy.commercial_name');
        if ($academy) {
            $query->whereHas('academy', function ($q) use ($academy) {
                // Use JSON_SEARCH to find any occurrence of $city within the JSON column, regardless of the key (locale)
                $q->whereRaw("JSON_SEARCH(lower(commercial_name), 'one', lower(?)) IS NOT NULL", ["%{$academy}%"]);
            });
        }

        $city = request()->input('city.name');
        if ($city) {
            $query->whereHas('city', function ($q) use ($city) {
                // Use JSON_SEARCH to find any occurrence of $city within the JSON column, regardless of the key (locale)
                $q->whereRaw("JSON_SEARCH(lower(name), 'one', lower(?)) IS NOT NULL", ["%{$city}%"]);
            });
        }

        $area = request()->input('area.name');
        if ($area) {
            $query->whereHas('city', function ($q) use ($area) {
                // Use JSON_SEARCH to find any occurrence of $city within the JSON column, regardless of the key (locale)
                $q->whereRaw("JSON_SEARCH(lower(name), 'one', lower(?)) IS NOT NULL", ["%{$area}%"]);
            });
        }

        return $query->select('addresses.*');
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
            ->dom('Bfltip')
            ->scrollX()
            ->scrollY()
            ->parameters([
                'responsive'   => true,
                'autoWidth'    => false,
                'lengthMenu'   => [[10, 25, 50, -1], [10, 25, 50, 'All records']],
                'buttons'      => [
                    ['extend' => 'print', 'className' => 'btn btn-primary', 'text' => '<i class="fa fa-print"></i>'.trans('admin.print')],
                    ['extend' => 'excel', 'className' => 'btn btn-success', 'text' => '<i class="fa fa-file"></i>'.trans('admin.export')],

                ],
                'order' => [
                    0, 'desc'
                ],
                'language' =>
                    (app()->getLocale() === 'ar') ?
                        [
                            'url' => url('//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json')
                        ] :
                        [
                            'url' => url('//cdn.datatables.net/plug-ins/1.13.4/i18n/English.json')
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
            ['name' => 'address', 'data' => 'address', 'title' => trans('admin.address.address')],
            ['name' => 'country.name', 'data' => 'country_id', 'title' => trans('admin.address.country')],
            ['name' => 'city.name', 'data' => 'city_id', 'title' => trans('admin.address.city')],
            ['name' => 'area.name', 'data' => 'area_id', 'title' => trans('admin.address.area')],
            ['name' => 'academy.commercial_name', 'data' => 'academy_id', 'title' => trans('admin.address.academy')],
            ['name' => 'longitude', 'data' => 'longitude', 'title' => trans('admin.address.longitude')],
            ['name' => 'latitude', 'data' => 'latitude', 'title' => trans('admin.address.latitude')],
            ['name' => 'location_owned', 'data' => 'location_owned', 'title' => trans('admin.location_owned')],
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
