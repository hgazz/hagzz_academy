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
        'discount_amount',
        'discount_reason',
        'discount_approved_by',
        'discount_approved_at',
        'status',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_approved_at' => 'datetime',
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
        $total = (float) $this->amount;
        $paid = $this->paid_amount;
        $discount = (float) ($this->discount_amount ?? 0);
        return max(0, round($total - $paid - $discount, 2));
    }
}
