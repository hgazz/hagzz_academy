<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class VenueSpace extends Model
{
    use HasTranslations;

    protected $guarded = [];
    public array $translatable = ['name', 'description'];
    protected $casts = ['active' => 'boolean', 'hourly_price' => 'decimal:2'];

    public function venue() { return $this->belongsTo(Venue::class); }
    public function sport() { return $this->belongsTo(Sport::class); }
    public function bookings() { return $this->hasMany(VenueBooking::class); }
}
