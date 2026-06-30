<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueCustomer extends Model
{
    protected $guarded = [];
    public function bookings() { return $this->hasMany(VenueBooking::class); }
}
