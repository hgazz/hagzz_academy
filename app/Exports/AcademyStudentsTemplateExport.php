<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AcademyStudentsTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'name',
            'phone',
            'email',
            'gender',
            'birth_date',
            'guardian_name',
            'guardian_phone',
            'status',
            'medical_notes',
            'notes',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Ahmed Mohamed',
                '01000000000',
                'student@example.com',
                'male',
                '2014-01-20',
                'Mohamed Ali',
                '01011111111',
                'active',
                'No medical notes',
                'Imported sample row',
            ],
        ];
    }
}
