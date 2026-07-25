<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademyCampParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_camp_id',
        'academy_student_id',
        'user_id',
        'name',
        'phone',
        'emergency_phone',
        'passport_number',
        'passport_expiry',
        'visa_status',
        'tshirt_size',
        'medical_notes',
        'room_number',
        'total_fee',
        'paid_amount',
        'payment_status',
        'status',
        'notes',
    ];

    protected $casts = [
        'passport_expiry' => 'date',
        'total_fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function camp(): BelongsTo
    {
        return $this->belongsTo(AcademyCamp::class, 'academy_camp_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademyStudent::class, 'academy_student_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRemainingFeeAttribute(): float
    {
        return max(0, (float) $this->total_fee - (float) $this->paid_amount);
    }
}
