<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function joins(): HasMany
    {
        return $this->hasMany(Join::class);
    }

    public function getStatusAttribute($value)
    {
        return match ($value) {
            'paid' => trans('admin.bookings.paid'),
            default => trans('admin.academies.pending'),
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        $method = $this->payment_method ?: ($this->user_type === 'online' ? 'app_online' : 'cash');

        if ($method === 'other' && $this->payment_method_other) {
            return $this->payment_method_other;
        }

        return trans('admin.payment_methods.' . $method);
    }

    public function getCollectedAmountAttribute(): float
    {
        // Historical invoices predate paid_amount and were always fully paid.
        return $this->paid_amount === null
            ? (float) $this->amount
            : (float) $this->paid_amount;
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->amount - $this->collected_amount);
    }

    public function getPaymentStateAttribute(): string
    {
        if ($this->collected_amount <= 0) {
            return 'unpaid';
        }

        return $this->remaining_amount > 0 ? 'partial' : 'paid';
    }

    public function getPaymentStateLabelAttribute(): string
    {
        return trans('admin.bookings.payment_states.' . $this->payment_state);
    }

}
