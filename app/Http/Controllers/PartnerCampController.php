<?php

namespace App\Http\Controllers;

use App\Models\AcademyCamp;
use App\Models\AcademyCampExpense;
use App\Models\AcademyCampParticipant;
use App\Models\AcademyCampSupervisor;
use App\Models\AcademyStudent;
use App\Models\Coach;
use App\Models\Country;
use App\Models\PartnerExpenseCategory;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerCampController extends Controller
{
    private function resolveAcademyId(): int
    {
        $user = auth('academy')->user();
        if ($user instanceof \App\Models\PartnerUser && $user->academy_id) {
            return (int) $user->academy_id;
        }
        return (int) ($user->id ?? 0);
    }

    private function getAcademyCurrency(int $academyId): array
    {
        $academy = \App\Models\Academies::with('country')->find($academyId);
        return [
            'code' => $academy?->currency_code ?: 'EGP',
            'symbol' => $academy?->currency_symbol ?: 'ج.م',
        ];
    }

    public function index(Request $request)
    {
        $academyId = $this->resolveAcademyId();
        $currency = $this->getAcademyCurrency($academyId);

        $query = AcademyCamp::with(['sport', 'country', 'supervisors.coach'])
            ->withCount('participants')
            ->where('academy_id', $academyId);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title_ar', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('hotel_name', 'like', "%{$search}%")
                  ->orWhere('venue_name', 'like', "%{$search}%");
            });
        }

        $camps = $query->latest()->paginate(12);

        // General Metrics
        $totalCamps = AcademyCamp::where('academy_id', $academyId)->count();
        $domesticCamps = AcademyCamp::where('academy_id', $academyId)->where('type', 'domestic')->count();
        $internationalCamps = AcademyCamp::where('academy_id', $academyId)->where('type', 'international')->count();
        $totalParticipants = AcademyCampParticipant::whereHas('camp', fn ($q) => $q->where('academy_id', $academyId))->count();

        $sports = Sport::whereHas('academies', fn ($q) => $q->where('academies.id', $academyId))->get();

        return view('Academy.pages.camps.index', compact(
            'camps', 'totalCamps', 'domesticCamps', 'internationalCamps',
            'totalParticipants', 'sports', 'currency'
        ));
    }

    public function create()
    {
        $academyId = $this->resolveAcademyId();
        $currency = $this->getAcademyCurrency($academyId);
        $academy = Academies::with('country')->find($academyId);
        $homeCountry = $academy?->country ?: Country::where('iso2', 'EG')->first();
        $sports = Sport::all();
        $countries = Country::all();
        $coaches = Coach::where('academy_id', $academyId)->get();

        return view('Academy.pages.camps.create', compact('sports', 'countries', 'coaches', 'currency', 'homeCountry'));
    }

    public function store(Request $request)
    {
        $academyId = $this->resolveAcademyId();
        $currency = $this->getAcademyCurrency($academyId);

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'type' => 'required|in:domestic,international',
            'sport_id' => 'nullable|exists:sports,id',
            'country_id' => 'nullable|exists:countries,id',
            'city_name' => 'nullable|string|max:255',
            'venue_name' => 'nullable|string|max:255',
            'hotel_name' => 'nullable|string|max:255',
            'starts_on' => 'required|date',
            'ends_on' => 'required|date|after_or_equal:starts_on',
            'registration_deadline' => 'nullable|date|before_or_equal:starts_on',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'visa_required' => 'nullable|boolean',
            'description' => 'nullable|string',
            'room_features' => 'nullable|string',
            'venue_features' => 'nullable|string',
            'program_itinerary' => 'nullable|string',
            'included_services' => 'nullable|array',
            'supervisors' => 'nullable|array',
            'supervisors.*' => 'exists:coaches,id',
        ]);

        $camp = AcademyCamp::create([
            'academy_id' => $academyId,
            'title_ar' => $validated['title_ar'],
            'title_en' => $validated['title_en'] ?? null,
            'type' => $validated['type'],
            'sport_id' => $validated['sport_id'] ?? null,
            'country_id' => $validated['country_id'] ?? null,
            'city_name' => $validated['city_name'] ?? null,
            'venue_name' => $validated['venue_name'] ?? null,
            'hotel_name' => $validated['hotel_name'] ?? null,
            'starts_on' => $validated['starts_on'],
            'ends_on' => $validated['ends_on'],
            'registration_deadline' => $validated['registration_deadline'] ?? null,
            'capacity' => $validated['capacity'],
            'price' => $validated['price'],
            'deposit_amount' => $validated['deposit_amount'] ?? 0,
            'currency_code' => $currency['code'],
            'included_services' => $validated['included_services'] ?? [],
            'visa_required' => (bool) ($validated['visa_required'] ?? false),
            'status' => 'upcoming',
            'description' => $validated['description'] ?? null,
            'room_features' => $validated['room_features'] ?? null,
            'venue_features' => $validated['venue_features'] ?? null,
            'program_itinerary' => $validated['program_itinerary'] ?? null,
            'created_by' => auth('academy')->id(),
        ]);

        if (!empty($validated['supervisors'])) {
            foreach ($validated['supervisors'] as $coachId) {
                AcademyCampSupervisor::create([
                    'academy_camp_id' => $camp->id,
                    'coach_id' => $coachId,
                    'role' => 'coach',
                ]);
            }
        }

        return redirect()->route('academy.camps.show', $camp->id)
            ->with('success', app()->getLocale() === 'ar' ? 'تم إنشاء المعسكر بنجاح' : 'Training camp created successfully');
    }

    public function show($id)
    {
        $academyId = $this->resolveAcademyId();
        $currency = $this->getAcademyCurrency($academyId);

        $camp = AcademyCamp::with(['sport', 'country', 'supervisors.coach', 'participants.student', 'expenses.category'])
            ->where('academy_id', $academyId)
            ->findOrFail($id);

        $students = AcademyStudent::where('academy_id', $academyId)->get();
        $expenseCategories = PartnerExpenseCategory::where(function ($q) use ($academyId) {
            $q->whereNull('academy_id')->orWhere('academy_id', $academyId);
        })->get();

        $totalRevenue = (float) $camp->participants->sum('paid_amount');
        $totalExpenses = (float) $camp->expenses->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;
        $totalFeesExpected = (float) $camp->participants->sum('total_fee');
        $outstandingFees = max(0, $totalFeesExpected - $totalRevenue);

        return view('Academy.pages.camps.show', compact(
            'camp', 'currency', 'students', 'expenseCategories',
            'totalRevenue', 'totalExpenses', 'netProfit', 'outstandingFees'
        ));
    }

    public function edit($id)
    {
        $academyId = $this->resolveAcademyId();
        $currency = $this->getAcademyCurrency($academyId);
        $academy = Academies::with('country')->find($academyId);
        $homeCountry = $academy?->country ?: Country::where('iso2', 'EG')->first();
        $camp = AcademyCamp::with(['supervisors'])->where('academy_id', $academyId)->findOrFail($id);
        $sports = Sport::all();
        $countries = Country::all();
        $coaches = Coach::where('academy_id', $academyId)->get();

        return view('Academy.pages.camps.edit', compact('camp', 'sports', 'countries', 'coaches', 'currency', 'homeCountry'));
    }

    public function update(Request $request, $id)
    {
        $academyId = $this->resolveAcademyId();
        $camp = AcademyCamp::where('academy_id', $academyId)->findOrFail($id);

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'type' => 'required|in:domestic,international',
            'sport_id' => 'nullable|exists:sports,id',
            'country_id' => 'nullable|exists:countries,id',
            'city_name' => 'nullable|string|max:255',
            'venue_name' => 'nullable|string|max:255',
            'hotel_name' => 'nullable|string|max:255',
            'starts_on' => 'required|date',
            'ends_on' => 'required|date|after_or_equal:starts_on',
            'registration_deadline' => 'nullable|date',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'visa_required' => 'nullable|boolean',
            'status' => 'required|in:draft,upcoming,active,completed,cancelled',
            'description' => 'nullable|string',
            'room_features' => 'nullable|string',
            'venue_features' => 'nullable|string',
            'program_itinerary' => 'nullable|string',
            'included_services' => 'nullable|array',
        ]);

        $camp->update([
            'title_ar' => $validated['title_ar'],
            'title_en' => $validated['title_en'] ?? null,
            'type' => $validated['type'],
            'sport_id' => $validated['sport_id'] ?? null,
            'country_id' => $validated['country_id'] ?? null,
            'city_name' => $validated['city_name'] ?? null,
            'venue_name' => $validated['venue_name'] ?? null,
            'hotel_name' => $validated['hotel_name'] ?? null,
            'starts_on' => $validated['starts_on'],
            'ends_on' => $validated['ends_on'],
            'registration_deadline' => $validated['registration_deadline'] ?? null,
            'capacity' => $validated['capacity'],
            'price' => $validated['price'],
            'deposit_amount' => $validated['deposit_amount'] ?? 0,
            'visa_required' => (bool) ($validated['visa_required'] ?? false),
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
            'room_features' => $validated['room_features'] ?? null,
            'venue_features' => $validated['venue_features'] ?? null,
            'program_itinerary' => $validated['program_itinerary'] ?? null,
            'included_services' => $validated['included_services'] ?? [],
        ]);

        return redirect()->back()->with('success', app()->getLocale() === 'ar' ? 'تم تحديث بيانات المعسكر بنجاح' : 'Camp details updated successfully');
    }

    public function destroy($id)
    {
        $academyId = $this->resolveAcademyId();
        $camp = AcademyCamp::where('academy_id', $academyId)->findOrFail($id);
        $camp->delete();

        return redirect()->route('academy.camps.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم حذف المعسكر بنجاح' : 'Camp deleted successfully');
    }

    // Participant Management
    public function storeParticipant(Request $request, $campId)
    {
        $academyId = $this->resolveAcademyId();
        $camp = AcademyCamp::where('academy_id', $academyId)->findOrFail($campId);

        $validated = $request->validate([
            'academy_student_id' => 'nullable|exists:academy_students,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'emergency_phone' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:100',
            'passport_expiry' => 'nullable|date',
            'visa_status' => 'required|in:not_required,pending,issued,rejected',
            'tshirt_size' => 'nullable|string|max:20',
            'medical_notes' => 'nullable|string',
            'room_number' => 'nullable|string|max:50',
            'total_fee' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'status' => 'required|in:registered,confirmed,attended,cancelled',
            'notes' => 'nullable|string',
        ]);

        $paymentStatus = 'unpaid';
        if ($validated['paid_amount'] >= $validated['total_fee'] && $validated['total_fee'] > 0) {
            $paymentStatus = 'paid';
        } elseif ($validated['paid_amount'] > 0) {
            $paymentStatus = 'partial';
        }

        AcademyCampParticipant::create([
            'academy_camp_id' => $camp->id,
            'academy_student_id' => $validated['academy_student_id'] ?? null,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'emergency_phone' => $validated['emergency_phone'] ?? null,
            'passport_number' => $validated['passport_number'] ?? null,
            'passport_expiry' => $validated['passport_expiry'] ?? null,
            'visa_status' => $validated['visa_status'],
            'tshirt_size' => $validated['tshirt_size'] ?? null,
            'medical_notes' => $validated['medical_notes'] ?? null,
            'room_number' => $validated['room_number'] ?? null,
            'total_fee' => $validated['total_fee'],
            'paid_amount' => $validated['paid_amount'],
            'payment_status' => $paymentStatus,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', app()->getLocale() === 'ar' ? 'تم تسجيل المشترك في المعسكر بنجاح' : 'Participant registered successfully');
    }

    public function destroyParticipant($campId, $participantId)
    {
        $academyId = $this->resolveAcademyId();
        $camp = AcademyCamp::where('academy_id', $academyId)->findOrFail($campId);
        $participant = AcademyCampParticipant::where('academy_camp_id', $camp->id)->findOrFail($participantId);
        $participant->delete();

        return redirect()->back()->with('success', app()->getLocale() === 'ar' ? 'تم حذف المشترك من المعسكر' : 'Participant removed successfully');
    }

    // Expense Management
    public function storeExpense(Request $request, $campId)
    {
        $academyId = $this->resolveAcademyId();
        $camp = AcademyCamp::where('academy_id', $academyId)->findOrFail($campId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:partner_expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        AcademyCampExpense::create([
            'academy_camp_id' => $camp->id,
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'currency_code' => $camp->currency_code,
            'expense_date' => $validated['expense_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', app()->getLocale() === 'ar' ? 'تم تسجيل مصروف المعسكر بنجاح' : 'Camp expense recorded successfully');
    }

    public function destroyExpense($campId, $expenseId)
    {
        $academyId = $this->resolveAcademyId();
        $camp = AcademyCamp::where('academy_id', $academyId)->findOrFail($campId);
        $expense = AcademyCampExpense::where('academy_camp_id', $camp->id)->findOrFail($expenseId);
        $expense->delete();

        return redirect()->back()->with('success', app()->getLocale() === 'ar' ? 'تم حذف المصروف بنجاح' : 'Expense deleted successfully');
    }

    // Export CSV Roster
    public function exportRoster($campId)
    {
        $academyId = $this->resolveAcademyId();
        $camp = AcademyCamp::with(['participants'])->where('academy_id', $academyId)->findOrFail($campId);

        $filename = 'camp_roster_' . $camp->id . '_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($camp) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($file, [
                '#', 'اسم المشارك', 'الهاتف', 'هاتف الطوارئ', 'رقم الجواز', 'تاريخ انتهاء الجواز',
                'حالة التأشيرة', 'رقم الغرفة', 'مقاس الزي', 'إجمالي الرسوم', 'المدفوع', 'حالة الدفع', 'الملاحظات الطبية'
            ]);

            foreach ($camp->participants as $index => $p) {
                fputcsv($file, [
                    $index + 1,
                    $p->name,
                    $p->phone,
                    $p->emergency_phone ?: '-',
                    $p->passport_number ?: '-',
                    $p->passport_expiry ? $p->passport_expiry->format('Y-m-d') : '-',
                    $p->visa_status,
                    $p->room_number ?: '-',
                    $p->tshirt_size ?: '-',
                    $p->total_fee,
                    $p->paid_amount,
                    $p->payment_status,
                    $p->medical_notes ?: '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getCountryCities($countryId)
    {
        $country = Country::find($countryId);
        if (!$country) {
            return response()->json([]);
        }

        try {
            $dbCities = City::where('country_id', $countryId)
                ->orWhere('county_id', $countryId)
                ->get();

            if ($dbCities->isNotEmpty()) {
                $result = $dbCities->map(fn ($city) => [
                    'id' => $city->name,
                    'name' => $city->name,
                ]);
                return response()->json($result);
            }
        } catch (\Throwable $e) {
            // Ignore missing column fallback to dictionary
        }

        $iso = strtoupper($country->iso2 ?? '');
        $citiesMap = [
            'EG' => ['القاهرة', 'الإسكندرية', 'شرم الشيخ', 'الغردقة', 'الأقصر', 'أسوان', 'الجونة', 'مرسى علم', 'الساحل الشمالي', 'بورسعيد', 'المنصورة', 'طنطا'],
            'SA' => ['الرياض', 'جدة', 'الدمام', 'مكة المكرمة', 'المدينة المنورة', 'الخبر', 'أبها', 'تبوك', 'الطائف'],
            'AE' => ['دبي', 'أبوظبي', 'الشارقة', 'عجمان', 'رأس الخيمة', 'العين', 'الفجيرة'],
            'QA' => ['الدوحة', 'الريان', 'الوكرة', 'الخور', 'لوسيل'],
            'KW' => ['مدينة الكويت', 'حولي', 'السالمية', 'الأحمدي', 'الفروانية'],
            'OM' => ['مسقط', 'صلالة', 'صحار', 'نزوى'],
            'BH' => ['المنامة', 'المحرق', 'الرفاع', 'سترة'],
            'JO' => ['عمان', 'العقبة', 'إربد', 'الزرقاء'],
            'LB' => ['بيروت', 'طرابلس', 'صيدا', 'جبيل'],
            'ES' => ['مدريد', 'برشلونة', 'مالقة', 'فالنسيا', 'إشبيلية', 'بيلباو', 'غرناطة', 'ماربيا'],
            'TR' => ['إسطنبول', 'أنطاليا', 'أنقرة', 'بورصة', 'إزمير', 'بودروم', 'طرابزون'],
            'GB' => ['لندن', 'مانشستر', 'ليفربول', 'برمنغهام', 'غلاسكو', 'أدنبرة'],
            'FR' => ['باريس', 'مارسيليا', 'ليون', 'نيس', 'تولوز', 'بوردو'],
            'DE' => ['برلين', 'ميونيخ', 'فرانکفورت', 'هامبورغ', 'كولونيا', 'دورتموند'],
            'IT' => ['روما', 'ميلانو', 'فلورنسا', 'تورينو', 'فينيسيا (البندقية)', 'نابولي'],
            'NL' => ['أمستردام', 'روتردام', 'لاهاي', 'أوتريخت'],
            'PT' => ['لشبونة', 'بورتو', 'فارو', 'براغا'],
            'GR' => ['أثينا', 'سالونيك', 'هركليون (كريت)', 'رودس'],
            'US' => ['نيويورك', 'لوس أنجلوس', 'ميامي', 'أورلاندو', 'شيكاغو', 'واشنطن', 'سان فرانسيسكو', 'دالاس'],
            'CA' => ['تورونتو', 'مونتريال', 'فانكوفر', 'أوتاوا', 'كالغاري'],
            'RU' => ['موسكو', 'سان بطرسبرغ', 'سوتشي', 'كازان'],
            'JP' => ['طوكيو', 'أوساكا', 'كيوتو', 'يوكوهاما', 'سابورو'],
            'BR' => ['ريو دي جانيرو', 'ساو باولو', 'برازيليا', 'سالفادور'],
            'AR' => ['بوينس آيرس', 'كوردوبا', 'روزاريو'],
            'MA' => ['الدار البيضاء', 'الرباط', 'مراكش', 'طنجة', 'أغادير', 'فاس'],
            'TN' => ['تونس العاصمة', 'سوسة', 'الصفاقس', 'الحمامات'],
            'DZ' => ['الجزائر العاصمة', 'وهران', 'قسنطينة', 'عنابة'],
            'CY' => ['لارنكا', 'ليماسول', 'نيقوسيا', 'بافوس'],
            'CH' => ['جنيف', 'زيورخ', 'بازل', 'برن', 'لوزان'],
            'AT' => ['فيينا', 'سالزبورغ', 'إنسبروك'],
            'GE' => ['تبليسي', 'باتومي', 'كوبوليتي'],
            'AU' => ['سيدني', 'ملبورن', 'بريزبن', 'بيرث'],
        ];

        $cities = $citiesMap[$iso] ?? [];
        $result = collect($cities)->map(fn ($cityName) => [
            'id' => $cityName,
            'name' => $cityName,
        ]);

        return response()->json($result);
    }
}
