<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const PATH = 'images/users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'gender',
        'birth_date',
        'image',
        'country_code',
        'country_id',
        'city_id',
        'area_id',
        'language',
        'user_type',
        'country_code',
        'email',
        'child_type',
        'school_name',
        'parent_name',
        'parent_phone',
        'coach_preference',
        'frequent_attendance',
        'relation_with_child',
        'referral_source',
        'medical_condition',
        'medical_condition_details',
        'additional_information',
        'delivery_service',
        'club_member',
        'start_date'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function following()
    {
        return $this->HasMany(Follow::class, 'user_id');
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function getImageAttribute($value): string
    {
        return is_null($value) ? asset('assetsAdmin/people-fill.svg') : config('services.s3.url') . DIRECTORY_SEPARATOR . self::PATH . DIRECTORY_SEPARATOR . $value;
    }

    public function joins(): HasMany
    {
        return $this->hasMany(Join::class, 'user_id');
    }
    public function notifications(): MorphToMany
    {
        return $this->morphToMany(Notification::class, 'notificationable');
    }
}
