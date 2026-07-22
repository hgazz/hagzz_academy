<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Academies;
use App\Models\AcademyGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerGroupController extends Controller
{
    /**
     * Return all groups belonging to the authenticated academy.
     * Used by the mobile app to populate the "Add Session" form.
     */
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user() instanceof Academies, 403);

        $academyId = $request->user()->id;

        $groups = AcademyGroup::query()
            ->where('academy_id', $academyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'start_time', 'end_time', 'days'])
            ->map(fn (AcademyGroup $group) => [
                'id'         => $group->id,
                'name'       => $group->name,
                'start_time' => $group->start_time ? substr($group->start_time, 0, 5) : null,
                'end_time'   => $group->end_time   ? substr($group->end_time, 0, 5)   : null,
                'days'       => $group->days ?? [],
            ]);

        return response()->json(['data' => $groups]);
    }
}
