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
        'image',
        'name',
        'phone',
        'country_code', 'country_id', 'city_id', 'area_id',
        'email',
        'gender',
        'birth_date',
        'child_type', 'school_name', 'club_member', 'previous_club_name', 'club_card_number', 'club_card_file', 'coach_preference', 'frequent_attendance',
        'guardian_name',
        'guardian_phone',
        'relation_with_child', 'referral_source', 'delivery_service',
        'status',
        'medical_condition', 'injury_type', 'has_allergy', 'allergy_type', 'start_date',
        'medical_notes', 'medical_certificate',
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
        $businessType = $this->academy?->business_type;
        $isGym = in_array($businessType, ['gym', 'fitness_center', 'crossfit', 'health_club'], true);

        if ($isGym) {
            return asset('assetsAdmin/img/' . ($this->gender === 'female' ? 'gym-female-avatar.svg' : 'gym-member-avatar.svg'));
        }

        $fileName = $this->gender === 'female'
            ? 'default-user-female.webp'
            : 'default-user-male.webp';

        return asset('assetsAdmin/img/' . $fileName);
    }

    public function avatarUrl(): string
    {
        if ($this->image) {
            return str_starts_with($this->image, 'http') ? $this->image : ('/' . ltrim($this->image, '/'));
        }

        if ($this->user?->image) {
            return str_starts_with($this->user->image, 'http') ? $this->user->image : ('/' . ltrim($this->user->image, '/'));
        }

        return $this->defaultImageUrl();
    }

    public function hasSpecialCare(): bool
    {
        return ($this->medical_condition === 'yes' && !empty($this->injury_type))
            || ($this->has_allergy === 'yes' && !empty($this->allergy_type))
            || !empty($this->medical_notes);
    }

    public function specialCareSummary(): array
    {
        $summary = [];
        if ($this->medical_condition === 'yes' && !empty($this->injury_type)) {
            $summary['injury'] = $this->injury_type;
        }
        if ($this->has_allergy === 'yes' && !empty($this->allergy_type)) {
            $summary['allergy'] = $this->allergy_type;
        }
        if (!empty($this->medical_notes)) {
            $summary['notes'] = $this->medical_notes;
        }
        return $summary;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        // 1. Direct primary key lookup
        $student = $this->where($field ?? $this->getRouteKeyName(), $value)->first();
        if ($student) {
            return $student;
        }

        // 2. Fallback: if value was a User ID belonging to the authenticated academy
        $academyUser = auth('academy')->user();
        $academyId = (int) ($academyUser instanceof \App\Models\PartnerUser ? $academyUser->academy_id : ($academyUser?->id ?? auth('academy')->id()));
        if ($academyId && is_numeric($value)) {
            $studentByUser = $this->where('user_id', $value)
                ->where('academy_id', $academyId)
                ->first();
            if ($studentByUser) {
                return $studentByUser;
            }

            // 3. Fallback: if a User with this ID exists, create an AcademyStudent on the fly
            $user = \App\Models\User::find($value);
            if ($user) {
                return $this->firstOrCreate([
                    'user_id' => $user->id,
                    'academy_id' => $academyId,
                ], [
                    'name' => $user->name ?: 'مشترك',
                    'phone' => $user->phone ?: '0000000000',
                    'email' => $user->email,
                    'gender' => $user->gender ?: 'male',
                    'birth_date' => $user->birth_date,
                    'status' => 'active',
                ]);
            }
        }

        return null;
    }
}
