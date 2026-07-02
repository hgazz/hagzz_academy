<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SaasPlan extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];
    protected $guarded = [];
    protected $casts = ['features' => 'array', 'active' => 'boolean', 'monthly_price' => 'decimal:2', 'annual_price' => 'decimal:2'];
    public function subscriptions() { return $this->hasMany(TenantSubscription::class); }
    public function prices() { return $this->hasMany(SaasPlanPrice::class); }
}
