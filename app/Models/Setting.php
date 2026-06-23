<?php

namespace App\Models;

use App\Support\StorageUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value','type'];
    const PATH = 'images/setting/';

    public function getLogoAttribute($value)
    {
        return StorageUrl::asset($value, self::PATH);
    }
}
