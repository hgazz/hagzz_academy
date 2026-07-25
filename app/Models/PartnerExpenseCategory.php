<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_id',
        'name_ar',
        'name_en',
        'icon',
        'is_system',
    ];

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(PartnerExpense::class, 'category_id');
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
}
