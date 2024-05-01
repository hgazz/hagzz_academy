<?php

namespace App\Http\Controllers;

use App\DataTables\JoinDataTable;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(JoinDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.joins.index');
    }
}
