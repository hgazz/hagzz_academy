<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSubscription extends Model
{
    protected $fillable = [
        'academy_id',
        'saas_plan_id',
        'saas_plan_price_id',
        'billing_cycle',
        'status',
        'custom_price',
        'price_amount',
        'currency_code',
        'tax_rate',
        'tax_included',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'auto_renew',
    ];
    protected $casts = ['starts_at'=>'date','ends_at'=>'date','trial_ends_at'=>'date','auto_renew'=>'boolean','custom_price'=>'decimal:2','price_amount'=>'decimal:2','tax_rate'=>'decimal:2','tax_included'=>'boolean'];
    public function plan() { return $this->belongsTo(SaasPlan::class, 'saas_plan_id'); }
    public function planPrice() { return $this->belongsTo(SaasPlanPrice::class, 'saas_plan_price_id'); }
}
