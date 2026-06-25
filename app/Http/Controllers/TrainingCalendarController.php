<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\Request;

class TrainingCalendarController extends Controller
{
    public function index(Request $request)
    {
        $academyId = auth('academy')->id();
        $trainings = Training::query()
            ->with([
                'coach:id,name',
                'address:id,address',
                'sport:id,name',
            ])
            ->withCount('joins')
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->whereNotNull('classes_days')
            ->where('active', 1)
            ->where('academy_id', $academyId)
            ->orderBy('start_time')
            ->get();

        $events = $trainings->map(function (Training $training) {
            return [
                'id' => $training->id,
                'title' => $this->localizedValue($training->getRawOriginal('name')),
                'startTime' => optional($training->start_time)->format('H:i:s'),
                'endTime' => optional($training->end_time)->format('H:i:s'),
                'color' => $this->safeColor($training->color),
                'days' => $training->classes_days ?: [],
                'coach' => $this->localizedValue($training->coach?->getRawOriginal('name')),
                'sport' => $this->localizedValue($training->sport?->getRawOriginal('name')),
                'location' => $this->localizedValue($training->address?->getRawOriginal('address')),
                'bookings' => (int) $training->joins_count,
                'capacity' => (int) ($training->max_players ?? 0),
                'level' => $training->level,
                'editUrl' => route('academy.training.edit', $training),
            ];
        })->values();

        $todayName = strtolower(now()->englishDayOfWeek);
        $todayTrainings = $events
            ->filter(fn ($event) => collect($event['days'])->map(fn ($day) => strtolower($day))->contains($todayName))
            ->values();

        $calendarSummary = [
            'trainings' => $trainings->count(),
            'weeklySessions' => $trainings->sum(fn ($training) => count($training->classes_days ?: [])),
            'todaySessions' => $todayTrainings->count(),
            'bookings' => $trainings->sum('joins_count'),
            'todayTrainings' => $todayTrainings,
        ];

        return view('Academy.pages.calander.index', compact('events', 'calendarSummary'));
    }

    private function localizedValue(?string $value): string
    {
        if (blank($value)) {
            return app()->getLocale() === 'ar' ? 'غير محدد' : 'Not specified';
        }

        $translations = json_decode($value, true);
        if (!is_array($translations)) {
            return $value;
        }

        return $translations[app()->getLocale()]
            ?? $translations['en']
            ?? $translations['ar']
            ?? reset($translations)
            ?? $value;
    }

    private function safeColor(?string $color): string
    {
        if ($color && preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            return $color;
        }

        return '#2563eb';
    }
}
