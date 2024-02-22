<?php

namespace App\Http\Traits;

use App\Models\Coach;
use App\Models\TClass;

trait CoacheTrait
{
    private function getCoaches()
    {
       return  Coach::get(['id','name']);
    }

    private function getClass()
    {
        return TClass::get(['id','title']);
    }
}
