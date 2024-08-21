<?php

namespace App\DataTables;

use App\Http\Traits\DataTablesTrait;
use App\Models\Coach;
use App\Models\Follow;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
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
            ->addColumn('name_en', fn($raw) => $raw->getTranslation('name', 'en'))
            ->addColumn('name_ar', fn($raw) => $raw->getTranslation('name', 'ar'))
            ->editColumn('description_en', fn($raw) => $raw->getTranslation('description', 'en'))
            ->editColumn('description_ar', fn($raw) => $raw->getTranslation('description', 'ar'))
            ->editColumn('license_type', fn($raw) => $raw->license_type)
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
            ->addColumn('sports',function($q){
                return $q->sports->map(function($sport) {
                    return STR::limit($sport->name, 20, '...');
                })->implode(', ');
            })
            ->addColumn('actions', function (Coach $coach) {
                return view('Academy.pages.coaches.datatable.actions', compact('coach'));
            })
            ->rawColumns(['image', 'academy_id','training_count','follow_count', 'sports', 'actions', 'name_en', 'name_ar']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Coach $model): QueryBuilder
    {
        if ($this->query) {
            return $this->query;
        }

        return $model->newQuery()->with(['trainings', 'academy', 'sports'])->whereBelongsTo(auth('academy')->user() , 'academy');
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
            ['name' => 'id', 'data' => 'id', 'title' => trans('admin.id')],
            ['name' => 'name->en', 'data' => 'name_en', 'title' => trans('admin.area.name_en')],
            ['name' => 'name->ar', 'data' => 'name_ar', 'title' => trans('admin.area.name_ar')],
            ['name' => 'phone', 'data' => 'phone', 'title' => trans('admin.coaches.phone')],
            ['name' => 'gender', 'data' => 'gender', 'title' => trans('admin.training.gender')],
            ['name' => 'birth_date', 'data' => 'birth_date', 'title' => trans('admin.training.birth_date')],
            ['name' => 'description->en', 'data' => 'description_en', 'title' => trans('admin.training.description_en')],
            ['name' => 'description->ar', 'data' => 'description_ar', 'title' => trans('admin.training.description_ar')],
            ['name' => 'image', 'data' => 'image', 'title' => trans('admin.coaches.image')],
            ['name' => 'license', 'data' => 'license', 'title' => trans('admin.coaches.is_licensed'), 'orderable' => false, 'searchable' => false],
            ['name' => 'license_type', 'data' => 'license_type', 'title' => trans('admin.coaches.license_type'), 'orderable' => false, 'searchable' => false],
            ['name' => 'academy.commercial_name', 'data' => 'academy_id', 'title' => trans('admin.coaches.academy_id')],
            ['name' => 'training_count', 'data' => 'training_count', 'title' => trans('admin.training_count')],
            ['name' => 'follow_count', 'data' => 'follow_count', 'title' => trans('admin.follow_count')],
            ['name' => 'sports', 'data' => 'sports', 'title' => trans('admin.user.Sports'), 'orderable' => false, 'searchable' => false],
            ['name' => 'active', 'data' => 'active', 'title' => trans('admin.coaches.active'), 'orderable' => false, 'searchable' => false],
            ['name' => 'actions', 'data' => 'actions', 'title' => trans('admin.actions'), 'orderable' => false, 'searchable' => false],
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
