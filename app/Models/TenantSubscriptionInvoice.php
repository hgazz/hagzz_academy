<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSubscriptionInvoice extends Model
{
    protected $fillable = [
        'tenant_subscription_id',
        'academy_id',
        'invoice_number',
        'period_starts_at',
        'period_ends_at',
        'issued_at',
        'due_at',
        'list_amount',
        'discount_amount',
        'subtotal_amount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'currency_code',
        'status',
        'notes',
    ];

    protected $casts = [
        'period_starts_at' => 'date', 'period_ends_at' => 'date', 'issued_at' => 'date', 'due_at' => 'date',
        'list_amount' => 'decimal:2', 'discount_amount' => 'decimal:2', 'subtotal_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2', 'tax_amount' => 'decimal:2', 'total_amount' => 'decimal:2', 'paid_amount' => 'decimal:2',
    ];

    public function subscription() { return $this->belongsTo(TenantSubscription::class, 'tenant_subscription_id'); }
    public function academy() { return $this->belongsTo(Academies::class, 'academy_id'); }
    public function payments() { return $this->hasMany(TenantSubscriptionPayment::class, 'tenant_subscription_invoice_id'); }
    public function getBalanceAttribute(): float { return max(0, round((float) $this->total_amount - (float) $this->paid_amount, 2)); }
}
