<?php

namespace App\DataTables;


use App\Http\Traits\DataTablesTrait;
use App\Models\Training;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class TrainingDataTable extends DataTable
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
            ->editColumn('name', fn($raw) => $raw->name)
            ->editColumn('description', fn($raw) => $raw->description)
            ->editColumn('active', fn($raw) => $raw->active ? trans('admin.training.Active') : trans('admin.training.InActive'))
            ->editColumn('coach_id', function (Training $training) {
                return $training->coach->name;
            })
            ->editColumn('sport_id', function (Training $training) {
                return $training->sport->name;
            })
            ->addColumn('action', function (Training $training) {
                return view('Academy.pages.training.datatable.actions', compact('training'))->render();
            })
            ->editColumn('classes_days', function (Training $training) {
                return ! is_null($training->classes_days ) ? $training->classes_days : null;
            })
            ->editColumn('color', function (Training $training) {
                return "<div style='background-color: $training->color; width: 20px; height: 20px; border-radius: 2px'></div>";
            })
//            ->addColumn('classes', function (Training $training) {
//                return $training->classes->count();
//            })
//            ->addColumn('delete', function (Training $training) {
//                return view('Academy.pages.training.datatable.checkbox', compact('training'));
//            })
//            ->addColumn('publish', function (Training $training) {
//                return view('Academy.pages.training.datatable.publish', compact('training'));
//            })
            ->rawColumns(['action', 'coach_id', 'sport_id','image','class', 'active', 'classes_days', 'color']);

    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Training $model): QueryBuilder
    {
        return $model->newQuery()->with(['coach', 'sport'])->whereBelongsTo(auth('academy')->user(), 'academy');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $hideButtonsArray = array_column($this->getColumns(), 'title');
        $hideButtonsArray = $this->makeHideButtons($hideButtonsArray);
        return $this->builder()
                    ->setTableId('training-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->scrollX()
                    ->scrollY()
                    ->dom('Bfltip')
                    ->parameters([
                        'responsive'   => false,
                        'autoWidth'    => false,
                        'lengthMenu'   => [[10, 25, 50, -1], [10, 25, 50, 'All records']],
                        'buttons'      => [
                            $hideButtonsArray

                        ],
                        'order' => [
                            0,
                        ],
                        'language' =>
                            (app()->getLocale() === 'ar') ?
                                [
                                    'url' => asset('datatableAr.json')
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
//            ['name' => 'delete', 'data' => 'delete', 'title' => trans('admin.delete')],
//            ['name' => 'publish', 'data' => 'publish', 'title' => trans('admin.publish')],
            ['name' => 'id', 'data' => 'id', 'title' => trans('admin.id')],
            ['name' => 'name', 'data' => 'name', 'title' => trans('admin.training.name')],
            ['name' => 'price', 'data' => 'price', 'title' => trans('admin.training.price')],
            ['name' => 'discount_price', 'data' => 'discount_price', 'title' => trans('admin.training.discount')],
            ['name' => 'start_time', 'data' => 'start_time', 'title' => trans('admin.training.start_time')],
            ['name' => 'end_time', 'data' => 'end_time', 'title' => trans('admin.training.end_time')],
            ['name' => 'sport.name', 'data' => 'sport_id', 'title' => trans('admin.sport.sport')],
            ['name' => 'coach.name', 'data' => 'coach_id', 'title' => trans('admin.training.coach')],
            ['name' => 'level', 'data' => 'level', 'title' => trans('admin.training.level')],
            ['name' => 'gender', 'data' => 'gender', 'title' => trans('admin.training.gender')],
            ['name' => 'age_group', 'data' => 'age_group', 'title' => trans('admin.training.age_group')],
            ['name' => 'classes_number', 'data' => 'classes_number', 'title' => trans('admin.training.classes_number')],
            ['name' => 'classes_days', 'data' => 'classes_days', 'title' => trans('admin.training.classes_days')],
            ['name' => 'color', 'data' => 'color', 'title' => trans('admin.training.color')],
            ['name' => 'max_players', 'data' => 'max_players', 'title' => trans('admin.training.max_players')],
            ['name' => 'active', 'data' => 'active', 'title' => trans('admin.training.Active')],
//            ['name' => 'classes', 'data' => 'classes', 'title' => trans('admin.training.class'), 'exportable' => false, 'printable' => false, 'orderable' => false, 'searchable' => false],
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
