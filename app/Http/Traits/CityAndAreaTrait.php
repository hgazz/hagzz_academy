<?php

namespace App\Http\Traits;

use App\Models\Area;
use App\Models\City;

trait CityAndAreaTrait
{
    private function getCities()
    {
        return City::get(['id','name']);
    }
    private function getAreas()
    {
        return Area::get(['id','name']);
    }
}
