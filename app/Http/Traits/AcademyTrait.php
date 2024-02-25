<?php

namespace App\Http\Traits;

use App\Models\Academies;
use App\Models\Country;

trait AcademyTrait
{
    private function getCountry()
    {
        return Country::get(['id','name']);
    }
}
