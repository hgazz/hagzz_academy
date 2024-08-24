<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;
    protected  $guarded = [];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
    protected $casts = [
        'details' => 'array',
    ];

}
