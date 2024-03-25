<?php

namespace App\Providers;

use App\Models\Coach;
use App\Models\Follow;
use App\Models\Training;
use Illuminate\Support\Facades\DB;
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
        $views = ['*'];
        View::composer($views,function (\Illuminate\View\View $view){
            $follows = Follow::where('followable_id',auth('academy')->id())->count();
            $coaches = Coach::where('academy_id',auth('academy')->id())->count();
            $trainings = Training::where('academy_id', auth('academy')->id())->count();
            $totalPrice = Training::with(['joins' => function ($query) {
                $query->select('training_id', DB::raw('sum(price) as total_price'))
                    ->groupBy('training_id');
            }])
                ->where('academy_id', auth('academy')->id())
                ->get(['id', 'name']);
            $totalPriceValue = $totalPrice->sum('joins.total_price');
            return $view->with(['follows' => $follows,'totalPriceValue'=>$totalPriceValue, 'coaches' => $coaches, 'trainings' => $trainings]);
        });
    }
}
