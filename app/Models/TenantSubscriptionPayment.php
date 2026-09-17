<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSubscriptionPayment extends Model
{
    protected $fillable = [
        'tenant_subscription_invoice_id',
        'amount',
        'payment_method',
        'payment_reference',
        'paid_at',
        'notes',
        'status',
    ];
    protected $casts = ['paid_at' => 'datetime', 'amount' => 'decimal:2'];
    public function invoice() { return $this->belongsTo(TenantSubscriptionInvoice::class, 'tenant_subscription_invoice_id'); }
}
