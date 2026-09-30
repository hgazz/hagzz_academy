<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * GymGateEntry — سجل دخول/خروج بوابة الجيم أو المركز الصحي
 *
 * للمنشآت التي لا تعتمد على نظام الحصص (الجيم والمراكز الصحية):
 * يُسجَّل دخول وخروج العضو مباشرة عبر QR / باركود بدون ربط بحصة.
 */
class GymGateEntry extends Model
{
    protected $fillable = [
        'academy_student_id',
        'academy_subscription_id',
        'academy_id',
        'entered_at',
        'exited_at',
        'direction',
        'scan_method',
        'station',
        'subscription_valid',
        'is_guest',
        'notes',
    ];

    protected $casts = [
        'entered_at'         => 'datetime',
        'exited_at'          => 'datetime',
        'subscription_valid' => 'boolean',
        'is_guest'           => 'boolean',
    ];

    // ── العلاقات ────────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademyStudent::class, 'academy_student_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AcademyStudentSubscription::class, 'academy_subscription_id');
    }

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    // ── Accessor: هل العضو داخل الصالة؟ ────────────────────────────────
    public function getIsInsideAttribute(): bool
    {
        return $this->direction === 'in' && $this->exited_at === null;
    }

    // ── مدة الجلسة بالدقائق ─────────────────────────────────────────────
    public function getDurationMinutesAttribute(): ?int
    {
        if ($this->entered_at && $this->exited_at) {
            return (int) $this->entered_at->diffInMinutes($this->exited_at);
        }
        return null;
    }
}
