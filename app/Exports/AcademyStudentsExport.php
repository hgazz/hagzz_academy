<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AcademyStudentsExport implements FromView
{
    public function __construct(private readonly iterable $students, private readonly mixed $academy)
    {
    }

    public function view(): View
    {
        return view('Academy.pages.students.export', [
            'students' => $this->students,
            'academy' => $this->academy,
        ]);
    }
}
