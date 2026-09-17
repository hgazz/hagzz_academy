<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $fillable = [
        'id',
        'notifiable_id',
        'notifiable_type',
        'type',
        'title',
        'description',
        'image',
        'details',
        'data',
        'read_at',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
    protected $casts = [
        'details' => 'array',
    ];

}
