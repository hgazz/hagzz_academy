<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SaasPlanPrice extends Model
{
    protected $fillable = [
        'saas_plan_id',
        'country_id',
        'currency_code',
        'monthly_price',
        'annual_price',
        'tax_rate',
        'tax_included',
        'active',
    ];
    protected $casts = ['monthly_price'=>'decimal:2','annual_price'=>'decimal:2','tax_rate'=>'decimal:2','tax_included'=>'boolean','active'=>'boolean'];
    public function plan() { return $this->belongsTo(SaasPlan::class, 'saas_plan_id'); }
    public function country() { return $this->belongsTo(Country::class); }
}
