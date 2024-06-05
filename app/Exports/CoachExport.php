<?php

namespace App\Exports;

use App\Models\Coach;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class CoachExport implements FromView
{

    private $coachesExport;
    public function __construct()
    {
        $coachesData = session('coachesData', []);
        if (!empty($coachesData) && count($coachesData) > 0) {
            $this->coachesExport = $coachesData;
        } else {
            $coachesExport = Coach::where('academy_id',auth('academy')->id())->get();
            $this->coachesExport = $coachesExport;
        }
    }

    public function view(): View
    {

       return  view('Academy.pages.coaches.export',with(['coachesExport' => $this->coachesExport]));
    }
}
