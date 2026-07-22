<?php

namespace App\Support;

use App\Models\AcademyStudent;

class MembershipCode
{
    public static function make(AcademyStudent $student): string
    {
        $payload = $student->academy_id.'-'.$student->id;
        $signature = strtoupper(substr(hash_hmac('sha256', $payload, (string) config('app.key')), 0, 12));

        return 'HZ-'.$payload.'-'.$signature;
    }

    public static function studentId(string $code, int $academyId): ?int
    {
        $code = trim($code);
        if (blank($code)) {
            return null;
        }

        // 1. Standard HMAC format: HZ-{academy_id}-{student_id}-{signature}
        if (preg_match('/^HZ-(\d+)-(\d+)-([A-F0-9]{12})$/i', $code, $matches)) {
            if ((int) $matches[1] === $academyId) {
                return (int) $matches[2];
            }
        }

        // 2. Formats like HZ-1-2, MEMB-2, STUDENT-2, STU-2, ID-2
        if (preg_match('/(?:HZ|MEMB|STUDENT|STU|ID)-(?:(\d+)-)?(\d+)/i', $code, $matches)) {
            $stId = (int) end($matches);
            if (AcademyStudent::where('academy_id', $academyId)->where('id', $stId)->exists()) {
                return $stId;
            }
        }

        // 3. Plain numeric ID
        if (is_numeric($code)) {
            $stId = (int) $code;
            if (AcademyStudent::where('academy_id', $academyId)->where('id', $stId)->exists()) {
                return $stId;
            }
        }

        // 4. Fallback direct lookup in AcademyStudent table
        $student = AcademyStudent::where('academy_id', $academyId)
            ->where(function ($q) use ($code) {
                $q->where('membership_code', $code)
                  ->orWhere('phone', $code)
                  ->orWhere('national_id', $code);
            })->first();

        return $student?->id;
    }
}
