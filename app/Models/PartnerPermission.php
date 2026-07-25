<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerPermission extends Model
{
    protected $table = 'partner_permissions';

    protected $fillable = [
        'name',
        'display_name_ar',
        'display_name_en',
        'group',
    ];

    public function roles()
    {
        return $this->belongsToMany(PartnerRole::class, 'partner_role_permission', 'permission_id', 'role_id');
    }
}
