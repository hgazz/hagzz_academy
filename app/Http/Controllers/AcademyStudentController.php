<?php

namespace App\Http\Controllers;

use App\Exports\AcademyStudentsExport;
use App\Exports\AcademyStudentsTemplateExport;
use App\Imports\AcademyStudentsImport;
use App\Models\Academies;
use App\Models\AcademyStudent;
use App\Models\AcademyAttendanceRecord;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AcademyStudentController extends Controller
{
    public function index()
    {
        $students = AcademyStudent::with('user')
            ->where('academy_id', auth('academy')->id())
            ->latest()
            ->paginate(20);

        return view('Academy.pages.students.index', compact('students'));
    }

    public function create()
    {
        return view('Academy.pages.students.create');
    }

    public function profile(AcademyStudent $student)
    {
        $this->authorizeStudent($student);
        $student->load([
            'user.country', 'user.city', 'user.area', 'groups',
            'subscriptions.group', 'subscriptions.payments', 'attendanceRecords.session.group',
        ]);
        $subscription = $student->subscriptions->sortByDesc('starts_on')->first();
        $attendance = $student->attendanceRecords->groupBy('status')->map->count();
        $totalPaid = (float) $student->subscriptions->sum(fn ($item) => $item->payments->sum('amount'));
        $totalDue = (float) $student->subscriptions->sum('amount');
        $remainingDays = $subscription?->ends_on && $subscription->ends_on->isFuture()
            ? now()->startOfDay()->diffInDays($subscription->ends_on)
            : 0;

        return response()->json([
            'id' => $student->id,
            'name' => $student->name,
            'image' => $student->avatarUrl(),
            'fallback_image' => $student->defaultImageUrl(),
            'phone' => $student->phone ?: $student->user?->phone,
            'email' => $student->email ?: $student->user?->email,
            'gender' => $student->gender,
            'birth_date' => $student->birth_date?->format('Y-m-d'),
            'age' => $student->birth_date?->age,
            'status' => $student->status,
            'guardian_name' => $student->guardian_name ?: $student->user?->parent_name,
            'guardian_phone' => $student->guardian_phone ?: $student->user?->parent_phone,
            'location' => collect([$student->user?->area?->name, $student->user?->city?->name, $student->user?->country?->name])->filter()->join(' - '),
            'groups' => $student->groups->pluck('name')->filter()->values(),
            'medical_notes' => $student->medical_notes ?: $student->user?->medical_condition_details,
            'notes' => $student->notes ?: $student->user?->additional_information,
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'group' => $subscription->group?->name,
                'starts_on' => $subscription->starts_on?->format('Y-m-d'),
                'ends_on' => $subscription->ends_on?->format('Y-m-d'),
                'duration_days' => $subscription->starts_on && $subscription->ends_on ? $subscription->starts_on->diffInDays($subscription->ends_on) : null,
                'remaining_days' => $remainingDays,
                'amount' => (float) $subscription->amount,
                'paid' => $subscription->paid_amount,
                'remaining' => $subscription->remaining_amount,
                'status' => $subscription->status,
                'payment_status' => $subscription->payment_status,
                'last_payment_method' => $subscription->payments->sortByDesc('paid_at')->first()?->method_label,
            ] : null,
            'financials' => ['total_due' => $totalDue, 'total_paid' => $totalPaid, 'total_remaining' => max(0, $totalDue - $totalPaid)],
            'attendance' => [
                'present' => (int) $attendance->get('present', 0), 'late' => (int) $attendance->get('late', 0),
                'absent' => (int) $attendance->get('absent', 0), 'excused' => (int) $attendance->get('excused', 0),
                'total' => $student->attendanceRecords->count(),
            ],
            'recent_attendance' => $student->attendanceRecords->sortByDesc(fn ($record) => $record->session?->session_date)->take(8)->map(fn ($record) => [
                'date' => $record->session?->session_date?->format('Y-m-d'), 'group' => $record->session?->group?->name,
                'status' => $record->status, 'check_in' => $record->check_in_at,
            ])->values(),
            'edit_url' => route('academy.students.edit', $student),
        ]);
    }

    public function export()
    {
        $academy = Academies::find(auth('academy')->id());
        $students = $this->studentsQuery()->get();

        return Excel::download(new AcademyStudentsExport($students, $academy), 'academy-students.xlsx');
    }

    public function template()
    {
        return Excel::download(new AcademyStudentsTemplateExport(), 'academy-students-template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'students_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $import = new AcademyStudentsImport(auth('academy')->id());
        Excel::import($import, $request->file('students_file'));

        session()->flash(
            'success',
            trans('admin.student_management.students_imported', [
                'created' => $import->created,
                'updated' => $import->updated,
                'skipped' => $import->skipped,
            ])
        );

        return back();
    }

    public function print()
    {
        $academy = Academies::find(auth('academy')->id());
        $students = $this->studentsQuery()->get();

        return view('Academy.pages.students.print', compact('academy', 'students'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['academy_id'] = auth('academy')->id();

        AcademyStudent::create($data);

        session()->flash('success', trans('admin.student_management.student_created'));
        return to_route('academy.students.index');
    }

    public function edit(AcademyStudent $student)
    {
        $this->authorizeStudent($student);

        return view('Academy.pages.students.edit', compact('student'));
    }

    public function update(Request $request, AcademyStudent $student)
    {
        $this->authorizeStudent($student);
        $student->update($this->validated($request));

        session()->flash('success', trans('admin.student_management.student_updated'));
        return to_route('academy.students.index');
    }

    public function destroy(AcademyStudent $student)
    {
        $this->authorizeStudent($student);
        $student->delete();

        session()->flash('success', trans('admin.student_management.student_deleted'));
        return to_route('academy.students.index');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:active,inactive,suspended'],
            'medical_notes' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function authorizeStudent(AcademyStudent $student): void
    {
        abort_unless($student->academy_id === auth('academy')->id(), 404);
    }

    private function studentsQuery()
    {
        return AcademyStudent::with('user')
            ->where('academy_id', auth('academy')->id())
            ->orderBy('name');
    }
}
