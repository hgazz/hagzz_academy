<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PartnerUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'partner_users';

    protected $fillable = [
        'academy_id',
        'name',
        'email',
        'phone',
        'password',
        'is_owner',
        'access_all_branches',
        'access_all_sports',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_owner'            => 'boolean',
        'access_all_branches' => 'boolean',
        'access_all_sports'   => 'boolean',
    ];

    public function academy()
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    /**
     * All sports belonging to this partner's academy (global — not user-scoped).
     * Used in dropdowns for creating trainings etc.
     */
    public function sports()
    {
        return $this->academy
            ? $this->academy->sports()
            : collect();
    }

    /**
     * Sports directly assigned to this staff user (many-to-many).
     */
    public function assignedSports()
    {
        return $this->belongsToMany(Sport::class, 'partner_user_sports', 'user_id', 'sport_id');
    }

    public function currentSubscription()
    {
        return $this->academy ? $this->academy->currentSubscription() : null;
    }

    public function roles()
    {
        return $this->belongsToMany(PartnerRole::class, 'partner_user_roles', 'user_id', 'role_id');
    }

    public function assignedBranches()
    {
        return $this->belongsToMany(Academies::class, 'partner_user_branches', 'user_id', 'branch_id');
    }

    public function hasRole(string $roleName): bool
    {
        if ($this->is_owner && $roleName === 'owner') {
            return true;
        }
        return $this->roles->contains('name', $roleName);
    }

    public function hasPermissionTo(string $permissionName): bool
    {
        if ($this->is_owner) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permissionName)) {
                return true;
            }
        }

        return false;
    }

    // ─────────────────────────────────────────────
    // Branch Access
    // ─────────────────────────────────────────────

    public function canAccessBranch(int $branchId): bool
    {
        if ($this->is_owner || $this->access_all_branches) {
            return true;
        }

        if ($this->academy_id == $branchId) {
            return true;
        }

        return $this->assignedBranches()->where('academies.id', $branchId)->exists();
    }

    public function getAccessibleBranches()
    {
        if ($this->is_owner || $this->access_all_branches) {
            return Academies::where(function ($query) {
                $query->where('id', $this->academy_id)
                      ->orWhere('branch_to', $this->academy_id);
            })->get();
        }

        return $this->assignedBranches;
    }

    // ─────────────────────────────────────────────
    // Sport Access
    // ─────────────────────────────────────────────

    public function canAccessSport(int $sportId): bool
    {
        if ($this->is_owner || $this->access_all_sports) {
            return true;
        }

        return $this->assignedSports()->where('sports.id', $sportId)->exists();
    }

    /**
     * Returns the collection of sports accessible by this user.
     * For owners / access_all_sports=true: returns all academy sports.
     * For staff: returns only assigned sports.
     */
    public function getAccessibleSports()
    {
        if ($this->is_owner || $this->access_all_sports) {
            return $this->academy
                ? $this->academy->sports()->get()
                : collect();
        }

        return $this->assignedSports()->get();
    }

    public function getCommercialNameAttribute()
    {
        return $this->academy?->commercial_name;
    }

    public function getOwnerNameAttribute()
    {
        return $this->name;
    }

    public function getBusinessTypeAttribute()
    {
        return $this->academy?->business_type;
    }

    public function getCurrencyCodeAttribute()
    {
        return $this->academy?->currency_code ?: 'SAR';
    }

    public function getCurrencySymbolAttribute()
    {
        return $this->academy?->currency_symbol ?: (app()->getLocale() === 'ar' ? 'ر.س' : 'SAR');
    }

    public function getLogoAttribute()
    {
        return $this->academy?->logo;
    }

    public function getImageAttribute()
    {
        return $this->academy?->image;
    }

    public function hasVenueModule($subscription = null): bool
    {
        return $this->academy?->hasVenueModule($subscription) ?? false;
    }
}
