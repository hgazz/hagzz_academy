<?php

namespace App\Http\Controllers;

use App\Models\AcademyAttendanceSession;
use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Support\MembershipCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademyAttendanceController extends Controller
{
    public function index()
    {
        $sessions = AcademyAttendanceSession::with(['group'])
            ->withCount([
                'records',
                'records as present_count' => fn($query) => $query->where('status', 'present'),
                'records as late_count' => fn($query) => $query->where('status', 'late'),
                'records as absent_count' => fn($query) => $query->where('status', 'absent'),
                'records as excused_count' => fn($query) => $query->where('status', 'excused'),
            ])
            ->whereHas('group', fn($query) => $query->where('academy_id', auth('academy')->id()))
            ->latest('session_date')
            ->paginate(20);

        return view('Academy.pages.attendance.index', compact('sessions'));
    }

    public function create()
    {
        $groups = AcademyGroup::where('academy_id', auth('academy')->id())
            ->where('status', 'active')
            ->withCount('students')
            ->orderBy('name')
            ->get();

        return view('Academy.pages.attendance.create', compact('groups'));
    }

    public function scanner()
    {
        $sessions = AcademyAttendanceSession::with('group')
            ->whereDate('session_date', today())
            ->whereHas('group', fn ($query) => $query->where('academy_id', auth('academy')->id()))
            ->orderBy('starts_at')->get();

        return view('Academy.pages.attendance.scanner', compact('sessions'));
    }

    public function scan(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:120'],
            'session_id' => ['required', 'integer', 'exists:academy_attendance_sessions,id'],
            'mode' => ['required', 'in:auto,check_in,check_out'],
        ]);
        $academyId = auth('academy')->id();
        $studentId = MembershipCode::studentId($data['code'], $academyId);
        abort_unless($studentId, 422, app()->getLocale() === 'ar' ? 'الكارت غير صالح لهذه الأكاديمية.' : 'This card is not valid for this academy.');

        $student = AcademyStudent::where('academy_id', $academyId)->with(['user', 'subscriptions.payments'])->findOrFail($studentId);
        $session = AcademyAttendanceSession::with('group')->findOrFail($data['session_id']);
        abort_unless($session->group?->academy_id === $academyId, 404);
        abort_unless($session->group->students()->whereKey($student->id)->exists(), 422, app()->getLocale() === 'ar' ? 'الطالب غير مسجل في هذه المجموعة.' : 'Student is not assigned to this group.');

        $record = $session->records()->firstOrCreate(['academy_student_id' => $student->id]);
        $action = $data['mode'];
        if ($action === 'auto') {
            $action = blank($record->check_in_at) ? 'check_in' : (blank($record->check_out_at) ? 'check_out' : 'duplicate');
        }
        if ($action === 'duplicate') {
            return response()->json($this->scanResponse($student, $record, 'duplicate'));
        }
        if ($action === 'check_in') {
            if (filled($record->check_in_at)) return response()->json($this->scanResponse($student, $record, 'duplicate'));
            $late = $session->starts_at && now()->format('H:i:s') > date('H:i:s', strtotime($session->starts_at.' +15 minutes'));
            $record->update(['status' => $late ? 'late' : 'present', 'check_in_at' => now()->format('H:i:s')]);
        } else {
            if (blank($record->check_in_at)) {
                return response()->json(['message' => app()->getLocale() === 'ar' ? 'يجب تسجيل الحضور أولًا.' : 'Check-in is required first.'], 422);
            }
            if (filled($record->check_out_at)) return response()->json($this->scanResponse($student, $record, 'duplicate'));
            $record->update(['check_out_at' => now()->format('H:i:s')]);
        }

        return response()->json($this->scanResponse($student, $record->fresh(), $action));
    }

    private function scanResponse(AcademyStudent $student, $record, string $action): array
    {
        $subscription = $student->subscriptions->sortByDesc('starts_on')->first();
        $subscriptionValid = $subscription && $subscription->status === 'active'
            && (!$subscription->ends_on || $subscription->ends_on->isToday() || $subscription->ends_on->isFuture());

        return [
            'action' => $action, 'student' => ['name' => $student->name, 'image' => $student->avatarUrl(), 'fallback' => $student->defaultImageUrl(), 'phone' => $student->phone ?: $student->guardian_phone],
            'record' => ['status' => $record->status, 'check_in' => $record->check_in_at, 'check_out' => $record->check_out_at],
            'subscription' => ['valid' => (bool) $subscriptionValid, 'ends_on' => $subscription?->ends_on?->format('Y-m-d'), 'remaining' => $subscription?->remaining_amount ?? 0],
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academy_group_id' => ['required', 'integer', 'exists:academy_groups,id'],
            'session_date' => ['required', 'date'],
            'starts_at' => ['nullable', 'date_format:H:i'],
            'ends_at' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string'],
        ]);

        $group = AcademyGroup::with('students')->findOrFail($data['academy_group_id']);
        abort_unless($group->academy_id === auth('academy')->id(), 404);

        $session = DB::transaction(function () use ($data, $group) {
            $session = AcademyAttendanceSession::firstOrCreate(
                [
                    'academy_group_id' => $group->id,
                    'session_date' => $data['session_date'],
                ],
                $data
            );

            foreach ($group->students as $student) {
                $session->records()->firstOrCreate([
                    'academy_student_id' => $student->id,
                ]);
            }

            return $session;
        });

        session()->flash('success', trans('admin.student_management.attendance_session_created'));
        return to_route('academy.attendance.show', $session);
    }

    public function show(AcademyAttendanceSession $attendance)
    {
        $attendance->load(['group.students', 'records.student']);
        abort_unless($attendance->group->academy_id === auth('academy')->id(), 404);

        foreach ($attendance->group->students as $student) {
            $attendance->records()->firstOrCreate([
                'academy_student_id' => $student->id,
            ]);
        }

        $attendance->load(['group', 'records.student']);
        $attendance->setRelation(
            'records',
            $attendance->records->sortBy(fn($record) => $record->student?->name)->values()
        );

        $summary = [
            'present' => $attendance->records->where('status', 'present')->count(),
            'late' => $attendance->records->where('status', 'late')->count(),
            'absent' => $attendance->records->where('status', 'absent')->count(),
            'excused' => $attendance->records->where('status', 'excused')->count(),
            'total' => $attendance->records->count(),
        ];

        return view('Academy.pages.attendance.show', ['session' => $attendance, 'summary' => $summary]);
    }

    public function update(Request $request, AcademyAttendanceSession $attendance)
    {
        $attendance->load('group');
        abort_unless($attendance->group->academy_id === auth('academy')->id(), 404);

        $records = $request->validate([
            'records' => ['required', 'array'],
            'records.*.status' => ['required', 'in:present,absent,late,excused'],
            'records.*.check_in_at' => ['nullable', 'date_format:H:i'],
            'records.*.check_out_at' => ['nullable', 'date_format:H:i'],
            'records.*.notes' => ['nullable', 'string'],
        ])['records'];

        foreach ($attendance->records as $record) {
            if (isset($records[$record->id])) {
                $record->update($records[$record->id]);
            }
        }

        session()->flash('success', trans('admin.student_management.attendance_updated'));
        return back();
    }
}
