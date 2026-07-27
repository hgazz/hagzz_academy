<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: application/json');

$academies = \App\Models\Academies::all();

$report = [];
foreach ($academies as $a) {
    $students = \App\Models\AcademyStudent::where('academy_id', $a->id)->get();
    $report[] = [
        'academy_id' => $a->id,
        'academy_name' => $a->name,
        'academy_email' => $a->email,
        'academy_phone' => $a->phone,
        'students_count' => $students->count(),
        'students_list' => $students->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'phone' => $s->phone, 'status' => $s->status]),
    ];
}

$allUsers = \App\Models\User::all();

echo json_encode([
    'academies_report' => $report,
    'total_academy_students' => \App\Models\AcademyStudent::count(),
    'total_users' => $allUsers->count(),
    'users_sample' => $allUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'phone' => $u->phone]),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
