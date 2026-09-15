<?php

namespace App\Providers;

use App\Models\Coach;
use App\Models\Follow;
use App\Models\Training;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::share([
            'follows' => 0,
            'totalPriceValue' => 0,
            'coaches' => 0,
            'trainings' => 0,
        ]);
    }
}
