<?php

namespace App\DataTables;

use App\Http\Traits\DataTablesTrait;
use App\Models\TClass;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class TClassDataTable extends DataTable
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
            ->editColumn('training_id', fn($raw) => $raw->training->name)
            ->editColumn('title', fn($raw) => $raw->title)
            ->editColumn('subtitle', fn($raw) => $raw->subtitle)
            ->addColumn('action', function (TClass $class) {
                return view('Academy.pages.clasess.datatable.actions', compact('class'))->render();
            })
//            ->addColumn('checkbox',function (TClass $class){
//                return view('Academy.pages.clasess.datatable.checkbox',compact('class'));
//            })
            ->filterColumn('training.name', function ($query, $keyword) {
                $query->whereHas('training',function ($q) use($keyword){
                    $q->whereRaw("JSON_SEARCH(lower(name), 'one', lower(?)) IS NOT NULL", ["%{$keyword}%"]);
                });
            })
            ->rawColumns(['action',]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(TClass $model): QueryBuilder
    {
       $query = $model->newQuery()->with('training')
           ->whereHas('training.academy', function ($query) {
               $query->where('academy_id', auth('academy')->id());
           });

        $sport = request()->input('training.name');
        if ($sport) {
            $query->whereHas('training', function ($q) use ($sport) {
                // Use JSON_SEARCH to find any occurrence of $city within the JSON column, regardless of the key (locale)
                $q->whereRaw("JSON_SEARCH(lower(name), 'one', lower(?)) IS NOT NULL", ["%{$sport}%"]);
            });
        }

        return $query->select('t_classes.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $hideButtonsArray = array_column($this->getColumns(), 'title');
        $hideButtonsArray = $this->makeHideButtons($hideButtonsArray);
        return $this->builder()
                    ->setTableId('tclass-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfltip')
                    ->selectStyleSingle()
                    ->scrollX()
                    ->scrollY()
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
//            ['name' => 'checkbox', 'data' => 'checkbox', 'title' => trans('admin.actions'), 'exportable' => false, 'printable' => false, 'orderable' => false, 'searchable' => false],
            ['name' => 'id', 'data' => 'id', 'title' => trans('admin.id')],
            ['name' => 'training.name', 'data' => 'training_id', 'title' => trans('admin.clasess.training')],
            ['name' => 'title', 'data' => 'title', 'title' => trans('admin.clasess.title')],
//            ['name' => 'subtitle', 'data' => 'subtitle', 'title' => trans('admin.clasess.subtitle')],
            ['name' => 'date', 'data' => 'date', 'title' => trans('admin.clasess.date')],
            ['name' => 'start_time', 'data' => 'start_time', 'title' => trans('admin.clasess.start_time')],
            ['name' => 'end_time', 'data' => 'end_time', 'title' => trans('admin.clasess.end_time')],
            ['name' => 'action', 'data' => 'action', 'title' => trans('admin.actions'), 'exportable' => false, 'printable' => false, 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'TClass_' . date('YmdHis');
    }
}
