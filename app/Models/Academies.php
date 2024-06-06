<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Translatable\HasTranslations;

class Academies extends Authenticatable
{
    use HasFactory, HasTranslations, Notifiable;

    public array $translatable = ['commercial_name'];
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
        'contract_number',
        'account_manager',
        'is_registered',
        'branch_to',
        'contract_date',
        'start_date',
        'end_date',
        'first_name',
        'last_name',
        'app_name',
        'image',
        'linkedin',
        'website',
        'bank_account_type',
        'bank_name',
        'beneficiary_name',
        'commission_percentage',
        'bank_account_number',
        'settlement_days_count',
        'non_refund_days_count',
        'contract_link'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function getLogoAttribute($value): string
    {
        return config('services.s3.url') . DIRECTORY_SEPARATOR . self::PATH . DIRECTORY_SEPARATOR . $value;
    }

    public function sports(): BelongsToMany
    {
        return $this->belongsToMany(Sport::class,'academy_sport','academy_id','sport_id');
    }

    public function follows(): MorphMany
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class, 'partner_id');
    }

}
