<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

$controller = app(\App\Http\Controllers\DashboardController::class);

echo "=== TESTING ALL ACADEMIES ===\n";
$academies = \App\Models\Academies::all();
foreach ($academies as $academy) {
    try {
        auth('academy')->login($academy);
        $res = $controller->index();
        echo "ACADEMY ID {$academy->id} ('{$academy->name}'): SUCCESS\n";
    } catch (\Throwable $e) {
        echo "ACADEMY ID {$academy->id} ('{$academy->name}'): ERROR -> " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}

echo "\n=== TESTING ALL PARTNER USERS ===\n";
$partnerUsers = \App\Models\PartnerUser::all();
foreach ($partnerUsers as $user) {
    try {
        auth('academy')->login($user);
        $res = $controller->index();
        echo "PARTNER_USER ID {$user->id} ('{$user->name}', academy_id: {$user->academy_id}): SUCCESS\n";
    } catch (\Throwable $e) {
        echo "PARTNER_USER ID {$user->id} ('{$user->name}', academy_id: {$user->academy_id}): ERROR -> " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}
