<?php

namespace App\Imports;

use App\Models\AcademyStudent;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AcademyStudentsImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;
    public int $updated = 0;
    public int $skipped = 0;

    public function __construct(private readonly int $academyId)
    {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $data = $this->normalizeRow($row);

            if ($data['name'] === '') {
                $this->skipped++;
                continue;
            }

            $student = $this->findExistingStudent($data);

            if ($student) {
                $student->update($data);
                $this->updated++;
                continue;
            }

            AcademyStudent::create(array_merge($data, ['academy_id' => $this->academyId]));
            $this->created++;
        }
    }

    private function findExistingStudent(array $data): ?AcademyStudent
    {
        if (empty($data['phone']) && empty($data['email'])) {
            return null;
        }

        return AcademyStudent::where('academy_id', $this->academyId)
            ->where(function ($query) use ($data) {
                if (! empty($data['phone'])) {
                    $query->orWhere('phone', $data['phone']);
                }

                if (! empty($data['email'])) {
                    $query->orWhere('email', $data['email']);
                }
            })
            ->first();
    }

    private function normalizeRow(Collection $row): array
    {
        $gender = Str::lower(trim((string) $this->value($row, ['gender', 'الجنس'])));
        $status = Str::lower(trim((string) $this->value($row, ['status', 'الحالة'])));

        return [
            'name' => trim((string) $this->value($row, ['name', 'student_name', 'الاسم', 'اسم الطالب'])),
            'phone' => $this->nullableString($this->value($row, ['phone', 'mobile', 'الهاتف', 'رقم الهاتف'])),
            'email' => $this->nullableString($this->value($row, ['email', 'البريد', 'البريد الإلكتروني'])),
            'gender' => match ($gender) {
                'female', 'f', 'girl', 'أنثى', 'انثى' => 'female',
                'male', 'm', 'boy', 'ذكر' => 'male',
                default => null,
            },
            'birth_date' => $this->nullableString($this->value($row, ['birth_date', 'date_of_birth', 'تاريخ الميلاد'])),
            'guardian_name' => $this->nullableString($this->value($row, ['guardian_name', 'parent_name', 'ولي الأمر', 'اسم ولي الأمر'])),
            'guardian_phone' => $this->nullableString($this->value($row, ['guardian_phone', 'parent_phone', 'هاتف ولي الأمر'])),
            'status' => match ($status) {
                'inactive', 'غير نشط' => 'inactive',
                'suspended', 'موقوف' => 'suspended',
                default => 'active',
            },
            'medical_notes' => $this->nullableString($this->value($row, ['medical_notes', 'medical', 'ملاحظات طبية'])),
            'notes' => $this->nullableString($this->value($row, ['notes', 'ملاحظات'])),
        ];
    }

    private function value(Collection $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            if ($row->has($key)) {
                return $row->get($key);
            }
        }

        return null;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
