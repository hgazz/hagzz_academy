<?php

namespace App\Http\Controllers;

use App\Models\AcademyAttendanceSession;
use App\Models\AcademyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademyAttendanceController extends Controller
{
    public function index()
    {
        $sessions = AcademyAttendanceSession::with(['group'])
            ->whereHas('group', fn($query) => $query->where('academy_id', auth('academy')->id()))
            ->latest('session_date')
            ->paginate(20);

        return view('Academy.pages.attendance.index', compact('sessions'));
    }

    public function create()
    {
        $groups = AcademyGroup::where('academy_id', auth('academy')->id())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('Academy.pages.attendance.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academy_group_id' => ['required', 'integer', 'exists:academy_groups,id'],
            'session_date' => ['required', 'date'],
            'starts_at' => ['nullable', 'date_format:H:i'],
            'ends_at' => ['nullable', 'date_format:H:i', 'after:starts_at'],
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
        $attendance->load(['group', 'records.student']);
        abort_unless($attendance->group->academy_id === auth('academy')->id(), 404);

        return view('Academy.pages.attendance.show', ['session' => $attendance]);
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
