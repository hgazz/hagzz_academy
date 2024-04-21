<?php

namespace App\DataTables;

use App\Models\Coach;
use App\Models\Join;
use App\Models\TClass;
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
            ->editColumn('license', fn($raw) => $raw->license ? trans('admin.coaches.is_licensed') : trans('admin.coaches.no_licensed'))
            ->addColumn('total_bookings', function (Coach $coach) {
                return Join::whereHas('training', function ($query) use ($coach) {
                    $query->where('coach_id', $coach->id)
                        ->where('academy_id', auth('academy')->id());
                })->count();
            })
            ->addColumn('active_bookings', function (Coach $coach) {
                return Join::whereHas('training', function ($query) use ($coach) {
                    $query->where('coach_id', $coach->id)
                        ->whereActive(1)
                        ->where('academy_id', auth('academy')->id());
                })->count();
            })
            ->addColumn('total_hours', function (Coach $coach) {
               $class = TClass::whereHas('training', function($query) use ($coach) {
                    $query->where('coach_id', $coach->id)
                        ->where('academy_id', auth('academy')->id());
                })->first();
               return $coach->trainings()->count() > 0 !== null ? ceil($class->duration_in_hours) : 0;
            })
            ->addColumn('action', function (Coach $coach) {
                return view('Academy.pages.coaches.datatable.actions', compact('coach'))->render();
            })
            ->rawColumns(['image', 'total_bookings', 'active_bookings', 'total_hours', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Coach $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['academy', 'trainings'])
            ->whereBelongsTo(auth('academy')->user(),'academy');
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
            ['name' => 'name', 'data' => 'name', 'title' => trans('admin.coaches.name')],
            ['name' => 'description', 'data' => 'description', 'title' => trans('admin.coaches.description')],
            ['name' => 'image', 'data' => 'image', 'title' => trans('admin.coaches.image')],
            ['name' => 'license', 'data' => 'license', 'title' => trans('admin.coaches.is_licensed')],
            ['name' => 'total_bookings', 'data' => 'total_bookings', 'title' => trans('admin.coaches.total_bookings'), 'orderable' => false, 'searchable' => false],
            ['name' => 'active_bookings', 'data' => 'active_bookings', 'title' => trans('admin.coaches.active_bookings'), 'orderable' => false, 'searchable' => false],
            ['name' => 'total_hours', 'data' => 'total_hours', 'title' => trans('admin.coaches.total_hours'), 'orderable' => false, 'searchable' => false],
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
