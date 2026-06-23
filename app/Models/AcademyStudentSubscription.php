<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyStudentSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_student_id',
        'academy_group_id',
        'starts_on',
        'ends_on',
        'amount',
        'status',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'amount' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademyStudent::class, 'academy_student_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(AcademyGroup::class, 'academy_group_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(AcademyStudentPayment::class);
    }

    public function getPaidAmountAttribute(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->sum('amount');
        }

        return (float) $this->payments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->amount - $this->paid_amount);
    }
}
