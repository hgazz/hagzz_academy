<?php

namespace App\DataTables;

use App\Http\Traits\DataTablesTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
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
            ->addColumn('country',function (User $user){
              return $user?->country?->name;
            })
            ->addColumn('city',function (User $user){
                return $user?->city?->name;
            })
            ->addColumn('area',function (User $user){
                return $user?->area?->name;
            })
            ->addColumn('image', function (User $user) {
                return sprintf(
                    '<img src="%s" alt="%s" class="user-avatar" loading="lazy" onerror="this.onerror=null;this.src=\'%s\';">',
                    e($user->image),
                    e($user->name ?? trans('admin.user.user')),
                    e($user->defaultImageUrl())
                );
            })
            ->rawColumns(['image']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['city', 'country', 'area', 'joins', 'joins.training', 'joins.user'])
            ->whereHas('joins', function ($q) {
            $q->whereHas('training', function ($q) {
                $q->where('academy_id', auth('academy')->id());
            });
        });
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $hideButtonsArray = array_column($this->getColumns(), 'title');
        $hideButtonsArray = $this->makeHideButtons($hideButtonsArray);
        return $this->builder()
                    ->setTableId('user-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfltip')
                    ->scrollX()
                    ->scrollY()
                    ->selectStyleSingle()
                    ->parameters([
                        'scrollX' => true,
                        'scrollY' => true,
                        'autoWidth' => false,
                        'lengthMenu' => [[10, 25, 50, -1], [10, 25, 50, 'All records']],
                        'buttons' => [
                            $hideButtonsArray,
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
                                    'url' => asset('datatableEn.json')
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
            ['name' => 'image', 'data' => 'image', 'title' => trans('admin.banners.image')],
            ['name' => 'name', 'data' => 'name', 'title' => trans('admin.user.name')],
            ['name' => 'phone', 'data' => 'phone', 'title' => trans('admin.user.phone')],
            ['name' => 'gender', 'data' => 'gender', 'title' => trans('admin.user.gender')],
            ['name' => 'user_type', 'data' => 'user_type', 'title' => trans('admin.user.user_type')],
            ['name' => 'birth_date', 'data' => 'birth_date', 'title' => trans('admin.user.birth_date')],
            ['name' => 'country.name', 'data' => 'country', 'title' => trans('admin.user.country')],
            ['name' => 'city.name', 'data' => 'city', 'title' => trans('admin.user.city')],
            ['name' => 'area.name', 'data' => 'area', 'title' => trans('admin.user.area')],

        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'User_' . date('YmdHis');
    }
}
