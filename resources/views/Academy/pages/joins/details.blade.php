@extends('Academy.Layouts.master')

@section('title', trans('admin.training.Show Details'))


@section('content')
    <div class="middle-content container-xxl p-0">

        <!--  BEGIN BREADCRUMBS  -->
        <div class="secondary-nav">
            <div class="breadcrumbs-container" data-page-heading="Analytics">
                <header class="header navbar navbar-expand-sm">
                    <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </a>
                    <div class="d-flex breadcrumb-content">
                        <div class="page-header">

                            <div class="page-title">
                            </div>

                            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('academy.report.joins') }}">{{ trans('admin.joins') }}</a> </li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.training.Show Details')}}</li>
                                </ol>
                            </nav>

                        </div>
                    </div>
                </header>
            </div>
        </div>
        <!--  END BREADCRUMBS  -->

        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="row m-auto">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <div class="simple-tab">

                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="home-tab-icon" data-bs-toggle="tab" data-bs-target="#home-tab-icon-pane" type="button" role="tab" aria-controls="home-tab-icon-pane" aria-selected="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg> {{ trans('admin.training.main_details') }}
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="details-tab-icon" data-bs-toggle="tab" data-bs-target="#details-tab-icon-pane" type="button" role="tab" aria-controls="details-tab-icon-pane" aria-selected="true">

                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>{{ trans('admin.user.user_details') }}
                                            </button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="home-tab-icon-pane" role="tabpanel" aria-labelledby="home-tab-icon" tabindex="0">
                                            <div class="container-fluid mt-4">
                                                <div class="row">
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.name') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->getTranslation('name', 'en') ." | ". $join->training->getTranslation('name', 'ar')}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.price') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->price}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.classes_days') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{ $join?->training?->classes_days != null ? implode('-', $join?->training?->classes_days) : null }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.academies.commercial_name') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->academy->commercial_name}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.coach') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->coach->name}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.max_players') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->max_players}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.level') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->level}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.gender') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->gender}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.age_group') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->age_group}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.address') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->address->address}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.sport.sport') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->sport->name}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.Active') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->active ? trans('admin.training.active') : trans('admin.training.InActive')}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.description_en') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->getTranslation('description', 'ar')}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.training.description_ar') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->training->getTranslation('description', 'en')}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="tab-pane fade show" id="details-tab-icon-pane" role="tabpanel" aria-labelledby="details-tab-icon" tabindex="0">
                                            <div class="container-fluid mt-4">
                                                <div class="row">
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.bookings.user') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->user->name}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.academies.parent_name') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->user->parent_name ?? 'N/A'}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.academies.parent_phone') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{ $join->user->parent_phone  ?? 'N/A'}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.academies.user_type') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->user->child_type ?? 'N/A'}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.academies.school_name') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->user->school_name ?? 'N/A'}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.academies.coach_preference') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->user->coach_preference ?? 'N/A'}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.academies.frequent_attendance') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->user->frequent_attendance ?? 'N/A'}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.academies.child_with_relation') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->user->relation_with_child ?? 'N/A'}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.academies.how_did_you_hear_about_us') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->user->referral_source ?? 'N/A'}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.academies.medical_condition') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->user->medical_condition ?? 'N/A'}}</p>
                                                                @if($join->user->getRawOriginal('medical_condition') == 'yes')
                                                                    <p class="card-text text-dark fw-bold">{{$join->user->medical_condition_details}}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mt-2">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title">{{ trans('admin.academies.additional_information') }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <p class="card-text text-dark fw-bold">{{$join->user->additional_information ?? 'N/A'}}</p>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

