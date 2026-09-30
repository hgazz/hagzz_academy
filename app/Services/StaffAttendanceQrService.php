<?php

namespace App\Services;

class StaffAttendanceQrService
{
    const SLOT_DURATION_SECONDS = 20;

    /**
     * Generate dynamic rolling QR token for a branch screen.
     */
    public static function generateToken(int $academyId, int $branchId, ?int $timestamp = null): array
    {
        $time = $timestamp ?? time();
        $slot = (int) floor($time / self::SLOT_DURATION_SECONDS);
        $secret = config('app.key', 'hagzz_secret_key_attendance');

        $rawHash = hash_hmac('sha256', "staff_attendance:{$academyId}:{$branchId}:{$slot}", $secret);
        $token = substr($rawHash, 0, 32);
        $secondsRemaining = self::SLOT_DURATION_SECONDS - ($time % self::SLOT_DURATION_SECONDS);

        // Verification payload URL
        $scanUrl = route('academy.staff-attendance.process-scan', [
            'aid' => $academyId,
            'bid' => $branchId,
            'token' => $token,
            'slot' => $slot,
        ]);

        return [
            'token' => $token,
            'slot' => $slot,
            'branch_id' => $branchId,
            'academy_id' => $academyId,
            'expires_in' => $secondsRemaining,
            'scan_url' => $scanUrl,
        ];
    }

    /**
     * Verify whether a submitted token from a QR scan is valid and unexpired.
     */
    public static function verifyToken(int $academyId, int $branchId, string $token, int $slot): bool
    {
        $currentSlot = (int) floor(time() / self::SLOT_DURATION_SECONDS);

        // Allow current slot or previous slot (tolerance for scan/network delay: max 40 seconds)
        if ($slot < ($currentSlot - 1) || $slot > ($currentSlot + 1)) {
            return false;
        }

        $secret = config('app.key', 'hagzz_secret_key_attendance');
        $expectedHash = hash_hmac('sha256', "staff_attendance:{$academyId}:{$branchId}:{$slot}", $secret);
        $expectedToken = substr($expectedHash, 0, 32);

        return hash_equals($expectedToken, $token);
    }
}
