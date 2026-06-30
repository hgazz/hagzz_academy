<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Venue extends Model
{
    use HasTranslations;

    protected $guarded = [];
    public array $translatable = ['name'];
    protected $casts = ['active' => 'boolean'];

    public function academy() { return $this->belongsTo(Academies::class, 'academy_id'); }
    public function spaces() { return $this->hasMany(VenueSpace::class); }
}
