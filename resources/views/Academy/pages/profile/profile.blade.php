@extends('Academy.Layouts.master')
@section('title', trans('admin.bokit'))
@push('css')
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link href="{{ asset('assetsAdmin/src/assets/css/light/components/tabs.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/assets/css/light/elements/alert.css') }}">

    <link href="{{ asset('assetsAdmin/src/assets/css/dark/components/tabs.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/assets/css/dark/elements/alert.css') }}">
    <style>
        .saas-summary { background: linear-gradient(135deg, #0f766e, #155e75); color: #fff; border: 0; border-radius: 8px; overflow: hidden; box-shadow: 0 14px 30px rgba(15, 118, 110, .18); }
        .saas-summary__body { padding: 24px; }
        .saas-summary__top { display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; }
        .saas-summary__title { display: flex; align-items: center; gap: 12px; }
        .saas-summary__icon { display: grid; place-items: center; width: 44px; height: 44px; flex: 0 0 44px; border-radius: 8px; background: rgba(255,255,255,.15); }
        .saas-summary__icon svg { width: 23px; height: 23px; }
        .saas-summary h3 { color: #fff; margin: 0 0 3px; font-size: 21px; }
        .saas-summary p { color: rgba(255,255,255,.76); margin: 0; }
        .saas-status { padding: 7px 12px; border-radius: 999px; background: rgba(255,255,255,.16); font-weight: 700; white-space: nowrap; }
        .saas-status.is-active { background: #dcfce7; color: #166534; }
        .saas-status.is-trial { background: #fef3c7; color: #92400e; }
        .saas-summary__grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-top: 20px; }
        .saas-detail { padding: 14px; border: 1px solid rgba(255,255,255,.17); border-radius: 8px; background: rgba(255,255,255,.08); min-width: 0; }
        .saas-detail span { display: block; color: rgba(255,255,255,.7); font-size: 12px; margin-bottom: 5px; }
        .saas-detail strong { display: block; color: #fff; font-size: 15px; overflow-wrap: anywhere; }
        .saas-limits { display: flex; flex-wrap: wrap; gap: 9px; margin-top: 14px; }
        .saas-limit { display: inline-flex; align-items: center; gap: 7px; padding: 8px 11px; border-radius: 7px; background: rgba(255,255,255,.1); font-size: 13px; }
        .saas-limit svg { width: 16px; height: 16px; }
        .saas-empty { background: #fff; color: #334155; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(15,23,42,.06); }
        .saas-empty h3 { color: #0f172a; }
        .saas-empty p { color: #64748b; }
        .saas-empty .saas-summary__icon { background: #ecfeff; color: #0f766e; }
        @media (max-width: 991px) { .saas-summary__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 575px) { .saas-summary__body { padding: 18px; } .saas-summary__top { flex-direction: column; } .saas-summary__grid { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('content')
    <!--BEGIN CONTENT AREA  -->
    <div class="layout-px-spacing">
        <div class="middle-content container-xxl p-0">

            <!--BEGIN BREADCRUMBS -->
            <div class="secondary-nav">
                <div class="breadcrumbs-container" data-page-heading="Analytics">
                    <header class="header navbar navbar-expand-sm">
                        <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-menu">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                        </a>
                        <div class="d-flex breadcrumb-content">
                            <div class="page-header">

                                <div class="page-title"></div>

                                <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="#">{{ trans('admin.profile.user') }}</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            {{ trans('admin.profile.profile') }}</li>
                                    </ol>
                                </nav>

                            </div>
                        </div>
                    </header>
                </div>
            </div>
            <!--END BREADCRUMBS  -->

            @php
                $isArabic = app()->getLocale() === 'ar';
                $subscriptionActive = $saasSubscription && in_array($saasSubscription->status, ['active', 'trial'], true)
                    && (!$saasSubscription->ends_at || $saasSubscription->ends_at->isToday() || $saasSubscription->ends_at->isFuture());
                $remainingDays = $saasSubscription?->ends_at && $saasSubscription->ends_at->isFuture()
                    ? now()->startOfDay()->diffInDays($saasSubscription->ends_at)
                    : null;
            @endphp
            <section class="saas-summary {{ $saasSubscription ? '' : 'saas-empty' }} mt-4" aria-label="{{ $isArabic ? 'اشتراك ساس' : 'SaaS subscription' }}">
                <div class="saas-summary__body">
                    <div class="saas-summary__top">
                        <div class="saas-summary__title">
                            <span class="saas-summary__icon"><i data-feather="layers"></i></span>
                            <div>
                                <p>{{ $isArabic ? 'اشتراك المنصة' : 'Platform subscription' }}</p>
                                <h3>{{ $saasSubscription?->plan?->name ?? ($isArabic ? 'لم يتم تعيين باقة بعد' : 'No plan assigned yet') }}</h3>
                            </div>
                        </div>
                        @if($saasSubscription)
                            <span class="saas-status {{ $subscriptionActive ? ($saasSubscription->status === 'trial' ? 'is-trial' : 'is-active') : '' }}">
                                {{ match($saasSubscription->status) { 'active' => $isArabic ? 'نشط' : 'Active', 'trial' => $isArabic ? 'تجريبي' : 'Trial', 'expired' => $isArabic ? 'منتهي' : 'Expired', default => $isArabic ? 'غير نشط' : 'Inactive' } }}
                            </span>
                        @endif
                    </div>

                    @if($saasSubscription)
                        <div class="saas-summary__grid">
                            <div class="saas-detail"><span>{{ $isArabic ? 'دورة الفوترة' : 'Billing cycle' }}</span><strong>{{ $saasSubscription->billing_cycle === 'annual' ? ($isArabic ? 'سنوي' : 'Annual') : ($isArabic ? 'شهري' : 'Monthly') }}</strong></div>
                            <div class="saas-detail"><span>{{ $isArabic ? 'قيمة الاشتراك' : 'Subscription price' }}</span><strong>{{ number_format((float) ($saasSubscription->price_amount ?? $saasSubscription->custom_price ?? 0), 2) }} {{ $saasSubscription->currency_code }}</strong></div>
                            <div class="saas-detail"><span>{{ $isArabic ? 'تاريخ البداية' : 'Start date' }}</span><strong>{{ $saasSubscription->starts_at?->format('Y-m-d') ?? '-' }}</strong></div>
                            <div class="saas-detail"><span>{{ $isArabic ? 'تاريخ الانتهاء' : 'End date' }}</span><strong>{{ $saasSubscription->ends_at?->format('Y-m-d') ?? ($isArabic ? 'غير محدد' : 'Open-ended') }}</strong></div>
                            <div class="saas-detail"><span>{{ $isArabic ? 'مدة الاشتراك' : 'Subscription duration' }}</span><strong>{{ $saasSubscription->starts_at && $saasSubscription->ends_at ? $saasSubscription->starts_at->diffInDays($saasSubscription->ends_at) . ' ' . ($isArabic ? 'يوم' : 'days') : ($isArabic ? 'مفتوح' : 'Open-ended') }}</strong></div>
                            <div class="saas-detail"><span>{{ $isArabic ? 'المدة المتبقية' : 'Remaining time' }}</span><strong>{{ $remainingDays !== null ? $remainingDays . ' ' . ($isArabic ? 'يوم' : 'days') : ($isArabic ? 'غير محدد' : 'Not specified') }}</strong></div>
                            <div class="saas-detail"><span>{{ $isArabic ? 'التجديد التلقائي' : 'Auto renewal' }}</span><strong>{{ $saasSubscription->auto_renew ? ($isArabic ? 'مفعّل' : 'Enabled') : ($isArabic ? 'غير مفعّل' : 'Disabled') }}</strong></div>
                            <div class="saas-detail"><span>{{ $isArabic ? 'نوع النشاط' : 'Business type' }}</span><strong>{{ match($user->business_type) { 'venue' => $isArabic ? 'ملاعب' : 'Venues', 'hybrid' => $isArabic ? 'أكاديمية وملاعب' : 'Academy & venues', default => $isArabic ? 'أكاديمية' : 'Academy' } }}</strong></div>
                        </div>
                        @if($saasSubscription->plan)
                            <div class="saas-limits">
                                <span class="saas-limit"><i data-feather="home"></i>{{ $isArabic ? 'الملاعب' : 'Venues' }}: {{ $saasSubscription->plan->max_venues }}</span>
                                <span class="saas-limit"><i data-feather="map-pin"></i>{{ $isArabic ? 'المساحات' : 'Spaces' }}: {{ $saasSubscription->plan->max_spaces }}</span>
                                <span class="saas-limit"><i data-feather="users"></i>{{ $isArabic ? 'الموظفون' : 'Staff' }}: {{ $saasSubscription->plan->max_staff }}</span>
                            </div>
                        @endif
                    @else
                        <p class="mt-3">{{ $isArabic ? 'تواصل مع إدارة المنصة لتعيين باقة مناسبة لهذا الحساب.' : 'Contact platform administration to assign a suitable plan to this account.' }}</p>
                    @endif
                </div>
            </section>

            <div class="row layout-top-spacing">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="margin-bottom:24px;">
                <div class="widget-content widget-content-area">
                    <div class="simple-tab">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                        data-bs-target="#home-tab-pane" type="button" role="tab"
                                        aria-controls="home-tab-pane"
                                        aria-selected="true">{{ trans('admin.Personal') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#profile-tab-pane" type="button" role="tab"
                                        aria-controls="profile-tab-pane"
                                        aria-selected="false">{{ trans('admin.contract_information') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab"
                                        data-bs-target="#contact-tab-pane" type="button" role="tab"
                                        aria-controls="contact-tab-pane"
                                        aria-selected="false">{{ trans('admin.bank_information') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="disabled-tab" data-bs-toggle="tab"
                                        data-bs-target="#disabled-tab-pane" type="button" role="tab"
                                        aria-controls="disabled-tab-pane"
                                        aria-selected="false">{{ trans('admin.settlements') }}</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel"
                                 aria-labelledby="home-tab" tabindex="0">
                                <div class="row">
                                    <h3 class="text-center mt-2">{{ trans('admin.contract_information') }}</h3>
                                    <form action="{{ route('academy.profile.update', auth()->user()) }}" class="text-center"
                                          method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="from-label" for="email">{{ trans('admin.profile.email') }}: </label>
                                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', auth('academy')->user()->email)}}" placeholder="{{ trans('admin.profile.email') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="from-label" for="username">{{ trans('admin.profile.name') }}: </label>
                                                <input type="text" class="form-control" id="username" name="name"
                                                       value="{{ old('name', auth()->user()->commercial_name) }}"
                                                       placeholder="{{ trans('admin.profile.name') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="from-label" for="phone">{{ trans('admin.profile.phone') }}: </label>
                                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="{{ trans('admin.profile.phone') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="from-label" for="photo">{{ trans('admin.logo') }}:</label>
                                                <input type="file" class="form-control" id="photo" name="logo" accept="image/*">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <h3 class="text-center">{{ trans('admin.social_links') }}</h3>
                                            <div class="col-md-6">
                                                <label class="from-label" for="link__facebook">{{ trans('admin.facebook_link') }}</label>
                                                <input type="text" class="form-control" id="link__facebook" name="facebook" placeholder="{{ trans('admin.facebook_link') }}" value="{{ old('facebook', auth('academy')->user()->facebook) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="from-label" for="link__instagram">{{ trans('admin.instagram_link') }}</label>
                                                <input type="text" class="form-control" id="link__instagram" name="instagram" placeholder="Enter Instagram Page Link"
                                                       value="{{ old('instagram', auth('academy')->user()->instagram) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="from-label" for="link__linkedIn">{{ trans('admin.linkedIn_link') }}</label>
                                                <input type="text" class="form-control" id="link__linkedIn" name="linkedin" value="{{ old('linkedin', auth('academy')->user()->linkedin) }}" placeholder="{{ trans('admin.linkedIn_link') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="from-label" for="link__website">{{ trans('admin.website_link') }}</label>
                                                <input type="text" class="form-control" id="link__website" name="website"
                                                       placeholder="{{ trans('admin.website_link') }}"
                                                       value="{{ old('website', auth('academy')->user()->website) }}">
                                            </div>
                                        </div>

                                        <button class="btn btn-secondary fs-4 mt-3 w-100 m-auto"
                                                type="submit">{{ trans('admin.profile.save') }}</button>
                                    </form>
                                </div>

                            </div>
                            <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                <h3 class="text-center mt-2">{{ trans('admin.contract_information') }}</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="cname">{{ trans('admin.contract_number') }}:</label>
                                        <input class="form-control" disabled type="text" id="cname" name="contract_number"
                                               placeholder="{{ trans('admin.contract_number') }}"
                                               value="{{ old('contract_number', auth('academy')->user()->contract_number) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cdate">{{ trans('admin.contract_date') }}</label>
                                        <input disabled class="form-control" type="date" id="cdate" name="cdate"
                                               placeholder="{{ trans('admin.contract_date') }}" value="{{ old('contract_date', auth('academy')->user()->contract_date) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="csdate">{{ trans('admin.contract_start_date') }}</label>
                                        <input disabled class="form-control" type="date" id="csdate" name="csdate"
                                               placeholder="{{ trans('admin.contract_start_date') }}"
                                               value="{{ old('start_date', auth('academy')->user()->start_date) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cedate">{{ trans('admin.contract_end_date') }}</label>
                                        <input disabled class="form-control" type="date" id="cedate" name="cedate"
                                               placeholder="{{ trans('admin.contract_end_date') }}"
                                               value="{{ old('end_date', auth('academy')->user()->end_date) }}">
                                    </div>
                                    <div class="col-md-6">
                                        @if(! is_null(auth('academy')->user()->contract_link))
                                            <a href="{{  auth('academy')->user()->contract_link }}" target="_blank">{{ trans('admin.download_contract') }}</a>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="commission_percentage">{{ trans('admin.commission_percentage') }}</label>
                                        <input disabled class="form-control" type="number" id="commission_percentage" name="commission_percentage"
                                                   placeholder="{{ trans('admin.commission_percentage') }}"
                                               value="{{ old('commission_percentage', auth('academy')->user()->commission_percentage) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel"
                                 aria-labelledby="contact-tab" tabindex="0">
                                <h3 class="text-center mt-2">{{ trans('admin.bank_information') }}</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="btype">{{ trans('admin.bank_type') }}</label>
                                        <input disabled class="form-control" type="text" id="btype" name="btype"
                                               placeholder="{{ trans('admin.bank_type') }}"
                                               value="{{ old('bank_account_type', auth('academy')->user()->bank_account_type) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bname">{{ trans('admin.bank_name') }}</label>
                                        <input disabled class="form-control" type="text" id="bname" name="bname"
                                               value="{{ old('bank_name', auth('academy')->user()->bank_name) }}"
                                               placeholder="{{ trans('admin.bank_name') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bnum">{{ trans('admin.bank_account_number') }}</label>
                                        <input disabled class="form-control" type="text" id="bnum" name="bnum"
                                               value="{{ old('bank_account_number', auth('academy')->user()->bank_account_number)}}"
                                               placeholder="{{ trans('admin.bank_account_number') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="beneficiary_name">{{ trans('admin.beneficiary_name') }}</label>
                                        <input disabled class="form-control" type="text" id="beneficiary_name" name="beneficiary_name"
                                               value="{{ old('beneficiary_name', auth('academy')->user()->beneficiary_name)}}"
                                               placeholder="{{ trans('admin.beneficiary_name') }}">
                                    </div>
                                </div>



                            </div>
                            <div class="tab-pane fade" id="disabled-tab-pane" role="tabpanel"
                                 aria-labelledby="disabled-tab" tabindex="0">
                                <h3 class="text-center mt-2">{{ trans('admin.settlements') }}</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="nrefund" class="form-label">{{ trans('admin.non_refund_days_acount') }}</label>
                                        <input disabled class="form-control" type="number" id="nrefund" name="nrefund" value="{{ old('non_refund_days_count', auth('academy')->user()->non_refund_days_count) }}" placeholder="{{ trans('admin.non_refund_days_acount') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="scount" class="form-label">{{ trans('admin.settlement_days_count') }}</label>
                                        <input disabled class="form-control" type="number" id="scount" name="scount" value="{{ old('settlement_days_count', auth('academy')->user()->settlement_days_count)}}" placeholder="settlements Days Count">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                </div>
            </div>
        </div>
    </div>

@endsection
