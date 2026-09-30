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
        if (file_exists(app_path('Helpers/facility_helper.php'))) {
            require_once app_path('Helpers/facility_helper.php');
        }
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

        View::composer('*', function ($view) {
            $user = auth('academy')->user();
            if ($user) {
                $type = ($user instanceof \App\Models\Academies)
                    ? ($user->business_type ?? 'academy')
                    : ($user instanceof \App\Models\PartnerUser ? ($user->academy?->business_type ?? 'academy') : 'academy');

                \App\Support\FacilityTerminology::applyDynamicTranslations($type);

                $view->with([
                    'facilityType'       => $type,
                    'isGymFacility'      => in_array($type, ['gym', 'health_center', 'hybrid']),
                    'isHealthFacility'   => in_array($type, ['health_center', 'hybrid']),
                    'hasCampsModule'     => in_array($type, ['academy', 'hybrid']),
                    'termStudent'        => facility_term('student', $type),
                    'termStudentsPlural' => facility_term('students_list', $type),
                    'termAddStudent'     => facility_term('add_student', $type),
                    'termCoaches'        => facility_term('coaches_list', $type),
                    'termTraining'       => facility_term('training', $type, true),
                    'termAttendance'     => facility_term('attendance', $type),
                    'termSubscriptions'  => facility_term('subscription', $type, true),
                    'termGroups'         => facility_term('group', $type, true),
                ]);
            }
        });
    }
}
