<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerRole extends Model
{
    protected $table = 'partner_roles';

    protected $fillable = [
        'academy_id',
        'name',
        'display_name_ar',
        'display_name_en',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function academy()
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(PartnerPermission::class, 'partner_role_permission', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->belongsToMany(PartnerUser::class, 'partner_user_roles', 'role_id', 'user_id');
    }
}
