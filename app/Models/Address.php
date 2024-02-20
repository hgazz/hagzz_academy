<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Address extends Model
{
    use HasFactory,HasTranslations;

    protected $fillable = [
        'academy_id',
        'city_id',
        'area_id',
        'longitude',
        'latitude',
        'address',
        'active',
    ];

    public $translatable = ['address'];
    public static $translatableColumns = [
        'address'=>[
            'type'=>'text',
            'validations'=>'required|string|max:255',
            'is_textarea'=>false
        ]
    ];

    public static function getTranslatableFields()
    {
        return array_keys(self::$translatableColumns);
    }
    public function academy()
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
