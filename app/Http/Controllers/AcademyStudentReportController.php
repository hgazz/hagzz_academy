<?php

namespace App\Http\Controllers;

use App\Models\AcademyAttendanceRecord;
use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\AcademyStudentPayment;
use App\Models\AcademyStudentSubscription;
use Illuminate\Support\Facades\DB;

class AcademyStudentReportController extends Controller
{
    public function index()
    {
        $academyId = auth('academy')->id();

        $attendanceByStatus = AcademyAttendanceRecord::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->whereHas('session.group', fn($query) => $query->where('academy_id', $academyId))
            ->groupBy('status')
            ->pluck('total', 'status');

        $latestPayments = AcademyStudentPayment::with('subscription.student')
            ->whereHas('subscription.student', fn($query) => $query->where('academy_id', $academyId))
            ->latest('paid_at')
            ->limit(10)
            ->get();

        $expiringSubscriptions = AcademyStudentSubscription::with(['student', 'group'])
            ->whereHas('student', fn($query) => $query->where('academy_id', $academyId))
            ->where('status', 'active')
            ->whereBetween('ends_on', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('ends_on')
            ->limit(10)
            ->get();

        $students = AcademyStudent::with(['country', 'city', 'area'])
            ->where('academy_id', $academyId)->orderBy('name')->get();

        return view('Academy.pages.student_reports.index', [
            'activeStudentsCount' => AcademyStudent::where('academy_id', $academyId)->where('status', 'active')->count(),
            'activeGroupsCount' => AcademyGroup::where('academy_id', $academyId)->where('status', 'active')->count(),
            'activeSubscriptionsCount' => AcademyStudentSubscription::whereHas('student', fn($query) => $query->where('academy_id', $academyId))
                ->where('status', 'active')
                ->count(),
            'paidAmount' => AcademyStudentPayment::whereHas('subscription.student', fn($query) => $query->where('academy_id', $academyId))->sum('amount'),
            'attendanceByStatus' => $attendanceByStatus,
            'latestPayments' => $latestPayments,
            'expiringSubscriptions' => $expiringSubscriptions,
            'students' => $students,
        ]);
    }
}
