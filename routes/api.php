<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\PartnerAttendanceController;
use App\Http\Controllers\Api\Mobile\PartnerAuthController;
use App\Http\Controllers\Api\Mobile\PartnerDashboardController;
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
        $filePath = public_path('uploads/avatars/' . $filename);
        if (!file_exists($filePath)) {
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
        $filePath = public_path($path);
        if (!file_exists($filePath)) {
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
        Route::get('notifications', [PartnerStudentController::class, 'notifications']);
        Route::get('messages', [PartnerStudentController::class, 'messages']);
        Route::get('schedule', PartnerScheduleController::class);
        Route::get('attendance/sessions', [PartnerAttendanceController::class, 'sessions']);
        Route::post('attendance/scan', [PartnerAttendanceController::class, 'scan']);
    });
});
