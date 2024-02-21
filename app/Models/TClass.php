<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TClass extends Model
{
    use HasFactory , HasTranslations;

    protected $fillable = [
        'title',
        'subtitle',
        'date'
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

    public static function getTranslatableFields()
    {
        return array_keys(self::$translatableColumns);
    }

}
