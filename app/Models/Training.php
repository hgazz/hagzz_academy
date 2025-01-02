<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Training extends Model
{
    use HasTranslations,SoftDeletes;

    protected $fillable = [
        'name',
        'price',
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
        'discount_price',
        'start_time',
        'end_time',
        'classes_days',
        'color',
        'classes_number'
    ];

    protected $casts = [
        'classes_days' => 'array',
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

    public function getLevelAttribute($value)
    {
        return match ($value) {
            'Any_Level' => trans('admin.training.Any_Level'),
            'Beginner' => trans('admin.training.beginner'),
            'Intermediate' => trans('admin.training.intermediate'),
            'Advanced' => trans('admin.training.advanced'),
            default => 'Unknown',
        };
    }

    public function getGenderAttribute($value)
    {
        return match ($value) {
            'All' => trans('admin.training.all'),
            'Men' => trans('admin.training.men'),
            'Women' => trans('admin.training.women'),
            default => 'Unknown',
        };
    }

    public function getAgeGroupAttribute($value)
    {
        return match ($value) {
            'All' => trans('admin.training.all'),
            'Kids' => trans('admin.training.kids'),
            'Juniors' => trans('admin.training.juniors'),
            'Adults' => trans('admin.training.adults'),
            default => 'Unknown',
        };
    }

}
