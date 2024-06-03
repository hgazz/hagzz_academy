<?php

namespace App\Http\Controllers;

use App\DataTables\SettlementDataTable;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function index(SettlementDataTable $datatable)
    {
        return $datatable->render('Academy.pages.settlements.index');
    }
}
