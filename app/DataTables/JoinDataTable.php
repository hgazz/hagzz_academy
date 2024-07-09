<?php

namespace App\DataTables;

use App\Http\Traits\DataTablesTrait;
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
    use DataTablesTrait;
//    protected $query;

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
            ->editColumn('name', fn($raw) => $raw->name)
            ->addColumn('training_id', fn($join) => $join->training->name ?? '')
            ->addColumn('partner_name', fn($join) => $join->training->academy->commercial_name ?? '')
            ->addColumn('sport', fn($join) => $join->training->sport->name ?? '')
            ->addColumn('level', fn($join) => $join->training->level ?? '')
            ->addColumn('age_group', fn($join) => $join->training->age_group ?? '')
            ->addColumn('classes', fn($join) => $join?->training?->classes?->count() ?? '')
            ->addColumn('start_date', fn($join) => $join->training->start_date ?? '')
            ->addColumn('end_date', fn($join) => $join->training->end_date ?? '')
            ->addColumn('coach', fn($join) => $join?->training?->coach?->name ?? '')
            ->addColumn('count', fn($join) => $join->training?->joins?->count() ?? '')
            ->addColumn('max_player', fn($join) => $join->training->max_players ?? '')
            ->addColumn('price', fn($join) => $join?->training?->price)
            ->addColumn('discount_price', fn($join) => $join?->training?->discount_price)
            ->rawColumns([
                'training',
                'partner_name',
                'level',
                'sport',
                'age_group',
                'classes',
                'start_date',
                'end_date',
                'coach',
                'count',
                'max_player',
                'price',
                'discount_price'
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Join $model): QueryBuilder
    {
//        if ($this->query) {
//            return $this->query;
//        }

        return $model->newQuery()->with([
            'training' => function ($query) {
                $query->where('academy_id', auth('academy')->id());
            },
            'user'
        ]);
    }


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {

        $hideButtonsArray = array_column($this->getColumns(), 'title');
        $hideButtonsArray = $this->makeHideButtons($hideButtonsArray);
        return $this->builder()
                    ->setTableId('join-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->scrollX()
                    ->scrollY()
                    ->selectStyleSingle()
                    ->dom('Bfltip')
                    ->parameters([
                        'responsive'   => true,
                        'autoWidth'    => false,
                        'lengthMenu'   => [[10, 25, 50, -1], [10, 25, 50, 'All records']],
                        'buttons'      => [
                            $hideButtonsArray

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
            ['name' => 'id', 'data' => 'id', 'title' => trans('admin.id')],
            ['name' => 'training.name', 'data' => 'training_id', 'title' => trans('admin.training.name')],
            ['name' => 'training.academy.commercial_name', 'data' => 'partner_name', 'title' => trans('admin.academy')],
            ['name' => 'training.level', 'data' => 'level', 'title' => trans('admin.training.level')],
            ['name' => 'sport', 'data' => 'sport', 'title' => trans('admin.clasess.sport')],
            ['name' => 'age_group', 'data' => 'age_group', 'title' => trans('admin.training.age_group')],
            ['name' => 'classes', 'data' => 'classes', 'title' => trans('admin.classes')],
            ['name' => 'start_date', 'data' => 'start_date', 'title' => trans('admin.training.start_date')],
            ['name' => 'end_date', 'data' => 'end_date', 'title' => trans('admin.training.end_date')],
            ['name' => 'coach', 'data' => 'coach', 'title' => trans('admin.training.coach')],
            ['name' => 'count', 'data' => 'count', 'title' => trans('admin.booking_count')],
            ['name' => 'max_player', 'data' => 'max_player', 'title' => trans('admin.training.max_players')],
            ['name' => 'price', 'data' => 'price', 'title' => trans('admin.training.price')],
            ['name' => 'discount_price', 'data' => 'discount_price', 'title' => trans('admin.discount_price')],
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
