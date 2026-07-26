<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Academies;
use App\Models\AcademyGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerScheduleController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $academy = $user instanceof \App\Models\PartnerUser ? $user->academy : $user;
        abort_unless($academy instanceof Academies, 403);

        $groups = AcademyGroup::query()
            ->with(['coach:id,name', 'sport:id,name'])
            ->withCount('students')
            ->where('academy_id', $academy->id)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get()
            ->map(fn (AcademyGroup $group) => [
                'id' => $group->id,
                'name' => $group->name,
                'days' => $group->days ?: [],
                'start_time' => $group->start_time,
                'end_time' => $group->end_time,
                'capacity' => $group->capacity,
                'students_count' => $group->students_count,
                'coach' => $group->coach?->name,
                'sport' => $group->sport?->name,
            ]);

        return response()->json(['data' => $groups]);
    }
}
