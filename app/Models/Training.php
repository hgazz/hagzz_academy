<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Training extends Model
{
    use HasFactory,HasTranslations;

    protected $fillable = [
        'name',
        'price',
        'start_date',
        'end_date',
        'description',
        'max_players',
        'level',
        'gender',
        'age_group',
        'address_id',
        'coach_id',
        'academy_id',
        'active',
        'sport_id',
        'discount_price'
    ];

    public $translatable = ['name','description'];

    public static $translatableColumns = [
        'name'=>[
            'type'=>'text',
            'validations'=>'required|string|max:255',
            'is_textarea'=>false
        ],
        'description'=>[
            'type'=>'text',
            'validations'=>'required|string|min:5',
            'is_textarea'=>true
        ]
    ];

    public static function getTranslatableFields()
    {
        return array_keys(self::$translatableColumns);
    }
    public function joins()
    {
        return $this->hasMany(Join::class, 'training_id');
    }
    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coach_id');
    }

    public function classes()
    {
        return $this->hasMany(TClass::class ,'training_id');
    }

    public function academy()
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
    public function sport()
    {
        return $this->belongsTo(Sport::class,'sport_id');
    }
}
