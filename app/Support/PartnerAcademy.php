<?php

namespace App\Support;

use App\Models\Academies;
use App\Models\PartnerUser;

final class PartnerAcademy
{
    public static function id(): int
    {
        $user = auth('academy')->user();
        abort_unless($user instanceof PartnerUser || $user instanceof Academies, 401);

        $id = (int) ($user instanceof PartnerUser ? $user->academy_id : $user->id);
        abort_unless($id > 0, 403);

        return $id;
    }
}
