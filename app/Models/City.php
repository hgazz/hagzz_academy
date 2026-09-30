<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['name', 'country_id'];

    protected static function booted()
    {
        static::creating(function ($city) {
            if (empty($city->country_id)) {
                $city->country_id = 4; // Default to Egypt
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
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the areas associated with the city.
     *
     * @return HasMany
     */
    public function areas(): HasMany
    {
        return $this->hasMany(Area::class, 'city_id', 'id');
    }
}
