<?php

namespace App\Http\Traits;

use App\Models\Invoice;
use App\Models\Join;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait TrainingsTrait
{
    private function getFullTrainings(): int
    {
        return \DB::table('trainings')
            ->select('trainings.id')
            ->join('joins', 'trainings.id', '=', 'joins.training_id')
            ->where('trainings.academy_id', Auth::id())
            ->groupBy('trainings.id', 'trainings.max_players')
            ->havingRaw('COUNT(joins.id) = trainings.max_players')
            ->count();
    }

    private function getCancelledTrainings(): int
    {
        return Invoice::where('is_refunded', 1)
            ->whereHas('training', function ($query) {
                $query->where('academy_id', Auth::id());
            })->count();
    }

    private function getInProgressTrainings(): int
    {
        return \DB::table('trainings')
            ->select('trainings.id')
            ->join('joins', 'trainings.id', '=', 'joins.training_id')
            ->where('trainings.academy_id', Auth::id())
//            ->where('trainings.start_date', '<=', now())
//            ->where('trainings.end_date', '>=', now())
            ->groupBy('trainings.id', 'trainings.max_players')
            ->havingRaw('COUNT(joins.id) = trainings.max_players')
            ->count();
    }
    private function getUpcomingTrainings(): int
    {
        return \DB::table('trainings')
            ->select('trainings.id')
            ->where('trainings.academy_id', Auth::id())
//            ->where('trainings.start_date', '>', Carbon::today())
            ->count();
    }
}
