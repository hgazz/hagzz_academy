<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyCompetition extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_id',
        'sport_id',
        'home_team_name',
        'opponent_name',
        'competition_date',
        'starts_at',
        'venue',
        'status',
        'home_score',
        'opponent_score',
        'result_notes',
        'notes',
    ];

    protected $casts = [
        'competition_date' => 'date',
    ];

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function playerRows(): HasMany
    {
        return $this->hasMany(AcademyCompetitionPlayer::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(AcademyStudent::class, 'academy_competition_players')
            ->withPivot(['role', 'notes'])
            ->withTimestamps();
    }
}
