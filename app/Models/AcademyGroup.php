<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_id',
        'training_id',
        'coach_id',
        'sport_id',
        'name',
        'days',
        'start_time',
        'end_time',
        'capacity',
        'status',
        'notes',
    ];

    protected $casts = [
        'days' => 'array',
    ];

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(AcademyStudent::class, 'academy_group_students', 'academy_group_id', 'academy_student_id')
            ->withPivot(['joined_at', 'status'])
            ->withTimestamps();
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AcademyAttendanceSession::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(AcademyStudentSubscription::class);
    }
}
