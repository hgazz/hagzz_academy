<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

try {
    $user = \App\Models\Academies::first() ?: \App\Models\PartnerUser::first();
    if (!$user) {
        die("NO_USER_FOUND");
    }
    auth('academy')->login($user);
    $controller = app(\App\Http\Controllers\DashboardController::class);
    $response = $controller->index();
    echo "RENDER_SUCCESS: " . (is_object($response) ? get_class($response) : gettype($response));
} catch (\Throwable $e) {
    echo "DASHBOARD_ERROR: " . $e->getMessage() . "\nFILE: " . $e->getFile() . ":" . $e->getLine() . "\n\nTRACE:\n" . $e->getTraceAsString();
}
