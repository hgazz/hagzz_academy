@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'تسجيل حجز مباشر' : 'Create Offline Booking')

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/assets/css/heroui-theme.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 48px;
        border: 1.5px solid var(--heroui-border);
        border-radius: var(--heroui-radius-md);
        display: flex;
        align-items: center;
        padding: 0 14px;
        background-color: var(--heroui-bg);
        transition: all 0.2s ease;
    }
    .select2-container--default.select2-container--open .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--heroui-primary);
        background: var(--heroui-surface);
        box-shadow: 0 0 0 4px var(--heroui-primary-light);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 46px;
        padding: 0;
        color: var(--heroui-text);
        font-size: 14px;
        font-weight: 600;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
        right: 12px;
    }
    [dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__arrow {
        left: 12px;
        right: auto;
    }
    .select2-dropdown {
        border: 1.5px solid var(--heroui-border);
        border-radius: var(--heroui-radius-md);
        box-shadow: var(--heroui-shadow-lg);
        background: var(--heroui-surface);
        color: var(--heroui-text);
        z-index: 1060;
        overflow: hidden;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1.5px solid var(--heroui-border);
        border-radius: var(--heroui-radius-sm);
        padding: 10px 14px;
        font-size: 13px;
        outline: none;
        background: var(--heroui-bg);
        color: var(--heroui-text);
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--heroui-primary);
    }
    .select2-results__option {
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        color: var(--heroui-text);
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: var(--heroui-primary) !important;
        color: #ffffff !important;
    }
    .select2-container {
        width: 100% !important;
    }

    /* Financial dynamic live preview cards */
    .financial-preview-card {
        background: var(--heroui-bg);
        border: 1.5px solid var(--heroui-border);
        border-radius: var(--heroui-radius-lg);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: all 0.25s ease;
    }
    .financial-preview-card:hover {
        border-color: rgba(99, 102, 241, 0.3);
        background: var(--heroui-surface);
        box-shadow: var(--heroui-shadow-sm);
    }
    .financial-preview-card .fp-value {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.5px;
        line-height: 1.1;
    }

    /* Ultra-Compact Logo-Only Payment Badges */
    .heroui-payment-grid {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        align-items: center !important;
    }
    .heroui-payment-card {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 5px 12px !important;
        height: 38px !important;
        min-width: 58px !important;
        border: 1.5px solid var(--heroui-border) !important;
        border-radius: 10px !important;
        background: var(--heroui-surface) !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        margin-bottom: 0 !important;
        user-select: none !important;
        position: relative !important;
    }
    .heroui-payment-card input[type="radio"] {
        position: absolute !important;
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        pointer-events: none !important;
    }
    .heroui-payment-card:hover {
        border-color: var(--heroui-primary) !important;
        background: var(--heroui-surface-hover) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06) !important;
    }
    .heroui-payment-card.active,
    .heroui-payment-card:has(input[type="radio"]:checked) {
        border-color: var(--heroui-primary) !important;
        background: rgba(99, 102, 241, 0.1) !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.22) !important;
    }
    .heroui-payment-card img {
        height: 22px !important;
        max-width: 48px !important;
        object-fit: contain !important;
        display: block !important;
        filter: drop-shadow(0 1px 1px rgba(0,0,0,0.06)) !important;
    }
</style>
@endpush

@section('content')
@php
    $ar = app()->getLocale() === 'ar';
    $userAuth = auth('academy')->user();
    $academy = ($userAuth instanceof \App\Models\PartnerUser && $userAuth->academy) ? $userAuth->academy : ($userAuth?->academy ?: $userAuth);
    $facilityType = $facilityType ?? ($academy?->business_type ?? 'academy');
    $isGymFacility = $isGymFacility ?? in_array($facilityType, ['gym', 'health_center', 'fitness']);
    $academyCountry = $academy?->country;
    $countryIso = $academyCountry?->iso2 ?: 'SA';
    $currencyLabel = $academy?->currency_symbol ?: ($countryIso === 'EG' ? ($ar ? 'ج.م' : 'EGP') : ($ar ? 'ر.س' : 'SAR'));
@endphp
    <div class="middle-content container-xxl p-0 heroui-wrapper">

        <!--  BEGIN BREADCRUMBS  -->
        <div class="secondary-nav mb-4">
            <div class="breadcrumbs-container" data-page-heading="Analytics">
                <header class="header navbar navbar-expand-sm">
                    <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </a>
                    <div class="d-flex breadcrumb-content">
                        <div class="page-header">
                            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('academy.training.index') }}">{{ !empty($isGymFacility) ? ($ar ? 'باقات واشتراكات النادي' : 'Membership Plans') : trans('admin.training.trainings') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('academy.report.offline-joins') }}">{{ !empty($isGymFacility) ? ($ar ? 'سجل الاشتراكات' : 'Memberships') : trans('admin.bookings.offline_bookings') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $ar ? (!empty($isGymFacility) ? 'تسجيل اشتراك جديد' : 'تسجيل حجز مباشر') : (!empty($isGymFacility) ? 'Register Membership' : 'Create Offline Booking') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </header>
            </div>
        </div>
        <!--  END BREADCRUMBS  -->

        @if(session('new_booking'))
            @php $nb = session('new_booking'); @endphp
            <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a, #1e293b); color: #fff;">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: rgba(34, 197, 94, 0.2); width: 56px; height: 56px; flex-shrink: 0;">
                                <i class="fa-solid fa-circle-check text-success fa-2x"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <h5 class="fw-bold mb-0 text-white" style="font-size: 1.15rem;">
                                        {{ $ar ? (!empty($isGymFacility) ? 'تم تسجيل وتفعيل اشتراك العضو بنجاح! 🎉' : 'تم تأكيد وحفظ الحجز بنجاح! 🎉') : 'Registration Confirmed Successfully! 🎉' }}
                                    </h5>
                                    @if(!empty($nb['sent_via_cloud_api']))
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-2 py-1 rounded-pill small">
                                            <i class="fa-brands fa-whatsapp me-1"></i> {{ $ar ? 'تم إرسال إشعار الواتساب تلقائياً' : 'WhatsApp sent automatically' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-white-50" style="font-size: 13px;">
                                    <span class="text-light fw-bold">{{ $nb['student_name'] }}</span>
                                    @if(!empty($nb['student_phone']))
                                        <span class="font-monospace text-white-50 ms-1 me-1">({{ $nb['student_phone'] }})</span>
                                    @endif
                                    • {{ $ar ? 'الباقة:' : 'Plan:' }} <span class="text-info fw-bold">{{ $nb['plan_name'] }}</span>
                                    • {{ $ar ? 'المسدد:' : 'Paid:' }} <span class="text-success fw-bold">{{ number_format($nb['paid'], 2) }} {{ $nb['currency'] }}</span>
                                    @if($nb['remaining'] > 0)
                                        • {{ $ar ? 'المتبقي:' : 'Balance:' }} <span class="text-warning fw-bold">{{ number_format($nb['remaining'], 2) }} {{ $nb['currency'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Direct Action Buttons -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if(!empty($nb['clean_phone']) && !empty($nb['whatsapp_url']))
                                <a href="{{ $nb['whatsapp_url'] }}" target="_blank" class="btn btn-success fw-bold px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" style="background: #25D366; border-color: #25D366;">
                                    <i class="fa-brands fa-whatsapp fa-lg"></i>
                                    <span>{{ $ar ? 'إرسال الفاتورة بالواتساب فوراً' : 'Send via WhatsApp' }}</span>
                                </a>
                            @endif

                            @if(!empty($nb['print_a4_url']))
                                <a href="{{ $nb['print_a4_url'] }}" target="_blank" class="btn btn-primary fw-bold px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #0f766e, #0d9488); border: none;">
                                    <i class="fa-solid fa-print"></i>
                                    <span>{{ $ar ? 'طباعة الفاتورة A4' : 'Print A4 Invoice' }}</span>
                                </a>
                            @endif

                            @if(!empty($nb['print_pos_url']))
                                <a href="{{ $nb['print_pos_url'] }}" target="_blank" class="btn btn-outline-light fw-bold px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>{{ $ar ? 'إيصال كاشير (POS)' : 'Thermal POS' }}</span>
                                </a>
                            @endif

                            @if(!empty($nb['card_url']))
                                <a href="{{ $nb['card_url'] }}" target="_blank" class="btn btn-outline-light fw-bold px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-id-card"></i>
                                    <span>{{ $ar ? 'كارت العضوية' : 'Member Card' }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Compact Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white p-2 rounded-3" style="font-size: 15px;">
                        <i class="{{ !empty($isGymFacility) ? 'fa-solid fa-address-card' : 'fas fa-cash-register' }}"></i>
                    </span>
                    <span>{{ $ar ? (!empty($isGymFacility) ? 'تسجيل اشتراك / باقة عضوية' : 'تسجيل حجز مباشر') : (!empty($isGymFacility) ? 'Register Membership Plan' : 'Create Offline Booking') }}</span>
                </h4>
                <p class="text-muted mb-0" style="font-size: 13px;">
                    {{ $ar ? (!empty($isGymFacility) ? 'اختر العضو والباقة وحدد المبلغ المدفوع لتأكيد الاشتراك مباشرة.' : 'اختر الطالب والبرنامج وسجل المبلغ المدفوع.') : 'Select member and plan to record the booking.' }}
                </p>
            </div>
            <div>
                <a href="{{ route('academy.students.create') }}" class="btn btn-primary btn-sm px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm rounded-3">
                    <i class="fas fa-user-plus"></i>
                    <span>{{ $ar ? (!empty($isGymFacility) ? 'تسجيل عضو جديد' : 'إضافة طالب جديد') : (!empty($isGymFacility) ? 'Register Member' : 'Add New Student') }}</span>
                </a>
            </div>
        </div>

        <!-- HeroUI Form Box -->
        <div class="heroui-card-table mb-4">
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4">
                    <i class="fa fa-circle-exclamation me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('academy.storeBooking') }}">
                @csrf
                <div class="row g-4">
                    <!-- Student Selector -->
                    <div class="col-lg-{{ isset($sports) && $sports->count() > 1 ? '4' : '6' }}">
                        <label class="heroui-filter-label" for="academy_student_id">
                            <i class="{{ !empty($isGymFacility) ? 'fa-solid fa-user-check' : 'fa-solid fa-user-graduate' }} text-primary"></i>
                            <span>{{ $ar ? (!empty($isGymFacility) ? 'العضو المشترك' : 'الطالب أو العضو') : (!empty($isGymFacility) ? 'Member' : 'Student or Member') }}</span>
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="academy_student_id" name="academy_student_id" required>
                            <option value="">{{ $ar ? (!empty($isGymFacility) ? 'اختر أو ابحث عن العضو بالاسم أو الهاتف...' : 'اختر أو ابحث عن الطالب بالاسم أو الهاتف...') : (!empty($isGymFacility) ? 'Select or search member by name or phone...' : 'Select or search student...') }}</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('academy_student_id') == $student->id)>
                                    {{ $student->name }}{{ $student->phone ? ' — ' . $student->phone : '' }}{{ (!empty($isGymFacility) || !$student->guardian_name) ? '' : ' (ولي الأمر: ' . $student->guardian_name . ')' }}
                                </option>
                            @endforeach
                        </select>
                        @if($students->isEmpty())
                            <small class="text-danger d-block mt-2">
                                <i class="fa fa-info-circle me-1"></i> {{ $ar ? (!empty($isGymFacility) ? 'لا يوجد أعضاء نشطون حالياً. يرجى تسجيل العضو أولاً.' : 'لا يوجد طلاب نشطون حالياً. يرجى إضافة الطالب أولاً.') : (!empty($isGymFacility) ? 'No active members found. Please register a member first.' : 'No active students found. Please add a student first.') }}
                            </small>
                        @endif
                    </div>

                    <!-- Optional Category Filter (only for academies or gyms with multiple distinct activity categories) -->
                    @if(isset($sports) && $sports->count() > 1 && empty($isGymFacility))
                    <div class="col-lg-4" id="sport_wrap">
                        <label class="heroui-filter-label" for="sport_id">
                            <i class="fa-solid fa-trophy text-warning"></i>
                            <span>{{ $ar ? 'نوع الرياضة' : 'Sport Type' }}</span>
                        </label>
                        <div class="heroui-select-wrap">
                            <select class="heroui-select" id="sport_id">
                                <option value="">{{ $ar ? '✨ جميع الرياضات' : '✨ All Sports' }}</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <!-- Training / Membership Plan Selector -->
                    <div class="col-lg-{{ (isset($sports) && $sports->count() > 1 && empty($isGymFacility)) ? '4' : '6' }}">
                        <label class="heroui-filter-label" for="training_id">
                            <i class="{{ !empty($isGymFacility) ? 'fa-solid fa-address-card' : 'fa-solid fa-dumbbell' }} text-primary"></i>
                            <span>{{ $ar ? (!empty($isGymFacility) ? 'باقة العضوية / نوع ومدة الاشتراك' : trans('admin.training.training_name')) : (!empty($isGymFacility) ? 'Membership Plan / Duration' : trans('admin.training.training_name')) }}</span>
                            <span class="text-danger">*</span>
                        </label>
                        <div class="heroui-select-wrap">
                            <select class="heroui-select" id="training_id" name="training_id" required>
                                <option value="">{{ $ar ? (!empty($isGymFacility) ? 'اختر باقة العضوية أو مدة الاشتراك...' : trans('admin.academies.select_training')) : (!empty($isGymFacility) ? 'Select Membership Plan / Duration...' : trans('admin.academies.select_training')) }}</option>
                                @foreach($data as $training)
                                    <option value="{{ $training->id }}"
                                            data-sport="{{ $training->sport_id }}"
                                            data-price="{{ number_format((float)$training->price, 2, '.', '') }}"
                                            @selected(old('training_id', request('training_id')) == $training->id)>
                                        {{ $training->name }} — ({{ number_format((float)$training->price, 2) }} {{ $currencyLabel }}){{ $training->classes_number ? ' • ' . $training->classes_number . ($ar ? ' حصة' : ' sessions') : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Financial Inputs Row -->
                    <div class="col-md-4">
                        <label class="heroui-filter-label">
                            <i class="fa-solid fa-tag text-primary"></i>
                            <span>{{ $ar ? (!empty($isGymFacility) ? 'قيمة الاشتراك' : 'قيمة الحجز') : (!empty($isGymFacility) ? 'Membership Fee' : 'Booking Fee') }}</span>
                            <span class="text-muted fw-bold">({{ $currencyLabel }})</span>
                        </label>
                        <input id="price" class="heroui-select bg-light text-muted fw-bold" type="number" step="0.01" readonly placeholder="0.00">
                    </div>

                    <div class="col-md-4">
                        <label class="heroui-filter-label" for="paid_amount">
                            <i class="fa-solid fa-money-bill-wave text-success"></i>
                            <span>{{ $ar ? 'المبلغ المدفوع الآن' : 'Paid Amount' }}</span>
                            <span class="text-danger">*</span>
                            <span class="text-muted fw-bold">({{ $currencyLabel }})</span>
                        </label>
                        <input id="paid_amount" name="paid_amount" class="heroui-select fw-bold text-success" type="number" min="0" step="0.01" value="{{ old('paid_amount', 0) }}" required placeholder="0.00">
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="heroui-filter-label mb-0">
                                <i class="fa-solid fa-scale-balanced text-danger"></i>
                                <span>{{ $ar ? 'المبلغ المتبقي' : 'Remaining Amount' }}</span>
                                <span class="text-muted fw-bold">({{ $currencyLabel }})</span>
                            </label>
                            <span id="display_status">
                                <span class="heroui-chip heroui-chip-default" style="font-size: 11px; padding: 2px 8px;">{{ $ar ? 'في انتظار الاختيار' : 'Awaiting Selection' }}</span>
                            </span>
                        </div>
                        <input id="remaining_amount" class="heroui-select bg-light text-danger fw-bold" type="number" step="0.01" readonly placeholder="0.00">
                    </div>

                    <!-- Payment Methods Section -->
                    <div class="col-12 mt-4 pt-3 border-top">
                        <label class="heroui-filter-label mb-3">
                            <i class="fa-solid fa-credit-card text-primary"></i>
                            <span>{{ trans('admin.payment_method') }}</span>
                            <span class="text-danger">*</span>
                        </label>
                        <div class="heroui-payment-grid">
                            @foreach(App\Helpers\PaymentMethodHelper::getMethodsForCountry($countryIso) as $pm)
                                <label class="heroui-payment-card @checked(old('payment_method', 'cash') === $pm['id'])"
                                       title="{{ $ar ? $pm['name_ar'] : $pm['name_en'] }}"
                                       data-bs-toggle="tooltip">
                                    <input type="radio" name="payment_method" value="{{ $pm['id'] }}" @checked(old('payment_method', 'cash') === $pm['id']) required>
                                    <img src="{{ $pm['logo'] }}" alt="{{ $pm['name_ar'] }}">
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Optional Other Method Specification -->
                    <div class="col-md-6 d-none" id="other_wrap">
                        <label class="heroui-filter-label" for="payment_method_other">
                            <i class="fa fa-pen text-muted"></i>
                            <span>{{ trans('admin.payment_method_other') }}</span>
                        </label>
                        <input id="payment_method_other" name="payment_method_other" class="heroui-select" value="{{ old('payment_method_other') }}" placeholder="{{ $ar ? 'اكتب اسم طريقة الدفع...' : 'Specify payment method...' }}">
                    </div>

                    <!-- Form Action Buttons -->
                    <div class="col-12 d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('academy.report.offline-joins') }}" class="heroui-btn heroui-btn-light">
                            <span>{{ $ar ? 'إلغاء' : 'Cancel' }}</span>
                        </a>
                        <button class="heroui-btn heroui-btn-primary px-4 py-2" type="submit" @disabled($students->isEmpty())>
                            <i class="fas fa-check-circle"></i>
                            <span>{{ $ar ? (!empty($isGymFacility) ? 'تأكيد وحفظ الاشتراك' : 'تأكيد وحفظ الحجز') : (!empty($isGymFacility) ? 'Confirm & Save Subscription' : 'Confirm & Save Booking') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- RECENT REGISTRATIONS TABLE -->
        @if(isset($recentJoins) && $recentJoins->isNotEmpty())
        <div class="heroui-card-table mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary bg-opacity-10 text-secondary p-2 rounded-3">
                        <i class="fa-solid fa-list-check"></i>
                    </span>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 15px;">
                            {{ $ar ? (!empty($isGymFacility) ? 'آخر الاشتراكات المسجلة حديثاً' : 'آخر الحجوزات المسجلة حديثاً') : (!empty($isGymFacility) ? 'Recent Registered Memberships' : 'Recent Bookings') }}
                        </h5>
                        <small class="text-muted">{{ $ar ? 'سجل فوري بآخر الاشتراكات والعمليات للطباعة والمتابعة السريعة' : 'Instant list of latest entries for print and follow-up' }}</small>
                    </div>
                </div>
                <a href="{{ route('academy.report.offline-joins') }}" class="btn btn-sm btn-outline-primary fw-bold rounded-3">
                    <span>{{ $ar ? 'عرض كافة الاشتراكات' : 'View All' }}</span>
                    <i class="fa-solid fa-arrow-left ms-1 me-1"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="ps-3">{{ $ar ? '#' : '#' }}</th>
                            <th>{{ $ar ? (!empty($isGymFacility) ? 'العضو' : 'الطالب / المتدرب') : 'Member / Student' }}</th>
                            <th>{{ $ar ? (!empty($isGymFacility) ? 'باقة الاشتراك' : 'البرنامج') : 'Plan / Program' }}</th>
                            <th>{{ $ar ? 'المسدد / الإجمالي' : 'Paid / Total' }}</th>
                            <th>{{ $ar ? 'حالة السداد' : 'Payment Status' }}</th>
                            <th>{{ $ar ? 'التاريخ' : 'Date' }}</th>
                            <th class="text-end pe-3">{{ $ar ? 'إجراءات سريعة' : 'Quick Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentJoins as $rj)
                            @php
                                $rInvoice = $rj->invoice;
                                $rStudent = $rj->student ?: $rj->user;
                                $rTotal = (float) ($rInvoice?->amount ?: ($rj->price ?: 0));
                                $rPaid = (float) ($rInvoice?->collected_amount ?: 0);
                                $rRem = max(0, $rTotal - $rPaid);
                                $rState = $rInvoice?->payment_state ?: ($rRem <= 0 ? 'paid' : ($rPaid > 0 ? 'partial' : 'unpaid'));
                                $rInvId = $rInvoice?->id ?: $rj->invoice_id;

                                // WhatsApp message
                                $rPhone = $rStudent?->phone ?: ($rj->phone ?: null);
                                $rRawPhone = (string) $rPhone;
                                $rDigits = preg_replace('/\D+/', '', $rRawPhone);
                                if (str_starts_with($rDigits, '00')) {
                                    $rClean = substr($rDigits, 2);
                                } elseif (str_starts_with($rDigits, '01') && strlen($rDigits) === 11) {
                                    $rClean = '20' . substr($rDigits, 1);
                                } elseif (str_starts_with($rDigits, '05') && strlen($rDigits) === 10) {
                                    $rClean = '966' . substr($rDigits, 1);
                                } elseif (str_starts_with($rDigits, '0') && strlen($rDigits) > 7) {
                                    $rClean = substr($rDigits, 1);
                                } else {
                                    $rClean = $rDigits;
                                }

                                $rPublicUrl = $rInvId ? route('invoices.public.view', ['type' => 'booking', 'id' => $rInvId]) : '';
                                $rName = $rStudent?->name ?: ($rj->name ?: 'العميل');
                                $rItem = $rj->training?->name ?: 'باقة الاشتراك';

                                if ($ar) {
                                    $rWaMsg = "مرحباً بك كابتن *{$rName}* 🌟\n"
                                        . "يسعدنا تأكيد تسجيل اشتراكك:\n\n"
                                        . "📋 *الباقة:* {$rItem}\n"
                                        . "🔢 *رقم الفاتورة:* #" . ($rInvoice?->order_number ?: $rj->id) . "\n"
                                        . "💰 *الإجمالي:* " . number_format($rTotal, 2) . " {$currencyLabel}\n"
                                        . "✅ *المسدد:* " . number_format($rPaid, 2) . " {$currencyLabel}\n"
                                        . "⏳ *المتبقي:* " . number_format($rRem, 2) . " {$currencyLabel}\n\n"
                                        . ($rPublicUrl ? "🔗 *رابط الفاتورة الموحدة:* \n{$rPublicUrl}\n\n" : "")
                                        . "نتمنى لك تدريباً ممتعاً! 💪🔥";
                                } else {
                                    $rWaMsg = "Hello *{$rName}* 🌟\n"
                                        . "Your booking is confirmed:\n"
                                        . "📋 *Plan:* {$rItem}\n"
                                        . "💰 *Total:* " . number_format($rTotal, 2) . " {$currencyLabel}\n"
                                        . "✅ *Paid:* " . number_format($rPaid, 2) . " {$currencyLabel}\n"
                                        . ($rPublicUrl ? "🔗 *Invoice:* \n{$rPublicUrl}\n" : "");
                                }
                                $rWaUrl = 'https://api.whatsapp.com/send?' . ($rClean ? 'phone=' . $rClean . '&' : '') . 'text=' . urlencode($rWaMsg);
                            @endphp
                            <tr>
                                <td class="ps-3 font-monospace text-secondary fw-bold">#{{ $rj->id }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $rStudent?->name ?: '-' }}</div>
                                    @if($rPhone)
                                        <small class="text-muted font-monospace" dir="ltr">{{ $rPhone }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border px-2 py-1 fw-bold">
                                        {{ $rj->training?->name ?: '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">{{ number_format($rPaid, 2) }}</span>
                                    <span class="text-muted">/ {{ number_format($rTotal, 2) }} {{ $currencyLabel }}</span>
                                </td>
                                <td>
                                    @if($rState === 'paid')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-circle-check me-1"></i> {{ $ar ? 'مسدد بالكامل' : 'Paid' }}
                                        </span>
                                    @elseif($rState === 'partial')
                                        <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25 px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-clock-half-duller me-1"></i> {{ $ar ? 'سداد جزئي' : 'Partial' }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">
                                            {{ $ar ? 'غير مسدد' : 'Unpaid' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $rj->created_at ? $rj->created_at->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        @if($rClean)
                                            <a href="{{ $rWaUrl }}" target="_blank" class="btn btn-outline-success" title="{{ $ar ? 'إرسال الفاتورة عبر واتساب' : 'Send WhatsApp' }}" data-bs-toggle="tooltip">
                                                <i class="fa-brands fa-whatsapp text-success"></i>
                                            </a>
                                        @endif
                                        @if($rInvId)
                                            <a href="{{ route('academy.invoices.bookings.print', ['invoice' => $rInvId, 'paper' => 'a4']) }}" target="_blank" class="btn btn-outline-primary" title="{{ $ar ? 'طباعة فاتورة A4 موحدة' : 'Print A4 Invoice' }}" data-bs-toggle="tooltip">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                            <a href="{{ route('academy.invoices.bookings.print', ['invoice' => $rInvId, 'paper' => 'pos']) }}" target="_blank" class="btn btn-outline-secondary" title="{{ $ar ? 'إيصال حراري POS' : 'Thermal Receipt' }}" data-bs-toggle="tooltip">
                                                <i class="fa-solid fa-receipt"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('academy.report.view-booking-details', $rj->id) }}" class="btn btn-outline-dark" title="{{ $ar ? 'عرض التفاصيل' : 'Details' }}" data-bs-toggle="tooltip">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Select2 on Student Select for quick search
    if (window.jQuery && $('#academy_student_id').length) {
        $('#academy_student_id').select2({
            placeholder: "{{ $ar ? (!empty($isGymFacility) ? 'اختر أو ابحث عن العضو بالاسم أو الهاتف...' : 'اختر أو ابحث عن الطالب بالاسم أو الهاتف...') : (!empty($isGymFacility) ? 'Select or search member by name or phone...' : 'Select or search student by name or phone...') }}",
            allowClear: true,
            width: '100%',
            dir: "{{ $ar ? 'rtl' : 'ltr' }}"
        });
    }

    const sport = document.getElementById('sport_id');
    const training = document.getElementById('training_id');
    const paid = document.getElementById('paid_amount');
    const price = document.getElementById('price');
    const remaining = document.getElementById('remaining_amount');
    const otherWrap = document.getElementById('other_wrap');
    const otherInput = document.getElementById('payment_method_other');
    const methodRadios = document.querySelectorAll('input[name="payment_method"]');

    // Display elements
    const displayPrice = document.getElementById('display_price');
    const displayPaid = document.getElementById('display_paid');
    const displayRemaining = document.getElementById('display_remaining');
    const displayStatus = document.getElementById('display_status');

    // Filter trainings by sport if multiple sports exist
    if (sport && training) {
        const trainingOptions = Array.from(training.querySelectorAll('option[data-sport]'));

        sport.addEventListener('change', () => {
            const selectedSport = sport.value;
            const currentSelected = training.value;
            let currentValid = false;

            trainingOptions.forEach(opt => {
                const optSport = opt.dataset.sport;
                const match = !selectedSport || optSport === selectedSport;
                opt.hidden = !match;
                if (!match && opt.value === currentSelected) {
                    currentValid = false;
                } else if (match && opt.value === currentSelected) {
                    currentValid = true;
                }
            });

            if (!currentValid && currentSelected) {
                training.value = '';
                totals();
            }
        });

        // If training is pre-selected, sync sport
        if (training.value) {
            const selectedOpt = training.selectedOptions[0];
            if (selectedOpt && selectedOpt.dataset.sport && sport.querySelector(`option[value="${selectedOpt.dataset.sport}"]`)) {
                sport.value = selectedOpt.dataset.sport;
            }
        }
    }

    function totals() {
        const total = Number(training?.selectedOptions?.[0]?.dataset?.price || 0);
        const paidVal = Number(paid?.value || 0);
        const remVal = Math.max(0, total - paidVal);

        if (price) price.value = total.toFixed(2);
        if (remaining) remaining.value = remVal.toFixed(2);

        // Update live preview cards
        if (displayPrice) displayPrice.textContent = total.toFixed(2);
        if (displayPaid) displayPaid.textContent = paidVal.toFixed(2);
        if (displayRemaining) displayRemaining.textContent = remVal.toFixed(2);

        if (displayStatus) {
            if (total <= 0) {
                displayStatus.innerHTML = `<span class="heroui-chip heroui-chip-default">{{ $ar ? (!empty($isGymFacility) ? 'اختر باقة العضوية' : 'اختر التدريب') : (!empty($isGymFacility) ? 'Select Plan' : 'Select Training') }}</span>`;
            } else if (remVal === 0 && paidVal >= total) {
                displayStatus.innerHTML = `<span class="heroui-chip heroui-chip-success"><span class="chip-dot pulse"></span> {{ $ar ? 'مسدد بالكامل' : 'Fully Paid' }}</span>`;
            } else if (paidVal > 0 && remVal > 0) {
                displayStatus.innerHTML = `<span class="heroui-chip heroui-chip-primary"><span class="chip-dot"></span> {{ $ar ? 'سداد جزئي' : 'Partially Paid' }}</span>`;
            } else {
                displayStatus.innerHTML = `<span class="heroui-chip heroui-chip-default">{{ $ar ? 'غير مسدد' : 'Unpaid' }}</span>`;
            }
        }
    }

    function methodState() {
        const selected = document.querySelector('input[name="payment_method"]:checked')?.value;
        if (otherWrap) {
            const isOther = selected === 'other';
            otherWrap.classList.toggle('d-none', !isOther);
            if (otherInput) otherInput.required = isOther;
        }

        // Highlight card
        document.querySelectorAll('.heroui-payment-card').forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            if (input && input.checked) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });
    }

    if (training) training.addEventListener('change', totals);
    if (paid) paid.addEventListener('input', totals);
    methodRadios.forEach(r => r.addEventListener('change', methodState));

    totals();
    methodState();
});
</script>
@endpush
