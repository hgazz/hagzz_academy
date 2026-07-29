<?php

namespace App\Http\Controllers;

use App\DataTables\TrainingDataTable;
use App\Exports\TrainingsExport;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\Training\TrainingRequest;
use App\Http\Traits\CoacheTrait;
use App\Models\Academies;
use App\Models\AcademyStudent;
use App\Models\Address;
use App\Models\Area;
use App\Models\City;
use App\Models\Coach;
use App\Models\CoachSport;
use App\Models\Country;
use App\Models\Follow;
use App\Models\Invoice;
use App\Models\Join;
use App\Models\Training;
use App\Models\User;
use App\Services\Firebase\NotificationService;
use App\Services\TranslatableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TrainingController extends Controller
{
    use CoacheTrait;
   private $trainingModel, $addressModel, $coachModel;
   public function __construct(Training $training, Address $address, Coach $coach)
   {
       $this->trainingModel = $training;
       $this->addressModel = $address;
       $this->coachModel = $coach;
   }

   public function index(TrainingDataTable $dataTable)
   {
        return $dataTable->render('Academy.pages.training.index');
   }
    public function create()
    {
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $service = new PartnerAccessService($authUser);

        $sports = $authUser->getAccessibleSports();
        $academyCoaches = $service->scopeCoaches(
            $this->coachModel::where('active', 1)
        )->get();

        $addressQuery = $this->addressModel::query();
        if (!$authUser->is_owner && !$authUser->access_all_branches) {
            $branchIds = $service->accessibleBranchIds() ?? [];
            $addressQuery->whereIn('academy_id', $branchIds);
        } else {
            $addressQuery->where('academy_id', $authUser->academy_id);
        }
        $addresses = $addressQuery->get();

        return view('Academy.pages.training.create', compact('academyCoaches', 'addresses', 'sports'));
    }

    public function getCoachesBySports($id)
    {
        $coaches = CoachSport::where('sport_id', $id)
            ->whereHas('coach', function ($query)  {
                // Filter coaches by the academy of the authenticated user
                $query->select('id', 'name')
                ->where('academy_id', auth('academy')->id());
            })
            ->with(['coach' => function ($query) {
                $query->select('id', 'name'); // Limit fields to avoid unnecessary data
            }])
            ->get()
            ->pluck('coach')
            ->unique(); // Remove duplicate coaches (if any)

        // Return a structured JSON response
        return response()->json([
            'coaches' => $coaches
        ]);
    }
    public function store(TrainingRequest $request)
    {
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        if (!$authUser->canAccessSport($request->sport_id)) {
            abort(403, 'غير مصرح لك بإضافة تدريب لهذه الرياضة');
        }

        \DB::transaction(function() use ($request){
            $translatable = TranslatableService::generateTranslatableFields($this->trainingModel::getTranslatableFields() , $request->validated());
            $this->trainingModel->create(array_merge($translatable,[
                'start_date'=> $request->start_date,
                'end_date'=> $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'coach_id'=> $request->coach_id,
                'price'=> $request->price,
                'max_players'=> $request->max_players,
                'level'=> $request->level,
                'gender' => $request->gender,
                'age_group' => $request->age_group,
                'address_id' => $request->address_id,
                'academy_id' => auth()->id(),
                'sport_id' => $request->sport_id,
                'discount_price' => $request->discount_price,
                'classes_days' => $request->classes_days,
                'color' => $this->normalizeTrainingColor($request->color),
               'classes_number' => $request->classes_number
            ]));
        });
        session()->flash('success',trans('admin.training.created_successfully'));
        return to_route('academy.training.index');
    }

    public function edit(Training $training)
    {
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        abort_unless((int) $training->academy_id === (int) $authUser->academy_id, 404);

        if (!$authUser->canAccessSport($training->sport_id)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الرياضة');
        }

        $service = new PartnerAccessService($authUser);
        $academyCoaches = $service->scopeCoaches(
            $this->coachModel::where(function ($query) use ($training) {
                $query->where('active', 1)
                    ->orWhere('id', $training->coach_id);
            })
        )->get(['coaches.id', 'coaches.name']);

        $sports = $authUser->getAccessibleSports();

        $addressQuery = $this->addressModel::query();
        if (!$authUser->is_owner && !$authUser->access_all_branches) {
            $branchIds = $service->accessibleBranchIds() ?? [];
            $addressQuery->whereIn('academy_id', $branchIds);
        } else {
            $addressQuery->where('academy_id', $authUser->academy_id);
        }
        $addresses = $addressQuery->get();

        return view('Academy.pages.training.edit', compact('academyCoaches', 'sports', 'training', 'addresses'));
    }

    public function update(Training $training , TrainingRequest $request)
    {
        abort_unless((int) $training->academy_id === (int) auth('academy')->id(), 404);

        try {
            DB::transaction(function () use ($request, $training) {
                $originalStartDate = $training->start_date;
                $translatable = TranslatableService::generateTranslatableFields($this->trainingModel::getTranslatableFields(), $request->validated());
                $training->update(array_merge($translatable, [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'coach_id' => $request->coach_id,
                    'price' => $request->price,
                    'max_players'=> $request->max_players,
                    'level'=> $request->level,
                    'gender' => $request->gender,
                    'age_group' => $request->age_group,
                    'address_id' => $request->address_id,
                    'sport_id' => $request->sport_id,
                    'discount_price' => $request->discount_price,
                    'classes_days' => $request->classes_days,
               'color' => $this->normalizeTrainingColor($request->color),
              'classes_number' => $request->classes_number
                ]));
                $details = [
                    'training_id' => $training->id,
                    'longitude' => $training->longitude,
                    'latitude' => $training->latitude,
                    'academy_name' => auth('academy')->user()->commercial_name
                ];
                //notifications to users
                if ($originalStartDate != $training->start_date) {
                    $title = 'Booking Rescheduled';
                    $body = 'Your booking with ' . $training->academy->getTranslation('commercial_name', 'en') . ' is rescheduled.please check the new dates';
                    $joins = Join::where('training_id', $training->id)->get();
                    $data = [
                        'title' => $title,
                        'body' => $body,
                        'image' => auth('academy')->user()->image,
                        'details' => $details,
                        "id" => $training->id,
                        'page' => 'details',
                        'class_id' => null
                    ];
                    $joins->map(function ($join) use ($data) {
                        NotificationService::firebaseNotification($data,$join->user->fcm_token,);
                    });

                }
            });
            session()->flash('success',trans('admin.training.updated_successfully'));
            return to_route('academy.training.index');
        }catch (\Exception $e){
            report($e);
            return back()->withInput()->with('error', $e->getMessage());
        }



    }

    public function updateActive(Training $training)
    {
        if ($training->active){
            $newStatus = 0;
            $successMessage = trans('admin.training.status_inactive_successfully');
        } else {
            $newStatus = 1;
            $successMessage = trans('admin.training.status_active_successfully');
        }

        $training->update([
            'active' => $newStatus,
        ]);

        $this->sendNotification($training);
        session()->flash('success', $successMessage);
        return redirect()->route('academy.training.index');
    }
    public function delete(Request $request)
    {
       $training = $this->trainingModel->findOrFail($request->id);
       $training->delete();
       return response()->json(['data' => [
            'status' => 'success',
            'model'   => trans('admin.training.training'),
            'message' => trans('admin.training.deleted_successfully'),
       ]]);
    }

    public function createBooking()
    {
        $data = $this->trainingModel::where('academy_id', auth('academy')->id())->get();
        $students = AcademyStudent::where('academy_id', auth('academy')->id())
            ->where('status', 'active')->orderBy('name')->get(['id', 'name', 'phone', 'guardian_name']);
        return view('Academy.pages.training.create_booking', compact('data', 'students'));
    }
    public function getAreaByCity(Request $request)
    {
        $cityId = $request->city_id;
        if (blank($cityId)) {
            return response()->json([]);
        }

        $locale = app()->getLocale();
        
        if (is_numeric($cityId)) {
            try {
                $areas = Area::where('city_id', $cityId)->get()->map(function ($area) use ($locale) {
                    $name = $area->getTranslation('name', $locale, false) ?: $area->name;
                    $displayName = is_array($name) ? ($name[$locale] ?? reset($name)) : (string) $name;
                    return [
                        'id' => $area->id,
                        'name' => $displayName,
                    ];
                });
                if ($areas->isNotEmpty()) {
                    return response()->json($areas);
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $cityName = is_numeric($cityId) ? City::find($cityId)?->name : $cityId;
        $cityNameStr = is_array($cityName) ? ($cityName[$locale] ?? reset($cityName)) : (string) $cityName;

        $areasMap = [
            'القاهرة' => ['التجمع الخامس', 'مدينة نصر', 'المعادي', 'مصر الجديدة', 'الزمالك', 'الشروق', 'مدينتي', 'العاصمة الإدارية', 'وسط البلد', 'المقطم', 'عين شمس'],
            'Cairo' => ['Fifth Settlement', 'Nasr City', 'Maadi', 'Heliopolis', 'Zamalek', 'Shorouk', 'Madinaty', 'New Capital', 'Downtown'],
            'الجيزة' => ['الشيخ زايد', '٦ أكتوبر', 'الدقي', 'المهندسين', 'الهرم', 'فيصل', 'حدائق الأهرام'],
            'Giza' => ['Sheikh Zayed', '6th of October', 'Dokki', 'Mohandessin', 'Haram', 'Faisal'],
            'الإسكندرية' => ['سموحة', 'ميامي', 'المنتزة', 'ستانلي', 'رشدي', 'العجمي', 'سيدي بشر', 'جليم'],
            'Alexandria' => ['Smouha', 'Miami', 'Montazah', 'Stanley', 'Roushdy', 'Agami'],
            'الرياض' => ['العليا', 'النرجس', 'الياسمين', 'الملقا', 'الصحافة', 'الملز', 'الشفا', 'النسيم'],
            'Riyadh' => ['Olaya', 'An Narjis', 'Alyasmin', 'Al Malqa', 'As Sahafah', 'Al Malaz'],
            'جدة' => ['الروضة', 'الشاطئ', 'الحمراء', 'الزهراء', 'السلامة', 'النعيم', 'المرجان'],
            'Jeddah' => ['Ar Rawdah', 'Ash Shati', 'Al Hamra', 'Az Zahra', 'As Salamah'],
            'دبي' => ['داون تاون دبي', 'دبي مارينا', 'البرشاء', 'جميرا', 'المرقبات', 'دبي لاند', 'القرية العالمية'],
            'Dubai' => ['Downtown Dubai', 'Dubai Marina', 'Al Barsha', 'Jumeirah', 'Al Muraqqabat'],
            'الدوحة' => ['الدفنة', 'اللؤلؤة', 'الخليج الغربي', 'مشيرب', 'السد', 'الوعب'],
            'Doha' => ['West Bay', 'The Pearl', 'Msheireb', 'Al Sadd', 'Al Waab'],
        ];

        $list = [];
        foreach ($areasMap as $key => $items) {
            if ($cityNameStr && (mb_stripos($cityNameStr, $key) !== false || mb_stripos($key, $cityNameStr) !== false)) {
                $list = $items;
                break;
            }
        }

        $result = collect($list)->map(fn ($areaName) => [
            'id' => $areaName,
            'name' => $areaName,
        ]);

        return response()->json($result->values());
    }

    public function getCityByCountry(Request $request)
    {
        $countryId = $request->country_id;
        if (blank($countryId)) {
            return response()->json([]);
        }

        $locale = app()->getLocale();
        $isArabic = $locale === 'ar';

        try {
            $dbCities = City::query()
                ->where('country_id', $countryId)
                ->orWhere('county_id', $countryId)
                ->get();

            if ($dbCities->isNotEmpty()) {
                $result = $dbCities->map(function ($city) use ($locale) {
                    $name = $city->getTranslation('name', $locale, false) ?: $city->name;
                    $displayName = is_array($name) ? ($name[$locale] ?? reset($name)) : (string) $name;
                    return [
                        'id' => $city->id,
                        'name' => $displayName,
                    ];
                });
                return response()->json($result);
            }
        } catch (\Throwable $e) {
            // Ignore missing column fallback to dictionary
        }

        $country = Country::find($countryId);
        if (!$country) {
            return response()->json([]);
        }

        $iso = strtoupper($country->iso2 ?? '');
        $citiesMapAr = [
            'EG' => ['القاهرة', 'الإسكندرية', 'الجيزة', 'شرم الشيخ', 'الغردقة', 'الأقصر', 'أسوان', 'الجونة', 'مرسى علم', 'الساحل الشمالي', 'بورسعيد', 'المنصورة', 'طنطا', 'الزقازيق', 'الإسماعيلية', 'السويس', 'أسيوط', 'سوهاج'],
            'SA' => ['الرياض', 'جدة', 'الدمام', 'مكة المكرمة', 'المدينة المنورة', 'الخبر', 'أبها', 'تبوك', 'الطائف', 'القصيم', 'حائل', 'نجران', 'جازان'],
            'AE' => ['دبي', 'أبوظبي', 'الشارقة', 'عجمان', 'رأس الخيمة', 'العين', 'الفجيرة', 'أم القيوين'],
            'QA' => ['الدوحة', 'الريان', 'الوكرة', 'الخور', 'لوسيل', 'أم صلال', 'الشمال'],
            'KW' => ['مدينة الكويت', 'حولي', 'السالمية', 'الأحمدي', 'الفروانية', 'الجهراء', 'مبارك الكبير'],
            'OM' => ['مسقط', 'صلالة', 'صحار', 'نزوى', 'صور', 'البريمي'],
            'BH' => ['المنامة', 'المحرق', 'الرفاع', 'سترة', 'مدينة عيسى', 'مدينة حمد'],
            'JO' => ['عمان', 'العقبة', 'إربد', 'الزرقاء', 'السلط', 'مأدبا'],
            'LB' => ['بيروت', 'طرابلس', 'صيدا', 'جبيل', 'زحلة', 'صور'],
            'ES' => ['مدريد', 'برشلونة', 'مالقة', 'فالنسيا', 'إشبيلية', 'بيلباو', 'غرناطة', 'ماربيا'],
            'TR' => ['إسطنبول', 'أنطاليا', 'أنقرة', 'بورصة', 'إزمير', 'بودروم', 'طرابزون'],
            'GB' => ['لندن', 'مانشستر', 'ليفربول', 'برمنغهام', 'غلاسكو', 'أدنبرة'],
            'FR' => ['باريس', 'مارسيليا', 'ليون', 'نيس', 'تولوز', 'بوردو'],
            'DE' => ['برلين', 'ميونيخ', 'فرانکفورت', 'هامبورغ', 'كولونيا', 'دورتموند'],
            'IT' => ['روما', 'ميلانو', 'فلورنسا', 'تورينو', 'فينيسيا', 'نابولي'],
            'NL' => ['أمستردام', 'روتردام', 'لاهاي', 'أوتريخت'],
            'PT' => ['لشبونة', 'بورتو', 'فارو', 'براغا'],
            'GR' => ['أثينا', 'سالونيك', 'هركليون', 'رودس'],
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

        $citiesMapEn = [
            'EG' => ['Cairo', 'Alexandria', 'Giza', 'Sharm El Sheikh', 'Hurghada', 'Luxor', 'Aswan', 'El Gouna', 'Marsa Alam', 'North Coast', 'Port Said', 'Mansoura', 'Tanta'],
            'SA' => ['Riyadh', 'Jeddah', 'Dammam', 'Makkah', 'Madinah', 'Khobar', 'Abha', 'Tabuk', 'Taif', 'Qassim'],
            'AE' => ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Ras Al Khaimah', 'Al Ain', 'Fujairah'],
            'QA' => ['Doha', 'Al Rayyan', 'Al Wakrah', 'Al Khor', 'Lusail'],
            'KW' => ['Kuwait City', 'Hawalli', 'Salmiya', 'Ahmadi', 'Farwaniya'],
            'OM' => ['Muscat', 'Salalah', 'Sohar', 'Nizwa'],
            'BH' => ['Manama', 'Muharraq', 'Riffa', 'Sitra'],
            'JO' => ['Amman', 'Aqaba', 'Irbid', 'Zarqa'],
            'LB' => ['Beirut', 'Tripoli', 'Sidon', 'Byblos'],
            'ES' => ['Madrid', 'Barcelona', 'Malaga', 'Valencia', 'Seville'],
            'TR' => ['Istanbul', 'Antalya', 'Ankara', 'Bursa', 'Izmir', 'Bodrum'],
            'GB' => ['London', 'Manchester', 'Liverpool', 'Birmingham', 'Edinburgh'],
            'FR' => ['Paris', 'Marseille', 'Lyon', 'Nice', 'Toulouse'],
            'DE' => ['Berlin', 'Munich', 'Frankfurt', 'Hamburg', 'Cologne'],
            'IT' => ['Rome', 'Milan', 'Florence', 'Turin', 'Venice'],
            'US' => ['New York', 'Los Angeles', 'Miami', 'Chicago', 'Washington D.C.', 'San Francisco'],
            'CA' => ['Toronto', 'Montreal', 'Vancouver', 'Ottawa'],
            'MA' => ['Casablanca', 'Rabat', 'Marrakech', 'Tangier', 'Agadir'],
        ];

        $list = $isArabic ? ($citiesMapAr[$iso] ?? []) : ($citiesMapEn[$iso] ?? $citiesMapAr[$iso] ?? []);

        $result = collect($list)->map(function ($cityName) {
            $existing = City::where('name', 'like', "%{$cityName}%")->first();
            return [
                'id' => $existing ? $existing->id : $cityName,
                'name' => $cityName,
            ];
        });

        return response()->json($result->values());
    }
    public function storeBooking(BookingRequest $request)
    {
        try {
            $training = $this->trainingModel
                ->where('academy_id', auth('academy')->id())
                ->findOrFail($request->training_id);
            $student = AcademyStudent::where('academy_id', auth('academy')->id())
                ->findOrFail($request->academy_student_id);
            if (blank($student->phone)) {
                throw ValidationException::withMessages([
                    'academy_student_id' => app()->getLocale() === 'ar'
                        ? 'أضف رقم هاتف الطالب إلى ملفه قبل إنشاء الحجز.'
                        : 'Add the student phone number before creating a booking.',
                ]);
            }
            $totalAmount = round((float) $training->price, 2);
            $paidAmount = round((float) $request->paid_amount, 2);

            if ($paidAmount > $totalAmount) {
                throw ValidationException::withMessages([
                    'paid_amount' => trans('admin.bookings.paid_amount_exceeds_total'),
                ]);
            }

            DB::beginTransaction();
            $user = $student->user ?: User::where('phone', $student->phone)->first();
            $user = User::updateOrCreate(
                ['id' => $user?->id],
                [
                    'name' => $student->name, 'phone' => $student->phone,
                    'gender' => $student->gender, 'country_code' => $student->country_code,
                    'country_id' => $student->country_id, 'city_id' => $student->city_id,
                    'area_id' => $student->area_id,
                    'user_type'=> 'system',
                    'birth_date'=> $student->birth_date, 'club_member' => $student->club_member,
                    'email' => $student->email, 'child_type' => $student->child_type,
                    'school_name' => $student->school_name, 'parent_name' => $student->guardian_name,
                    'parent_phone' => $student->guardian_phone, 'coach_preference' => $student->coach_preference,
                    'frequent_attendance' => $student->frequent_attendance,
                    'relation_with_child' => $student->relation_with_child,
                    'referral_source' => $student->referral_source, 'delivery_service' => $student->delivery_service,
                    'medical_condition' => $student->medical_condition, 'start_date' => $student->start_date,
                    'medical_condition_details' => $student->medical_notes,
                    'additional_information' => $student->notes,
                ]);
            if ((int) $student->user_id !== (int) $user->id) $student->update(['user_id' => $user->id]);
            $booking = Invoice::create([
                'user_id' => $user->id,
                'training_id' => $request->training_id,
                'amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'order_number' => uniqid(),
                'status' => $paidAmount >= $totalAmount ? 'paid' : 'pending',
                'user_type' => 'offline',
                'payment_method' => $request->payment_method,
                'payment_method_other' => $request->payment_method === 'other' ? $request->payment_method_other : null,
            ]);
            Join::create([
                'user_id' => $user->id,
                'academy_student_id' => $student->id,
                'training_id' => $request->training_id,
                'price' => $booking->amount,
                'invoice_id' => $booking->id,
            ]);
            DB::commit();
            session()->flash('success', __('admin.training.Booking created successfully'));
            return back();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new TrainingsExport() ,'training.xlsx');
    }

    /**
     * @param Training $training
     * @return void
     */
    public function sendNotification(Training $training): void
    {
        if ($training->active) {
            $details = [
                'training_id' => $training->id,
                'longitude' => $training->longitude,
                'latitude' => $training->latitude,
                'academy_name' => auth('academy')->user()->commercial_name
            ];
            $AcademyTitle = 'Don’t miss out!';
            $AcademyBody = 'just added a new activity. Check it out!';
            $academyFollows = Follow::where([
                'followable_type' => Academies::class,
                'followable_id' => auth('academy')->id(),
            ])->get();
            $academyFollows->map(function ($follow) use ($AcademyTitle, $AcademyBody, $details) {
                NotificationService::dbNotification($follow->user_id, User::class, 1, $AcademyTitle, $AcademyBody, auth('academy')->user()->image, $details);
            });

            $coachTitle = 'Don’t miss out!';
            $coachBody = $training->coach->name . ' is leading a new training.Tap for details';
            $coachFollows = Follow::where([
                'followable_type' => Coach::class,
                'followable_id' => $training->coach_id,
            ])->get();
            $coachFollows->map(function ($follow) use ($coachTitle, $coachBody, $details) {
                NotificationService::dbNotification($follow->user_id, User::class, 1, $coachTitle, $coachBody, auth('academy')->user()->image, $details);
            });
        }
    }

    public function bulkDelete(Request $request)
    {
        $trainingIds = json_decode($request->ids);

        foreach ($trainingIds as $trainingId) {
            $training = $this->trainingModel->findOrFail($trainingId);
            if ($training->joins()->count() > 0) {
                continue;
            }
            $training->delete();
        }
        session()->flash('success', 'Training Deleted Successfully');
        return back();
    }

    public function publish(Request $request)
    {
        $trainingIds = json_decode($request->pub_ids);
        foreach ($trainingIds as $trainingId) {
            $training = $this->trainingModel->findOrFail($trainingId);
            $status = ($training->active)  ? 0 : 1;
            $training->update(['active'=>$status]);
        }
        session()->flash('success', trans('admin.training.status_active_successfully'));
        return back();
    }

    private function normalizeTrainingColor(?string $color): string
    {
        $color = trim((string) $color);

        if (preg_match('/^#?([0-9a-fA-F]{3})$/', $color, $matches)) {
            return sprintf(
                '#%s%s%s%s%s%s',
                $matches[1][0],
                $matches[1][0],
                $matches[1][1],
                $matches[1][1],
                $matches[1][2],
                $matches[1][2]
            );
        }

        if (preg_match('/^#?([0-9a-fA-F]{6})$/', $color, $matches)) {
            return '#' . strtolower($matches[1]);
        }

        return '#2563eb';
    }
}
