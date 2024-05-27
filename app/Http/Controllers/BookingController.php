<?php

namespace App\Http\Controllers;

use App\DataTables\BookingDataTable;
use App\Models\Training;

class BookingController extends Controller
{
    public function index(BookingDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.booking.index');
    }

    public function show($id)
    {
        $training = Training::with([
           'joins.user'=>function($q){
                 $q->select('id','name','phone','gender','image');
           },
            'joins.invoice'=>function($q){
                $q->select('id','order_number','status','amount');
            }
       ])->findOrFail($id);
        return view('Academy.pages.booking.show', compact('training'));
    }
}
