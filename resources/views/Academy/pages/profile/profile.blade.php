@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'الملف الشخصي وإعدادات الأكاديمية' : 'Academy Profile & Settings')

@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/light/components/tabs.css') }}" rel="stylesheet" type="text/css">
    <style>
        .profile-header-banner { background: linear-gradient(135deg, #1e293b, #0f172a); color: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 10px 30px rgba(15,23,42,0.12); margin-bottom: 24px; }
        .profile-avatar-wrapper { width: 90px; height: 90px; border-radius: 14px; background: #fff; padding: 4px; box-shadow: 0 4px 14px rgba(0,0,0,0.15); flex: 0 0 90px; overflow: hidden; }
        .profile-avatar-wrapper img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; }
        .saas-card-summary { background: linear-gradient(135deg, #0f766e, #0369a1); color: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 10px 25px rgba(15,118,110,0.15); }
        .saas-grid-item { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.18); border-radius: 10px; padding: 12px 14px; }
        .saas-grid-item span { display: block; color: rgba(255,255,255,0.7); font-size: 12px; margin-bottom: 3px; }
        .saas-grid-item strong { display: block; color: #fff; font-size: 15px; font-weight: 700; }
        .nav-tabs .nav-link { font-weight: 700; color: #64748b; border: 0; border-bottom: 3px solid transparent; border-radius: 0; padding: 12px 20px; }
        .nav-tabs .nav-link.active { color: #1b55e2; background: transparent; border-bottom-color: #1b55e2; }
    </style>
@endpush

@section('content')
@php
    $ar = app()->getLocale() === 'ar';
    $subscriptionActive = $saasSubscription && in_array($saasSubscription->status, ['active', 'trial'], true)
        && (!$saasSubscription->ends_at || $saasSubscription->ends_at->isToday() || $saasSubscription->ends_at->isFuture());
    $remainingDays = $saasSubscription?->ends_at && $saasSubscription->ends_at->isFuture()
        ? now()->startOfDay()->diffInDays($saasSubscription->ends_at)
        : null;

    $country = $user->country;
    $currencyCode = $user->currency_code;
    $currencySymbol = $user->currency_symbol;
@endphp

<div class="middle-content container-xxl p-0">
    <!--  BREADCRUMBS  -->
    <div class="secondary-nav mb-4">
        <div class="breadcrumbs-container">
            <header class="header navbar navbar-expand-sm">
                <a href="javascript:void(0);" class="btn-toggle sidebarCollapse"><i data-feather="menu"></i></a>
                <div class="d-flex breadcrumb-content">
                    <nav class="breadcrumb-style-one">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $ar ? 'الملف الشخصي وإعدادات المنشأة' : 'Profile & Facility Settings' }}</li>
                        </ol>
                    </nav>
                </div>
            </header>
        </div>
    </div>

    <!-- PROFILE HEADER BANNER -->
    <div class="profile-header-banner">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-avatar-wrapper">
                    <img id="header-logo-preview" src="{{ $user->logo }}" alt="{{ $user->commercial_name }}" onerror="this.onerror=null;this.src='{{ asset('assetsAdmin/logo/Icon-Primary.svg') }}';">
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h2 class="fw-bold text-white mb-0">{{ $user->commercial_name }}</h2>
                        <span class="badge bg-success rounded-pill px-3 py-2 fs-6"><i class="fa-solid fa-circle-check me-1"></i> {{ $ar ? 'حساب معتمد' : 'Verified Partner' }}</span>
                        <span class="badge bg-primary bg-opacity-25 text-white border border-light border-opacity-25 rounded-pill px-3 py-2 fs-6">
                            <i class="fa-solid fa-earth-americas me-1"></i> {{ $country?->getTranslation('name', app()->getLocale()) ?: 'مصر' }} ({{ $currencySymbol }})
                        </span>
                    </div>
                    <p class="text-white-50 mb-0 d-flex align-items-center gap-3 flex-wrap small">
                        <span><i class="fa-solid fa-envelope me-1"></i> {{ $user->email }}</span>
                        <span><i class="fa-solid fa-phone me-1"></i> {{ $user->phone }}</span>
                        <span><i class="fa-solid fa-briefcase me-1"></i> {{ match($user->business_type) { 'venue' => $ar ? 'ملاعب وحجوزات' : 'Venues', 'hybrid' => $ar ? 'أكاديمية وملاعب' : 'Academy & Venues', default => $ar ? 'أكاديمية تدريب' : 'Academy' } }}</span>
                    </p>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <a href="#editProfileSection" class="btn btn-light fw-bold px-3">
                    <i class="fa-solid fa-pen-to-square me-1"></i> {{ $ar ? 'تعديل البيانات' : 'Edit Profile' }}
                </a>
            </div>
        </div>
    </div>

    <!-- SAAS SUBSCRIPTION SUMMARY CARD -->
    <div class="saas-card-summary mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-white bg-opacity-20 p-2 rounded-circle">
                    <i class="fa-solid fa-layer-group fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-white mb-0">{{ $ar ? 'اشتراك المنصة والباقة الحالية' : 'Platform Subscription' }}</h5>
                    <small class="text-white-50">{{ $saasSubscription?->plan?->name ?? ($ar ? 'باقة غير محددة' : 'Custom Plan') }}</small>
                </div>
            </div>
            @if($saasSubscription)
                <span class="badge {{ $subscriptionActive ? ($saasSubscription->status === 'trial' ? 'bg-warning text-dark' : 'bg-success') : 'bg-danger' }} fs-6 px-3 py-2 fw-bold">
                    {{ match($saasSubscription->status) { 'active' => $ar ? 'اشتراك نشط' : 'Active', 'trial' => $ar ? 'فترة تجريبية' : 'Trial', 'expired' => $ar ? 'اشتراك منتهي' : 'Expired', default => $ar ? 'غير نشط' : 'Inactive' } }}
                </span>
            @endif
        </div>

        @if($saasSubscription)
            <div class="row g-2">
                <div class="col-md-3 col-6">
                    <div class="saas-grid-item">
                        <span>{{ $ar ? 'دورة الفوترة' : 'Billing Cycle' }}</span>
                        <strong>{{ $saasSubscription->billing_cycle === 'annual' ? ($ar ? 'سنوي (Annual)' : 'Annual') : ($ar ? 'شهري (Monthly)' : 'Monthly') }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="saas-grid-item">
                        <span>{{ $ar ? 'قيمة الاشتراك' : 'Subscription Cost' }}</span>
                        <strong>{{ number_format((float) ($saasSubscription->price_amount ?? $saasSubscription->custom_price ?? 0), 2) }} {{ $saasSubscription->currency_code ?: $currencySymbol }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="saas-grid-item">
                        <span>{{ $ar ? 'تاريخ البداية' : 'Start Date' }}</span>
                        <strong>{{ $saasSubscription->starts_at?->format('Y-m-d') ?? '-' }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="saas-grid-item">
                        <span>{{ $ar ? 'تاريخ الانتهاء' : 'End Date' }}</span>
                        <strong>{{ $saasSubscription->ends_at?->format('Y-m-d') ?? ($ar ? 'مفتوح' : 'Open-ended') }}</strong>
                    </div>
                </div>
            </div>
        @else
            <div class="p-3 bg-white bg-opacity-10 rounded-3">
                <p class="mb-0 text-white-50"><i class="fa-solid fa-info-circle me-1"></i> {{ $ar ? 'حسابك يعمل بالشروط القياسية، للتطوير تواصل مع إدارة المنصة لتعيين باقة مخصصة.' : 'Contact platform admin to upgrade your plan.' }}</p>
            </div>
        @endif
    </div>

    <!-- TABS CARD -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white" id="editProfileSection">
        <div class="card-header bg-white p-0 border-bottom">
            <ul class="nav nav-tabs px-3" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-tab-pane" type="button" role="tab">
                        <i class="fa-solid fa-user me-1"></i> {{ $ar ? 'البيانات الأساسية والتواصل' : 'Basic & Social Info' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="commercial-tab" data-bs-toggle="tab" data-bs-target="#commercial-tab-pane" type="button" role="tab">
                        <i class="fa-solid fa-id-card me-1"></i> {{ $ar ? 'السجل والتراخيص' : 'Commercial & License' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contract-tab" data-bs-toggle="tab" data-bs-target="#contract-tab-pane" type="button" role="tab">
                        <i class="fa-solid fa-file-contract me-1"></i> {{ $ar ? 'بيانات العقد والعمولة' : 'Contract & Commission' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank-tab-pane" type="button" role="tab">
                        <i class="fa-solid fa-building-columns me-1"></i> {{ $ar ? 'الحساب البنكي والتحصيل' : 'Bank & Payouts' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="settlement-tab" data-bs-toggle="tab" data-bs-target="#settlement-tab-pane" type="button" role="tab">
                        <i class="fa-solid fa-sliders me-1"></i> {{ $ar ? 'شروط التسوية والاسترداد' : 'Settlement Rules' }}
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="profileTabsContent">
                <!-- TAB 1: BASIC INFO & SOCIAL LINKS -->
                <div class="tab-pane fade show active" id="basic-tab-pane" role="tabpanel">
                    <form action="{{ route('academy.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-pen-nib text-primary me-2"></i> {{ $ar ? 'تحديث البيانات الأساسية والشعار' : 'Update Basic Info & Logo' }}</h5>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $ar ? 'الاسم التجاري للأكاديمية / المنشأة:' : 'Commercial Name:' }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control fw-bold" value="{{ old('name', $user->commercial_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $ar ? 'البريد الإلكتروني للرئيسي:' : 'Primary Email:' }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $ar ? 'رقم الهاتف للتواصل:' : 'Phone Number:' }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $ar ? 'تحديث شعار الأكاديمية (Logo):' : 'Update Logo:' }}</label>
                                <input type="file" name="logo" id="logo-input" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-share-nodes text-primary me-2"></i> {{ $ar ? 'روابط التواصل الاجتماعي والموقع' : 'Social Media & Website' }}</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fa-brands fa-facebook text-primary me-1"></i> {{ $ar ? 'رابط فيسبوك:' : 'Facebook Link:' }}</label>
                                <input type="url" name="facebook" class="form-control" value="{{ old('facebook', $user->facebook) }}" placeholder="https://facebook.com/yourpage">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fa-brands fa-instagram text-danger me-1"></i> {{ $ar ? 'رابط إنستغرام:' : 'Instagram Link:' }}</label>
                                <input type="url" name="instagram" class="form-control" value="{{ old('instagram', $user->instagram) }}" placeholder="https://instagram.com/yourprofile">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fa-brands fa-linkedin text-info me-1"></i> {{ $ar ? 'رابط لينكد إن:' : 'LinkedIn Link:' }}</label>
                                <input type="url" name="linkedin" class="form-control" value="{{ old('linkedin', $user->linkedin) }}" placeholder="https://linkedin.com/in/yourcompany">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fa-solid fa-globe text-secondary me-1"></i> {{ $ar ? 'الموقع الإلكتروني الرسمي:' : 'Official Website:' }}</label>
                                <input type="url" name="website" class="form-control" value="{{ old('website', $user->website) }}" placeholder="https://yourwebsite.com">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4 py-2 fs-6">
                                <i class="fa-solid fa-save me-1"></i> {{ $ar ? 'حفظ التعديلات' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: COMMERCIAL & LICENSE INFO -->
                <div class="tab-pane fade" id="commercial-tab-pane" role="tabpanel">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-invoice text-success me-2"></i> {{ $ar ? 'بيانات السجل التجاري والرخصة' : 'Commercial & License Details' }}</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'رقم السجل التجاري / الرخصة:' : 'Trade License No:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->trade_license_number ?: 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'تاريخ انتهاء الرخصة:' : 'License Expiry Date:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->trade_license_expire_date ?: 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'الرقم الضريبي:' : 'Tax Number:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->tax_number ?: 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'رقم الهوية الوطنية / الإقامة للمالك:' : 'Owner National ID:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->national_id_number ?: 'N/A' }}" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'عنوان المنشأة الرئيسي:' : 'Main Office Address:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->address ?: 'N/A' }}" disabled>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: CONTRACT & COMMISSION -->
                <div class="tab-pane fade" id="contract-tab-pane" role="tabpanel">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-contract text-warning me-2"></i> {{ $ar ? 'معلومات العقد ونسبة المنصة' : 'Contract & Platform Commission' }}</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'رقم العقد:' : 'Contract Number:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->contract_number ?: 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'تاريخ توقيع العقد:' : 'Contract Date:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->contract_date ?: 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'تاريخ بداية العقد:' : 'Start Date:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->start_date ?: 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'تاريخ نهاية العقد:' : 'End Date:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->end_date ?: 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'نسبة عمولة المنصة:' : 'Platform Commission:' }}</label>
                            <input type="text" class="form-control fw-bold text-primary" value="{{ $user->commission_percentage ?: '0' }}%" disabled>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            @if(!is_null($user->contract_link))
                                <a href="{{ $user->contract_link }}" target="_blank" class="btn btn-outline-primary fw-bold w-100">
                                    <i class="fa-solid fa-file-pdf me-1"></i> {{ $ar ? 'تحميل العقد الموثق PDF' : 'Download Contract PDF' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 4: BANK & PAYOUTS -->
                <div class="tab-pane fade" id="bank-tab-pane" role="tabpanel">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-building-columns text-info me-2"></i> {{ $ar ? 'بيانات الحساب البنكي لتحويل المبالغ' : 'Bank Account Details for Payouts' }}</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'نوع الحساب:' : 'Account Type:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->bank_account_type ?: 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'اسم البنك:' : 'Bank Name:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->bank_name ?: 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'رقم الحساب / الايبان IBAN:' : 'Account Number / IBAN:' }}</label>
                            <input type="text" class="form-control font-monospace fw-bold" value="{{ $user->bank_account_number ?: 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'اسم المستفيد المعرف بالبنك:' : 'Beneficiary Name:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->beneficiary_name ?: 'N/A' }}" disabled>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: SETTLEMENT RULES -->
                <div class="tab-pane fade" id="settlement-tab-pane" role="tabpanel">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-clock-rotate-left text-danger me-2"></i> {{ $ar ? 'شروط وقواعد التسويات المالية' : 'Settlement & Refund Rules' }}</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'عدد أيام عدم الاسترداد بعد الحجز:' : 'Non-refundable Days:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->non_refund_days_count ?: '0' }} {{ $ar ? 'أيام' : 'days' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">{{ $ar ? 'دورة التسويات المالية (أيام الدفع):' : 'Settlement Cycle:' }}</label>
                            <input type="text" class="form-control" value="{{ $user->settlement_days_count ?: '0' }} {{ $ar ? 'أيام' : 'days' }}" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.getElementById('logo-input')?.addEventListener('change', function (e) {
    const file = e.target.files?.[0];
    const preview = document.getElementById('header-logo-preview');
    if (file && preview) {
        preview.src = URL.createObjectURL(file);
    }
});
</script>
@endpush
