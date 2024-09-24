<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Coach extends Model
{
    use HasTranslations;
    public $translatable = ['name','description','license','license_type'];

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
        ],
        'license'=>[
            'type'=>'text',
            'validations'=>'required|string|max:255',
            'is_textarea'=>false
        ],
        'license_type'=>[
            'type'=>'text',
            'validations'=>'required|string|max:255',
            'is_textarea'=>false
        ],
    ];

    public static function getTranslatableFields()
    {
        return array_keys(self::$translatableColumns);
    }
    const PATH = 'images/coaches';
    protected $fillable = [
        'name',
        'description',
        'image',
        'phone',
        'active',
        'academy_id',
        'license',
        'license_type',
        'gender',
        'birth_date'
    ];

    public function academy()
    {
        return $this->belongsTo(Academies::class, 'academy_id', 'id');
    }

    public function getActiveAttribute($value)
    {
        return $value ? trans('admin.coaches.active') : trans('admin.coaches.inactive');
    }


    public function getImageAttribute($value): string
    {
        return config('services.s3.url') . DIRECTORY_SEPARATOR . self::PATH . DIRECTORY_SEPARATOR . $value;
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class, 'coach_id');
    }

    public function sports(): BelongsToMany
    {
        return $this->belongsToMany(Sport::class, 'coach_sports');
    }

    public function getGenderAttribute($value)
    {
        return $value ? trans('admin.coaches.male') : trans('admin.coaches.female');
    }
}
