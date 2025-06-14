<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class TClass extends Model
{
    use HasFactory , HasTranslations , HasTranslations;

    protected $fillable = [
        'title',
        'subtitle',
        'date',
        'start_time',
        'end_time',
        'training_id',
        'out_comes',
        'bring_with_me',
        'sport_id'
    ];
    public $translatable = ['title'];

    public static $translatableColumns = [
        'title'=>[
            'type'=>'text',
            'validations'=>'required|string|max:255',
            'is_textarea'=>false
        ],
    ];
    public static function getTranslatableFields()
    {
        return array_keys(self::$translatableColumns);
    }


    protected $casts = [
        'out_comes' => 'array',
        'bring_with_me' => 'array',
    ];
    public function training()
    {
        return $this->belongsTo(Training::class,'training_id');
    }


    public function academy()
    {
        return $this->belongsTo(Academies::class, 'academy_id', 'id');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id', 'id');
    }

//    public function getOutComesAttribute($value)
//    {
//        return json_decode($value, true);
//
//    }
//
//    public function getBringWithMeAttribute($value)
//    {
//        return json_decode($value, true);
//
//    }

    public function getDurationInHoursAttribute()
    {
        $startTime = \Carbon\Carbon::parse($this->start_time);
        $endTime = \Carbon\Carbon::parse($this->end_time);
        return $startTime->diffInMinutes($endTime) / 60; // convert minutes to hours
    }

}
