<?php

namespace App\DataTables;

use App\Http\Traits\DataTablesTrait;
use App\Models\Gallery;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class GalleryDataTable extends DataTable
{
    use DataTablesTrait;
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('image', function (Gallery $gallery) {
                return '<img src="' . $gallery->image . '" width="120" height="80" class="img-thumbnail">';
            })
            ->editColumn('active', function (Gallery $gallery) {
                return $gallery->active ? trans('admin.publish') : trans('admin.not_published');
            })
            ->addColumn('action', function (Gallery $gallery) {
                return view('Academy.pages.gallery.datatable.actions', compact('gallery'))->render();
            })
            ->rawColumns(['action', 'image', 'active']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Gallery $model): QueryBuilder
    {
        return $model->newQuery()->whereBelongsTo(auth('academy')->user(),'academy');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $hideButtonsArray = array_column($this->getColumns(), 'title');
        $hideButtonsArray = $this->makeHideButtons($hideButtonsArray);
        return $this->builder()
                    ->setTableId('gallery-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->selectStyleSingle()
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
            ['name' => 'image', 'data' => 'image', 'title' => trans('admin.gallery.image')],
            ['name' => 'active', 'data' => 'active', 'title' => trans('admin.address.active'),  'searchable' => false],
            ['name' => 'action', 'data' => 'action', 'title' => trans('admin.actions'), 'exportable' => false, 'printable' => false, 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Gallery_' . date('YmdHis');
    }
}
