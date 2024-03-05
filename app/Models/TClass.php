<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class TClass extends Model
{
    use HasFactory , HasTranslations;

    protected $fillable = [
        'title',
        'subtitle',
        'date',
        'academy_id',
        'sport_id',
        'start_time',
        'end_time',
        'training_id',
    ];
    public $translatable = ['title','subtitle'];

    public static $translatableColumns = [
        'title'=>[
            'type'=>'text',
            'validations'=>'required|string|max:255',
            'is_textarea'=>false
        ],
        'subtitle'=>[
            'type'=>'text',
            'validations'=>'required|string|max:255',
            'is_textarea'=>false
        ]
    ];

    public function training()
    {
        return $this->belongsTo(Training::class,'training_id');
    }
    public static function getTranslatableFields()
    {
        return array_keys(self::$translatableColumns);
    }

    public function academy()
    {
        return $this->belongsTo(Academies::class, 'academy_id', 'id');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id', 'id');
    }

}
