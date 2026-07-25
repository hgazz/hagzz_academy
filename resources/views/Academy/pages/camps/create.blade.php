@extends('Academy.Layouts.master')

@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

@section('title', $isArabic ? 'إطلاق معسكر تدريبي جديد' : 'Launch New Training Camp')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-plus-circle text-primary me-2"></i>
                {{ $isArabic ? 'إطلاق معسكر تدريبي جديد' : 'Launch New Training Camp' }}
            </h3>
            <p class="text-muted small mb-0">{{ $isArabic ? 'أدخل كافة التفاصيل الأساسية واللوجستية والمالية للمعسكر' : 'Enter basic, logistic, and pricing details for the camp' }}</p>
        </div>
        <a href="{{ route('academy.camps.index') }}" class="btn btn-outline-secondary fw-bold">
            <i class="fa-solid fa-arrow-right me-1"></i> {{ $isArabic ? 'عودة للمعسكرات' : 'Back to Camps' }}
        </a>
    </div>

    <form method="POST" action="{{ route('academy.camps.store') }}">
        @csrf

        <div class="row g-4">
            <!-- LEFT COLUMN: CAMP DETAILS -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-info-circle text-primary me-2"></i> {{ $isArabic ? 'البيانات الأساسية للمعسكر' : 'Basic Camp Information' }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">{{ $isArabic ? 'عنوان المعسكر (بالعربية)' : 'Camp Title (Arabic)' }} <span class="text-danger">*</span></label>
                                <input type="text" name="title_ar" class="form-control" required placeholder="{{ $isArabic ? 'مثال: معسكر إسبانيا الدولي لكرة القدم' : 'e.g., Spain International Football Camp' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ $isArabic ? 'نوع المعسكر' : 'Camp Type' }} <span class="text-danger">*</span></label>
                                <select name="type" id="campTypeSelect" class="form-select" required>
                                    <option value="domestic">{{ $isArabic ? '🇪🇬 معسكر محلي (داخل مصر)' : 'Domestic (Egypt)' }}</option>
                                    <option value="international">{{ $isArabic ? '✈️ معسكر دولي (خارج مصر)' : 'International (Outside Egypt)' }}</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $isArabic ? 'الرياضة المستهدفة' : 'Target Sport' }}</label>
                                <select name="sport_id" class="form-select">
                                    <option value="">{{ $isArabic ? 'جميع الرياضات / معسكر متعدد' : 'All Sports / Multi-Sport' }}</option>
                                    @foreach(is_iterable($sports ?? null) ? $sports : [] as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $isArabic ? 'الدولة المستضيفة' : 'Destination Country' }}</label>
                                <select name="country_id" id="country_select" class="form-select">
                                    <option value="">{{ $isArabic ? 'اختر الدولة...' : 'Select Country...' }}</option>
                                    @foreach(is_iterable($countries ?? null) ? $countries : [] as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->iso2 ?: $c->currency_code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ $isArabic ? 'المدينة' : 'City' }}</label>
                                <select name="city_name" id="city_select" class="form-select mb-2">
                                    <option value="">{{ $isArabic ? 'اختر الدولة أولاً...' : 'Select Country first...' }}</option>
                                </select>
                                <input type="text" id="custom_city_input" class="form-control d-none" placeholder="{{ $isArabic ? 'اكتب اسم المدينة يدوياً...' : 'Type custom city name...' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ $isArabic ? 'اسم الفندق / منتجع الإقامة' : 'Hotel / Resort Name' }}</label>
                                <input type="text" name="hotel_name" class="form-control" placeholder="{{ $isArabic ? 'اسم الفندق والإقامة' : 'Hotel name' }}">
                            </div>
                            <div class="col-md-4">
                                 <label class="form-label fw-bold">{{ $isArabic ? 'مكان التدريب / النادي' : 'Training Facility / Club' }}</label>
                                <input type="text" name="venue_name" class="form-control" placeholder="{{ $isArabic ? 'اسم الملعب أو النادي' : 'Stadium/Facility' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DETAILED SPECIFICATIONS & PROGRAM -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-hotel text-primary me-2"></i> {{ $isArabic ? 'تفاصيل الغرف، الملاعب وبرنامج المعسكر' : 'Room, Facility & Program Details' }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fa-solid fa-bed text-info me-1"></i> {{ $isArabic ? 'مميزات ومواصفات الغرف والإقامة' : 'Room & Accommodation Features' }}</label>
                            <textarea name="room_features" class="form-control" rows="2" placeholder="{{ $isArabic ? 'مثال: غرف ثنائية وثلاثية فاخرة، تكييف، شاشة، Wi-Fi، حمام خاص، إطلالة على البسين...' : 'e.g. Double & Triple rooms, AC, Wi-Fi, private bathroom...' }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fa-solid fa-futbol text-success me-1"></i> {{ $isArabic ? 'مميزات وتجهيزات الملاعب والصالات' : 'Pitch & Facility Features' }}</label>
                            <textarea name="venue_features" class="form-control" rows="2" placeholder="{{ $isArabic ? 'مثال: ملاعب نجيل طبيعي معتمدة قانونية، صالة لياقة بدنية (Gym)، حمام سباحة أولمبي، غرفة قياسات رياضية...' : 'e.g. Legal natural grass pitches, gym, swimming pool, changing rooms...' }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fa-solid fa-clipboard-list text-warning me-1"></i> {{ $isArabic ? 'برنامج وجدول المعسكر اليومي (Program Itinerary)' : 'Daily Camp Program & Schedule' }}</label>
                            <textarea name="program_itinerary" class="form-control" rows="4" placeholder="{{ $isArabic ? 'مثال:\n- 08:00 ص: الإفطار والتوجه للملعب\n- 09:30 ص: التدريب الصباحي البدني\n- 01:30 م: الغداء وفترة الراحة\n- 05:00 م: التكتيك والتدريب المسائي والمباريات...' : 'Daily itinerary...' }}"></textarea>
                        </div>
                    </div>
                </div>

                <!-- DATES & LOGISTICS -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-calendar-days text-primary me-2"></i> {{ $isArabic ? 'التواريخ والخدمات المشمولة' : 'Dates & Included Services' }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ $isArabic ? 'تاريخ بداية المعسكر' : 'Start Date' }} <span class="text-danger">*</span></label>
                                <input type="date" name="starts_on" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ $isArabic ? 'تاريخ نهاية المعسكر' : 'End Date' }} <span class="text-danger">*</span></label>
                                <input type="date" name="ends_on" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ $isArabic ? 'آخر موعد للتسجيل' : 'Registration Deadline' }}</label>
                                <input type="date" name="registration_deadline" class="form-control">
                            </div>
                        </div>

                        <label class="form-label fw-bold d-block mb-2">{{ $isArabic ? 'الخدمات المشمولة في اشتراك المعسكر:' : 'Included Services:' }}</label>
                        <div class="row g-2">
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="flights" id="srv1">
                                    <label class="form-check-label" for="srv1">✈️ {{ $isArabic ? 'تذاكر الطيران' : 'Flights' }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="hotel" id="srv2" checked>
                                    <label class="form-check-label" for="srv2">🏨 {{ $isArabic ? 'الإقامة بالفندق' : 'Hotel Accommodation' }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="meals" id="srv3" checked>
                                    <label class="form-check-label" for="srv3">🍔 {{ $isArabic ? 'الوجبات كاملة' : 'Full Board Meals' }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="transports" id="srv4" checked>
                                    <label class="form-check-label" for="srv4">🚌 {{ $isArabic ? 'الانتقالات الداخلية' : 'Transports' }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="kit" id="srv5" checked>
                                    <label class="form-check-label" for="srv5">👕 {{ $isArabic ? 'حقيبة وزي المعسكر' : 'Camp Uniform Kit' }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="certificates" id="srv6" checked>
                                    <label class="form-check-label" for="srv6">📜 {{ $isArabic ? 'شهادات المشاركة' : 'Certificates' }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="visa_required" value="1" id="visaSwitch">
                                <label class="form-check-label fw-bold text-dark" for="visaSwitch">
                                    {{ $isArabic ? 'يتطلب الحصول على تأشيرة دخول (Visa Required)' : 'Visa Required for Entry' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: PRICING & SUPERVISORS -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-coins text-primary me-2"></i> {{ $isArabic ? 'التسعير والأعداد' : 'Pricing & Capacity' }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $isArabic ? 'السعة الإجمالية (عدد المقاعد)' : 'Total Capacity' }} <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" class="form-control" min="1" value="20" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $isArabic ? 'سعر اشتراك المشترك للفرد (' . $currency['symbol'] . ')' : 'Price Per Person (' . $currency['code'] . ')' }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $isArabic ? 'مبلغ العربون / الدفعة الأولى' : 'Deposit Amount' }}</label>
                            <input type="number" step="0.01" name="deposit_amount" class="form-control" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-shield text-primary me-2"></i> {{ $isArabic ? 'طاقم الإشراف والمدربين' : 'Supervisors & Coaches' }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <label class="form-label fw-bold mb-2">{{ $isArabic ? 'اختر المدربين والمشرفين:' : 'Select Coaches:' }}</label>
                        @foreach(is_iterable($coaches ?? null) ? $coaches : [] as $coach)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="supervisors[]" value="{{ $coach->id }}" id="coach_{{ $coach->id }}">
                                <label class="form-check-label" for="coach_{{ $coach->id }}">
                                    <strong>{{ $coach->name }}</strong>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                            <i class="fa-solid fa-check-circle me-1"></i>
                            {{ $isArabic ? 'حفظ وإطلاق المعسكر' : 'Save & Launch Camp' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country_select');
    const citySelect = document.getElementById('city_select');
    const customCityInput = document.getElementById('custom_city_input');

    if (countrySelect && citySelect) {
        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            citySelect.innerHTML = '<option value="">{{ $isArabic ? "جاري تحميل المدن..." : "Loading cities..." }}</option>';
            
            if (!countryId) {
                citySelect.innerHTML = '<option value="">{{ $isArabic ? "اختر الدولة أولاً..." : "Select Country first..." }}</option>';
                customCityInput.classList.add('d-none');
                customCityInput.removeAttribute('name');
                citySelect.name = 'city_name';
                return;
            }

            fetch(`{{ url('partner/camps/api/countries') }}/${countryId}/cities`)
                .then(res => res.json())
                .then(cities => {
                    let html = '<option value="">{{ $isArabic ? "اختر المدينة..." : "Select City..." }}</option>';
                    if (cities && cities.length > 0) {
                        cities.forEach(c => {
                            html += `<option value="${c.name}">${c.name}</option>`;
                        });
                    }
                    html += '<option value="__custom__">{{ $isArabic ? "✏️ أخرى (أدخل اسمها يدوياً)" : "✏️ Other (Custom city)" }}</option>';
                    citySelect.innerHTML = html;
                })
                .catch(() => {
                    citySelect.innerHTML = '<option value="__custom__">{{ $isArabic ? "✏️ أدخل المدينة يدوياً" : "✏️ Enter Custom City" }}</option>';
                });
        });

        citySelect.addEventListener('change', function() {
            if (this.value === '__custom__') {
                customCityInput.classList.remove('d-none');
                customCityInput.name = 'city_name';
                customCityInput.focus();
                citySelect.removeAttribute('name');
            } else {
                customCityInput.classList.add('d-none');
                customCityInput.removeAttribute('name');
                citySelect.name = 'city_name';
            }
        });
    }
});
</script>
@endsection
