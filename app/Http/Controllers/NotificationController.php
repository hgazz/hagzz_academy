<?php

namespace App\Http\Controllers;


use App\DataTables\NotificationDataTable;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(NotificationDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.notification.index');
    }
}
