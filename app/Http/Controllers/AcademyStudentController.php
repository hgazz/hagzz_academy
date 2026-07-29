<?php

namespace App\Http\Controllers;

use App\Exports\AcademyStudentsExport;
use App\Exports\AcademyStudentsTemplateExport;
use App\Imports\AcademyStudentsImport;
use App\Models\Academies;
use App\Models\AcademyStudent;
use App\Models\AcademyAttendanceRecord;
use App\Models\Country;
use App\Models\City;
use App\Models\Area;
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
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $service = new \App\Services\PartnerAccessService($authUser);

        $students = $service->scopeStudents(AcademyStudent::with('user'))
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
        $this->processLocationData($data, $request);
        $data['academy_id'] = auth('academy')->id();

        if ($request->hasFile('medical_certificate')) {
            $path = $request->file('medical_certificate')->store('students/medical_certificates', 'public');
            $data['medical_certificate'] = 'storage/' . $path;
        }
        if ($request->hasFile('club_card_file')) {
            $path = $request->file('club_card_file')->store('students/club_cards', 'public');
            $data['club_card_file'] = 'storage/' . $path;
        }

        $student = AcademyStudent::create($data);
        $this->syncLinkedUser($student);

        session()->flash('success', trans('admin.student_management.student_created'));
        return to_route('academy.students.index');
    }

    public function edit(AcademyStudent $student)
    {
        $this->authorizeStudent($student);

        $countries = Country::orderBy('name')->get(['id', 'name', 'iso2']);
        return view('Academy.pages.students.edit', compact('student', 'countries'));
    }

    public function update(Request $request, AcademyStudent $student)
    {
        $this->authorizeStudent($student);
        $data = $this->validated($request);
        $this->processLocationData($data, $request);

        if ($request->hasFile('medical_certificate')) {
            $path = $request->file('medical_certificate')->store('students/medical_certificates', 'public');
            $data['medical_certificate'] = 'storage/' . $path;
        }
        if ($request->hasFile('club_card_file')) {
            $path = $request->file('club_card_file')->store('students/club_cards', 'public');
            $data['club_card_file'] = 'storage/' . $path;
        }

        $student->update($data);
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'country_id' => ['nullable'],
            'city_id' => ['nullable'],
            'area_id' => ['nullable'],
            'custom_city_name' => ['nullable', 'string', 'max:255'],
            'custom_area_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'child_type' => ['nullable', 'in:parent,child,athlete'], 'school_name' => ['nullable', 'string', 'max:255'],
            'club_member' => ['nullable', 'in:yes,no'],
            'club_card_number' => ['nullable', 'string', 'max:100'],
            'club_card_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'coach_preference' => ['nullable', 'in:male,female,not_important'],
            'frequent_attendance' => ['nullable', 'in:daily,weekly,monthly'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'relation_with_child' => ['nullable', 'in:father,mother,brother,sister,guardian'],
            'referral_source' => ['nullable', 'in:friends,facebook,hagzz_app'],
            'delivery_service' => ['nullable', 'in:yes,no'],
            'status' => ['required', 'in:active,inactive,suspended'],
            'medical_notes' => ['nullable', 'string'],
            'medical_certificate' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'medical_condition' => ['nullable', 'in:yes,no'], 'start_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        return $validated;
    }

    private function processLocationData(array &$data, Request $request): void
    {
        // Auto-assign country_code if missing
        if (empty($data['country_code']) && !empty($data['country_id'])) {
            $country = Country::find($data['country_id']);
            if ($country && !empty($country->iso2)) {
                $codeMap = [
                    'EG' => '+20', 'SA' => '+966', 'AE' => '+971', 'QA' => '+974', 'KW' => '+965',
                    'OM' => '+968', 'BH' => '+973', 'JO' => '+962', 'LB' => '+961', 'IQ' => '+964',
                    'LY' => '+218', 'SD' => '+249', 'TN' => '+216', 'MA' => '+212', 'DZ' => '+213',
                    'YE' => '+967', 'SY' => '+963', 'PS' => '+970', 'TR' => '+90', 'GB' => '+44',
                    'US' => '+1', 'CA' => '+1', 'FR' => '+33', 'DE' => '+49', 'ES' => '+34',
                ];
                $data['country_code'] = $codeMap[strtoupper($country->iso2)] ?? null;
            }
        }

        // Process City
        $cityId = $data['city_id'] ?? null;
        $customCityName = trim((string) ($request->input('custom_city_name') ?? ''));

        if ($cityId === '__custom__' || $customCityName !== '') {
            if ($customCityName !== '') {
                $city = City::firstOrCreate(
                    ['name' => $customCityName],
                    ['country_id' => $data['country_id'] ?? null]
                );
                $data['city_id'] = $city->id;
            } else {
                $data['city_id'] = null;
            }
        } elseif (is_numeric($cityId)) {
            $data['city_id'] = (int) $cityId;
        } elseif (!empty($cityId)) {
            $city = City::firstOrCreate(
                ['name' => $cityId],
                ['country_id' => $data['country_id'] ?? null]
            );
            $data['city_id'] = $city->id;
        } else {
            $data['city_id'] = null;
        }

        // Process Area
        $areaId = $data['area_id'] ?? null;
        $customAreaName = trim((string) ($request->input('custom_area_name') ?? ''));

        if ($areaId === '__custom__' || $customAreaName !== '') {
            if ($customAreaName !== '') {
                $area = Area::firstOrCreate(
                    ['name' => $customAreaName],
                    ['city_id' => $data['city_id'] ?? null]
                );
                $data['area_id'] = $area->id;
            } else {
                $data['area_id'] = null;
            }
        } elseif (is_numeric($areaId)) {
            $data['area_id'] = (int) $areaId;
        } elseif (!empty($areaId)) {
            $area = Area::firstOrCreate(
                ['name' => $areaId],
                ['city_id' => $data['city_id'] ?? null]
            );
            $data['area_id'] = $area->id;
        } else {
            $data['area_id'] = null;
        }

        unset($data['custom_city_name'], $data['custom_area_name']);
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
            'club_member' => $student->club_member, 'club_card_number' => $student->club_card_number,
            'club_card_file' => $student->club_card_file, 'medical_certificate' => $student->medical_certificate,
            'parent_name' => $student->guardian_name, 'parent_phone' => $student->guardian_phone,
            'coach_preference' => $student->coach_preference, 'frequent_attendance' => $student->frequent_attendance,
            'relation_with_child' => $student->relation_with_child, 'referral_source' => $student->referral_source,
            'delivery_service' => $student->delivery_service, 'medical_condition' => $student->medical_condition,
            'start_date' => $student->start_date, 'medical_condition_details' => $student->medical_notes,
            'additional_information' => $student->notes,
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
