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
use App\Models\TClass;
use App\Models\Training;
use App\Models\User;
use App\Services\Firebase\NotificationService;
use App\Services\PartnerAccessService;
use App\Services\TranslatableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

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
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $service = new PartnerAccessService($authUser);

        $trainingsQuery = $service->scopeTrainings($this->trainingModel->newQuery());
        $metrics = [
            'total' => (clone $trainingsQuery)->count(),
            'active' => (clone $trainingsQuery)->where('active', 1)->count(),
            'inactive' => (clone $trainingsQuery)->where('active', 0)->count(),
        ];

        return $dataTable->render('Academy.pages.training.index', compact('metrics'));
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
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        if (!$authUser) {
            return response()->json(['coaches' => []]);
        }

        $service = new PartnerAccessService($authUser);
        $query = Coach::query()->where('active', 1);

        // Filter coaches strictly to this partner's academy & accessible branches/sports
        $service->scopeCoaches($query);

        // Filter by the requested sport
        $query->whereHas('sports', function ($q) use ($id) {
            $q->where('sports.id', $id);
        });

        $coaches = $query->get(['coaches.id', 'coaches.name']);

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

        $generatedCount = 0;

        DB::transaction(function () use ($request, $authUser, &$generatedCount) {
            $translatable = TranslatableService::generateTranslatableFields($this->trainingModel::getTranslatableFields(), $request->validated());
            $training = $this->trainingModel->create(array_merge($translatable, [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'coach_id' => $request->coach_id,
                'price' => $request->price,
                'max_players' => $request->max_players,
                'level' => $request->level,
                'gender' => $request->gender,
                'age_group' => $request->age_group,
                'address_id' => $request->address_id,
                'academy_id' => $authUser->academy_id,
                'sport_id' => $request->sport_id,
                'discount_price' => $request->discount_price,
                'classes_days' => $request->classes_days,
                'color' => $this->normalizeTrainingColor($request->color),
                'classes_number' => $request->classes_number,
                'active' => 1,
            ]));

            if ($request->boolean('auto_generate_classes', true) && !empty($request->classes_days) && (int) $request->classes_number > 0) {
                $generatedCount = $this->autoGenerateClasses($training, $request);
            }
        });

        $successMsg = trans('admin.training.created_successfully');
        if ($generatedCount > 0) {
            $successMsg .= ' ' . (app()->getLocale() === 'ar'
                ? "وتم توليد {$generatedCount} حصة تدريبية مجدولة في التقويم تلقائياً."
                : "and {$generatedCount} scheduled sessions were automatically generated in the calendar.");
        }

        session()->flash('success', $successMsg);
        return to_route('academy.training.index');
    }

    /**
     * Automatically generate scheduled TClass sessions for a new training
     */
    private function autoGenerateClasses(Training $training, Request $request): int
    {
        $startDateInput = $request->input('classes_start_date');
        $currentDate = $startDateInput ? \Illuminate\Support\Carbon::parse($startDateInput) : now();
        $targetDays = array_map('strtolower', (array) $request->classes_days);
        $count = (int) ($request->classes_number ?: 12);
        $generated = 0;
        $maxDaysToScan = max(90, $count * 14);
        $scanned = 0;

        $startTime = $training->start_time ? $training->start_time->format('H:i') : $request->start_time;
        $endTime = $training->end_time ? $training->end_time->format('H:i') : $request->end_time;
        $arName = $training->getTranslation('name', 'ar') ?: $training->name;
        $enName = $training->getTranslation('name', 'en') ?: $training->name;

        $defaultBringWithMe = [
            'ar' => [
                'الزي الرياضي المناسب للتمارين',
                'حذاء رياضي ملائم',
                'زجاجة مياه خاصة',
                'منشفة شخصية',
            ],
            'en' => [
                'Appropriate athletic sportswear',
                'Suitable sports training shoes',
                'Personal water bottle',
                'Personal towel',
            ],
        ];

        while ($generated < $count && $scanned < $maxDaysToScan) {
            $dayName = strtolower($currentDate->format('l'));
            if (in_array($dayName, $targetDays)) {
                $generated++;

                if ($generated === 1) {
                    $outcomes = [
                        'ar' => [
                            'التعارف وشرح خطة وأهداف البرنامج التدريبي',
                            'الإحماء وتقييم المستوى واللياقة البدنية الأساسية',
                        ],
                        'en' => [
                            'Orientation, program overview, and training goals',
                            'Warm-up, baseline fitness, and initial skill assessment',
                        ],
                    ];
                } elseif ($generated === $count) {
                    $outcomes = [
                        'ar' => [
                            'تطبيق المهارات والتكتيكات في منافسة وتدريب تطبيقي',
                            'تقييم التطور الفردي ومراجعة الأداء الختامي',
                        ],
                        'en' => [
                            'Practical match play and tactical application',
                            'Individual progress evaluation and performance review',
                        ],
                    ];
                } else {
                    $outcomes = [
                        'ar' => [
                            'تطوير المهارات الفردية وبناء اللياقة البدنية',
                            'تنفيذ تمارين تكتيكية وتطبيقات عملية مشتركة',
                        ],
                        'en' => [
                            'Skill development and physical conditioning drills',
                            'Tactical exercises and teamwork drills',
                        ],
                    ];
                }

                TClass::create([
                    'title' => [
                        'ar' => "حصة {$generated} - {$arName}",
                        'en' => "Session {$generated} - {$enName}",
                    ],
                    'date' => $currentDate->toDateString(),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'training_id' => $training->id,
                    'out_comes' => $outcomes,
                    'bring_with_me' => $defaultBringWithMe,
                ]);
            }
            $currentDate->addDay();
            $scanned++;
        }

        return $generated;
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

    public function update(Training $training, TrainingRequest $request)
    {
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        abort_unless((int) $training->academy_id === (int) $authUser->academy_id, 404);

        try {
            DB::transaction(function () use ($request, $training, $authUser) {
                $originalStartDate = $training->start_date;
                $translatable = TranslatableService::generateTranslatableFields($this->trainingModel::getTranslatableFields(), $request->validated());
                $training->update(array_merge($translatable, [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'coach_id' => $request->coach_id,
                    'price' => $request->price,
                    'max_players' => $request->max_players,
                    'level' => $request->level,
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
                    'academy_name' => $authUser->commercial_name
                ];
                //notifications to users
                if ($originalStartDate != $training->start_date) {
                    $title = 'Booking Rescheduled';
                    $body = 'Your booking with ' . $training->academy->getTranslation('commercial_name', 'en') . ' is rescheduled.please check the new dates';
                    $joins = Join::where('training_id', $training->id)->get();
                    $data = [
                        'title' => $title,
                        'body' => $body,
                        'image' => $authUser->image,
                        'details' => $details,
                        "id" => $training->id,
                        'page' => 'details',
                        'class_id' => null
                    ];
                    $joins->map(function ($join) use ($data) {
                        NotificationService::firebaseNotification($data, $join->user->fcm_token,);
                    });
                }
            });
            session()->flash('success', trans('admin.training.updated_successfully'));
            return to_route('academy.training.index');
        } catch (\Exception $e) {
            report($e);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateActive(Training $training)
    {
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        abort_unless((int) $training->academy_id === (int) $authUser->academy_id, 404);

        if ($training->active) {
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
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $service = new PartnerAccessService($authUser);
        $training = $service->scopeTrainings($this->trainingModel->newQuery())->findOrFail($request->id);
        $training->delete();
        return response()->json(['data' => [
            'status' => 'success',
            'model'   => trans('admin.training.training'),
            'message' => trans('admin.training.deleted_successfully'),
        ]]);
    }

    public function createBooking()
    {
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $service = new PartnerAccessService($authUser);
        $data = $service->scopeTrainings($this->trainingModel->newQuery())->with('sport')->get();
        $students = $service->scopeStudents(AcademyStudent::query())
            ->where('status', 'active')->orderBy('name')->get(['id', 'name', 'phone', 'guardian_name']);

        $academy = ($authUser instanceof \App\Models\PartnerUser && $authUser->academy) ? $authUser->academy : $authUser;
        $sports = $academy ? $academy->sports()->get() : collect();
        if ($sports->isEmpty()) {
            $sports = $data->pluck('sport')->filter()->unique('id')->values();
        }

        $facilityType = $academy?->business_type ?? 'academy';
        $isGymFacility = in_array($facilityType, ['gym', 'health_center', 'fitness']);

        $recentJoins = $service->scopeBookings(Join::query())
            ->with(['user', 'student', 'training', 'invoice'])
            ->latest('id')
            ->limit(5)
            ->get();

        return view('Academy.pages.training.create_booking', compact('data', 'students', 'sports', 'academy', 'facilityType', 'isGymFacility', 'recentJoins'));
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

        $dbCityId = is_numeric($cityId) ? (int)$cityId : (City::where('name->ar', $cityNameStr)->orWhere('name->en', $cityNameStr)->value('id') ?? 1);
        $result = collect($list)->map(function ($areaName) use ($dbCityId) {
            $existing = Area::where('city_id', $dbCityId)
                ->where(function ($q) use ($areaName) {
                    $q->where('name->ar', $areaName)
                      ->orWhere('name->en', $areaName)
                      ->orWhere('name', $areaName)
                      ->orWhere('name', 'like', "%\"{$areaName}\"%");
                })->first();

            if (!$existing) {
                $existing = Area::firstOrCreate([
                    'city_id' => $dbCityId,
                    'name' => ['ar' => $areaName, 'en' => $areaName],
                ]);
            }

            return [
                'id' => $existing->id,
                'name' => $areaName,
            ];
        });

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

        $result = collect($list)->map(function ($cityName) use ($countryId) {
            $existing = City::where('country_id', $countryId)
                ->where(function ($q) use ($cityName) {
                    $q->where('name->ar', $cityName)
                      ->orWhere('name->en', $cityName)
                      ->orWhere('name', $cityName)
                      ->orWhere('name', 'like', "%\"{$cityName}\"%");
                })->first();

            if (!$existing) {
                $existing = City::firstOrCreate(
                    ['country_id' => $countryId, 'name' => ['ar' => $cityName, 'en' => $cityName]]
                );
            }

            return [
                'id' => $existing->id,
                'name' => $cityName,
            ];
        });

        return response()->json($result->values());
    }

    public function storeBooking(BookingRequest $request)
    {
        try {
            /** @var \App\Models\PartnerUser $authUser */
            $authUser = auth('academy')->user();
            $service = new PartnerAccessService($authUser);
            $training = $service->scopeTrainings($this->trainingModel->newQuery())->findOrFail($request->training_id);
            $student = $service->scopeStudents(AcademyStudent::query())->findOrFail($request->academy_student_id);

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
            $userData = [
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
                $userData['image'] = $student->image;
            }
            $user = User::updateOrCreate(
                ['id' => $user?->id],
                $userData
            );
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

            $academy = ($authUser instanceof \App\Models\PartnerUser && $authUser->academy) ? $authUser->academy : $authUser;
            $ar = app()->getLocale() === 'ar';
            $currency = $academy?->currency_symbol ?: 'SAR';
            $facilityName = $academy?->commercial_name ?: ($academy?->name ?: 'النادي الرياضي');
            $facilityType = $academy?->business_type ?? 'academy';
            $isGym = in_array($facilityType, ['gym', 'health_center', 'fitness']);
            $itemName = $training->name ?: ($isGym ? 'باقة اشتراك الجيم' : 'البرنامج التدريبي');
            $remaining = max(0, $totalAmount - $paidAmount);
            $publicInvUrl = route('invoices.public.view', ['type' => 'booking', 'id' => $booking->id]);

            // Format clean phone for WhatsApp
            $rawPhone = (string) $student->phone;
            $digits = preg_replace('/\D+/', '', $rawPhone);
            if (str_starts_with($digits, '00')) {
                $cleanPhone = substr($digits, 2);
            } elseif (str_starts_with($digits, '01') && strlen($digits) === 11) {
                $cleanPhone = '20' . substr($digits, 1);
            } elseif (str_starts_with($digits, '05') && strlen($digits) === 10) {
                $cleanPhone = '966' . substr($digits, 1);
            } elseif (str_starts_with($digits, '0') && strlen($digits) > 7) {
                $cleanPhone = substr($digits, 1);
            } else {
                $cleanPhone = $digits;
            }

            if (app()->getLocale() === 'ar') {
                $waText = "مرحباً بك كابتن *{$student->name}* 🌟\n"
                    . "يسعدنا تأكيد تسجيل وتفعيل اشتراكك في *{$facilityName}*:\n\n"
                    . "📋 *باقة الاشتراك:* {$itemName}\n"
                    . "🔢 *رقم الفاتورة:* #{$booking->order_number}\n"
                    . "💰 *الإجمالي:* " . number_format($totalAmount, 2) . " {$currency}\n"
                    . "✅ *المسدد:* " . number_format($paidAmount, 2) . " {$currency}\n"
                    . "⏳ *المتبقي:* " . number_format($remaining, 2) . " {$currency}\n\n"
                    . "🔗 *رابط الفاتورة الإلكترونية الموحدة:* \n{$publicInvUrl}\n\n"
                    . "نتمنى لك أوقاتاً ممتعة وتدريباً رائعاً! 💪🔥";
            } else {
                $waText = "Hello *{$student->name}* 🌟\n"
                    . "We are pleased to confirm your membership at *{$facilityName}*:\n\n"
                    . "📋 *Plan:* {$itemName}\n"
                    . "🔢 *Invoice #:* #{$booking->order_number}\n"
                    . "💰 *Total:* " . number_format($totalAmount, 2) . " {$currency}\n"
                    . "✅ *Paid:* " . number_format($paidAmount, 2) . " {$currency}\n"
                    . "⏳ *Balance:* " . number_format($remaining, 2) . " {$currency}\n\n"
                    . "🔗 *Electronic Invoice:* \n{$publicInvUrl}\n\n"
                    . "Have a great workout! 💪🔥";
            }

            $waUrl = 'https://api.whatsapp.com/send?' . ($cleanPhone ? 'phone=' . $cleanPhone . '&' : '') . 'text=' . urlencode($waText);

            $sentViaCloudApi = false;
            try {
                $channel = \App\Models\WhatsAppChannel::where('academy_id', $academy?->id)->first();
                if ($channel && $channel->isReady() && $cleanPhone) {
                    $waService = app(\App\Services\WhatsAppCloudService::class);
                    $waService->sendText($channel, $cleanPhone, $waText);
                    $sentViaCloudApi = true;
                }
            } catch (\Throwable $waErr) {
                \Log::warning('WhatsApp Cloud API booking notification error: ' . $waErr->getMessage());
            }

            session()->flash('new_booking', [
                'join_id' => $join->id,
                'invoice_id' => $booking->id,
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_phone' => $student->phone,
                'clean_phone' => $cleanPhone,
                'plan_name' => $itemName,
                'total' => $totalAmount,
                'paid' => $paidAmount,
                'remaining' => $remaining,
                'currency' => $currency,
                'whatsapp_url' => $waUrl,
                'whatsapp_text' => $waText,
                'print_a4_url' => route('academy.invoices.bookings.print', ['invoice' => $booking->id, 'paper' => 'a4']),
                'print_pos_url' => route('academy.invoices.bookings.print', ['invoice' => $booking->id, 'paper' => 'pos']),
                'card_url' => route('academy.students.card', $student->id),
                'details_url' => route('academy.report.view-booking-details', $join->id),
                'sent_via_cloud_api' => $sentViaCloudApi,
            ]);

            session()->flash('success', $isGym ? ($ar ? 'تم تسجيل وتفعيل اشتراك العضو بنجاح' : 'Membership registered and activated successfully') : __('admin.training.Booking created successfully'));
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
        return Excel::download(new TrainingsExport(), 'training.xlsx');
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
                'academy_name' => auth('academy')->user()?->commercial_name
            ];
            $AcademyTitle = 'Don’t miss out!';
            $AcademyBody = 'just added a new activity. Check it out!';
            $academyFollows = Follow::where([
                'followable_type' => Academies::class,
                'followable_id' => $training->academy_id,
            ])->get();
            $academyFollows->map(function ($follow) use ($AcademyTitle, $AcademyBody, $details) {
                NotificationService::dbNotification($follow->user_id, User::class, 1, $AcademyTitle, $AcademyBody, auth('academy')->user()?->image, $details);
            });

            $coachTitle = 'Don’t miss out!';
            $coachBody = $training->coach?->name . ' is leading a new training.Tap for details';
            $coachFollows = Follow::where([
                'followable_type' => Coach::class,
                'followable_id' => $training->coach_id,
            ])->get();
            $coachFollows->map(function ($follow) use ($coachTitle, $coachBody, $details) {
                NotificationService::dbNotification($follow->user_id, User::class, 1, $coachTitle, $coachBody, auth('academy')->user()?->image, $details);
            });
        }
    }

    public function bulkDelete(Request $request)
    {
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $service = new PartnerAccessService($authUser);
        $trainingIds = json_decode($request->ids ?? '[]');

        foreach ($trainingIds as $trainingId) {
            $training = $service->scopeTrainings($this->trainingModel->newQuery())->find($trainingId);
            if ($training && $training->joins()->count() === 0) {
                $training->delete();
            }
        }
        session()->flash('success', 'Training Deleted Successfully');
        return back();
    }

    public function publish(Request $request)
    {
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $service = new PartnerAccessService($authUser);
        $trainingIds = json_decode($request->pub_ids ?? '[]');
        foreach ($trainingIds as $trainingId) {
            $training = $service->scopeTrainings($this->trainingModel->newQuery())->find($trainingId);
            if ($training) {
                $status = ($training->active) ? 0 : 1;
                $training->update(['active' => $status]);
            }
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
