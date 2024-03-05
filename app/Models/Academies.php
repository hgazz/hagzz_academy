<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Translatable\HasTranslations;

class Academies extends Authenticatable
{
    use HasFactory, HasTranslations;

    public $translatable = ['commercial_name'];
    const PATH ='images/academies';
    protected $fillable = [
        'email',
        'phone',
        'password',
        'status',
        'role',
        'commercial_name',
        'trade_license_number',
        'trade_license_expire_date',
        'tax_number',
        'percentage',
        'national_id_number',
        'address',
        'owner_name',
        'name',
        'facebook',
        'instagram',
        'image',
        'logo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function getLogoAttribute($value)
    {
        return config('services.s3.url') . DIRECTORY_SEPARATOR . self::PATH . DIRECTORY_SEPARATOR . $value;
    }

    public function sports()
    {
        return $this->belongsToMany(Sport::class,'academy_sport','academy_id','sport_id');
    }

}
