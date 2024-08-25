<?php

namespace App\DataTables;

use App\Models\Academies;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class NotificationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('notifiable', function ($notification) {
                return $notification?->notifiable?->name ;
            })
            ->editColumn('notifiable_type', function ($notification) {
                return  $notification->notifiable_type == "App\\Models\\User" ? trans("admin.notifications.user") : trans("admin.notifications.partner")  ;
            })
            ->editColumn('description', function ($notification) {
                return $notification->description ?? null;
            })
            ->addColumn('training', function ($notification) {
               return Notification::whereJsonContains('details->training_id', $notification->details['training_id'])
                    ->join('trainings', 'trainings.id', '=', Notification::raw('json_unquote(details->"$.training_id")'))
                    ->first(['trainings.name']);
            })
            ->editColumn('created_at', function ($notification) {
                $date = Carbon::parse($notification->created_at);
                return $date->format('F j, Y');
            })
            ->addColumn('action', 'notification.action')
            ->setRowId('id')
            ->rawColumns(['notifiable', 'training']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Notification $model): QueryBuilder
    {
        return $model->newQuery()
            ->where(['notifiable_type' => Academies::class, 'notifiable_id' => auth('academy')->id()])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('notification-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfltip')
            ->selectStyleSingle()
            ->orderBy(0, 'desc')
            ->parameters([
                'scrollX' => true,
                'scrollY' => true,
                'autoWidth' => false,
                'lengthMenu' => [[10, 25, 50, -1], [10, 25, 50, 'All records']],
                'buttons' => [

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
            ['data' => 'notifiable', 'name' => 'notifiable', 'title' =>trans("admin.profile.user"), 'searchable' => false, 'orderable' => false],
            ['data' => 'notifiable_type', 'name' => 'notifiable_type', 'title' =>trans("admin.notifications.type")],
            ['data' => 'title', 'name' => 'title', 'title' =>trans("admin.notifications.title")],
            ['data' => 'description', 'name' => 'description', 'title' =>trans("admin.notifications.description")],
            ['data' => 'training', 'name' => 'training', 'title' =>trans("admin.training.training"), 'searchable' => false, 'orderable' => false],
            ['data' => 'created_at', 'name' => 'created_at', 'title' =>trans("admin.notifications.created_at")],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Notification_' . date('YmdHis');
    }
}
