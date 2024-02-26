<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasFactory,HasTranslations;

    protected $fillable = ['name'];
    protected $translatable = ['name'];
    public static $translatableColumns = [
        'name'=>[
            'type'=>'text',
            'validations'=>'required|string|max:255',
            'is_textarea'=>false
        ],
    ];
    public static function getTranslatableFields()
    {
        return array_keys(self::$translatableColumns);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class , 'county_id','id');
    }

    public function address(): HasMany
    {
        return $this->hasMany(Address::class , 'address_id','id');
    }
}
