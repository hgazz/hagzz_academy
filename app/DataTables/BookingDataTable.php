<?php

namespace App\DataTables;

use App\Http\Traits\DataTablesTrait;
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
    use DataTablesTrait;
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
            ->addColumn('commercial_name', function ($query) {
                return $query->training->academy->commercial_name ?? '';
            })
            ->addColumn('coach_name', function ($query) {
                return $query->training->coach->name ?? '';
            })
            ->addColumn('following', function ($query) {
                $followingUserIds = $query->user->following->pluck('user_id')->toArray();
                if (empty($followingUserIds)){
                    return  '<i class="fa-solid fa-xmark text-danger fs-2"></i>';
                }
                return '<i class="fa-solid fa-check fs-2 text-success"></i>';
            })
            ->rawColumns(['username','phone','training','following', 'commercial_name', 'coach_name']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Join $model): QueryBuilder
    {
        return $model->newQuery()->whereHas('training', function ($query) {
            $query->where('academy_id', auth('academy')->id());
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
                    ->setTableId('booking-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->selectStyleSingle()
                    ->scrollX()
                    ->scrollY()
                    ->dom('Bfltip')
            ->parameters([
                'scrollX' => true,
                'scrollY' => true,
                'autoWidth' => false,
                'lengthMenu' => [[10, 25, 50, -1], [10, 25, 50, 'All records']],
                'buttons' => [
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
            ['name' => 'user.name', 'data' => 'username', 'title' => trans('admin.profile.name')],
            ['name' => 'phone', 'data' => 'phone', 'title' => trans('admin.profile.phone')],
            ['name' => 'training.name', 'data' => 'training', 'title' => trans('admin.training.training'), 'searchable' => false],
            ['name' => 'training.academy.commercial_name', 'data' => 'commercial_name', 'title' => trans('admin.address.academy'), 'searchable' => false],
            ['name' => 'training.coach.name', 'data' => 'coach_name', 'title' => trans('admin.coach_name'), 'searchable' => false],
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
