<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueBooking extends Model
{
    protected $fillable = [
        'academy_id',
        'venue_space_id',
        'venue_customer_id',
        'reference',
        'booking_type',
        'title',
        'starts_at',
        'ends_at',
        'status',
        'source',
        'total_amount',
        'paid_amount',
        'discount_amount',
        'discount_reason',
        'discount_approved_at',
        'discount_approved_by',
        'payment_method',
        'payment_method_other',
        'notes',
    ];

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
