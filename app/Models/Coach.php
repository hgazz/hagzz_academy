<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    use HasFactory;

    const PATH = 'images/coaches';
    protected $fillable = [
        'name',
        'description',
        'image',
        'active',
        'academy_id',
    ];

    public function academy()
    {
        return $this->belongsTo(Academies::class, 'academy_id', 'id');
    }

    public function getActiveAttribute($value)
    {
        return $value ? trans('admin.coaches.active') : trans('admin.coaches.inactive');
    }


    public function getImageAttribute($value)
    {
        return config('services.s3.url') . DIRECTORY_SEPARATOR . self::PATH . DIRECTORY_SEPARATOR . $value;
    }
}
