<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\TermsController;
use App\Http\Controllers\TrainingCalendarController;
use App\Http\Controllers\TrainingController;
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
    Route::group(['prefix' => 'partner', 'as' => 'academy.', 'controller' => AuthController::class], function () {
        Route::group(['middleware' => 'guest:academy'], function () {
            Route::get('/login', 'loginPage')->name('loginPage');
            Route::post('/login', 'login')->name('login');
        });
        Route::get('/logout', 'logout')->name('logout')->middleware('auth:academy');
    });

    Route::group(['prefix' => 'partner', 'middleware' => 'auth:academy', 'as' => 'academy.'], function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('booking/filter', [DashboardController::class, 'filterBookings'])->name('filter-bookings');
        Route::get('/revenue-data', [DashboardController::class, 'getRevenueDataByMonth'])->name('revenue-data');
        Route::get('/chart/users-by-month', [DashboardController::class, 'getUserDataByMonthAjax'])->name('getUserDataByMonth');
        Route::get('/chart/users-by-year', [DashboardController::class, 'getUserDataByYearAjax'])->name('getUserDataByYear');
        Route::get('/beginner/sports', [DashboardController::class, 'getBeginnerSportsCount'])->name('beginner.sports');
        Route::get('/intermediate/sports', [DashboardController::class, 'getIntermediateSportsCount'])->name('intermediate.sports');
        Route::get('/advanced/sports', [DashboardController::class, 'getAdvancedSportsCount'])->name('advanced.sports');
        Route::get('/notifications/unread-count', [DashboardController::class, 'getUnreadNotificationCount'])->name('check-notifications');


        Route::controller(ProfileController::class)->group(function (){
            Route::get('profile','index')->name('profile.index');
            Route::put('profile/update/{user}', 'update')->name('profile.update');
        });

        Route::controller(AddressController::class)->group(function (){
            Route::get('address','index')->name('address.index');
            Route::get('address/create', 'create')->name('address.create');
            Route::post('address/store', 'store')->name('address.store');
            Route::get('address/edit/{address}','edit')->name('address.edit');
            Route::put('address/update/{address}','update')->name('address.update');
            Route::delete('address/delete','delete')->name('address.delete');
            Route::get('address/area/{city}','getAreaByCity')->name('area.getAreaByCity');
            Route::get('address/edit/area/{city}','getAreaByCity')->name('area.getAreaByCity');
            Route::get('address/country/{country}','getAllCountry')->name('country.getCountry');
            Route::get('address/edit/country/{country}','getAllCountry')->name('country.getCountry');
            Route::get('address/export','export')->name('address.export');

        });

        // coach routes
        Route::controller(CoachController::class)->group(function(){
            Route::get('coach','index')->name('coach');
            Route::get('coach/create','create')->name('coach.create');
            Route::post('coach/store','store')->name('coach.store');
            Route::get('coach/edit/{coach}','edit')->name('coach.edit');
            Route::put('coach/update/{coach}','update')->name('coach.update');
            Route::delete('coach/delete','delete')->name('coach.delete');
            Route::get('coach/export','export')->name('coach.export');
        });
        Route::controller(NotificationController::class)->group(function () {
            Route::get('notification', 'index')->name('notification.index');
            Route::get('notification/markAsRead/{notification}', 'markAsRead')->name('notification.markAsRead');
        });
        Route::controller( ClassesController::class)->group(function(){
            Route::get('classes','index')->name('class.index');
            Route::get('class/create','create')->name('class.create');
            Route::get('training/date','checkTrainingDate')->name('class.trainingDate');
            Route::post('class/store','store')->name('class.store');
            Route::get('class/edit/{class}','edit')->name('class.edit');
            Route::put('class/update/{class}','update')->name('class.update');
            Route::delete('class/delete','delete')->name('class.delete');
            Route::get('class/export','export')->name('class.export');
            Route::delete('class/bulkDelete','bulkDelete')->name('class.bulkDelete');
        });

        Route::controller(GalleryController::class)->group(function(){
            Route::get('gallery','index')->name('gallery.index');
            Route::get('gallery/create','create')->name('gallery.create');
            Route::post('gallery/store','store')->name('gallery.store');
            Route::get('gallery/edit/{gallery}','edit')->name('gallery.edit');
            Route::put('gallery/update/{gallery}','update')->name('gallery.update');
            Route::delete('gallery/delete','delete')->name('gallery.delete');
        });

        Route::controller(TrainingController::class)->group(function (){
            Route::get('training','index')->name('training.index');
            Route::get('training/create','create')->name('training.create');
            Route::post('training/store','store')->name('training.store');
            Route::get('training/edit/{training}','edit')->name('training.edit');
            Route::put('training/update/{training}','update')->name('training.update');
            Route::delete('training/delete','delete')->name('training.delete');
            Route::put('active/{training}','updateActive')->name('training.updateActive');
            Route::get('training/export','export')->name('training.export');
            Route::get('training/getCoachesBySports/{id}','getCoachesBySports');
            Route::post('trainings/areas','getAreaByCity')->name('training.getAreaByCity');
            Route::post('trainings/cities','getCityByCountry')->name('training.getCities');
            Route::delete('training/bulk','bulkDelete')->name('training.bulkDelete');
            Route::post('trainings/publish','publish')->name('training.publish');
        });

        Route::get('training/createBooking',[TrainingController::class, 'createBooking'])->name('createBooking');
        Route::post('trainings/booking',[TrainingController::class, 'storeBooking'])->name('storeBooking');
        Route::controller(BookingController::class)->group(function (){
            Route::get('booking/show/{id}','show')->name('booking.show');
        });
        Route::controller(CustomerController::class)->group(function (){
            Route::get('users','index')->name('users.index');
        });
        Route::controller(TermsController::class)->group(function (){
            Route::get('terms','index')->name('terms.index');
        });

        Route::controller(SettlementController::class)->group(function (){
            Route::get('settlements','index')->name('settlement.index');
        });

        Route::prefix('report')->as('report.')->controller(ReportController::class)
            ->group(function (){
                Route::get('settlement','settlement')->name('settlement.index');
                Route::get('settlement/filter','filter')->name('settlement.filter');
                Route::get('settlement/export','export')->name('settlement.export');
                Route::get('invoice','invoice')->name('invoice.filter');
                Route::get('transaction','transaction')->name('transaction.index');
                Route::get('bookings/export','bookingExport')->name('booking.export');
                Route::get('joins','joins')->name('joins');
                Route::get('joins/offline', 'offlineJoins')->name('offline-joins');
                Route::get('joins/offline/filter', 'offlineJoinFilter')->name('joins-offline-filter');
                Route::get('joins/{join}','viewBookingDetails')->name('view-booking-details');
                Route::get('joins/export/{join}', 'exportBookingFile')->name('export-booking-file');
                Route::get('joins/filter','joinFilter')->name('join.filter');
                Route::get('joins/export','joinExport')->name('join.export');
                Route::get('coach','coach')->name('coach');
                Route::get('coach/filter','coachFilter')->name('coach.filter');
                Route::get('coach/export','coachExport')->name('coach.export');
            });

        Route::get('/calendar', [TrainingCalendarController::class, 'index'])->name('calendar.index');
    });

});
