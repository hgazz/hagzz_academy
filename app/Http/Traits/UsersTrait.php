<?php

namespace App\Http\Traits;

use App\Support\PartnerAcademy;
use App\Models\Join;
use App\Models\User;

trait UsersTrait
{
    private function getAllUsersCount(): int
    {
        return User::count();
    }

    private function getUsersByPartner()
    {
        $users = Join::whereHas('training', function ($query) {
            $query->where('academy_id', PartnerAcademy::id());
        })->get()->unique('user_id');

        return count($users);
    }
}
