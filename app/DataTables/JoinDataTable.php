<?php

namespace App\DataTables;

use App\Models\Join;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class JoinDataTable extends DataTable
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
            ->addColumn('gender', function ($query) {
                return $query->user->gender ?? '';
            })
            ->addColumn('image', function (Join$join) {
                return '<img src="' . $join->user->image . '" width="120" height="80" class="img-thumbnail">';
            })
            ->addColumn('following', function ($query) {
                $followingUserIds = $query->user->following->pluck('user_id')->toArray();
                if (empty($followingUserIds)){
                    return  '<i class="fa-solid fa-xmark text-danger fs-2"></i>';
                }
                return '<i class="fa-solid fa-check fs-2 text-success"></i>';
            })
            ->rawColumns(['action', 'username','phone','gender','image','following']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Join $model): QueryBuilder
    {
        return $model->newQuery()->with([
            'training'=>function($model){
                $model->where('academy_id',auth('academy')->id())->get();
            }
        ]);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('join-table')
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
            ['name' => 'image', 'data' => 'image', 'title' => trans('admin.profile.image')],
            ['name' => 'username', 'data' => 'username', 'title' => trans('admin.profile.name')],
            ['name' => 'phone', 'data' => 'phone', 'title' => trans('admin.profile.phone')],
            ['name' => 'gender', 'data' => 'gender', 'title' => trans('admin.profile.gender')],
            ['name' => 'following', 'data' => 'following', 'title' => trans('admin.profile.following')],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Join_' . date('YmdHis');
    }
}
