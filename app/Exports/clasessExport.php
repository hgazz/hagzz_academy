<?php

namespace App\Exports;

use App\Models\TClass;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class clasessExport implements FromView
{

    public function view(): View
    {
        $classes = TClass::with(['academy','training','sport'])->where('academy_id',auth('academy')->id())->get();
        return view('Academy.pages.clasess.export',compact('classes'));
    }
}
