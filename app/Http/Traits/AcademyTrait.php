<?php

namespace App\Http\Traits;

use App\Models\Academies;

trait AcademyTrait
{
    private function getAcademies()
    {
        return Academies::get(['id','name']);
    }
}
