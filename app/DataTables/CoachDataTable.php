<?php

namespace App\DataTables;

use App\Models\Coach;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CoachDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('image', function (Coach $coach) {
                return '<img src="' . $coach->image . '" width="100" height="100">';
            })
            ->editColumn('academy_id', function (Coach $coach) {
                return $coach->academy->first_name . ' ' . $coach->academy->last_name;
            })
            ->addColumn('action', function (Coach $coach) {
                return view('Academy.pages.coaches.datatable.actions', compact('coach'))->render();
            })
            ->rawColumns(['image', 'academy_id', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Coach $model): QueryBuilder
    {
        return $model->newQuery()->with('academy')->whereBelongsTo(auth('academy')->user(),'academy');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('coach-table')
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
            ['name' => 'name', 'data' => 'name', 'title' => trans('admin.coaches.name')],
            ['name' => 'description', 'data' => 'description', 'title' => trans('admin.coaches.description')],
            ['name' => 'image', 'data' => 'image', 'title' => trans('admin.coaches.image')],
            ['name' => 'academy_id', 'data' => 'academy_id', 'title' => trans('admin.coaches.academy')],
            ['name' => 'active', 'data' => 'active', 'title' => trans('admin.coaches.active')],
            ['name' => 'action', 'data' => 'action', 'title' => trans('admin.actions'), 'exportable' => false, 'printable' => false, 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Coach_' . date('YmdHis');
    }
}
