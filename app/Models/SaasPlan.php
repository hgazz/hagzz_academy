<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasPlan extends Model
{
    protected $guarded = [];
    protected $casts = ['features' => 'array', 'active' => 'boolean'];
    public function subscriptions() { return $this->hasMany(TenantSubscription::class); }
    public function prices() { return $this->hasMany(SaasPlanPrice::class); }
}
