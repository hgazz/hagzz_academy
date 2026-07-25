<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyCamp extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_id',
        'sport_id',
        'title_ar',
        'title_en',
        'type',
        'country_id',
        'city_name',
        'venue_name',
        'hotel_name',
        'starts_on',
        'ends_on',
        'registration_deadline',
        'capacity',
        'price',
        'deposit_amount',
        'currency_code',
        'included_services',
        'visa_required',
        'status',
        'description',
        'room_features',
        'venue_features',
        'program_itinerary',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'registration_deadline' => 'date',
        'included_services' => 'array',
        'visa_required' => 'boolean',
        'price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function supervisors(): HasMany
    {
        return $this->hasMany(AcademyCampSupervisor::class, 'academy_camp_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(AcademyCampParticipant::class, 'academy_camp_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(AcademyCampExpense::class, 'academy_camp_id');
    }

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : ($this->title_en ?: $this->title_ar);
    }

    public function getTotalRevenueAttribute(): float
    {
        return (float) $this->participants()->sum('paid_amount');
    }

    public function getTotalExpensesAmountAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function getNetProfitAttribute(): float
    {
        return $this->total_revenue - $this->total_expenses_amount;
    }
}
