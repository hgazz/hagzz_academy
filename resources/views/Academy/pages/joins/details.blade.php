@extends('Academy.Layouts.master')

@section('title', (app()->getLocale() === 'ar' ? 'تفاصيل الحجز #' : 'Booking Details #') . $join->id)

@section('content')
@php
    $ar = app()->getLocale() === 'ar';
    $invoice = $join->invoice;
    $training = $join->training;

    // Resolve academy safely
    $academy = $training?->academy;
    if (!$academy && auth('academy')->check()) {
        $userAuth = auth('academy')->user();
        $academy = ($userAuth instanceof \App\Models\PartnerUser && $userAuth->academy) ? $userAuth->academy : $userAuth;
    }

    // Resolve AcademyStudent (prioritize actual AcademyStudent profile over raw User)
    $academyId = (int) ($academy?->id ?: (auth('academy')->user()?->academy_id ?: auth('academy')->id()));
    $student = $join->student ?: ($join->academy_student_id ? \App\Models\AcademyStudent::find($join->academy_student_id) : null);
    if (!$student && $join->user_id) {
        $student = \App\Models\AcademyStudent::where('user_id', $join->user_id)
            ->where('academy_id', $academyId)
            ->first();
    }
    if (!$student) {
        $student = $join->user;
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

    $facilityType = $academy?->business_type ?? 'academy';
    $isGym = in_array($facilityType, ['gym', 'health_center', 'fitness']);

    $invId = $invoice?->id ?: $join->invoice_id;
    $publicInvUrl = $invId ? route('invoices.public.view', ['type' => 'booking', 'id' => $invId]) : '';
    $printA4Url = $invId ? route('academy.invoices.bookings.print', ['invoice' => $invId, 'paper' => 'a4']) : '#';
    $printPosUrl = $invId ? route('academy.invoices.bookings.print', ['invoice' => $invId, 'paper' => 'pos']) : '#';

    $phone = $student?->phone ?: ($join->phone ?: ($student?->parent_phone ?: null));
    $rawPhone = (string) $phone;
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

    $buyerName = $student?->name ?: ($join->name ?: ($ar ? 'عميلنا العزيز' : 'Member'));
    $facilityName = $academy?->commercial_name ?: ($academy?->name ?: 'النادي الرياضي');
    $itemName = $training?->name ?: ($isGym ? ($ar ? 'باقة اشتراك الجيم' : 'Gym Membership') : 'البرنامج التدريبي');

    if ($ar) {
        $waText = "مرحباً بك كابتن *{$buyerName}* 🌟\n"
            . "يسعدنا تأكيد تسجيل اشتراكك في *{$facilityName}*:\n\n"
            . "📋 *الباقة / الاشتراك:* {$itemName}\n"
            . "🔢 *رقم الفاتورة:* #" . ($invoice?->order_number ?: $join->id) . "\n"
            . "💰 *الإجمالي:* " . number_format($totalAmount, 2) . " {$currency}\n"
            . "✅ *المسدد:* " . number_format($paidAmount, 2) . " {$currency}\n"
            . "⏳ *المتبقي:* " . number_format($remainingAmount, 2) . " {$currency}\n\n"
            . ($publicInvUrl ? "🔗 *رابط الفاتورة الإلكترونية الموحدة:* \n{$publicInvUrl}\n\n" : "")
            . "نتمنى لك تدريباً ممتعاً ومليئاً بالنشاط! 💪🔥";
    } else {
        $waText = "Hello *{$buyerName}* 🌟\n"
            . "We are delighted to confirm your membership at *{$facilityName}*:\n\n"
            . "📋 *Plan:* {$itemName}\n"
            . "🔢 *Invoice #:* #" . ($invoice?->order_number ?: $join->id) . "\n"
            . "💰 *Total:* " . number_format($totalAmount, 2) . " {$currency}\n"
            . "✅ *Paid:* " . number_format($paidAmount, 2) . " {$currency}\n"
            . "⏳ *Balance:* " . number_format($remainingAmount, 2) . " {$currency}\n\n"
            . ($publicInvUrl ? "🔗 *Electronic Invoice:* \n{$publicInvUrl}\n\n" : "")
            . "Have a great workout! 💪🔥";
    }
    $waShareUrl = 'https://api.whatsapp.com/send?' . ($cleanPhone ? 'phone=' . $cleanPhone . '&' : '') . 'text=' . urlencode($waText);
@endphp

<style>
    /* HeroUI Details Page Theme */
    .join-details-wrap {
        max-width: 1320px;
        margin: 0 auto;
        padding-bottom: 40px;
    }
    .join-card {
        background: #ffffff;
        border: 1px solid var(--heroui-border, #e2e8f0);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .join-card-header {
        padding: 14px 18px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .join-card-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .join-card-body {
        padding: 18px;
    }

    /* Metric Pills */
    .metric-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .metric-box:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .metric-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .metric-total .metric-icon { background: #eff6ff; color: #2563eb; }
    .metric-paid .metric-icon { background: #ecfdf5; color: #059669; }
    .metric-rem .metric-icon { background: #fef2f2; color: #dc2626; }
    .metric-method .metric-icon { background: #f0fdf4; color: #0d9488; }

    .metric-label {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 2px;
    }
    .metric-value {
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .metric-currency {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        margin-inline-start: 2px;
    }

    /* Info Grid */
    .info-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px 20px;
    }
    .info-item label {
        display: block;
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .info-item .val {
        font-size: 0.92rem;
        font-weight: 700;
        color: #1e293b;
    }

    /* Payment Badge Banner */
    .pay-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 800;
        font-size: 0.85rem;
    }
    .pay-status-paid { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .pay-status-partial { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .pay-status-unpaid { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

    /* Financial table */
    .fin-table {
        width: 100%;
        margin-top: 12px;
    }
    .fin-table tr td {
        padding: 8px 0;
        border-bottom: 1px dashed #f1f5f9;
        font-size: 0.85rem;
    }
    .fin-table tr:last-child td {
        border-bottom: none;
    }
    .fin-table td.lbl { color: #64748b; font-weight: 600; }
    .fin-table td.amt { text-align: end; font-weight: 800; color: #0f172a; }
</style>

<div class="join-details-wrap">
    <!-- TOP HEADER & ACTIONS -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pt-2">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('academy.report.joins') }}" class="btn btn-sm btn-white border px-3 py-2 rounded-3 text-secondary fw-bold shadow-xs">
                <i class="fa-solid fa-arrow-right me-1 ms-1"></i> {{ $ar ? 'رجوع للقائمة' : 'Back' }}
            </a>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="m-0 fw-bold text-dark" style="font-size: 1.35rem;">
                        {{ $ar ? 'تفاصيل الحجز' : 'Booking Details' }} <span class="text-primary font-monospace">#{{ $join->id }}</span>
                    </h1>
                    @if($isCanceled)
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill" style="font-size: 12px;">
                            <i class="fa-solid fa-ban me-1"></i> {{ $ar ? 'حجز ملغى' : 'Cancelled' }}
                        </span>
                    @else
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill" style="font-size: 12px;">
                            <i class="fa-solid fa-circle-check me-1"></i> {{ $ar ? 'نشط ومؤكد' : 'Confirmed' }}
                        </span>
                    @endif
                </div>
                <div class="text-muted small mt-1">
                    <i class="fa-regular fa-clock me-1 ms-1"></i>
                    {{ $ar ? 'تاريخ التسجيل:' : 'Registered On:' }} 
                    <span class="fw-semibold text-secondary">{{ $join->created_at ? $join->created_at->format('Y-m-d h:i A') : '-' }}</span>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('academy.report.export-booking-file', $join) }}" class="btn btn-sm btn-outline-success fw-bold px-3 py-2 rounded-3 shadow-xs">
                <i class="fa-solid fa-file-excel me-1"></i> {{ $ar ? 'تصدير Excel' : 'Export Excel' }}
            </a>

            @if($cleanPhone)
            <a href="{{ $waShareUrl }}" target="_blank" class="btn btn-sm btn-success fw-bold px-3 py-2 rounded-3 shadow-xs" style="background: #25D366; border-color: #25D366;">
                <i class="fa-brands fa-whatsapp me-1"></i> {{ $ar ? 'إرسال الفاتورة بالواتساب' : 'Send via WhatsApp' }}
            </a>
            @endif

            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle fw-bold px-3 py-2 rounded-3 shadow-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: linear-gradient(135deg, #0f766e, #0d9488); border: none;">
                    <i class="fa-solid fa-print me-1"></i> {{ $ar ? 'طباعة الفاتورة الموحدة' : 'Print Invoice' }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                    <li>
                        <a class="dropdown-item py-2 fw-semibold d-flex align-items-center gap-2" href="{{ $printA4Url }}" target="_blank">
                            <i class="fa-solid fa-file-invoice text-primary"></i>
                            <span>{{ $ar ? 'طباعة قياسية (ورق A4 / PDF)' : 'Standard (A4 / PDF)' }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 fw-semibold d-flex align-items-center gap-2" href="{{ $printPosUrl }}" target="_blank">
                            <i class="fa-solid fa-receipt text-success"></i>
                            <span>{{ $ar ? 'إيصال كاشير حراري (POS 80mm)' : 'Thermal Receipt (POS 80mm)' }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 4 COMPACT METRIC CARDS -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="metric-box metric-total">
                <div class="metric-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <div>
                    <div class="metric-label">{{ $ar ? 'إجمالي الحجز' : 'Total Amount' }}</div>
                    <div class="metric-value text-primary">
                        {{ number_format($totalAmount, 2) }} <span class="metric-currency">{{ $currency }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="metric-box metric-paid">
                <div class="metric-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div>
                    <div class="metric-label">{{ $ar ? 'المبلغ المحصل' : 'Collected' }}</div>
                    <div class="metric-value text-success">
                        {{ number_format($paidAmount, 2) }} <span class="metric-currency">{{ $currency }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="metric-box metric-rem">
                <div class="metric-icon"><i class="fa-solid fa-receipt"></i></div>
                <div>
                    <div class="metric-label">{{ $ar ? 'المتبقي' : 'Remaining' }}</div>
                    <div class="metric-value {{ $remainingAmount > 0 ? 'text-danger' : 'text-secondary' }}">
                        {{ number_format($remainingAmount, 2) }} <span class="metric-currency">{{ $currency }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="metric-box metric-method">
                <div class="metric-icon">
                    @if($methodInfo && isset($methodInfo['logo']))
                        <img src="{{ $methodInfo['logo'] }}" style="height: 20px; max-width: 28px; object-fit: contain;" alt="">
                    @else
                        <i class="fa-solid fa-credit-card"></i>
                    @endif
                </div>
                <div>
                    <div class="metric-label">{{ $ar ? 'طريقة السداد' : 'Payment Method' }}</div>
                    <div class="metric-value text-dark" style="font-size: 0.95rem;">
                        {{ $methodInfo ? ($ar ? $methodInfo['name_ar'] : $methodInfo['name_en']) : ucfirst($paymentMethod) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN 2-COLUMN CONTENT -->
    <div class="row g-3">
        <!-- LEFT COLUMN (MEMBER & SUBSCRIPTION DETAILS) -->
        <div class="col-lg-8">
            <!-- MEMBER / STUDENT DETAILS CARD -->
            <div class="join-card">
                <div class="join-card-header">
                    <h2 class="join-card-title">
                        <i class="fa-solid fa-id-card text-teal-600" style="color: #0f766e;"></i>
                        <span>{{ $isGym ? ($ar ? 'بيانات العضو / المشترك' : 'Member Details') : ($ar ? 'بيانات المشترك / الطالب' : 'Student & Member Details') }}</span>
                    </h2>
                    @if($student?->code || $join->student_code)
                        <span class="badge bg-light text-dark font-monospace border px-2 py-1" style="font-size: 11px;">
                            {{ $student?->code ?: $join->student_code }}
                        </span>
                    @endif
                </div>
                <div class="join-card-body">
                    <div class="info-row">
                        <div class="info-item">
                            <label>{{ $ar ? 'الاسم بالكامل' : 'Full Name' }}</label>
                            <div class="val text-dark fs-6">{{ $student?->name ?: ($join->name ?: '—') }}</div>
                        </div>

                        <div class="info-item">
                            <label>{{ $ar ? 'رقم الهاتف / الاتصال' : 'Phone Number' }}</label>
                            @if($phone)
                                <div class="d-flex align-items-center gap-2">
                                    <span class="val font-monospace" dir="ltr">{{ $phone }}</span>
                                    <a href="tel:{{ $phone }}" class="btn btn-sm btn-light border py-0 px-2 text-primary" title="{{ $ar ? 'اتصال' : 'Call' }}">
                                        <i class="fa-solid fa-phone" style="font-size: 11px;"></i>
                                    </a>
                                    @if($cleanPhone)
                                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="btn btn-sm btn-light border py-0 px-2 text-success" title="WhatsApp">
                                            <i class="fa-brands fa-whatsapp" style="font-size: 12px;"></i>
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="val text-muted">—</div>
                            @endif
                        </div>

                        @if(!$isGym && ($student?->parent_name || $join->parent_name))
                            <div class="info-item">
                                <label>{{ $ar ? 'اسم ولي الأمر' : 'Parent Name' }}</label>
                                <div class="val">{{ $student?->parent_name ?: $join->parent_name }}</div>
                            </div>
                        @endif

                        @if(!$isGym && ($student?->parent_phone || $join->parent_phone) && ($student?->parent_phone !== $phone))
                            <div class="info-item">
                                <label>{{ $ar ? 'هاتف ولي الأمر' : 'Parent Phone' }}</label>
                                <div class="val font-monospace" dir="ltr">{{ $student?->parent_phone ?: $join->parent_phone }}</div>
                            </div>
                        @endif

                        <div class="info-item">
                            <label>{{ $ar ? 'النوع / الجنس' : 'Gender' }}</label>
                            <div class="val">
                                @php($gender = $student?->gender ?: $join->gender)
                                @if($gender === 'male' || $gender === 'ذكر')
                                    <span class="badge bg-light text-primary border px-2 py-1"><i class="fa-solid fa-mars me-1"></i> {{ $ar ? 'ذكر' : 'Male' }}</span>
                                @elseif($gender === 'female' || $gender === 'أنثى')
                                    <span class="badge bg-light text-danger border px-2 py-1"><i class="fa-solid fa-venus me-1"></i> {{ $ar ? 'أنثى' : 'Female' }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>

                        @if($student?->age || $join->birthdate)
                            <div class="info-item">
                                <label>{{ $ar ? 'تاريخ الميلاد / العمر' : 'Age / Birth Date' }}</label>
                                <div class="val">{{ $student?->age ?: $join->birthdate }}</div>
                            </div>
                        @endif

                        @if(!$isGym && ($student?->school_name || $join->school_name))
                            <div class="info-item">
                                <label>{{ $ar ? 'المدرسة / المؤسسة' : 'School' }}</label>
                                <div class="val">{{ $student?->school_name ?: $join->school_name }}</div>
                            </div>
                        @endif
                    </div>

                    @if($student?->medical_condition || $join->medical_condition || $student?->medical_condition_details)
                        <div class="mt-3 p-3 rounded-3" style="background: #fffbeb; border: 1px solid #fef3c7;">
                            <div class="text-warning-emphasis fw-bold small mb-1">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $ar ? 'الحالة الصحية أو ملاحظات طبية:' : 'Medical Notes:' }}
                            </div>
                            <div class="text-dark small">
                                {{ $student?->medical_condition_details ?: ($student?->medical_condition ?: $join->medical_condition) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TRAINING PROGRAM / PACKAGE DETAILS CARD -->
            <div class="join-card">
                <div class="join-card-header">
                    <h2 class="join-card-title">
                        <i class="fa-solid fa-dumbbell text-teal-600" style="color: #0f766e;"></i>
                        <span>{{ $isGym ? ($ar ? 'تفاصيل الباقة والاشتراك' : 'Package & Membership Details') : ($ar ? 'تفاصيل البرنامج التدريبي' : 'Training Program Details') }}</span>
                    </h2>
                </div>
                <div class="join-card-body">
                    <div class="info-row">
                        <div class="info-item">
                            <label>{{ $isGym ? ($ar ? 'اسم الباقة / الاشتراك' : 'Package Name') : ($ar ? 'البرنامج التدريبي' : 'Program') }}</label>
                            <div class="val text-primary">{{ $training?->name ?: ($join->package_name ?: '—') }}</div>
                        </div>

                        <div class="info-item">
                            <label>{{ $ar ? 'المنشأة' : 'Facility' }}</label>
                            <div class="val">{{ $academy?->commercial_name ?: ($academy?->name ?: '—') }}</div>
                        </div>

                        @if($training?->sport)
                            <div class="info-item">
                                <label>{{ $ar ? 'النشاط / الرياضة' : 'Sport / Activity' }}</label>
                                <div class="val">{{ $training->sport->name }}</div>
                            </div>
                        @endif

                        @if($training?->coach)
                            <div class="info-item">
                                <label>{{ $isGym ? ($ar ? 'المدرب / الكابتن' : 'Trainer') : ($ar ? 'المدرب المسؤول' : 'Coach') }}</label>
                                <div class="val">{{ $training->coach->name }}</div>
                            </div>
                        @endif

                        @if($training?->classes_days)
                            <div class="info-item">
                                <label>{{ $ar ? 'أيام ومواعيد الحصص' : 'Schedule' }}</label>
                                <div class="val">
                                    {{ is_array($training->classes_days) ? implode(', ', $training->classes_days) : $training->classes_days }}
                                </div>
                            </div>
                        @endif

                        @if($training?->age_group || $training?->level)
                            <div class="info-item">
                                <label>{{ $ar ? 'الفئة والمستوى' : 'Level & Category' }}</label>
                                <div class="val">{{ implode(' - ', array_filter([$training->age_group, $training->level])) }}</div>
                            </div>
                        @endif

                        @if($training?->address?->address)
                            <div class="info-item" style="grid-column: 1 / -1;">
                                <label>{{ $ar ? 'الفرع / الموقع' : 'Location' }}</label>
                                <div class="val text-muted small"><i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $training->address->address }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDEBAR (PAYMENT BREAKDOWN & INVOICE) -->
        <div class="col-lg-4">
            <!-- PAYMENT STATUS CARD -->
            <div class="join-card">
                <div class="join-card-header">
                    <h2 class="join-card-title">
                        <i class="fa-solid fa-receipt text-teal-600" style="color: #0f766e;"></i>
                        <span>{{ $ar ? 'حالة السداد والفاتورة' : 'Invoice & Payment' }}</span>
                    </h2>
                </div>
                <div class="join-card-body">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div>
                            <div class="text-muted small fw-bold mb-1">{{ $ar ? 'حالة السداد الحالية' : 'Current Status' }}</div>
                            @if($paymentState === 'paid')
                                <span class="pay-status-pill pay-status-paid">
                                    <i class="fa-solid fa-circle-check"></i> {{ $ar ? 'مدفوع بالكامل' : 'Fully Paid' }}
                                </span>
                            @elseif($paymentState === 'partial')
                                <span class="pay-status-pill pay-status-partial">
                                    <i class="fa-solid fa-clock-half-duller"></i> {{ $ar ? 'مدفوع جزئياً' : 'Partially Paid' }}
                                </span>
                            @else
                                <span class="pay-status-pill pay-status-unpaid">
                                    <i class="fa-solid fa-circle-xmark"></i> {{ $ar ? 'غير مدفوع' : 'Unpaid' }}
                                </span>
                            @endif
                        </div>
                        <div class="text-end">
                            <div class="text-muted small fw-bold mb-1">{{ $ar ? 'رقم الفاتورة' : 'Invoice #' }}</div>
                            <span class="fw-bold font-monospace text-dark">{{ $invoice?->order_number ? '#' . $invoice->order_number : '#' . $join->id }}</span>
                        </div>
                    </div>

                    <table class="fin-table">
                        <tr>
                            <td class="lbl">{{ $ar ? 'سعر الاشتراك الأصلي:' : 'Original Price:' }}</td>
                            <td class="amt">{{ number_format($totalAmount, 2) }} {{ $currency }}</td>
                        </tr>
                        @if($invoice?->discount_amount && $invoice->discount_amount > 0)
                            <tr>
                                <td class="lbl text-danger">{{ $ar ? 'قيمة الخصم:' : 'Discount:' }}</td>
                                <td class="amt text-danger">-{{ number_format($invoice->discount_amount, 2) }} {{ $currency }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="lbl">{{ $ar ? 'المبلغ المحصل فعلياً:' : 'Collected:' }}</td>
                            <td class="amt text-success">{{ number_format($paidAmount, 2) }} {{ $currency }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">{{ $ar ? 'الرصيد المتبقي للسداد:' : 'Remaining:' }}</td>
                            <td class="amt {{ $remainingAmount > 0 ? 'text-danger' : 'text-secondary' }}">
                                {{ number_format($remainingAmount, 2) }} {{ $currency }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- UNIFIED INVOICE & ACTIONS CARD -->
            <div class="join-card">
                <div class="join-card-header">
                    <h2 class="join-card-title">
                        <i class="fa-solid fa-file-invoice text-primary"></i>
                        <span>{{ $ar ? 'إجراءات الفاتورة والتواصل' : 'Invoice & Actions' }}</span>
                    </h2>
                </div>
                <div class="join-card-body d-flex flex-column gap-2">
                    <a href="{{ $printA4Url }}" target="_blank" class="btn btn-outline-primary d-flex align-items-center justify-content-between p-2 rounded-3 text-start shadow-xs">
                        <span class="d-flex align-items-center gap-2 fw-bold" style="font-size: 13px;">
                            <i class="fa-solid fa-file-pdf text-danger fa-lg"></i>
                            <span>{{ $ar ? 'فاتورة إلكترونية موحدة (A4)' : 'Unified A4 Invoice' }}</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square small text-muted"></i>
                    </a>

                    <a href="{{ $printPosUrl }}" target="_blank" class="btn btn-outline-secondary d-flex align-items-center justify-content-between p-2 rounded-3 text-start shadow-xs">
                        <span class="d-flex align-items-center gap-2 fw-bold" style="font-size: 13px;">
                            <i class="fa-solid fa-receipt text-success fa-lg"></i>
                            <span>{{ $ar ? 'إيصال كاشير حراري (POS 80mm)' : 'Thermal Receipt (POS)' }}</span>
                        </span>
                        <i class="fa-solid fa-print small text-muted"></i>
                    </a>

                    @if($cleanPhone)
                    <a href="{{ $waShareUrl }}" target="_blank" class="btn btn-success d-flex align-items-center justify-content-between p-2 rounded-3 text-start shadow-xs" style="background: #25D366; border-color: #25D366;">
                        <span class="d-flex align-items-center gap-2 fw-bold text-white" style="font-size: 13px;">
                            <i class="fa-brands fa-whatsapp fa-lg"></i>
                            <span>{{ $ar ? 'إرسال الفاتورة عبر واتساب للعميل' : 'Send via WhatsApp' }}</span>
                        </span>
                        <i class="fa-solid fa-paper-plane small text-white"></i>
                    </a>
                    @endif

                    @if($student?->id)
                    <a href="{{ route('academy.students.card', $student->id) }}" target="_blank" class="btn btn-outline-dark d-flex align-items-center justify-content-between p-2 rounded-3 text-start shadow-xs">
                        <span class="d-flex align-items-center gap-2 fw-bold" style="font-size: 13px;">
                            <i class="fa-solid fa-id-card text-info fa-lg"></i>
                            <span>{{ $isGym ? ($ar ? 'كارت العضوية الرقمي' : 'Digital Member Card') : ($ar ? 'كارنيه الطالب' : 'Student Card') }}</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square small text-muted"></i>
                    </a>
                    @endif
                </div>
            </div>

            @if($student?->referral_source || $student?->club_member || $student?->additional_information || $join->notes)
                <!-- ADDITIONAL METADATA CARD -->
                <div class="join-card">
                    <div class="join-card-header">
                        <h2 class="join-card-title">
                            <i class="fa-solid fa-circle-info text-secondary"></i>
                            <span>{{ $ar ? 'معلومات إضافية' : 'Additional Info' }}</span>
                        </h2>
                    </div>
                    <div class="join-card-body">
                        <ul class="list-unstyled mb-0" style="font-size: 0.85rem;">
                            @if($student?->referral_source)
                                <li class="mb-2 d-flex justify-content-between">
                                    <span class="text-muted">{{ $ar ? 'مصدر المعرفة:' : 'Referral:' }}</span>
                                    <span class="fw-bold text-dark">{{ $student->referral_source }}</span>
                                </li>
                            @endif
                            @if($student?->club_member)
                                <li class="mb-2 d-flex justify-content-between">
                                    <span class="text-muted">{{ $ar ? 'عضوية النادي:' : 'Club Member:' }}</span>
                                    <span class="fw-bold text-dark">{{ $student->club_member }}</span>
                                </li>
                            @endif
                            @if($student?->additional_information || $join->notes)
                                <li class="pt-2 border-top">
                                    <span class="text-muted d-block mb-1">{{ $ar ? 'ملاحظات:' : 'Notes:' }}</span>
                                    <span class="fw-semibold text-secondary">{{ $student?->additional_information ?: $join->notes }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
