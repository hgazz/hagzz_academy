<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerStaffAttendance extends Model
{
    use HasFactory;

    protected $table = 'partner_staff_attendances';

    protected $fillable = [
        'academy_id',
        'branch_id',
        'staff_type',
        'partner_user_id',
        'coach_id',
        'attendance_date',
        'check_in_at',
        'check_out_at',
        'work_minutes',
        'status',
        'verification_method',
        'ip_address',
        'device_info',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'work_minutes' => 'integer',
    ];

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'branch_id');
    }

    public function partnerUser(): BelongsTo
    {
        return $this->belongsTo(PartnerUser::class, 'partner_user_id');
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class, 'coach_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(PartnerUser::class, 'created_by_user_id');
    }

    public function getStaffNameAttribute(): string
    {
        if ($this->staff_type === 'coach' && $this->coach) {
            return $this->coach->name;
        }
        if ($this->partnerUser) {
            return $this->partnerUser->name;
        }
        return '-';
    }

    public function getStaffPhoneAttribute(): ?string
    {
        if ($this->staff_type === 'coach' && $this->coach) {
            return $this->coach->phone;
        }
        return $this->partnerUser?->phone;
    }

    public function getDurationFormattedAttribute(): string
    {
        if (!$this->work_minutes) {
            return '-';
        }
        $h = intdiv($this->work_minutes, 60);
        $m = $this->work_minutes % 60;
        $isAr = app()->getLocale() === 'ar';
        if ($h > 0) {
            return "{$h} " . ($isAr ? 'س' : 'h') . " " . ($m > 0 ? "{$m} " . ($isAr ? 'د' : 'm') : '');
        }
        return "{$m} " . ($isAr ? 'دقيقة' : 'min');
    }
}
