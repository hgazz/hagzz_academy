<?php

namespace App\DataTables;

use App\Models\TClass;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TClassDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('sport_id', fn($raw) => $raw->sport->name)
            ->editColumn('title', fn($raw) => $raw->title)
            ->editColumn('subtitle', fn($raw) => $raw->subtitle)
            ->addColumn('action', function (TClass $class) {
                return view('Academy.pages.clasess.datatable.actions', compact('class'))->render();
            })
            ->filterColumn('sport.name', function ($query, $keyword) {
                $query->whereHas('sport',function ($q) use($keyword){
                    $q->whereRaw("JSON_SEARCH(lower(name), 'one', lower(?)) IS NOT NULL", ["%{$keyword}%"]);
                });
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(TClass $model): QueryBuilder
    {
       $query = $model->newQuery()->with('sport')
           ->whereBelongsTo(auth('academy')->user(),'academy');

        $sport = request()->input('sport.name');
        if ($sport) {
            $query->whereHas('sport', function ($q) use ($sport) {
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
        return $this->builder()
                    ->setTableId('tclass-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle()
            ->scrollX()
            ->scrollY()
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
            ['name' => 'sport.name', 'data' => 'sport_id', 'title' => trans('admin.clasess.sport')],
            ['name' => 'title', 'data' => 'title', 'title' => trans('admin.clasess.title')],
            ['name' => 'subtitle', 'data' => 'subtitle', 'title' => trans('admin.clasess.subtitle')],
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
