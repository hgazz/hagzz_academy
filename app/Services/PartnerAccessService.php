<?php

namespace App\Services;

use App\Models\PartnerUser;
use Illuminate\Database\Eloquent\Builder;

/**
 * PartnerAccessService
 *
 * Central service that applies Branch + Sport scope filters
 * to any Eloquent query based on the authenticated PartnerUser's permissions.
 *
 * Usage:
 *   $service = new PartnerAccessService(auth('academy')->user());
 *   $service->scopeTrainings(Training::query())->get();
 *   $service->scopeCoaches(Coach::query())->get();
 *   $service->scopeBookings(Join::query())->get();
 */
class PartnerAccessService
{
    public function __construct(
        private readonly PartnerUser $user
    ) {}

    // ─────────────────────────────────────────────
    // Public helpers
    // ─────────────────────────────────────────────

    /** IDs of branches this user can access. NULL means all. */
    public function accessibleBranchIds(): ?array
    {
        if ($this->user->is_owner || $this->user->access_all_branches) {
            return null;
        }

        return $this->user->getAccessibleBranches()->pluck('id')->all();
    }

    /** IDs of sports this user can access. NULL means all. */
    public function accessibleSportIds(): ?array
    {
        if ($this->user->is_owner || $this->user->access_all_sports) {
            return null;
        }

        return $this->user->getAccessibleSports()->pluck('id')->all();
    }

    // ─────────────────────────────────────────────
    // Scope: Trainings  (Training model)
    // Columns: academy_id, sport_id, address_id
    // ─────────────────────────────────────────────

    public function scopeTrainings(Builder $query): Builder
    {
        // Always scoped to the owning academy
        $query->where('trainings.academy_id', $this->user->academy_id);

        // Branch filter via address -> academy_id
        $branchIds = $this->accessibleBranchIds();
        if ($branchIds !== null) {
            $query->whereHas('address', fn (Builder $q) =>
                $q->whereIn('academy_id', $branchIds)
            );
        }

        // Sport filter
        $sportIds = $this->accessibleSportIds();
        if ($sportIds !== null) {
            $query->whereIn('trainings.sport_id', $sportIds);
        }

        return $query;
    }

    // ─────────────────────────────────────────────
    // Scope: Coaches  (Coach model)
    // Coaches are linked to academy_id + sports via coach_sports
    // ─────────────────────────────────────────────

    public function scopeCoaches(Builder $query): Builder
    {
        $query->where('coaches.academy_id', $this->user->academy_id);

        // Sport filter — coach must teach at least one of the allowed sports
        $sportIds = $this->accessibleSportIds();
        if ($sportIds !== null) {
            $query->whereHas('sports', fn (Builder $q) =>
                $q->whereIn('sports.id', $sportIds)
            );
        }

        // Branches: coaches don't have a direct branch_id,
        // so we limit via trainings they are assigned to in accessible branches
        $branchIds = $this->accessibleBranchIds();
        if ($branchIds !== null) {
            $query->whereHas('trainings', fn (Builder $q) =>
                $q->whereHas('address', fn (Builder $a) =>
                    $a->whereIn('academy_id', $branchIds)
                )
            );
        }

        return $query;
    }

    // ─────────────────────────────────────────────
    // Scope: Bookings / Joins  (Join model)
    // Joins -> training -> sport_id / address -> academy_id
    // ─────────────────────────────────────────────

    public function scopeBookings(Builder $query): Builder
    {
        $query->whereHas('training', function (Builder $q) {
            $q->where('academy_id', $this->user->academy_id);

            $branchIds = $this->accessibleBranchIds();
            if ($branchIds !== null) {
                $q->whereHas('address', fn (Builder $a) =>
                    $a->whereIn('academy_id', $branchIds)
                );
            }

            $sportIds = $this->accessibleSportIds();
            if ($sportIds !== null) {
                $q->whereIn('sport_id', $sportIds);
            }
        });

        return $query;
    }

    // ─────────────────────────────────────────────
    // Scope: Classes / TClass  (TClass model)
    // TClass -> training_id -> sport_id / address
    // ─────────────────────────────────────────────

    public function scopeClasses(Builder $query): Builder
    {
        $query->whereHas('training', function (Builder $q) {
            $q->where('academy_id', $this->user->academy_id);

            $branchIds = $this->accessibleBranchIds();
            if ($branchIds !== null) {
                $q->whereHas('address', fn (Builder $a) =>
                    $a->whereIn('academy_id', $branchIds)
                );
            }

            $sportIds = $this->accessibleSportIds();
            if ($sportIds !== null) {
                $q->whereIn('sport_id', $sportIds);
            }
        });

        return $query;
    }

    // ─────────────────────────────────────────────
    // Scope: Students  (AcademyStudent model)
    // Students linked to academy_id
    // ─────────────────────────────────────────────

    public function scopeStudents(Builder $query): Builder
    {
        $query->where('academy_id', $this->user->academy_id);

        $sportIds = $this->accessibleSportIds();
        if ($sportIds !== null) {
            // Students linked via training -> sport_id through joins/subscriptions
            $query->whereHas('trainings', fn (Builder $q) =>
                $q->whereIn('sport_id', $sportIds)
            );
        }

        return $query;
    }
}
