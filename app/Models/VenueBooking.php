<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueBooking extends Model
{
    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_approved_at' => 'datetime',
    ];

    public function space()
    {
        return $this->belongsTo(VenueSpace::class, 'venue_space_id');
    }

    public function customer()
    {
        return $this->belongsTo(VenueCustomer::class, 'venue_customer_id');
    }

    public function getRemainingAmountAttribute(): float
    {
        $total = (float) $this->total_amount;
        $paid = (float) $this->paid_amount;
        $discount = (float) ($this->discount_amount ?? 0);
        return max(0, round($total - $paid - $discount, 2));
    }

    public function getPaymentStatusAttribute(): string
    {
        $paid = (float) $this->paid_amount;
        $discount = (float) ($this->discount_amount ?? 0);
        $total = (float) $this->total_amount;

        if (round($paid + $discount, 2) >= $total && $total > 0) {
            return 'paid';
        }

        if ($paid > 0 || $discount > 0) {
            return 'partial';
        }

        return 'unpaid';
    }
}
