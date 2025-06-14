<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function joins(): HasMany
    {
        return $this->hasMany(Join::class);
    }

    public function getStatusAttribute($value)
    {
        return match ($value) {
            'paid' => trans('admin.bookings.paid'),
            default => trans('admin.academies.pending'),
        };
    }

}
