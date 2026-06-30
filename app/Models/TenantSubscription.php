<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSubscription extends Model
{
    protected $guarded = [];
    protected $casts = ['starts_at'=>'date','ends_at'=>'date','trial_ends_at'=>'date','auto_renew'=>'boolean','custom_price'=>'decimal:2','price_amount'=>'decimal:2','tax_rate'=>'decimal:2','tax_included'=>'boolean'];
    public function plan() { return $this->belongsTo(SaasPlan::class, 'saas_plan_id'); }
    public function planPrice() { return $this->belongsTo(SaasPlanPrice::class, 'saas_plan_price_id'); }
}
