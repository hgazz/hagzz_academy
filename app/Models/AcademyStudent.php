<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_id',
        'user_id',
        'name',
        'phone',
        'email',
        'gender',
        'birth_date',
        'guardian_name',
        'guardian_phone',
        'status',
        'medical_notes',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(AcademyGroup::class, 'academy_group_students')
            ->withPivot(['joined_at', 'status'])
            ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(AcademyStudentSubscription::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AcademyAttendanceRecord::class);
    }
}
