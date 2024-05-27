<?php

namespace App\DataTables;

use App\Models\Booking;
use App\Models\Join;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BookingDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('username', function ($query) {
                return $query->user->name ?? '';
            })
            ->addColumn('phone', function ($query) {
                return $query->user->phone ?? '';
            })
            ->addColumn('training', function ($query) {
                return $query->training->name ?? '';
            })
            ->addColumn('following', function ($query) {
                $followingUserIds = $query->user->following->pluck('user_id')->toArray();
                if (empty($followingUserIds)){
                    return  '<i class="fa-solid fa-xmark text-danger fs-2"></i>';
                }
                return '<i class="fa-solid fa-check fs-2 text-success"></i>';
            })
            ->rawColumns(['username','phone','training','following']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Join $model): QueryBuilder
    {
        return $model->newQuery()->with(['user','training'])
            ->whereHas('training',function($q){
            $q->where('academy_id',auth('academy')->id());
        });
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('booking-table')
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
            ['name' => 'username', 'data' => 'username', 'title' => trans('admin.profile.name')],
            ['name' => 'phone', 'data' => 'phone', 'title' => trans('admin.profile.phone')],
            ['name' => 'training.name', 'data' => 'training', 'title' => trans('admin.training.training'), 'searchable' => false],
            ['name' => 'following', 'data' => 'following', 'title' => trans('admin.profile.following')],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Booking_' . date('YmdHis');
    }
}
