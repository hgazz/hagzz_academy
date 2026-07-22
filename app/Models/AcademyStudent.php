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
        'country_code', 'country_id', 'city_id', 'area_id',
        'email',
        'gender',
        'birth_date',
        'child_type', 'school_name', 'club_member', 'coach_preference', 'frequent_attendance',
        'guardian_name',
        'guardian_phone',
        'relation_with_child', 'referral_source', 'delivery_service',
        'status',
        'medical_condition', 'start_date',
        'medical_notes',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'start_date' => 'date',
    ];

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo { return $this->belongsTo(Country::class); }
    public function city(): BelongsTo { return $this->belongsTo(City::class); }
    public function area(): BelongsTo { return $this->belongsTo(Area::class); }

    public function bookings(): HasMany
    {
        return $this->hasMany(Join::class, 'academy_student_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(AcademyGroup::class, 'academy_group_students', 'academy_student_id', 'academy_group_id')
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

    public function defaultImageUrl(): string
    {
        $fileName = $this->gender === 'female'
            ? 'default-user-female.webp'
            : 'default-user-male.webp';

        return asset('assetsAdmin/img/' . $fileName);
    }

    public function avatarUrl(): string
    {
        return $this->user?->image ?? $this->defaultImageUrl();
    }
}
