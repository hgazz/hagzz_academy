<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueBooking extends Model
{
    protected $guarded = [];
    protected $casts = [
        'starts_at' => 'datetime', 'ends_at' => 'datetime',
        'total_amount' => 'decimal:2', 'paid_amount' => 'decimal:2',
    ];

    public function space() { return $this->belongsTo(VenueSpace::class, 'venue_space_id'); }
    public function customer() { return $this->belongsTo(VenueCustomer::class, 'venue_customer_id'); }
    public function getRemainingAmountAttribute(): float { return max(0, (float) $this->total_amount - (float) $this->paid_amount); }
    public function getPaymentStatusAttribute(): string
    {
        if ((float) $this->paid_amount <= 0) return 'unpaid';
        return $this->remaining_amount > 0 ? 'partial' : 'paid';
    }
}
