<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SaasPlanPrice extends Model
{
    protected $guarded = [];
    protected $casts = ['monthly_price'=>'decimal:2','annual_price'=>'decimal:2','tax_rate'=>'decimal:2','tax_included'=>'boolean','active'=>'boolean'];
    public function plan() { return $this->belongsTo(SaasPlan::class, 'saas_plan_id'); }
    public function country() { return $this->belongsTo(Country::class); }
}
