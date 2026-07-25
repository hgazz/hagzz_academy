@extends('Academy.Layouts.master')

@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

@section('title', $isArabic ? 'تعديل المعسكر التدريبي' : 'Edit Training Camp')

@section('content')
<div class="container-fluid py-4">
    <!-- TOP HEADER -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-pen-to-square text-primary me-2"></i>
                {{ $isArabic ? 'تعديل بيانات المعسكر: ' . $camp->title : 'Edit Camp: ' . $camp->title }}
            </h3>
            <p class="text-muted small mb-0">{{ $isArabic ? 'تحديث كافة التفاصيل والمواعيد والإقامة والخدمات المشمولة' : 'Update camp details, schedule, accommodation & included services' }}</p>
        </div>
        <a href="{{ route('academy.camps.show', $camp->id) }}" class="btn btn-outline-secondary fw-bold">
            <i class="fa-solid fa-arrow-right me-1"></i> {{ $isArabic ? 'عودة لمركز المعسكر' : 'Back to Camp Hub' }}
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('academy.camps.update', $camp->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <!-- LEFT COLUMN: MAIN DETAILS -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-circle-info text-primary me-2"></i> {{ $isArabic ? 'البيانات الأساسية للمعسكر' : 'Basic Camp Information' }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $isArabic ? 'اسم المعسكر (بالعربية)' : 'Camp Title (Arabic)' }} <span class="text-danger">*</span></label>
                                <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $camp->title_ar) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $isArabic ? 'اسم المعسكر (بالإنجليزية)' : 'Camp Title (English)' }}</label>
                                <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $camp->title_en) }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $isArabic ? 'نوع المعسكر' : 'Camp Type' }} <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="domestic" {{ old('type', $camp->type) === 'domestic' ? 'selected' : '' }}>{{ $isArabic ? '🇪🇬 معسكر محلي (داخل مصر)' : 'Domestic' }}</option>
                                    <option value="international" {{ old('type', $camp->type) === 'international' ? 'selected' : '' }}>{{ $isArabic ? '✈️ معسكر دولي (خارج مصر)' : 'International' }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $isArabic ? 'الرياضة المستهدفة' : 'Target Sport' }}</label>
                                <select name="sport_id" class="form-select">
                                    <option value="">{{ $isArabic ? 'جميع الرياضات / معسكر متعدد' : 'All Sports / Multi-Sport' }}</option>
                                    @foreach(is_iterable($sports ?? null) ? $sports : [] as $s)
                                        <option value="{{ $s->id }}" {{ old('sport_id', $camp->sport_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $isArabic ? 'حالة المعسكر الحالية' : 'Current Status' }} <span class="text-danger">*</span></label>
                                <select name="status" class="form-select fw-bold text-primary" required>
                                    <option value="upcoming" {{ old('status', $camp->status) === 'upcoming' ? 'selected' : '' }}>{{ $isArabic ? '📅 قادم (Upcoming)' : 'Upcoming' }}</option>
                                    <option value="active" {{ old('status', $camp->status) === 'active' ? 'selected' : '' }}>{{ $isArabic ? '🟢 جاري ينظم الآن (Active)' : 'Active' }}</option>
                                    <option value="completed" {{ old('status', $camp->status) === 'completed' ? 'selected' : '' }}>{{ $isArabic ? '🏁 مكتمل (Completed)' : 'Completed' }}</option>
                                    <option value="cancelled" {{ old('status', $camp->status) === 'cancelled' ? 'selected' : '' }}>{{ $isArabic ? '🔴 ملغي (Cancelled)' : 'Cancelled' }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $isArabic ? 'الدولة المستضيفة' : 'Destination Country' }}</label>
                                <select name="country_id" id="country_select" class="form-select">
                                    <option value="">{{ $isArabic ? 'اختر الدولة...' : 'Select Country...' }}</option>
                                    @foreach(is_iterable($countries ?? null) ? $countries : [] as $c)
                                        <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-iso2="{{ strtoupper($c->iso2 ?? '') }}" {{ old('country_id', $camp->country_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->iso2 ?: $c->currency_code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $isArabic ? 'المدينة' : 'City' }}</label>
                                <input type="text" name="city_name" class="form-control" value="{{ old('city_name', $camp->city_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $isArabic ? 'اسم الفندق / منتجع الإقامة' : 'Hotel / Resort Name' }}</label>
                                <input type="text" name="hotel_name" class="form-control" value="{{ old('hotel_name', $camp->hotel_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $isArabic ? 'مكان التدريب / النادي' : 'Training Facility / Club' }}</label>
                                <input type="text" name="venue_name" class="form-control" value="{{ old('venue_name', $camp->venue_name) }}">
                            </div>

                            <!-- VISA DYNAMIC HINT FULL-WIDTH HORIZONTAL RECTANGLE BANNER -->
                            <div class="col-12" id="visa_dynamic_box_wrapper">
                                <div id="visa_dynamic_box" class="d-none mt-2">
                                    <div id="visa_alert_badge" class="p-3 bg-white rounded-3 border shadow-sm d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 w-100">
                                        <div class="d-flex align-items-center gap-3">
                                            <div id="visa_badge_icon" class="fs-3"></div>
                                            <div>
                                                <h6 id="visa_badge_title" class="fw-bold mb-1"></h6>
                                                <p id="visa_badge_desc" class="mb-0 text-dark fw-medium small"></p>
                                            </div>
                                        </div>
                                        <div id="visa_link_container">
                                            <a id="visa_official_link" href="#" target="_blank" class="btn btn-primary fw-bold text-white shadow-sm px-3 py-2 text-nowrap">
                                                <i class="fa-solid fa-passport me-1"></i>
                                                {{ $isArabic ? '🌐 شروط التأشيرة والتقديم الرسمي' : 'Visa Info & Official Portal' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $isArabic ? 'الوصف والتفاصيل العامة للمعسكر' : 'Camp Description' }}</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $camp->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fa-solid fa-bed text-info me-1"></i> {{ $isArabic ? 'مميزات ومواصفات الغرف والإقامة' : 'Room & Accommodation Features' }}</label>
                            <textarea name="room_features" class="form-control" rows="2" placeholder="{{ $isArabic ? 'غرف ثنائية وثلاثية فاخرة، تكييف، شاشة، Wi-Fi، حمام خاص...' : 'Room features...' }}">{{ old('room_features', $camp->room_features) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fa-solid fa-futbol text-success me-1"></i> {{ $isArabic ? 'مميزات وتجهيزات الملاعب والصالات' : 'Pitch & Facility Features' }}</label>
                            <textarea name="venue_features" class="form-control" rows="2" placeholder="{{ $isArabic ? 'ملاعب نجيل طبيعي، جيم، حمام سباحة...' : 'Facility features...' }}">{{ old('venue_features', $camp->venue_features) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fa-solid fa-clipboard-list text-warning me-1"></i> {{ $isArabic ? 'برنامج وجدول المعسكر اليومي (Program Itinerary)' : 'Daily Camp Program & Schedule' }}</label>
                            <textarea name="program_itinerary" class="form-control" rows="4" placeholder="{{ $isArabic ? 'جدول المواعيد اليومية...' : 'Daily itinerary...' }}">{{ old('program_itinerary', $camp->program_itinerary) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- DATES & SCHEDULE -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-calendar-days text-primary me-2"></i> {{ $isArabic ? 'التواريخ والمواعيد' : 'Dates & Deadline' }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ $isArabic ? 'تاريخ بداية المعسكر' : 'Start Date' }} <span class="text-danger">*</span></label>
                                <input type="date" name="starts_on" class="form-control" value="{{ old('starts_on', $camp->starts_on?->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ $isArabic ? 'تاريخ نهاية المعسكر' : 'End Date' }} <span class="text-danger">*</span></label>
                                <input type="date" name="ends_on" class="form-control" value="{{ old('ends_on', $camp->ends_on?->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ $isArabic ? 'آخر موعد للتسجيل' : 'Registration Deadline' }}</label>
                                <input type="date" name="registration_deadline" class="form-control" value="{{ old('registration_deadline', $camp->registration_deadline?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INCLUDED SERVICES -->
                @php
                    $incSrv = is_array($camp->included_services) ? $camp->included_services : [];
                @endphp
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-list-check text-primary me-2"></i> {{ $isArabic ? 'الخدمات والتجهيزات المشمولة بالباقة' : 'Included Services' }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="flights" id="srv1" {{ in_array('flights', $incSrv) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="srv1">✈️ {{ $isArabic ? 'تذاكر الطيران' : 'Flight Tickets' }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="hotel" id="srv2" {{ in_array('hotel', $incSrv) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="srv2">🏨 {{ $isArabic ? 'الإقامة الفندقية' : 'Hotel Accommodation' }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="meals" id="srv3" {{ in_array('meals', $incSrv) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="srv3">🍔 {{ $isArabic ? 'الوجبات الغذائية الكاملة' : 'Full Meals' }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="transport" id="srv4" {{ in_array('transport', $incSrv) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="srv4">🚌 {{ $isArabic ? 'المواصلات والنقل الداخلي' : 'Bus Transport' }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="tshirt" id="srv5" {{ in_array('tshirt', $incSrv) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="srv5">👕 {{ $isArabic ? 'الزي الموحد (T-Shirt)' : 'Camp Kit / T-Shirt' }}</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="included_services[]" value="certificates" id="srv6" {{ in_array('certificates', $incSrv) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="srv6">📜 {{ $isArabic ? 'شهادات المشاركة' : 'Certificates' }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="visa_required" value="1" id="visaSwitch" {{ old('visa_required', $camp->visa_required) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark" for="visaSwitch">
                                    {{ $isArabic ? 'يتطلب الحصول على تأشيرة دخول (Visa Required)' : 'Visa Required for Entry' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: PRICING -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-coins text-primary me-2"></i> {{ $isArabic ? 'التسعير والأعداد' : 'Pricing & Capacity' }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $isArabic ? 'السعة الإجمالية (عدد المقاعد)' : 'Total Capacity' }} <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" class="form-control" min="1" value="{{ old('capacity', $camp->capacity) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $isArabic ? 'سعر اشتراك المشترك للفرد (' . $currency['symbol'] . ')' : 'Price Per Person (' . $currency['code'] . ')' }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $camp->price) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $isArabic ? 'مبلغ العربون / الدفعة الأولى' : 'Deposit Amount' }}</label>
                            <input type="number" step="0.01" name="deposit_amount" class="form-control" value="{{ old('deposit_amount', $camp->deposit_amount) }}">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm mb-2">
                            <i class="fa-solid fa-save me-1"></i>
                            {{ $isArabic ? 'حفظ التعديلات' : 'Save Changes' }}
                        </button>
                        <a href="{{ route('academy.camps.show', $camp->id) }}" class="btn btn-light border w-100 fw-bold">
                            {{ $isArabic ? 'إلغاء' : 'Cancel' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country_select');
    const homeCountryId = "{{ $homeCountry?->id ?? '' }}";
    const homeCountryName = "{{ $homeCountry?->name ?? ($isArabic ? 'مصر' : 'Egypt') }}";

    const visaBox = document.getElementById('visa_dynamic_box');
    const visaBadge = document.getElementById('visa_alert_badge');
    const visaIcon = document.getElementById('visa_badge_icon');
    const visaTitle = document.getElementById('visa_badge_title');
    const visaDesc = document.getElementById('visa_badge_desc');
    const visaLink = document.getElementById('visa_official_link');
    const campTypeSelect = document.querySelector('select[name="type"]');
    const visaSwitch = document.getElementById('visaSwitch') || document.querySelector('input[name="visa_required"]');

    function checkVisaHint() {
        if (!countrySelect) return;
        const countryId = countrySelect.value;
        const selectedOption = countrySelect.options[countrySelect.selectedIndex];
        if (!countryId || !selectedOption) {
            if (visaBox) visaBox.classList.add('d-none');
            return;
        }

        const countryName = selectedOption.getAttribute('data-name') || selectedOption.text;
        const iso2 = selectedOption.getAttribute('data-iso2') || '';

        if (visaBox) {
            visaBox.classList.remove('d-none');
            if (countryId == homeCountryId || iso2 === 'EG') {
                // Domestic Camp
                visaBadge.className = 'p-3 bg-white rounded-3 border-start border-4 border-success border shadow-sm d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 w-100';
                visaIcon.innerHTML = '🟢 🇪🇬';
                visaTitle.className = 'fw-bold text-success mb-1 fs-6';
                visaTitle.innerText = '{{ $isArabic ? "معسكر محلي - لا تتطلب تأشيرة دخول للمواطنين" : "Domestic Camp - No Visa Required" }}';
                visaDesc.className = 'mb-0 text-dark fw-medium small';
                visaDesc.innerText = `{{ $isArabic ? "الدولة المستضيفة هي نفسها دولة الشريك" : "Host country matches partner home country" }} (${homeCountryName}). {{ $isArabic ? "تنقل وتدريب محلي بدون إجراءات تأشيرة." : "No visa required." }}`;
                if (visaLink) visaLink.classList.add('d-none');
            } else {
                // International Camp
                visaBadge.className = 'p-3 bg-white rounded-3 border-start border-4 border-primary border shadow-sm d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 w-100';
                visaIcon.innerHTML = '✈️ 🌍';
                visaTitle.className = 'fw-bold text-dark mb-1 fs-6';
                visaTitle.innerText = `{{ $isArabic ? "معسكر دولي - تتطلب تأشيرة دخول (Visa) إلى" : "International Camp - Visa Required to" }} (${countryName})`;
                visaDesc.className = 'mb-0 text-dark fw-medium small';
                visaDesc.innerText = `{{ $isArabic ? "للمواطنين والمقيمين التابعين لـ" : "For citizens of" }} (${homeCountryName})، {{ $isArabic ? "يُرجى الاستعلام واستخراج التأشيرة قبل موعد السفر." : "please check visa requirements before departure." }}`;

                if (visaLink) {
                    const q = encodeURIComponent(`visa requirements for ${homeCountryName} passport travelling to ${countryName} official application portal`);
                    visaLink.href = `https://www.google.com/search?q=${q}`;
                    visaLink.className = 'btn btn-primary fw-bold text-white shadow-sm px-3 py-2 text-nowrap';
                    visaLink.classList.remove('d-none');
                }
            }
        }
    }

    if (countrySelect) {
        countrySelect.addEventListener('change', checkVisaHint);
        checkVisaHint(); // trigger on page load
    }
});
</script>
@endsection
