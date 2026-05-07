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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $views = ['*'];
        View::composer($views,function (\Illuminate\View\View $view){
            if (! auth('academy')->check()) {
                return $view->with([
                    'follows' => 0,
                    'totalPriceValue' => 0,
                    'coaches' => 0,
                    'trainings' => 0,
                ]);
            }

            $academyId = auth('academy')->id();

            $follows = Follow::where('followable_id', $academyId)->count();
            $coaches = Coach::where('academy_id', $academyId)->count();
            $trainings = Training::where('academy_id', $academyId)->count();
            $totalPrice = Training::with(['joins' => function ($query) {
                $query->select('training_id', DB::raw('sum(price) as total_price'))
                    ->groupBy('training_id');
            }])
                ->where('academy_id', $academyId)
                ->get(['id', 'name']);
            $totalPriceValue = $totalPrice->sum('joins.total_price');
            return $view->with(['follows' => $follows,'totalPriceValue'=>$totalPriceValue, 'coaches' => $coaches, 'trainings' => $trainings]);
        });
    }
}
