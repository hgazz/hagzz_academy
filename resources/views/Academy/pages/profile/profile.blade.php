@extends('Academy.Layouts.master')
@section('title', trans('admin.bokit'))
@push('css')
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link href="{{ asset('assetsAdmin/src/assets/css/light/components/tabs.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/assets/css/light/elements/alert.css') }}">

    <link href="{{ asset('assetsAdmin/src/assets/css/dark/components/tabs.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/assets/css/dark/elements/alert.css') }}">
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
                                          method="post">
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

