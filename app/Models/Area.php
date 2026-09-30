<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Area extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['name', 'city_id'];
    protected static function booted()
    {
        static::creating(function ($area) {
            if (empty($area->city_id)) {
                $area->city_id = 1; // Default fallback to Cairo / primary city
            }
        });
    }

    public static $translatableColumns = [
        'name'=>[
            'type'=>'text',
            'validations'=>'required|string|max:255',
            'is_textarea'=>false
        ]
    ];

    public static function getTranslatableFields()
    {
        return array_keys(self::$translatableColumns);
    }
    /**
     * Get the city associated with the user.
     *
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
