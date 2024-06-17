<?php

namespace App\Http\Controllers;

use App\DataTables\JoinDataTable;
use App\DataTables\UserDataTable;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.users.index');
    }
}
