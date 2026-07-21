<?php

namespace App\Http\Controllers;

class SettlementController extends Controller
{
    public function index()
    {
        return redirect()->route('academy.report.settlement.index');
    }
}
