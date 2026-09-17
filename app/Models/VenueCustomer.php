<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueCustomer extends Model
{
    protected $fillable = [
        'academy_id',
        'user_id',
        'name',
        'phone',
        'email',
    ];
    public function bookings() { return $this->hasMany(VenueBooking::class); }
}
