<?php

namespace App\Exports;

use App\Models\Join;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BookingOfflineExport implements FromView
{
    public $join;

    public function __construct($join)
    {

        $this->join = $join;
    }

    public function view(): View
    {
        $join = Join::with(['invoice', 'user'])->find($this->join);
        return view('exports.booking-offline', compact('join'));
    }
}
