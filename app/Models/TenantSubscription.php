<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSubscription extends Model
{
    protected $guarded = [];
    protected $casts = ['starts_at' => 'date', 'ends_at' => 'date', 'trial_ends_at' => 'date'];
    public function plan() { return $this->belongsTo(SaasPlan::class, 'saas_plan_id'); }
}
