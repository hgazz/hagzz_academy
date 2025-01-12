<?php

namespace App\Services\Training;

use App\Models\Training;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TrainingCalendar
{
    public function getMonthEvents(int $month, int $year)
    {
        return Training::query()
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->whereNotNull('classes_days')
            ->whereNull('deleted_at')
            ->where([
                ['active', 1],
                ['academy_id', auth('academy')->id()]
            ])
            ->get();
//        return $trainings->map(function ($training) use ($month, $year) {
//            return $this->generateTrainingDates($training, $month, $year);
//        })
//            ->filter() // Remove empty collections
//            ->collapse();
    }

    private function generateTrainingDates(Training $training, int $month, int $year): Collection
    {
        $dates = collect();
        $classesDays = $training->classes_days;


        // Get the first and last day of the month
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        // Get the training start date
        $trainingStartDate = Carbon::parse($training->created_at)->startOfDay();


        // Loop through each day of the month
        $currentDate = $startOfMonth->copy();
        while ($currentDate <= $endOfMonth) {
            // Check if the current day is in classes_days and after training creation
            if (in_array($currentDate->dayOfWeek, $classesDays) && $currentDate >= $trainingStartDate) {
                $eventStart = $currentDate->format('Y-m-d') . ' ' . $training->start_time;
                $eventEnd = $currentDate->format('Y-m-d') . ' ' . $training->end_time;

                $dates->push([
                    'id' => $training->id,
                    'title' => $training->name,
                    'start' => $eventStart,
                    'end' => $eventEnd,
                    'color' => $training->color ?? '#3788d8',
                    'training' => [
                        'id' => $training->id,
                        'name' => $training->name,
                        'price' => $training->price,
                        'max_players' => $training->max_players,
                        'level' => $training->level,
                        'gender' => $training->gender,
                        'age_group' => $training->age_group
                    ]
                ]);
            }
            $currentDate->addDay();
        }

        return $dates;
    }
}
