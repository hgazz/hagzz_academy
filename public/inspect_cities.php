<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

header('Content-Type: application/json');
$countries = \App\Models\Country::all();
$cities = \App\Models\City::all();
$areas = \App\Models\Area::all();

echo json_encode([
    'countries' => $countries->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'iso2' => $c->iso2]),
    'cities_count' => $cities->count(),
    'cities_sample' => $cities->take(10)->map(fn($c) => ['id' => $c->id, 'country_id' => $c->country_id, 'name' => $c->name]),
    'areas_count' => $areas->count(),
    'areas_sample' => $areas->take(10)->map(fn($a) => ['id' => $a->id, 'city_id' => $a->city_id, 'name' => $a->name]),
], JSON_UNPRETTY_PRINT | JSON_UNESCAPED_UNICODE);
