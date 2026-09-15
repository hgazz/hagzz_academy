<?php

namespace App\Imports;

use App\Models\AcademyStudent;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AcademyStudentsImport implements ToCollection, WithHeadingRow
{
    public int $totalRows = 0;
    public int $created = 0;
    public int $updated = 0;
    public int $skipped = 0;
    public array $errors = [];

    public function __construct(private readonly int $academyId)
    {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // 1-based index + header row

            // Check if entire row is empty
            $nonEmpty = $row->filter(fn ($v) => !blank($v));
            if ($nonEmpty->isEmpty()) {
                continue;
            }

            $this->totalRows++;
            $data = $this->normalizeRow($row);

            if (empty($data['name'])) {
                $this->skipped++;
                $this->errors[] = [
                    'row' => $rowNumber,
                    'name' => '-',
                    'reason' => 'حقل الاسم فارغ أو لم يتم التعرف على عمود الاسم',
                ];
                continue;
            }

            try {
                // Enforce strict multi-tenant isolation: cannot overwrite id or academy_id
                unset($data['id'], $data['academy_id']);
                $data['academy_id'] = $this->academyId;

                $student = $this->findExistingStudent($data);

                if ($student) {
                    $student->update($data);
                    $this->updated++;
                } else {
                    AcademyStudent::create($data);
                    $this->created++;
                }
            } catch (\Throwable $e) {
                $this->skipped++;
                $this->errors[] = [
                    'row' => $rowNumber,
                    'name' => $data['name'],
                    'reason' => 'تعذر حفظ السجل: ' . Str::limit($e->getMessage(), 120),
                ];
            }
        }
    }

    /**
     * Find existing student strictly:
     * We match by NAME AND (PHONE or EMAIL or GUARDIAN_PHONE)
     * so that siblings/family members sharing the same parent phone number are NEVER overwritten!
     */
    private function findExistingStudent(array $data): ?AcademyStudent
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            return null;
        }

        $query = AcademyStudent::where('academy_id', $this->academyId)
            ->where('name', $name);

        $hasContact = false;
        $query->where(function ($sub) use ($data, &$hasContact) {
            if (!empty($data['phone'])) {
                $sub->orWhere('phone', $data['phone']);
                $hasContact = true;
            }
            if (!empty($data['email'])) {
                $sub->orWhere('email', $data['email']);
                $hasContact = true;
            }
            if (!empty($data['guardian_phone'])) {
                $sub->orWhere('guardian_phone', $data['guardian_phone']);
                $hasContact = true;
            }
        });

        // If no contact info exists at all, only match if name matches and contacts are also empty
        if (!$hasContact) {
            return $query->whereNull('phone')->first();
        }

        return $query->first();
    }

    private function normalizeRow(Collection $row): array
    {
        $genderRaw = Str::lower(trim((string) $this->value($row, ['gender', 'الجنس', 'النوع', 'sex'])));
        $statusRaw = Str::lower(trim((string) $this->value($row, ['status', 'الحالة', 'حالة الطالب'])));

        $name = trim((string) $this->value($row, [
            'name', 'student_name', 'full_name', 'player_name', 'athlete_name', 'student',
            'الاسم', 'اسم الطالب', 'اسم_الطالب', 'اسم اللاعب', 'اسم_اللاعب', 'اسم المتدرب',
            'اسم_المتدرب', 'اسم المشترك', 'اسم_المشترك', 'الاسم بالكامل', 'الاسم_بالكامل',
            'الاسم ثلاثي', 'الاسم رباعي'
        ]));

        $phone = $this->cleanPhone($this->value($row, [
            'phone', 'mobile', 'tel', 'cell', 'phone_number',
            'الهاتف', 'رقم الهاتف', 'رقم_الهاتف', 'الجوال', 'رقم الجوال', 'الموبايل', 'رقم الموبايل', 'تليفون'
        ]));

        $guardianPhone = $this->cleanPhone($this->value($row, [
            'guardian_phone', 'parent_phone', 'father_phone', 'mother_phone',
            'هاتف ولي الأمر', 'هاتف_ولي_الأمر', 'هاتف ولي الامر', 'هاتف_ولي_الامر',
            'رقم ولي الأمر', 'رقم_ولي_الأمر', 'رقم ولي الامر', 'جوال ولي الأمر'
        ]));

        return [
            'name' => $name,
            'phone' => $phone,
            'email' => $this->nullableString($this->value($row, ['email', 'البريد', 'البريد الإلكتروني', 'البريد_الإلكتروني', 'ايميل'])),
            'gender' => match ($genderRaw) {
                'female', 'f', 'girl', 'أنثى', 'انثى', 'بنت' => 'female',
                'male', 'm', 'boy', 'ذكر', 'ولد' => 'male',
                default => null,
            },
            'birth_date' => $this->parseDate($this->value($row, ['birth_date', 'date_of_birth', 'dob', 'تاريخ الميلاد', 'تاريخ_الميلاد', 'الميلاد'])),
            'country_code' => $this->nullableString($this->value($row, ['country_code', 'كود الدولة', 'رمز الدولة'])),
            'child_type' => $this->nullableString($this->value($row, ['child_type', 'نوع الطفل', 'التصنيف'])),
            'school_name' => $this->nullableString($this->value($row, ['school_name', 'المدرسة', 'اسم المدرسة'])),
            'club_member' => $this->nullableString($this->value($row, ['club_member', 'عضو نادي', 'عضوية النادي'])),
            'coach_preference' => $this->nullableString($this->value($row, ['coach_preference', 'المدرب المفضل'])),
            'guardian_name' => $this->nullableString($this->value($row, [
                'guardian_name', 'parent_name', 'father_name',
                'ولي الأمر', 'اسم ولي الأمر', 'ولي_الأمر', 'ولي الامر', 'اسم ولي الامر', 'الوالد'
            ])),
            'guardian_phone' => $guardianPhone,
            'relation_with_child' => $this->nullableString($this->value($row, ['relation_with_child', 'صلة القرابة', 'صلة_القرابة'])),
            'referral_source' => $this->nullableString($this->value($row, ['referral_source', 'مصدر التعرف'])),
            'delivery_service' => $this->nullableString($this->value($row, ['delivery_service', 'خدمة التوصيل'])),
            'medical_condition' => $this->nullableString($this->value($row, ['medical_condition', 'الحالة الصحية', 'حالة صحية'])),
            'start_date' => $this->parseDate($this->value($row, ['start_date', 'تاريخ البدء', 'تاريخ الانضمام'])),
            'status' => match ($statusRaw) {
                'inactive', 'غير نشط', 'غير_نشط' => 'inactive',
                'suspended', 'موقوف' => 'suspended',
                default => 'active',
            },
            'medical_notes' => $this->nullableString($this->value($row, ['medical_notes', 'medical', 'ملاحظات طبية', 'ملاحظات_طبية'])),
            'notes' => $this->nullableString($this->value($row, ['notes', 'ملاحظات'])),
        ];
    }

    private function value(Collection $row, array $keys): mixed
    {
        $normalizedRowKeys = [];
        foreach ($row->keys() as $k) {
            $norm = $this->normalizeKey((string) $k);
            $normalizedRowKeys[$norm] = $k;
        }

        foreach ($keys as $key) {
            $normKey = $this->normalizeKey($key);
            if (isset($normalizedRowKeys[$normKey])) {
                $actualKey = $normalizedRowKeys[$normKey];
                $val = $row->get($actualKey);
                if (!blank($val)) {
                    return $val;
                }
            }
        }

        return null;
    }

    private function normalizeKey(string $key): string
    {
        $clean = trim($key);
        // Replace Arabic alefs with plain alef, teh marbuta with heh
        $clean = str_replace(['أ', 'إ', 'آ'], 'ا', $clean);
        $clean = str_replace('ة', 'ه', $clean);
        $clean = str_replace(['_', '-', ' '], '', $clean);
        return Str::lower($clean);
    }

    private function cleanPhone(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }
        $phone = trim((string) $value);
        // Remove spaces, dashes, parentheses
        $phone = preg_replace('/[^\d\+]/', '', $phone);
        return $phone !== '' ? $phone : null;
    }

    private function parseDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        // Handle Excel numeric date serials
        if (is_numeric($value) && (float) $value > 10000 && (float) $value < 100000) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable) {
                // fall through
            }
        }

        try {
            return Carbon::parse(trim((string) $value))->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function nullableString(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }
        $val = trim((string) $value);
        return $val === '' ? null : $val;
    }
}
