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
            $payload = $matches[1].'-'.$matches[2];
            $signature = strtoupper(substr(hash_hmac('sha256', $payload, (string) config('app.key')), 0, 12));

            return (int) $matches[1] === $academyId && hash_equals($signature, strtoupper($matches[3]))
                ? (int) $matches[2] : null;
        }

        // Never reinterpret a malformed signed card as a legacy ID or phone number.
        if (preg_match('/^HZ-/i', $code) && !preg_match('/^HZ-\d+-\d+$/iD', $code)) {
            return null;
        }

        // 2. Formats like HZ-1-2, MEMB-2, STUDENT-2, STU-2, ID-2
        if (preg_match('/^(?:HZ|MEMB|STUDENT|STU|ID)-(?:(\d+)-)?(\d+)$/iD', $code, $matches)) {
            if ($matches[1] !== '' && (int) $matches[1] !== $academyId) {
                return null;
            }
            $stId = (int) end($matches);
            if (AcademyStudent::where('academy_id', $academyId)->where('id', $stId)->exists()) {
                return $stId;
            }
        }

        // 3. Plain numeric ID
        if (ctype_digit($code)) {
            $stId = (int) $code;
            if (AcademyStudent::where('academy_id', $academyId)->where('id', $stId)->exists()) {
                return $stId;
            }
        }

        // 4. Fallback direct lookup in AcademyStudent table (phone, club_card_number, email)
        $cleanPhone = preg_replace('/[^\d]/', '', $code);
        $student = AcademyStudent::where('academy_id', $academyId)
            ->where(function ($q) use ($code, $cleanPhone) {
                $q->where('phone', $code)
                  ->orWhere('club_card_number', $code)
                  ->orWhere('email', $code);
                if (preg_match('/^[+\d\s().-]+$/D', $code) && strlen($cleanPhone) >= 7) {
                    $q->orWhere('phone', 'like', "%{$cleanPhone}%");
                }
            })->first();

        return $student?->id;
    }
}
