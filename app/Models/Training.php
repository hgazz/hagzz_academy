<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Training extends Model
{
    use HasFactory,HasTranslations;
    const PATH = 'images/trainings';
    protected $fillable = [
        'name',
        'image',
        'price',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'description',
        'coach_id',
        'academy_id',
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
    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coach_id');
    }

    public function classes()
    {
        return $this->belongsToMany(TClass::class ,'training_classes','training_id','class_id');
    }

    public function getImageAttribute($value)
    {
        return config('services.s3.url') . DIRECTORY_SEPARATOR . self::PATH . DIRECTORY_SEPARATOR . $value;
    }

    public function academy()
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }
}
