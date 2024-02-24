<?php

namespace App\DataTables;

use App\Models\Address;
use App\Models\Training;
use App\Models\TrainingClass;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TrainingDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('name', fn($raw) => $raw->name)
            ->editColumn('description', fn($raw) => $raw->description)
            ->addColumn('coach_id', function (Training $training) {
                return $training->coach->name;
            })

            ->addColumn('image', function (Training $training) {
                return '<img src="' . $training->image . '" width="100" height="100">';
            })
            ->addColumn('class', function (Training $trainingClass) {
                foreach ($trainingClass->classes as $class) {
                  return  $class->title;
                }

            })
            ->addColumn('action', function (Training $training) {
                return view('Academy.pages.training.datatable.actions', compact('training'))->render();
            })
            ->rawColumns(['action', 'coach_id','image','class']);

    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Training $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('training-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
            ->scrollX()
            ->scrollY()
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
            ['name' => 'name', 'data' => 'name', 'title' => trans('admin.training.name')],
            ['name' => 'class', 'data' => 'class', 'title' => trans('admin.training.class')],
            ['name' => 'start_date', 'data' => 'start_date', 'title' => trans('admin.training.start_date')],
            ['name' => 'end_date', 'data' => 'end_date', 'title' => trans('admin.training.end_date')],
            ['name' => 'image', 'data' => 'image', 'title' => trans('admin.training.image')],
            ['name' => 'description', 'data' => 'description', 'title' => trans('admin.training.description')],
            ['name' => 'coach.name', 'data' => 'coach_id', 'title' => trans('admin.training.coach')],
            ['name' => 'action', 'data' => 'action', 'title' => trans('admin.actions'), 'exportable' => false, 'printable' => false, 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Training_' . date('YmdHis');
    }
}
