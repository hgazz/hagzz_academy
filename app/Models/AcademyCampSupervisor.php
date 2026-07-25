<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademyCampSupervisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_camp_id',
        'coach_id',
        'partner_user_id',
        'role',
        'notes',
    ];

    public function camp(): BelongsTo
    {
        return $this->belongsTo(AcademyCamp::class, 'academy_camp_id');
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class, 'coach_id');
    }

    public function partnerUser(): BelongsTo
    {
        return $this->belongsTo(PartnerUser::class, 'partner_user_id');
    }
}
