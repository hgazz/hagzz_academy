<?php

namespace App\Exports;

use App\Models\Coach;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class Coaches implements FromView
{

    public function view(): View
    {
        $coachesExport = Coach::with(['academy'])->get();
        return view('Academy.pages.coaches.export',compact('coachesExport'));
    }
}
