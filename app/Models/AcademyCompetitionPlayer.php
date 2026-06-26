<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademyCompetitionPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_competition_id',
        'academy_student_id',
        'role',
        'notes',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(AcademyCompetition::class, 'academy_competition_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademyStudent::class, 'academy_student_id');
    }
}
