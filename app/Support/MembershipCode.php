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
        if (!preg_match('/^HZ-(\d+)-(\d+)-([A-F0-9]{12})$/i', trim($code), $matches)) {
            return null;
        }
        if ((int) $matches[1] !== $academyId) {
            return null;
        }

        $payload = $matches[1].'-'.$matches[2];
        $expected = strtoupper(substr(hash_hmac('sha256', $payload, (string) config('app.key')), 0, 12));

        return hash_equals($expected, strtoupper($matches[3])) ? (int) $matches[2] : null;
    }
}
