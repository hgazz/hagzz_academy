<?php

namespace App\Http\Controllers;

use App\Models\AcademyStudent;
use Illuminate\Http\Request;

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
}
