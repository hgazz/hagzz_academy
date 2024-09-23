<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settlement extends Model
{
    use HasFactory;

    const STATUS_SETTLED = 'settled';
    const STATUS_PENDING = 'pending';

    protected $fillable = [
        'partner_id',
        'total_amount',
        'net_amount',
        'settlement_date',
        'status',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Academies::class, 'partner_id');
    }

    public function getStatusAttribute($value)
    {
        return match ($value){
            self::STATUS_SETTLED => trans('admin.settled'),
            default => trans('admin.academies.pending'),
        };
    }
}
