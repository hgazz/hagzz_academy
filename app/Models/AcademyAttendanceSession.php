<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyAttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_group_id',
        't_class_id',
        'session_date',
        'starts_at',
        'ends_at',
        'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(AcademyGroup::class, 'academy_group_id');
    }

    public function tClass(): BelongsTo
    {
        return $this->belongsTo(TClass::class, 't_class_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(AcademyAttendanceRecord::class);
    }
}
