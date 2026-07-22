<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Academies;
use App\Models\AcademyAttendanceRecord;
use App\Models\AcademyAttendanceSession;
use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\AcademyStudentSubscription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerDashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $academy = $request->user();
        abort_unless($academy instanceof Academies, 403);
        $academyId = $academy->id;

        $todayNameEn = strtolower(now()->format('l'));
        $todayNameAr = match($todayNameEn) {
            'saturday' => 'السبت',
            'sunday' => 'الأحد',
            'monday' => 'الاثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday' => 'الخميس',
            'friday' => 'الجمعة',
            default => 'اليوم',
        };

        // 1. Attendance Rate Stats
        $attendance = AcademyAttendanceRecord::query()
            ->whereHas('session.group', fn ($query) => $query->where('academy_id', $academyId))
            ->whereHas('session', fn ($query) => $query->whereDate('session_date', '>=', now()->subDays(30)))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $attendanceTotal = (int) $attendance->sum();
        $attended = (int) ($attendance->get('present', 0) + $attendance->get('late', 0));

        // 2. Today's Sessions (Strictly calculated for current day of week: Thursday / الخميس)
        $todaySessions = AcademyAttendanceSession::query()
            ->with(['group:id,name,academy_id,days,start_time,end_time', 'group.sport:id,name'])
            ->withCount('records')
            ->whereDate('session_date', today())
            ->whereHas('group', fn ($query) => $query->where('academy_id', $academyId))
            ->orderBy('starts_at')
            ->get();

        if ($todaySessions->isEmpty()) {
            $todayGroups = AcademyGroup::query()
                ->where('academy_id', $academyId)
                ->where('status', 'active')
                ->with(['sport:id,name', 'coach:id,name'])
                ->withCount('students')
                ->get()
                ->filter(function ($g) use ($todayNameEn, $todayNameAr) {
                    $days = is_array($g->days) ? $g->days : json_decode($g->days ?? '[]', true);
                    if (empty($days)) return true;
                    $daysStr = mb_strtolower(implode(' ', (array)$days));
                    return str_contains($daysStr, $todayNameEn) || str_contains($daysStr, mb_strtolower($todayNameAr));
                });

            $todaySessions = $todayGroups->map(fn ($g) => [
                'id' => $g->id,
                'group' => $g->name,
                'days' => is_array($g->days) ? implode('، ', $g->days) : ($g->days ?: $todayNameAr),
                'starts_at' => $g->start_time ?: '05:00 م',
                'ends_at' => $g->end_time ?: '06:30 م',
                'sport' => $g->sport?->name ?: 'كرة القدم',
                'coach' => $g->coach?->name ?: 'الكابتن الرئيسي',
                'records_count' => $g->students_count,
            ])->values();
        } else {
            $todaySessions = $todaySessions->map(fn ($session) => [
                'id' => $session->id,
                'group' => $session->group?->name,
                'days' => is_array($session->group?->days) ? implode('، ', $session->group->days) : $todayNameAr,
                'starts_at' => $session->starts_at,
                'ends_at' => $session->ends_at,
                'records_count' => $session->records_count,
            ]);
        }

        // 3. Expiring Subscriptions (Students needing contact for renewal)
        $expiringSubscriptions = AcademyStudentSubscription::query()
            ->where('status', 'active')
            ->whereBetween('ends_on', [today()->subDays(5), today()->addDays(14)])
            ->whereHas('student', fn ($query) => $query->where('academy_id', $academyId))
            ->with(['student:id,name,phone,guardian_name,guardian_phone', 'group:id,name'])
            ->orderBy('ends_on', 'asc')
            ->get()
            ->map(function ($sub) {
                $student = $sub->student;
                $endsOn = $sub->ends_on ? (is_string($sub->ends_on) ? $sub->ends_on : $sub->ends_on->format('Y-m-d')) : date('Y-m-d');
                $daysRemaining = max(0, (int) today()->diffInDays(Carbon::parse($endsOn), false));
                $phone = $student?->phone ?: $student?->guardian_phone ?: '';

                return [
                    'id' => $sub->id,
                    'student_id' => $student?->id,
                    'student_name' => $student?->name ?: 'طالب مسجل',
                    'phone' => $phone,
                    'guardian_name' => $student?->guardian_name ?: 'ولي الأمر',
                    'guardian_phone' => $student?->guardian_phone ?: $phone,
                    'group_name' => $sub->group?->name ?: 'المجموعة العامة',
                    'ends_on' => $endsOn,
                    'days_remaining' => $daysRemaining,
                    'status' => $sub->status ?: 'active',
                ];
            });

        return response()->json([
            'today_name' => $todayNameAr,
            'academy' => [
                'id' => $academyId,
                'name' => $academy->commercial_name ?: $academy->name,
                'logo' => $academy->logo,
            ],
            'summary' => [
                'active_students' => AcademyStudent::where('academy_id', $academyId)->where('status', 'active')->count(),
                'active_groups' => AcademyGroup::where('academy_id', $academyId)->where('status', 'active')->count(),
                'today_sessions' => count($todaySessions),
                'active_subscriptions' => AcademyStudentSubscription::where('status', 'active')
                    ->whereHas('student', fn ($query) => $query->where('academy_id', $academyId))->count(),
                'expiring_subscriptions' => count($expiringSubscriptions),
                'attendance_rate' => $attendanceTotal ? round(($attended / $attendanceTotal) * 100, 1) : 0,
            ],
            'today_sessions' => $todaySessions,
            'expiring_students' => $expiringSubscriptions->values(),
        ]);
    }
}
