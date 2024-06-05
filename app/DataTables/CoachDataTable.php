<?php

namespace App\DataTables;

use App\Http\Traits\DataTablesTrait;
use App\Models\Coach;
use App\Models\Follow;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class CoachDataTable extends DataTable
{
    use DataTablesTrait;

    protected $query;

    /**
     * Set a custom query.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return static
     */
    public function with(array|string $key, mixed $value = null): static
    {
        if (is_string($key) && $key === 'query') {
            $this->query = $value;
        }

        return $this;
    }
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('academy_id', function ($raw){
                return $raw->academy->commercial_name;
            })
            ->editColumn('image', function (Coach $coach) {
                return '<img src="' . $coach->image . '" width="90" height="70" class="img-thumbnail">';
            })
            ->editColumn('license', fn($raw) => $raw->license ? trans('admin.coaches.is_licensed') : trans('admin.coaches.no_licensed'))
            ->editColumn('active', function (Coach $coach) {
                return $coach->active ? trans('admin.address.active') : trans('admin.address.inactive');
            })
            ->filterColumn('active', function ($query, $keyword) {
                $query->where('active', $keyword === 'active' ? 1 : 0);
            })
            ->filterColumn('academy.commercial_name', function ($query, $keyword) {
                $query->whereHas('academy',function ($q) use($keyword){
                    $q->whereRaw("JSON_SEARCH(lower(commercial_name), 'one', lower(?)) IS NOT NULL", ["%{$keyword}%"]);
                });
            })
            ->addColumn('training_count',function($q){
                return $q->academy->trainings?->count() ;
            })
            ->addColumn('follow_count',function($q){
                return Follow::where('followable_id',$q->id)->count();
            })
            ->rawColumns(['image', 'academy_id','training_count','follow_count']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Coach $model): QueryBuilder
    {
        if ($this->query) {
            return $this->query;
        }

        return $model->newQuery()->whereBelongsTo(auth('academy')->user() , 'academy');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $hideButtonsArray = array_column($this->getColumns(), 'title');
        $hideButtonsArray = $this->makeHideButtons($hideButtonsArray);
        return $this->builder()
            ->setTableId('coach-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
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
                            'url' => url('//cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json')
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
            ['name' => 'name', 'data' => 'name', 'title' => trans('admin.coaches.name')],
            ['name' => 'description', 'data' => 'description', 'title' => trans('admin.coaches.description')],
            ['name' => 'image', 'data' => 'image', 'title' => trans('admin.coaches.image')],
            ['name' => 'license', 'data' => 'license', 'title' => trans('admin.coaches.is_licensed'), 'orderable' => false, 'searchable' => false],
            ['name' => 'academy.commercial_name', 'data' => 'academy_id', 'title' => trans('admin.coaches.academy_id')],
            ['name' => 'training_count', 'data' => 'training_count', 'title' => trans('admin.training_count')],
            ['name' => 'follow_count', 'data' => 'follow_count', 'title' => trans('admin.follow_count')],
            ['name' => 'active', 'data' => 'active', 'title' => trans('admin.coaches.active'), 'orderable' => false, 'searchable' => false],
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
