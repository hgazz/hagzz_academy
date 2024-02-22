<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'class_id'
    ];

    public $timestamps = false;
    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id');
    }

    public function tClass()
    {
        return $this->belongsTo(TClass::class, 'class_id');
    }
}
