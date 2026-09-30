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
use App\Models\PartnerUser;
use App\Models\User;
use App\Support\MembershipCode;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorSVG;

class AcademyStudentController extends Controller
{
    private function getAcademyId(): int
    {
        $user = auth('academy')->user();
        if ($user instanceof PartnerUser) {
            return (int) $user->academy_id;
        }
        return (int) ($user?->id ?? auth('academy')->id());
    }

    public function index(Request $request)
    {
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $service = new \App\Services\PartnerAccessService($authUser);

        $query = $service->scopeStudents(AcademyStudent::with('user'));

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('guardian_name', 'like', "%{$search}%")
                  ->orWhere('guardian_phone', 'like', "%{$search}%")
                  ->orWhere('membership_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        if ($request->filled('special_care')) {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('medical_condition', 'yes')->whereNotNull('injury_type')->where('injury_type', '!=', '');
                })->orWhere(function ($sub) {
                    $sub->where('has_allergy', 'yes')->whereNotNull('allergy_type')->where('allergy_type', '!=', '');
                })->orWhere(function ($sub) {
                    $sub->whereNotNull('medical_notes')->where('medical_notes', '!=', '');
                });
            });
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'oldest' => $query->oldest('id'),
            default => $query->latest('id'),
        };

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 50, 100], true)) {
            $perPage = 20;
        }

        $students = $query->paginate($perPage)->withQueryString();

        $baseQuery = $service->scopeStudents(AcademyStudent::query());
        $metrics = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('status', 'active')->count(),
            'inactive' => (clone $baseQuery)->where('status', 'inactive')->count(),
            'male' => (clone $baseQuery)->where('gender', 'male')->count(),
            'female' => (clone $baseQuery)->where('gender', 'female')->count(),
            'special_care' => (clone $baseQuery)->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('medical_condition', 'yes')->whereNotNull('injury_type')->where('injury_type', '!=', '');
                })->orWhere(function ($sub) {
                    $sub->where('has_allergy', 'yes')->whereNotNull('allergy_type')->where('allergy_type', '!=', '');
                })->orWhere(function ($sub) {
                    $sub->whereNotNull('medical_notes')->where('medical_notes', '!=', '');
                });
            })->count(),
        ];

        return view('Academy.pages.students.index', compact('students', 'metrics'));
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
            'bookings.training', 'bookings.invoice',
        ]);
        $subscriptions = $student->subscriptions->sortByDesc('starts_on');
        $subscription = $subscriptions->first();
        $attendance = $student->attendanceRecords->groupBy('status')->map->count();
        $totalPaid = (float) $student->subscriptions->sum(fn ($item) => $item->payments->sum('amount'));
        $totalDue = (float) $student->subscriptions->sum('amount');
        $totalDiscount = (float) $student->subscriptions->sum('discount_amount');

        foreach ($student->bookings as $booking) {
            $inv = $booking->invoice;
            $totalDue += (float) ($inv?->amount ?? $booking->price ?? 0);
            $totalPaid += (float) ($inv?->paid_amount ?? 0);
        }

        $totalRemaining = max(0, round($totalDue - $totalPaid - $totalDiscount, 2));
        $remainingDays = $subscription?->ends_on && $subscription->ends_on->isFuture()
            ? now()->startOfDay()->diffInDays($subscription->ends_on)
            : 0;

        $subscriptionsList = $subscriptions->map(function ($sub) {
            $subPaid = (float) $sub->payments->sum('amount');
            $subDisc = (float) ($sub->discount_amount ?? 0);
            $subTotal = (float) $sub->amount;
            $subRem = max(0, round($subTotal - $subPaid - $subDisc, 2));

            return [
                'id' => $sub->id,
                'group' => $sub->group?->name ?: '-',
                'starts_on' => $sub->starts_on?->format('Y-m-d'),
                'ends_on' => $sub->ends_on?->format('Y-m-d'),
                'amount' => $subTotal,
                'paid' => $subPaid,
                'discount' => $subDisc,
                'discount_reason' => $sub->discount_reason,
                'discount_approved_by' => $sub->discount_approved_by,
                'remaining' => $subRem,
                'status' => $sub->status,
                'payment_status' => $sub->payment_status,
                'invoice_url' => route('academy.invoices.students.print', ['subscription' => $sub, 'paper' => 'a4']),
                'payments' => $sub->payments->sortByDesc('paid_at')->map(fn ($p) => [
                    'amount' => (float) $p->amount,
                    'paid_at' => optional($p->paid_at)->format('Y-m-d'),
                    'method' => $p->method_label ?: $p->method,
                    'reference' => $p->reference,
                    'notes' => $p->notes,
                ])->values(),
            ];
        });

        $bookingItems = $student->bookings->sortByDesc('created_at')->map(function ($booking) {
            $inv = $booking->invoice;
            $amount = (float) ($inv?->amount ?? $booking->price ?? 0);
            $paid = (float) ($inv?->paid_amount ?? 0);
            $rem = max(0, round($amount - $paid, 2));
            $paymentStatus = ($rem == 0 && $amount > 0) ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
            $createdDate = $booking->created_at ? $booking->created_at->format('Y-m-d') : '-';
            $endDate = $booking->training?->end_date ? \Illuminate\Support\Carbon::parse($booking->training->end_date)->format('Y-m-d') : '-';

            return [
                'id' => 'B-' . $booking->id,
                'group' => $booking->training?->name ?: 'حجز مباشر / باقة عضوية',
                'starts_on' => $createdDate,
                'ends_on' => $endDate,
                'amount' => $amount,
                'paid' => $paid,
                'discount' => 0,
                'discount_reason' => null,
                'discount_approved_by' => null,
                'remaining' => $rem,
                'status' => 'active',
                'payment_status' => $paymentStatus,
                'invoice_url' => route('academy.report.offline-joins'),
                'payments' => ($inv && $paid > 0) ? [[
                    'amount' => $paid,
                    'paid_at' => $inv->created_at ? $inv->created_at->format('Y-m-d') : $createdDate,
                    'method' => $inv->payment_method_label ?: $inv->payment_method ?: 'كاش',
                    'reference' => $inv->order_number,
                    'notes' => null,
                ]] : [],
            ];
        });

        $subscriptionsList = $subscriptionsList->concat($bookingItems)->values();

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
            'country_name' => $student->country?->name ?: $student->user?->country?->name,
            'city_name' => $student->city?->name ?: $student->user?->city?->name,
            'area_name' => $student->area?->name ?: $student->user?->area?->name,
            'school_name' => $student->school_name,
            'club_member' => $student->club_member,
            'previous_club_name' => $student->previous_club_name,
            'child_type' => $student->child_type,
            'referral_source' => $student->referral_source,
            'groups' => $student->groups->pluck('name')->merge($student->bookings->map(fn($b) => $b->training?->name))->filter()->unique()->values(),
            'medical_condition' => $student->medical_condition,
            'injury_type' => $student->injury_type,
            'has_allergy' => $student->has_allergy,
            'allergy_type' => $student->allergy_type,
            'has_special_care' => $student->hasSpecialCare(),
            'special_care_summary' => $student->specialCareSummary(),
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
                'discount' => (float) ($subscription->discount_amount ?? 0),
                'discount_reason' => $subscription->discount_reason,
                'discount_approved_by' => $subscription->discount_approved_by,
                'remaining' => $subscription->remaining_amount,
                'status' => $subscription->status,
                'payment_status' => $subscription->payment_status,
                'last_payment_method' => $subscription->payments->sortByDesc('paid_at')->first()?->method_label,
            ] : ($student->bookings->isNotEmpty() ? [
                'id' => 'B-' . $student->bookings->last()->id,
                'group' => $student->bookings->last()->training?->name,
                'starts_on' => $student->bookings->last()->created_at?->format('Y-m-d'),
                'ends_on' => $student->bookings->last()->training?->end_date ? \Illuminate\Support\Carbon::parse($student->bookings->last()->training->end_date)->format('Y-m-d') : null,
                'duration_days' => null,
                'remaining_days' => 0,
                'amount' => (float) ($student->bookings->last()->invoice?->amount ?? $student->bookings->last()->price),
                'paid' => (float) ($student->bookings->last()->invoice?->paid_amount ?? 0),
                'discount' => 0,
                'discount_reason' => null,
                'discount_approved_by' => null,
                'remaining' => max(0, round((float) ($student->bookings->last()->invoice?->amount ?? $student->bookings->last()->price) - (float) ($student->bookings->last()->invoice?->paid_amount ?? 0), 2)),
                'status' => 'active',
                'payment_status' => ((float) ($student->bookings->last()->invoice?->paid_amount ?? 0) >= (float) ($student->bookings->last()->invoice?->amount ?? $student->bookings->last()->price)) ? 'paid' : ((float) ($student->bookings->last()->invoice?->paid_amount ?? 0) > 0 ? 'partial' : 'unpaid'),
                'last_payment_method' => $student->bookings->last()->invoice?->payment_method_label ?: $student->bookings->last()->invoice?->payment_method,
            ] : null),
            'all_subscriptions' => $subscriptionsList,
            'financials' => [
                'total_due' => $totalDue,
                'total_paid' => $totalPaid,
                'total_discount' => $totalDiscount,
                'total_remaining' => $totalRemaining,
            ],
            'attendance' => [
                'present' => (int) $attendance->get('present', 0),
                'late' => (int) $attendance->get('late', 0),
                'absent' => (int) $attendance->get('absent', 0),
                'excused' => (int) $attendance->get('excused', 0),
                'total' => $student->attendanceRecords->count(),
            ],
            'recent_attendance' => $student->attendanceRecords->sortByDesc(fn ($record) => $record->session?->session_date)->take(8)->map(fn ($record) => [
                'date' => $record->session?->session_date?->format('Y-m-d'),
                'group' => $record->session?->group?->name,
                'status' => $record->status,
                'check_in' => $record->check_in_at,
            ])->values(),
            'edit_url' => route('academy.students.edit', $student),
            'card_url' => route('academy.students.card', $student),
        ]);
    }

    public function card(Request $request, AcademyStudent $student)
    {
        $this->authorizeStudent($student);

        if ($request->has('bulk')) {
            return $this->cardsPrint($request);
        }

        $student->load(['academy', 'user', 'groups.sport', 'subscriptions.group', 'subscriptions.payments']);
        $membershipCode = MembershipCode::make($student);
        $subscription = $student->subscriptions->sortByDesc('starts_on')->first();
        $qrResult = (new SvgWriter())->write(new QrCode(data: $membershipCode, size: 280, margin: 8));
        $barcode = (new BarcodeGeneratorSVG())->getBarcode($membershipCode, BarcodeGeneratorSVG::TYPE_CODE_128, 1.55, 54);

        $student->computed_membership_code = $membershipCode;
        $student->computed_subscription = $subscription;
        $student->computed_qr = $qrResult->getDataUri();
        $student->computed_barcode = $barcode;

        $layout = (int) $request->input('layout', 1);
        if (!in_array($layout, [1, 2, 4, 8])) {
            $layout = 1;
        }

        return view('Academy.pages.students.card', [
            'student' => $student,
            'students' => collect([$student]),
            'academy' => $student->academy ?: Academies::find($this->getAcademyId()),
            'subscription' => $subscription,
            'membershipCode' => $membershipCode,
            'qrDataUri' => $qrResult->getDataUri(),
            'barcodeSvg' => $barcode,
            'layout' => $layout,
            'isBulk' => false,
        ]);
    }

    public function cardsPrint(Request $request)
    {
        $academy = Academies::find($this->getAcademyId());
        $service = new \App\Services\PartnerAccessService(auth('academy')->user());
        $query = $service->scopeStudents(
            AcademyStudent::with(['academy', 'user', 'groups.sport', 'subscriptions.group', 'subscriptions.payments'])
        );

        if ($request->filled('ids')) {
            $ids = is_array($request->ids) ? $request->ids : explode(',', (string) $request->ids);
            $query->whereIn('id', array_filter($ids));
        } else {
            if ($search = trim((string) $request->input('search'))) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('membership_number', 'like', "%{$search}%");
                });
            }
            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }
            if ($gender = $request->input('gender')) {
                $query->where('gender', $gender);
            }
        }

        $students = $query->orderBy('name')->limit(300)->get();

        $svgWriter = new SvgWriter();
        $barcodeGen = new BarcodeGeneratorSVG();

        foreach ($students as $st) {
            $code = MembershipCode::make($st);
            $sub = $st->subscriptions->sortByDesc('starts_on')->first();
            $qrResult = $svgWriter->write(new QrCode(data: $code, size: 260, margin: 6));
            $barcode = $barcodeGen->getBarcode($code, BarcodeGeneratorSVG::TYPE_CODE_128, 1.4, 46);

            $st->computed_membership_code = $code;
            $st->computed_subscription = $sub;
            $st->computed_qr = $qrResult->getDataUri();
            $st->computed_barcode = $barcode;
        }

        $layout = (int) $request->input('layout', 4);
        if (!in_array($layout, [1, 2, 4, 8])) {
            $layout = 4;
        }

        return view('Academy.pages.students.card', [
            'students' => $students,
            'student' => $students->first(),
            'academy' => $academy,
            'subscription' => $students->first()?->computed_subscription,
            'membershipCode' => $students->first()?->computed_membership_code,
            'qrDataUri' => $students->first()?->computed_qr,
            'barcodeSvg' => $students->first()?->computed_barcode,
            'layout' => $layout,
            'isBulk' => true,
        ]);
    }

    public function print(Request $request)
    {
        $academy = Academies::find($this->getAcademyId());
        $service = new \App\Services\PartnerAccessService(auth('academy')->user());
        $query = $service->scopeStudents(
            AcademyStudent::with(['user', 'groups.sport', 'subscriptions'])
        );

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('membership_number', 'like', "%{$search}%");
            });
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        $students = $query->orderBy('name')->get();

        return view('Academy.pages.students.print', [
            'academy' => $academy,
            'students' => $students,
        ]);
    }

    public function export()
    {
        $academy = Academies::find($this->getAcademyId());
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

        $import = new AcademyStudentsImport($this->getAcademyId());
        Excel::import($import, $request->file('students_file'));

        session()->flash(
            'success',
            trans('admin.student_management.students_imported', [
                'created' => $import->created,
                'updated' => $import->updated,
                'skipped' => $import->skipped,
            ])
        );

        session()->flash('import_summary', [
            'total' => $import->totalRows,
            'created' => $import->created,
            'updated' => $import->updated,
            'skipped' => $import->skipped,
            'errors' => $import->errors,
        ]);

        return back();
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->processLocationData($data, $request);
        $data['academy_id'] = $this->getAcademyId();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('students/avatars', 'public');
            $data['image'] = 'storage/' . $path;
        }
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

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('students/avatars', 'public');
            $data['image'] = 'storage/' . $path;
        }
        if ($request->hasFile('medical_certificate')) {
            $path = $request->file('medical_certificate')->store('students/medical_certificates', 'public');
            $data['medical_certificate'] = 'storage/' . $path;
        }
        if ($request->hasFile('club_card_file')) {
            $path = $request->file('club_card_file')->store('students/club_cards', 'public');
            $data['club_card_file'] = 'storage/' . $path;
        }

        // Strict tenant isolation: never allow changing academy_id
        $data['academy_id'] = $this->getAcademyId();

        $student->update($data);
        $this->syncLinkedUser($student);

        session()->flash('success', trans('admin.student_management.student_updated'));
        return to_route('academy.students.index');
    }

    public function uploadAvatar(Request $request, AcademyStudent $student)
    {
        $this->authorizeStudent($student);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $path = $request->file('image')->store('students/avatars', 'public');
        $imageUrl = 'storage/' . $path;

        $student->update(['image' => $imageUrl]);
        if ($student->user_id) {
            $student->user()->update(['image' => $imageUrl]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'avatar_url' => asset($imageUrl),
                'message' => trans('admin.student_management.avatar_updated_success'),
            ]);
        }

        session()->flash('success', trans('admin.student_management.avatar_updated_success'));
        return back();
    }

    public function destroy(AcademyStudent $student)
    {
        $this->authorizeStudent($student);
        $student->delete();

        session()->flash('success', trans('admin.student_management.student_deleted'));
        return to_route('academy.students.index');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $academyId = $this->getAcademyId();
        $count = AcademyStudent::where('academy_id', $academyId)
            ->whereIn('id', $validated['ids'])
            ->delete();

        session()->flash('success', trans('admin.student_management.bulk_deleted_success', ['count' => $count]));
        return to_route('academy.students.index');
    }

    public function bulkStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $academyId = $this->getAcademyId();
        $count = AcademyStudent::where('academy_id', $academyId)
            ->whereIn('id', $validated['ids'])
            ->update(['status' => $validated['status']]);

        session()->flash('success', trans('admin.student_management.bulk_status_success', ['count' => $count]));
        return to_route('academy.students.index');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive,lead,suspended'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable'],
            'area_id' => ['nullable'],
            'custom_city_name' => ['nullable', 'string', 'max:255'],
            'custom_area_name' => ['nullable', 'string', 'max:255'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'child_type' => ['nullable', 'string', 'max:100'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'club_member' => ['nullable', 'string', 'max:100'],
            'previous_club_name' => ['nullable', 'string', 'max:255'],
            'club_card_number' => ['nullable', 'string', 'max:100'],
            'coach_preference' => ['nullable', 'string', 'max:255'],
            'frequent_attendance' => ['nullable', 'string', 'max:255'],
            'relation_with_child' => ['nullable', 'string', 'max:100'],
            'referral_source' => ['nullable', 'string', 'max:255'],
            'delivery_service' => ['nullable', 'string', 'max:255'],
            'medical_condition' => ['nullable', 'string', 'max:255'],
            'injury_type' => ['nullable', 'string', 'max:255'],
            'has_allergy' => ['nullable', 'string', 'max:50'],
            'allergy_type' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'medical_notes' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'medical_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'club_card_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        return $validated;
    }

    private function processLocationData(array &$data, Request $request): void
    {
        $cityId = $request->input('city_id');
        if ($cityId === '__custom__' || blank($cityId)) {
            $cityId = $request->input('custom_city_name');
        }

        $areaId = $request->input('area_id');
        if ($areaId === '__custom__' || blank($areaId)) {
            $areaId = $request->input('custom_area_name');
        }

        $countryId = !empty($data['country_id']) ? (int) $data['country_id'] : 4;

        if (is_numeric($cityId) && (int) $cityId > 0) {
            $data['city_id'] = (int) $cityId;
        } elseif (!empty($cityId)) {
            // Find existing city by country first, matching translatable JSON or raw string
            $city = City::where('country_id', $countryId)
                ->where(function ($q) use ($cityId) {
                    $q->where('name->ar', $cityId)
                      ->orWhere('name->en', $cityId)
                      ->orWhere('name', $cityId)
                      ->orWhere('name', 'LIKE', '%"' . $cityId . '"%');
                })->first();

            // Fallback: check across all countries
            if (!$city) {
                $city = City::where(function ($q) use ($cityId) {
                    $q->where('name->ar', $cityId)
                      ->orWhere('name->en', $cityId)
                      ->orWhere('name', $cityId)
                      ->orWhere('name', 'LIKE', '%"' . $cityId . '"%');
                })->first();
            }

            // If not exists, create with translatable name and country_id
            if (!$city) {
                $city = City::create([
                    'name' => [
                        'ar' => $cityId,
                        'en' => $cityId,
                    ],
                    'country_id' => $countryId,
                ]);
            }
            $data['city_id'] = $city->id;
        } else {
            $data['city_id'] = null;
        }

        if (is_numeric($areaId) && (int) $areaId > 0) {
            $data['area_id'] = (int) $areaId;
        } elseif (!empty($areaId)) {
            $cityIdForArea = $data['city_id'] ?? null;
            $area = null;

            if ($cityIdForArea) {
                $area = Area::where('city_id', $cityIdForArea)
                    ->where(function ($q) use ($areaId) {
                        $q->where('name->ar', $areaId)
                          ->orWhere('name->en', $areaId)
                          ->orWhere('name', $areaId)
                          ->orWhere('name', 'LIKE', '%"' . $areaId . '"%');
                    })->first();
            }

            if (!$area) {
                $area = Area::where(function ($q) use ($areaId) {
                    $q->where('name->ar', $areaId)
                      ->orWhere('name->en', $areaId)
                      ->orWhere('name', $areaId)
                      ->orWhere('name', 'LIKE', '%"' . $areaId . '"%');
                })->first();
            }

            if (!$area) {
                $area = Area::create([
                    'name' => [
                        'ar' => $areaId,
                        'en' => $areaId,
                    ],
                    'city_id' => $cityIdForArea ?: 1,
                ]);
            }
            $data['area_id'] = $area->id;
        } else {
            $data['area_id'] = null;
        }

        unset($data['custom_city_name'], $data['custom_area_name']);
    }

    private function syncLinkedUser(AcademyStudent $student): void
    {
        if (! $student->user_id) return;
        $userCols = ['name', 'phone', 'gender', 'birth_date', 'country_id', 'city_id', 'area_id', 'medical_certificate', 'club_card_number', 'club_card_file'];
        $userPayload = [
            'name' => $student->name,
            'phone' => $student->phone,
            'gender' => $student->gender,
            'birth_date' => $student->birth_date,
            'country_id' => $student->country_id,
            'city_id' => $student->city_id,
            'area_id' => $student->area_id,
            'medical_certificate' => $student->medical_certificate,
            'club_card_number' => $student->club_card_number,
            'club_card_file' => $student->club_card_file,
        ];

        if ($student->image) {
            $userPayload['image'] = $student->image;
        }

        $student->user()->update($userPayload);
    }

    private function authorizeStudent(AcademyStudent $student): void
    {
        abort_unless((int) $student->academy_id === $this->getAcademyId(), 404);
    }

    private function studentsQuery()
    {
        return AcademyStudent::with('user')
            ->where('academy_id', $this->getAcademyId())
            ->orderBy('name');
    }
}
