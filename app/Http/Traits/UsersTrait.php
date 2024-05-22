<?php

namespace App\Http\Traits;

use App\Models\User;

trait UsersTrait
{
    private function getAllUsersCount(): int
    {
        return User::count();
    }
}
