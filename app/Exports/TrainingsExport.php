<?php

namespace App\Exports;

use App\Models\Training;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TrainingsExport implements FromView
{
    public function view(): View
    {
        $trainingExports = Training::with(['academy','address','sport','coach'])->get();
        return view('Academy.pages.training.export',compact('trainingExports'));
    }
}
