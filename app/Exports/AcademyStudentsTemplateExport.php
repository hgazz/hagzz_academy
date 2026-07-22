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
            'country_code', 'country_id', 'city_id', 'area_id', 'child_type', 'school_name', 'club_member',
            'coach_preference', 'frequent_attendance',
            'guardian_name',
            'guardian_phone',
            'relation_with_child', 'referral_source', 'delivery_service', 'medical_condition', 'start_date',
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
                '+20', '', '', '', 'child', 'Future School', 'no', 'not_important', 'weekly',
                'Mohamed Ali',
                '01011111111',
                'father', 'friends', 'no', 'no', '2026-07-22',
                'active',
                'No medical notes',
                'Imported sample row',
            ],
        ];
    }
}
