<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppChannel extends Model
{
    protected $fillable = ['academy_id', 'business_account_id', 'phone_number_id', 'display_phone_number', 'access_token', 'app_secret', 'verify_token', 'default_country_code', 'status', 'connected_at', 'last_webhook_at'];

    protected $casts = [
        'access_token' => 'encrypted',
        'app_secret' => 'encrypted',
        'verify_token' => 'encrypted',
        'connected_at' => 'datetime',
        'last_webhook_at' => 'datetime',
    ];

    public function academy() { return $this->belongsTo(Academies::class, 'academy_id'); }
    public function isReady(): bool { return $this->status === 'active' && filled($this->phone_number_id) && filled($this->access_token) && filled($this->app_secret); }
}
