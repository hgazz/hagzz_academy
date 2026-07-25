<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerActivityLog extends Model
{
    protected $table = 'partner_activity_logs';

    protected $fillable = [
        'academy_id',
        'user_id',
        'user_name',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function academy()
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function user()
    {
        return $this->belongsTo(PartnerUser::class, 'user_id');
    }

    public static function log(string $action, string $description, ?int $academyId = null, ?int $userId = null): self
    {
        $authUser = auth('academy')->user();
        $academyId = $academyId ?: ($authUser?->academy_id ?: $authUser?->id);
        $userId = $userId ?: $authUser?->id;
        $userName = $authUser?->name ?: ($authUser?->commercial_name ?: 'Partner User');

        return static::create([
            'academy_id' => $academyId ?: 1,
            'user_id' => $userId,
            'user_name' => $userName,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
