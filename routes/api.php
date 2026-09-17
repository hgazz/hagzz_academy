<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\PartnerAttendanceController;
use App\Http\Controllers\Api\Mobile\PartnerAuthController;
use App\Http\Controllers\Api\Mobile\PartnerDashboardController;
use App\Http\Controllers\Api\Mobile\PartnerGroupController;
use App\Http\Controllers\Api\Mobile\PartnerScheduleController;
use App\Http\Controllers\Api\Mobile\PartnerStudentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('mobile/v1')->group(function () {
    Route::get('media/avatars/{filename}', function ($filename) {
        $safeName = basename($filename);
        $filePath = realpath(public_path('uploads/avatars/' . $safeName));
        $baseAvatars = realpath(public_path('uploads/avatars'));

        if (!$filePath || !is_file($filePath) || ($baseAvatars && !str_starts_with($filePath, $baseAvatars . DIRECTORY_SEPARATOR))) {
            abort(404);
        }

        $file = file_get_contents($filePath);
        $type = mime_content_type($filePath) ?: 'image/png';

        return response($file, 200, [
            'Content-Type' => $type,
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
        ]);
    });

    Route::get('media/assets/{path}', function ($path) {
        if (str_contains($path, '..') || str_contains($path, '\\')) {
            abort(403);
        }

        $basePublic = realpath(public_path());
        $filePath = realpath(public_path($path));

        if (!$filePath || !is_file($filePath) || !str_starts_with($filePath, $basePublic . DIRECTORY_SEPARATOR)) {
            abort(404);
        }

        if (str_starts_with(basename($filePath), '.')) {
            abort(403);
        }

        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'css', 'js', 'json', 'pdf'];
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions, true)) {
            abort(403);
        }

        $file = file_get_contents($filePath);
        $type = mime_content_type($filePath) ?: 'application/octet-stream';

        return response($file, 200, [
            'Content-Type' => $type,
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
        ]);
    })->where('path', '.*');

    Route::post('auth/partner/login', [PartnerAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [PartnerAuthController::class, 'me']);
        Route::post('profile/update', [PartnerAuthController::class, 'updateProfile']);
        Route::post('auth/logout', [PartnerAuthController::class, 'logout']);
        Route::get('dashboard', PartnerDashboardController::class);
        Route::get('students', [PartnerStudentController::class, 'index']);
        Route::post('students', [PartnerStudentController::class, 'store']);
        Route::get('students/{student}', [PartnerStudentController::class, 'show']);
        Route::get('subscriptions', [PartnerStudentController::class, 'subscriptions']);
        Route::get('reports/financial', [PartnerStudentController::class, 'financialReport']);
        Route::get('notifications', [PartnerStudentController::class, 'notifications']);
        Route::get('messages', [PartnerStudentController::class, 'messages']);
        Route::get('schedule', PartnerScheduleController::class);
        Route::get('groups', [PartnerGroupController::class, 'index']);
        Route::get('attendance/sessions', [PartnerAttendanceController::class, 'sessions']);
        Route::post('attendance/sessions', [PartnerAttendanceController::class, 'store']);
        Route::post('attendance/scan', [PartnerAttendanceController::class, 'scan']);
    });
});
