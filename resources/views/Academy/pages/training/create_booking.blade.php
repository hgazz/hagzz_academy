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
</style>
@endpush

@section('content')
@php($ar = app()->getLocale() === 'ar')
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
                                    <li class="breadcrumb-item"><a href="{{ route('academy.training.index') }}">{{ trans('admin.training.trainings') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('academy.report.offline-joins') }}">{{ trans('admin.bookings.offline_bookings') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $ar ? 'تسجيل حجز مباشر' : 'Create Offline Booking' }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </header>
            </div>
        </div>
        <!--  END BREADCRUMBS  -->

        <!-- HeroUI Header Banner -->
        <div class="heroui-header-banner">
            <div>
                <h1 class="heroui-header-title">
                    <span class="title-icon"><i class="fas fa-cash-register"></i></span>
                    {{ $ar ? 'تسجيل حجز مباشر (أوفلاين)' : 'Create Offline Booking' }}
                </h1>
                <p class="heroui-header-subtitle">
                    {{ $ar ? 'تسجيل وحجز مقعد لطالب مسجل في البرنامج التدريبي وتحديد المبالغ المدفوعة وطريقة الدفع' : 'Register and book a seat for an enrolled student and record on-premise payments' }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('academy.report.offline-joins') }}" class="heroui-btn heroui-btn-light">
                    <i class="fas fa-list-check text-primary"></i>
                    <span>{{ trans('admin.bookings.offline_bookings') }}</span>
                </a>
                <a href="{{ route('academy.students.create') }}" class="heroui-btn heroui-btn-primary">
                    <i class="fas fa-user-plus"></i>
                    <span>{{ $ar ? 'إضافة طالب جديد' : 'Add New Student' }}</span>
                </a>
            </div>
        </div>

        <!-- Live Financial Summary Cards -->
        <div class="heroui-stat-grid mb-4">
            <div class="heroui-stat-card primary">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ $ar ? 'إجمالي قيمة التدريب' : 'Total Training Fee' }}</span>
                    <h2 class="stat-value" id="display_price">0.00</h2>
                </div>
                <div class="heroui-stat-icon primary">
                    <i class="fas fa-tag"></i>
                </div>
            </div>

            <div class="heroui-stat-card success">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ $ar ? 'المبلغ المسدد' : 'Paid Amount' }}</span>
                    <h2 class="stat-value text-success" id="display_paid">0.00</h2>
                </div>
                <div class="heroui-stat-icon success">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
            </div>

            <div class="heroui-stat-card secondary" id="remaining_card">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ $ar ? 'المبلغ المتبقي' : 'Remaining Balance' }}</span>
                    <h2 class="stat-value text-danger" id="display_remaining">0.00</h2>
                </div>
                <div class="heroui-stat-icon secondary">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>

            <div class="heroui-stat-card info">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ $ar ? 'حالة السداد المتوقعة' : 'Payment Status' }}</span>
                    <h2 class="stat-value text-info fs-5" id="display_status">
                        <span class="heroui-chip heroui-chip-default">{{ $ar ? 'في انتظار الاختيار' : 'Awaiting Selection' }}</span>
                    </h2>
                </div>
                <div class="heroui-stat-icon info">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
        </div>

        <!-- HeroUI Form Box -->
        <div class="heroui-card-table mb-4">
            @if($errors->any())
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
                            <i class="fa-solid fa-user-graduate text-primary"></i>
                            <span>{{ $ar ? 'الطالب أو العضو' : 'Student or Member' }}</span>
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="academy_student_id" name="academy_student_id" required>
                            <option value="">{{ $ar ? 'اختر أو ابحث عن الطالب بالاسم أو الهاتف...' : 'Select or search student by name or phone...' }}</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('academy_student_id') == $student->id)>
                                    {{ $student->name }}{{ $student->phone ? ' — ' . $student->phone : '' }}{{ $student->guardian_name ? ' (ولي الأمر: ' . $student->guardian_name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @if($students->isEmpty())
                            <small class="text-danger d-block mt-2">
                                <i class="fa fa-info-circle me-1"></i> {{ $ar ? 'لا يوجد طلاب نشطون حالياً. يرجى إضافة الطالب أولاً.' : 'No active students found. Please add a student first.' }}
                            </small>
                        @endif
                    </div>

                    <!-- Optional Sport Filter -->
                    @if(isset($sports) && $sports->count() > 1)
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

                    <!-- Training Selector -->
                    <div class="col-lg-{{ isset($sports) && $sports->count() > 1 ? '4' : '6' }}">
                        <label class="heroui-filter-label" for="training_id">
                            <i class="fa-solid fa-dumbbell text-primary"></i>
                            <span>{{ trans('admin.training.training_name') }}</span>
                            <span class="text-danger">*</span>
                        </label>
                        <div class="heroui-select-wrap">
                            <select class="heroui-select" id="training_id" name="training_id" required>
                                <option value="">{{ trans('admin.academies.select_training') }}</option>
                                @foreach($data as $training)
                                    <option value="{{ $training->id }}"
                                            data-sport="{{ $training->sport_id }}"
                                            data-price="{{ number_format((float)$training->price, 2, '.', '') }}"
                                            @selected(old('training_id', request('training_id')) == $training->id)>
                                        {{ $training->name }} — ({{ number_format((float)$training->price, 2) }} {{ $ar ? 'ج.م' : 'EGP' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Financial Inputs Row -->
                    <div class="col-md-4">
                        <label class="heroui-filter-label">
                            <i class="fa-solid fa-tag text-primary"></i>
                            <span>{{ $ar ? 'قيمة الحجز الأساسية' : 'Booking Fee' }}</span>
                        </label>
                        <input id="price" class="heroui-select bg-light text-muted fw-bold" type="number" step="0.01" readonly placeholder="0.00">
                    </div>

                    <div class="col-md-4">
                        <label class="heroui-filter-label" for="paid_amount">
                            <i class="fa-solid fa-money-bill-wave text-success"></i>
                            <span>{{ $ar ? 'المبلغ المدفوع نقدياً / الآن' : 'Paid Amount' }}</span>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="paid_amount" name="paid_amount" class="heroui-select fw-bold text-success" type="number" min="0" step="0.01" value="{{ old('paid_amount', 0) }}" required placeholder="0.00">
                    </div>

                    <div class="col-md-4">
                        <label class="heroui-filter-label">
                            <i class="fa-solid fa-scale-balanced text-danger"></i>
                            <span>{{ $ar ? 'المبلغ المتبقي' : 'Remaining Amount' }}</span>
                        </label>
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
                            @foreach(App\Helpers\PaymentMethodHelper::getMethodsForCountry(auth('academy')->user()?->academy?->country?->iso2 ?: 'SA') as $pm)
                                <label class="heroui-payment-card @checked(old('payment_method', 'cash') === $pm['id'])">
                                    <input type="radio" name="payment_method" value="{{ $pm['id'] }}" @checked(old('payment_method', 'cash') === $pm['id']) required>
                                    <img src="{{ $pm['logo'] }}" alt="{{ $pm['name_ar'] }}">
                                    <span>{{ $ar ? $pm['name_ar'] : $pm['name_en'] }}</span>
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
                            <span>{{ $ar ? 'تأكيد وحفظ الحجز' : 'Confirm & Save Booking' }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Select2 on Student Select for quick search
    if (window.jQuery && $('#academy_student_id').length) {
        $('#academy_student_id').select2({
            placeholder: "{{ $ar ? 'اختر أو ابحث عن الطالب بالاسم أو الهاتف...' : 'Select or search student by name or phone...' }}",
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
                displayStatus.innerHTML = `<span class="heroui-chip heroui-chip-default">{{ $ar ? 'اختر التدريب' : 'Select Training' }}</span>`;
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
