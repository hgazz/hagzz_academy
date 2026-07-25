@extends('Academy.Layouts.master')

@section('title', (app()->getLocale() === 'ar' ? 'تفاصيل الحجز #' : 'Booking Details #') . $join->id)

@section('content')
@php
    $ar = app()->getLocale() === 'ar';
    $invoice = $join->invoice;
    $training = $join->training;
    $student = $join->user;

    // Resolve academy safely
    $academy = $training?->academy;
    if (!$academy && auth('academy')->check()) {
        $userAuth = auth('academy')->user();
        $academy = ($userAuth instanceof \App\Models\PartnerUser && $userAuth->academy) ? $userAuth->academy : $userAuth;
    }

    $currency = 'SAR';
    if ($academy) {
        $currency = $academy->currency_symbol;
    } elseif ($training && $training->academy_id) {
        $ac = \App\Models\Academies::with('country')->find($training->academy_id);
        if ($ac) {
            $currency = $ac->currency_symbol;
        }
    } else {
        $currency = $ar ? 'ر.س' : 'SAR';
    }

    // Amounts calculation
    $totalAmount = (float) ($invoice?->amount ?: ($join->price ?: 0));
    $paidAmount = (float) ($invoice?->collected_amount ?: ($join->paid_amount ?: 0));
    $remainingAmount = (float) ($invoice?->remaining_amount ?: max(0, $totalAmount - $paidAmount));

    // Statuses
    $isCanceled = $invoice?->is_canceled || $join->status === 'cancelled';
    $paymentState = $invoice?->payment_state ?: ($remainingAmount <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid'));

    $paymentMethod = $invoice?->payment_method ?: 'cash';
    $allMethods = \App\Helpers\PaymentMethodHelper::getMethodsForCountry($academy?->country?->iso2 ?: 'SA');
    $methodInfo = collect($allMethods)->firstWhere('id', $paymentMethod);
@endphp

<div class="middle-content container-xxl p-0">
    <!--  BREADCRUMBS & TOP BAR  -->
    <div class="secondary-nav mb-4">
        <div class="breadcrumbs-container">
            <header class="header navbar navbar-expand-sm">
                <a href="javascript:void(0);" class="btn-toggle sidebarCollapse"><i data-feather="menu"></i></a>
                <div class="d-flex breadcrumb-content">
                    <nav class="breadcrumb-style-one">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('academy.joins') }}">{{ $ar ? 'إدارة الحجوزات' : 'Booking Management' }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $ar ? 'تفاصيل الحجز #' : 'Booking #' }}{{ $join->id }}</li>
                        </ol>
                    </nav>
                </div>
            </header>
        </div>
    </div>

    <!-- MAIN HEADER CARD -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-xl rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold fs-3" style="width:60px; height:60px;">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h3 class="fw-bold text-dark mb-0">{{ $ar ? 'تفاصيل حجز رقم' : 'Booking Details #' }}#{{ $join->id }}</h3>
                            @if($isCanceled)
                                <span class="badge bg-danger rounded-pill px-3 py-2 fs-6"><i class="fa-solid fa-ban me-1"></i> {{ $ar ? 'حجز ملغى' : 'Cancelled' }}</span>
                            @else
                                <span class="badge bg-success rounded-pill px-3 py-2 fs-6"><i class="fa-solid fa-circle-check me-1"></i> {{ $ar ? 'حجز نشط ومؤكد' : 'Active Booking' }}</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="fa-regular fa-calendar-check me-1"></i> {{ $ar ? 'تاريخ التقديم والتسجيل:' : 'Registered On:' }} 
                            <span class="fw-bold text-dark">{{ $join->created_at ? $join->created_at->format('Y-m-d h:i A') : '-' }}</span>
                        </p>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('academy.joins') }}" class="btn btn-outline-secondary fw-bold px-3">
                        <i class="fa-solid fa-arrow-right me-1"></i> {{ $ar ? 'رجوع للقائمة' : 'Back to List' }}
                    </a>
                    <a href="{{ route('academy.export-booking-file', $join) }}" class="btn btn-success fw-bold px-3">
                        <i class="fa-solid fa-file-excel me-1"></i> {{ $ar ? 'تصدير ملخص (Excel)' : 'Export Excel' }}
                    </a>
                    <button onclick="window.print()" class="btn btn-primary fw-bold px-3">
                        <i class="fa-solid fa-print me-1"></i> {{ $ar ? 'طباعة الحجز' : 'Print Invoice' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SUMMARY METRIC CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-primary border-4">
                <span class="text-muted small fw-bold uppercase">{{ $ar ? 'إجمالي قيمة الحجز' : 'Total Booking Cost' }}</span>
                <h3 class="fw-bold text-primary mb-0 mt-1">{{ number_format($totalAmount, 2) }} <small class="fs-6">{{ $currency }}</small></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-success border-4">
                <span class="text-muted small fw-bold uppercase">{{ $ar ? 'المبلغ المدفوع' : 'Paid Amount' }}</span>
                <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($paidAmount, 2) }} <small class="fs-6">{{ $currency }}</small></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start {{ $remainingAmount > 0 ? 'border-danger' : 'border-secondary' }} border-4">
                <span class="text-muted small fw-bold uppercase">{{ $ar ? 'المبلغ المتبقي' : 'Remaining Amount' }}</span>
                <h3 class="fw-bold {{ $remainingAmount > 0 ? 'text-danger' : 'text-secondary' }} mb-0 mt-1">{{ number_format($remainingAmount, 2) }} <small class="fs-6">{{ $currency }}</small></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-info border-4">
                <span class="text-muted small fw-bold uppercase">{{ $ar ? 'طريقة السداد' : 'Payment Method' }}</span>
                <div class="d-flex align-items-center gap-2 mt-1">
                    @if($methodInfo && isset($methodInfo['logo']))
                        <img src="{{ $methodInfo['logo'] }}" style="height:24px;" alt="logo">
                    @else
                        <i class="fa-solid fa-credit-card text-info fs-5"></i>
                    @endif
                    <h5 class="fw-bold text-dark mb-0 fs-6">{{ $methodInfo ? ($ar ? $methodInfo['name_ar'] : $methodInfo['name_en']) : ucfirst($paymentMethod) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- DETAILS CONTENT (2 COLUMNS) -->
    <div class="row g-4">
        <!-- LEFT MAIN COLUMN -->
        <div class="col-lg-8">
            <!-- STUDENT / PLAYER PROFILE CARD -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-user-graduate text-primary me-2"></i> {{ $ar ? 'بيانات المشترك / الطالب' : 'Student & Member Details' }}
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2">{{ $ar ? 'حساب طفل/عضو' : 'Member Account' }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'اسم المشترك / الطالب:' : 'Student Name:' }}</label>
                            <span class="fw-bold text-dark fs-5">{{ $student?->name ?: ($join->name ?: 'N/A') }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'اسم ولي الأمر:' : 'Parent Name:' }}</label>
                            <span class="fw-bold text-dark fs-6">{{ $student?->parent_name ?: ($join->parent_name ?: 'N/A') }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'رقم هاتف ولي الأمر:' : 'Parent Phone:' }}</label>
                            @php($phone = $student?->parent_phone ?: ($student?->phone ?: $join->phone))
                            @if($phone)
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-dark fs-6 font-monospace" dir="ltr">{{ $phone }}</span>
                                    <a href="tel:{{ $phone }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="{{ $ar ? 'اتصال' : 'Call' }}"><i class="fa-solid fa-phone"></i></a>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $phone) }}" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                                </div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'النوع / الجنس:' : 'Gender:' }}</label>
                            <span class="badge bg-light text-dark border fs-6">{{ $student?->gender ?: ($join->gender ?: 'N/A') }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'تاريخ الميلاد / العمر:' : 'Age / Birth Date:' }}</label>
                            <span class="fw-bold text-dark">{{ $student?->age ?: ($join->birthdate ?: 'N/A') }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'المدرسة / المؤسسة التعليمية:' : 'School Name:' }}</label>
                            <span class="fw-bold text-dark">{{ $student?->school_name ?: 'N/A' }}</span>
                        </div>
                        
                        @if($student?->medical_condition || $join->medical_condition)
                            <div class="col-12">
                                <div class="p-3 rounded-3 bg-warning bg-opacity-10 border border-warning">
                                    <label class="text-warning-emphasis small fw-bold d-block mb-1">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $ar ? 'حالات صحية أو ملاحظات طبية:' : 'Medical Condition:' }}
                                    </label>
                                    <span class="fw-bold text-dark">{{ $student?->medical_condition_details ?: ($student?->medical_condition ?: $join->medical_condition) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TRAINING PROGRAM & ACADEMY DETAILS CARD -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-dumbbell text-success me-2"></i> {{ $ar ? 'تفاصيل البرنامج التدريبي' : 'Training Program Details' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'المنشأة / الأكاديمية:' : 'Academy:' }}</label>
                            <span class="fw-bold text-primary fs-6"><i class="fa-solid fa-building me-1"></i> {{ $academy?->commercial_name ?: ($academy?->name ?: 'N/A') }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'البرنامج التدريبي:' : 'Training Program:' }}</label>
                            <span class="fw-bold text-dark fs-6">{{ $training?->name ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'الرياضة / النشاط:' : 'Sport:' }}</label>
                            <span class="badge bg-info text-dark fs-6"><i class="fa-solid fa-trophy me-1"></i> {{ $training?->sport?->name ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'المدرب المسؤول:' : 'Coach:' }}</label>
                            <span class="fw-bold text-dark"><i class="fa-solid fa-user-tie me-1"></i> {{ $training?->coach?->name ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'أيام التدريب والتوقيت:' : 'Classes Days & Schedule:' }}</label>
                            <span class="fw-bold text-dark">
                                {{ $training?->classes_days ? (is_array($training->classes_days) ? implode(', ', $training->classes_days) : $training->classes_days) : 'N/A' }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'الفئة العمرية والمستوى:' : 'Age Group & Level:' }}</label>
                            <span class="fw-bold text-dark">{{ $training?->age_group ?: 'N/A' }} | {{ $training?->level ?: 'N/A' }}</span>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small fw-bold d-block">{{ $ar ? 'عنوان الفرع أو الملعب:' : 'Training Address:' }}</label>
                            <span class="fw-bold text-dark"><i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $training?->address?->address ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDEBAR COLUMN -->
        <div class="col-lg-4">
            <!-- PAYMENT STATUS SUMMARY CARD -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-receipt text-warning me-2"></i> {{ $ar ? 'حالة السداد والفاتورة' : 'Invoice Status' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3 text-center">
                        @if($paymentState === 'paid')
                            <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success">
                                <i class="fa-solid fa-circle-check fa-3x text-success mb-2"></i>
                                <h5 class="fw-bold text-success mb-0">{{ $ar ? 'مدفوع بالكامل' : 'Fully Paid' }}</h5>
                                <span class="small text-muted">{{ $ar ? 'تم تحصيل كامل المبلغ المستحق' : 'Full amount collected' }}</span>
                            </div>
                        @elseif($paymentState === 'partial')
                            <div class="p-3 rounded-3 bg-warning bg-opacity-10 border border-warning">
                                <i class="fa-solid fa-clock-half-duller fa-3x text-warning mb-2"></i>
                                <h5 class="fw-bold text-warning-emphasis mb-0">{{ $ar ? 'مدفوع جزئياً' : 'Partially Paid' }}</h5>
                                <span class="small text-muted">{{ $ar ? 'متبقي أقساط أو مبالغ غير مستكمله' : 'Remaining balance pending' }}</span>
                            </div>
                        @else
                            <div class="p-3 rounded-3 bg-danger bg-opacity-10 border border-danger">
                                <i class="fa-solid fa-circle-xmark fa-3x text-danger mb-2"></i>
                                <h5 class="fw-bold text-danger mb-0">{{ $ar ? 'غير مدفوع' : 'Unpaid' }}</h5>
                                <span class="small text-muted">{{ $ar ? 'لم يتم تسجيل مبالغ مدفوعة بعد' : 'No payment recorded' }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="list-group list-group-flush border-top">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted small">{{ $ar ? 'سعر الاشتراك الأصلي:' : 'Subscription Price:' }}</span>
                            <span class="fw-bold text-dark">{{ number_format($totalAmount, 2) }} {{ $currency }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted small">{{ $ar ? 'إجمالي المحصل:' : 'Total Collected:' }}</span>
                            <span class="fw-bold text-success">{{ number_format($paidAmount, 2) }} {{ $currency }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted small">{{ $ar ? 'الرصيد المتبقي:' : 'Remaining Balance:' }}</span>
                            <span class="fw-bold text-danger">{{ number_format($remainingAmount, 2) }} {{ $currency }}</span>
                        </div>
                        @if($invoice?->order_number)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <span class="text-muted small">{{ $ar ? 'رقم الفاتورة:' : 'Invoice No:' }}</span>
                                <span class="fw-bold text-dark font-monospace">#{{ $invoice->order_number }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ADDITIONAL METADATA CARD -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-circle-info text-secondary me-2"></i> {{ $ar ? 'معلومات إضافية' : 'Additional Info' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <label class="text-muted small d-block">{{ $ar ? 'مصدر معرفة المنشأة:' : 'Referral Source:' }}</label>
                            <span class="fw-bold text-dark">{{ $student?->referral_source ?: 'N/A' }}</span>
                        </li>
                        <li class="mb-3">
                            <label class="text-muted small d-block">{{ $ar ? 'عضوية النادي:' : 'Club Member:' }}</label>
                            <span class="fw-bold text-dark">{{ $student?->club_member ?: 'N/A' }}</span>
                        </li>
                        <li>
                            <label class="text-muted small d-block">{{ $ar ? 'ملاحظات إضافية:' : 'Additional Notes:' }}</label>
                            <span class="fw-bold text-dark">{{ $student?->additional_information ?: 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
