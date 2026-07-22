<?php

namespace App\Http\Controllers;

use App\Exports\AcademyStudentsExport;
use App\Exports\AcademyStudentsTemplateExport;
use App\Imports\AcademyStudentsImport;
use App\Models\Academies;
use App\Models\AcademyStudent;
use App\Models\AcademyAttendanceRecord;
use App\Models\Country;
use App\Models\User;
use App\Support\MembershipCode;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorSVG;

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
        $countries = Country::orderBy('name')->get(['id', 'name']);
        return view('Academy.pages.students.create', compact('countries'));
    }

    public function profile(AcademyStudent $student)
    {
        $this->authorizeStudent($student);
        $student->load([
            'country', 'city', 'area', 'user.country', 'user.city', 'user.area', 'groups',
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
            'location' => collect([$student->area?->name ?: $student->user?->area?->name, $student->city?->name ?: $student->user?->city?->name, $student->country?->name ?: $student->user?->country?->name])->filter()->join(' - '),
            'school_name' => $student->school_name, 'club_member' => $student->club_member,
            'child_type' => $student->child_type, 'referral_source' => $student->referral_source,
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

    public function card(AcademyStudent $student)
    {
        $this->authorizeStudent($student);
        $student->load(['academy', 'user', 'groups.sport', 'subscriptions.group', 'subscriptions.payments']);
        $membershipCode = MembershipCode::make($student);
        $subscription = $student->subscriptions->sortByDesc('starts_on')->first();
        $qrResult = (new SvgWriter())->write(new QrCode(data: $membershipCode, size: 280, margin: 8));
        $barcode = (new BarcodeGeneratorSVG())->getBarcode($membershipCode, BarcodeGeneratorSVG::TYPE_CODE_128, 1.55, 54);

        return view('Academy.pages.students.card', [
            'student' => $student,
            'academy' => $student->academy,
            'subscription' => $subscription,
            'membershipCode' => $membershipCode,
            'qrDataUri' => $qrResult->getDataUri(),
            'barcodeSvg' => $barcode,
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

        $student = AcademyStudent::create($data);
        $this->syncLinkedUser($student);

        session()->flash('success', trans('admin.student_management.student_created'));
        return to_route('academy.students.index');
    }

    public function edit(AcademyStudent $student)
    {
        $this->authorizeStudent($student);

        $countries = Country::orderBy('name')->get(['id', 'name']);
        return view('Academy.pages.students.edit', compact('student', 'countries'));
    }

    public function update(Request $request, AcademyStudent $student)
    {
        $this->authorizeStudent($student);
        $student->update($this->validated($request));
        $this->syncLinkedUser($student);

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
            'country_code' => ['nullable', 'string', 'max:10'],
            'country_id' => ['nullable', 'exists:countries,id'], 'city_id' => ['nullable', 'exists:cities,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'child_type' => ['nullable', 'in:parent,child,athlete'], 'school_name' => ['nullable', 'string', 'max:255'],
            'club_member' => ['nullable', 'in:yes,no'], 'coach_preference' => ['nullable', 'in:male,female,not_important'],
            'frequent_attendance' => ['nullable', 'in:daily,weekly,monthly'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'relation_with_child' => ['nullable', 'in:father,mother,brother,sister,guardian'],
            'referral_source' => ['nullable', 'in:friends,facebook,hagzz_app'],
            'delivery_service' => ['nullable', 'in:yes,no'],
            'status' => ['required', 'in:active,inactive,suspended'],
            'medical_notes' => ['nullable', 'string'],
            'medical_condition' => ['nullable', 'in:yes,no'], 'start_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function syncLinkedUser(AcademyStudent $student): void
    {
        if (! $student->user_id) return;
        $student->user()->update([
            'name' => $student->name, 'phone' => $student->phone, 'email' => $student->email,
            'gender' => $student->gender, 'birth_date' => $student->birth_date,
            'country_code' => $student->country_code, 'country_id' => $student->country_id,
            'city_id' => $student->city_id, 'area_id' => $student->area_id,
            'child_type' => $student->child_type, 'school_name' => $student->school_name,
            'club_member' => $student->club_member, 'parent_name' => $student->guardian_name,
            'parent_phone' => $student->guardian_phone, 'coach_preference' => $student->coach_preference,
            'frequent_attendance' => $student->frequent_attendance, 'relation_with_child' => $student->relation_with_child,
            'referral_source' => $student->referral_source, 'delivery_service' => $student->delivery_service,
            'medical_condition' => $student->medical_condition, 'start_date' => $student->start_date,
            'medical_condition_details' => $student->medical_notes, 'additional_information' => $student->notes,
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
