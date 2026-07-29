<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\PartnerUser::where('email', 'res@hagzz.com')->first();
header('Content-Type: application/json');
echo json_encode([
    'exists' => (bool)$user,
    'id' => $user?->id,
    'name' => $user?->name,
    'email' => $user?->email,
    'is_owner' => $user?->is_owner,
    'roles' => $user?->roles->pluck('name')->toArray(),
    'access_all_branches' => $user?->access_all_branches,
    'access_all_sports' => $user?->access_all_sports,
    'accessible_sports_count' => $user?->getAccessibleSports()->count(),
    'accessible_branches_count' => $user?->getAccessibleBranches()->count(),
]);
