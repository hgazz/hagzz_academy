<?php

namespace App\Models;

use App\Support\StorageUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Sport extends Model
{
    use HasFactory, HasTranslations;

    const  PATH = 'images/sports/';
    public $translatable = ['name'];
    protected $fillable = [
        'name',
        'icon',
        'status',
    ];

    public function getIconAttribute($value)
    {
        return StorageUrl::asset($value, self::PATH);
    }

    public function academies()
    {
        return $this->belongsToMany(Academies::class,'academy_sport','sport_id','academy_id')->withPivot('sport_id');
    }

    public function coaches(): BelongsToMany
    {
        return $this->belongsToMany(Coach::class, 'coach_sports');
    }
}
