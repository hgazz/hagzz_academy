<?php

namespace App\Exports;

use App\Support\PartnerAcademy;
use App\Models\TClass;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class clasessExport implements FromView
{

    public function view(): View
    {
        $classes = TClass::with(['academy','training','sport'])
            ->whereHas('training', function ($query) {
                $query->where('academy_id', PartnerAcademy::id());
            })
            ->get();
        return view('Academy.pages.clasess.export',compact('classes'));
    }
}
