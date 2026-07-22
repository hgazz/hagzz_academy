<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Academies;
use App\Models\AcademyAttendanceRecord;
use App\Models\AcademyAttendanceSession;
use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\AcademyStudentSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerDashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $academy = $request->user();
        abort_unless($academy instanceof Academies, 403);
        $academyId = $academy->id;

        $attendance = AcademyAttendanceRecord::query()
            ->whereHas('session.group', fn ($query) => $query->where('academy_id', $academyId))
            ->whereHas('session', fn ($query) => $query->whereDate('session_date', '>=', now()->subDays(30)))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $attendanceTotal = (int) $attendance->sum();
        $attended = (int) ($attendance->get('present', 0) + $attendance->get('late', 0));

        $todaySessions = AcademyAttendanceSession::query()
            ->with('group:id,name,academy_id')
            ->withCount('records')
            ->whereDate('session_date', today())
            ->whereHas('group', fn ($query) => $query->where('academy_id', $academyId))
            ->orderBy('starts_at')
            ->get()
            ->map(fn ($session) => [
                'id' => $session->id,
                'group' => $session->group?->name,
                'starts_at' => $session->starts_at,
                'ends_at' => $session->ends_at,
                'records_count' => $session->records_count,
            ]);

        return response()->json([
            'academy' => [
                'id' => $academyId,
                'name' => $academy->commercial_name,
                'logo' => $academy->logo,
            ],
            'summary' => [
                'active_students' => AcademyStudent::where('academy_id', $academyId)->where('status', 'active')->count(),
                'active_groups' => AcademyGroup::where('academy_id', $academyId)->where('status', 'active')->count(),
                'today_sessions' => $todaySessions->count(),
                'active_subscriptions' => AcademyStudentSubscription::where('status', 'active')
                    ->whereHas('student', fn ($query) => $query->where('academy_id', $academyId))->count(),
                'expiring_subscriptions' => AcademyStudentSubscription::where('status', 'active')
                    ->whereBetween('ends_on', [today(), today()->addDays(14)])
                    ->whereHas('student', fn ($query) => $query->where('academy_id', $academyId))->count(),
                'attendance_rate' => $attendanceTotal ? round(($attended / $attendanceTotal) * 100, 1) : 0,
            ],
            'today_sessions' => $todaySessions,
        ]);
    }
}
