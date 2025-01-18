<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Services\Training\TrainingCalendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingCalendarController extends Controller
{
    private TrainingCalendar $calendarService;

    public function __construct(TrainingCalendar $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index(Request $request)
    {
        $events = [];
        $trainings = Training::query()
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->whereNotNull('classes_days')
            ->whereNull('deleted_at')
            ->where([
                ['active', 1],
                ['academy_id', auth('academy')->id()]
            ])
            ->get();
        foreach ($trainings as $training) {
            $events[] = [
                'id' => $training['id'],
                'title' => $training['name'],
                'start' => $training['start_time'],
                'end' => $training['end_time'],
                'color' => $training['color'],
                'days' => $training['classes_days'],
            ];
        }

        return view('Academy.pages.calander.index', compact('events'));

    }
}
