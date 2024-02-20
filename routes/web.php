<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function(){ //...
    Route::get('/', function () {
        return redirect(\route('academy.loginPage'));
    });
    // login page , login route and logout route
    Route::group(['prefix' => 'academy', 'as' => 'academy.', 'controller' => AuthController::class], function () {
        Route::group(['middleware' => 'guest:academy'], function () {
            Route::get('/login', 'loginPage')->name('loginPage');
            Route::post('/login', 'login')->name('login');
        });
        Route::post('/logout', 'logout')->name('logout')->middleware('auth:academy');
    });

    Route::group(['prefix' => 'academy', 'middleware' => 'auth:academy', 'as' => 'academy.'], function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');

        Route::controller(ProfileController::class)->group(function (){
            Route::get('profile','index')->name('profile.index');
            Route::put('profile/update/{user}', 'update')->name('profile.update');
        });

    });
});
