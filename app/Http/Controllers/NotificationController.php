<?php

namespace App\Http\Controllers;


use App\DataTables\NotificationDataTable;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(NotificationDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.notification.index');
    }

    public function markAsRead(Notification $notification)
    {
        $notification->update(['read_at' => now()]);
        return back();
    }

}
