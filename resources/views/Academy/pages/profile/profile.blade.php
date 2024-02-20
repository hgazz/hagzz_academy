@extends('Academy.Layouts.master')
@section('title', trans('admin.bokit'))
@push('css')
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" href="{{asset('assetsAdmin')}}/src/plugins/src/filepond/filepond.min.css">
    <link rel="stylesheet" href="{{asset('assetsAdmin')}}/src/plugins/src/filepond/FilePondPluginImagePreview.min.css">
    <link href="{{asset('assetsAdmin')}}/src/plugins/src/notification/snackbar/snackbar.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('assetsAdmin')}}/src/plugins/src/sweetalerts2/sweetalerts2.css">

    <link href="{{asset('assetsAdmin')}}/src/plugins/css/light/filepond/custom-filepond.css" rel="stylesheet" type="text/css" />
    <link href="{{asset('assetsAdmin')}}/src/assets/css/light/components/tabs.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{asset('assetsAdmin')}}/src/assets/css/light/elements/alert.css">

    <link href="{{asset('assetsAdmin')}}/src/plugins/css/light/sweetalerts2/custom-sweetalert.css" rel="stylesheet" type="text/css" />
    <link href="{{asset('assetsAdmin')}}/src/plugins/css/light/notification/snackbar/custom-snackbar.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/forms/switches.css">
    <link href="{{asset('assetsAdmin')}}/src/assets/css/light/components/list-group.css" rel="stylesheet" type="text/css">

    <link href="{{asset('assetsAdmin')}}/src/assets/css/light/users/account-setting.css" rel="stylesheet" type="text/css" />



    <link href="{{asset('assetsAdmin')}}/src/plugins/css/dark/filepond/custom-filepond.css" rel="stylesheet" type="text/css" />
    <link href="{{asset('assetsAdmin')}}/src/assets/css/dark/components/tabs.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{asset('assetsAdmin')}}/src/assets/css/dark/elements/alert.css">

    <link href="{{asset('assetsAdmin')}}/src/plugins/css/dark/sweetalerts2/custom-sweetalert.css" rel="stylesheet" type="text/css" />
    <link href="{{asset('assetsAdmin')}}/src/plugins/css/dark/notification/snackbar/custom-snackbar.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{asset('assetsAdmin')}}/src/assets/css/dark/forms/switches.css">
    <link href="{{asset('assetsAdmin')}}/src/assets/css/dark/components/list-group.css" rel="stylesheet" type="text/css">

    <link href="{{asset('assetsAdmin')}}/src/assets/css/dark/users/account-setting.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <!--  BEGIN CONTENT AREA  -->
    <div class="layout-px-spacing">

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
                                        <li class="breadcrumb-item"><a href="#">{{trans('admin.profile.user')}}</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{trans('admin.profile.profile')}}</li>
                                    </ol>
                                </nav>

                            </div>
                        </div>
                        <ul class="navbar-nav flex-row ms-auto breadcrumb-action-dropdown">
                            <li class="nav-item more-dropdown">
                                <div class="dropdown  custom-dropdown-icon">
                                    <a class="dropdown-toggle btn" href="#" role="button" id="customDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span>{{trans('admin.profile.Settings')}}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down custom-dropdown-arrow"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="customDropdown">

                                        <a class="dropdown-item" data-value="Settings" data-icon="<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;24&quot; height=&quot;24&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; class=&quot;feather feather-settings&quot;><circle cx=&quot;12&quot; cy=&quot;12&quot; r=&quot;3&quot;></circle><path d=&quot;M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z&quot;></path></svg>" href="javascript:void(0);">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg> Settings
                                        </a>

                                        <a class="dropdown-item" data-value="Mail" data-icon="<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;24&quot; height=&quot;24&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; class=&quot;feather feather-mail&quot;><path d=&quot;M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z&quot;></path><polyline points=&quot;22,6 12,13 2,6&quot;></polyline></svg>" href="javascript:void(0);">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> Mail
                                        </a>

                                        <a class="dropdown-item" data-value="Print" data-icon="<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;24&quot; height=&quot;24&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; class=&quot;feather feather-printer&quot;><polyline points=&quot;6 9 6 2 18 2 18 9&quot;></polyline><path d=&quot;M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2&quot;></path><rect x=&quot;6&quot; y=&quot;14&quot; width=&quot;12&quot; height=&quot;8&quot;></rect></svg>" href="javascript:void(0);">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg> Print
                                        </a>

                                        <a class="dropdown-item" data-value="Download" data-icon="<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;24&quot; height=&quot;24&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; class=&quot;feather feather-download&quot;><path d=&quot;M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4&quot;></path><polyline points=&quot;7 10 12 15 17 10&quot;></polyline><line x1=&quot;12&quot; y1=&quot;15&quot; x2=&quot;12&quot; y2=&quot;3&quot;></line></svg>" href="javascript:void(0);">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg> Download
                                        </a>

                                        <a class="dropdown-item" data-value="Share" data-icon="<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;24&quot; height=&quot;24&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; class=&quot;feather feather-share-2&quot;><circle cx=&quot;18&quot; cy=&quot;5&quot; r=&quot;3&quot;></circle><circle cx=&quot;6&quot; cy=&quot;12&quot; r=&quot;3&quot;></circle><circle cx=&quot;18&quot; cy=&quot;19&quot; r=&quot;3&quot;></circle><line x1=&quot;8.59&quot; y1=&quot;13.51&quot; x2=&quot;15.42&quot; y2=&quot;17.49&quot;></line><line x1=&quot;15.41&quot; y1=&quot;6.51&quot; x2=&quot;8.59&quot; y2=&quot;10.49&quot;></line></svg>" href="javascript:void(0);">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-share-2"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg> Share
                                        </a>

                                    </div>

                                </div>
                            </li>
                        </ul>
                    </header>
                </div>
            </div>
            <!--  END BREADCRUMBS  -->

            <div class="account-settings-container layout-top-spacing">

                <div class="account-content">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h2>{{trans('admin.profile.Settings')}}</h2>

                            <ul class="nav nav-pills" id="animateLine" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="animated-underline-home-tab" data-bs-toggle="tab" href="#animated-underline-home" role="tab" aria-controls="animated-underline-home" aria-selected="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg> Home</button>
                                </li>

                            </ul>
                        </div>
                    </div>

                    <div class="tab-content" id="animateLineContent-4">
                        <div class="tab-pane fade show active" id="animated-underline-home" role="tabpanel" aria-labelledby="animated-underline-home-tab">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                                    <form class="section general-info" action="{{route('academy.profile.update',$user)}}" method="post" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="info">
                                            <h6 class="">{{trans('admin.profile.General Information')}}</h6>
                                            <div class="row">
                                                <div class="col-lg-11 mx-auto">
                                                    <div class="row">
                                                        <div class="col-xl-2 col-lg-12 col-md-4">
                                                            <div class="profile-image  mt-4 pe-md-4">

                                                                <!-- // The classic file input element we'll enhance
                                                                // to a file pond, we moved the configuration
                                                                // properties to JavaScript -->

                                                                <div >
                                                                    <input type="file" class="form-control"
                                                                           name="logo"/>
                                                                </div>
                                                                <img src="{{'https://bokit-app.s3.eu-north-1.amazonaws.com/images/academies/'.$user->logo}}" class="mt-2" width="50" height="50">
                                                                @error('logo')
                                                                <span class="text-danger">{{$message}}</span>
                                                                @enderror

                                                            </div>
                                                        </div>
                                                        <div class="col-xl-10 col-lg-12 col-md-8 mt-md-0 mt-4">
                                                            <div class="form">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="fullName">{{trans('admin.profile.name')}}</label>
                                                                            <input type="text" class="form-control mb-3" name="name" id="fullName" placeholder="Name" value="{{$user->name ?? ''}}">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="profession">{{trans('admin.profile.owner_name')}}</label>
                                                                            <input type="text" class="form-control mb-3" name="owner_name" id="profession" placeholder="owner_name" value="{{$user->owner_name ?? ''}}">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="address">{{trans('admin.profile.email')}}</label>
                                                                            <input type="email" class="form-control mb-3" name="email" id="address" placeholder="Email" value="{{$user->email ?? ''}}" >
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="location">{{trans('admin.profile.phone')}}</label>
                                                                            <input type="tel" name="phone" class="form-control mb-3" id="location" placeholder="Phone" value="{{$user->phone ?? ''}}">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-11 mx-auto">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="input-group social-fb mb-3">
                                                                                    <span class="input-group-text me-3" id="fb"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></span>
                                                                                    <input type="text" class="form-control" name="facebook" placeholder="Facebook Username" aria-label="Username" aria-describedby="fb" value="{{$user->facebook ?? ''}}">
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-6">
                                                                                <div class="input-group social-github mb-3">
                                                                                    <span class="input-group-text me-3" id="github"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-github"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg></span>
                                                                                    <input type="text" class="form-control" name="instagram" placeholder="Instagram Username" aria-label="Username" aria-describedby="github" value="{{$user->instagram ?? ''}}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    <div class="col-md-12 mt-1">
                                                                        <div class="form-group text-end">
                                                                            <button class="btn btn-secondary">{{trans('admin.profile.save')}}</button>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="tab-pane fade" id="animated-underline-profile" role="tabpanel" aria-labelledby="animated-underline-profile-tab">
                            <div class="row">
                                <div class="col-xl-6 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info payment-info">
                                        <div class="info">
                                            <h6 class="">Billing Address</h6>
                                            <p>Changes to your <span class="text-success">Billing</span> information will take effect starting with scheduled payment and will be refelected on your next invoice.</p>

                                            <div class="list-group mt-4">
                                                <label class="list-group-item">
                                                    <div class="d-flex w-100">
                                                        <div class="billing-radio me-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="billingAddress" id="billingAddress1" checked>
                                                            </div>
                                                        </div>
                                                        <div class="billing-content">
                                                            <div class="fw-bold">Address #1</div>
                                                            <p>2249 Caynor Circle, New Brunswick, New Jersey</p>
                                                        </div>
                                                        <div class="billing-edit align-self-center ms-auto">
                                                            <button class="btn btn-dark">Edit</button>
                                                        </div>
                                                    </div>
                                                </label>

                                                <label class="list-group-item">
                                                    <div class="d-flex w-100">
                                                        <div class="billing-radio me-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="billingAddress" id="billingAddress2">
                                                            </div>
                                                        </div>
                                                        <div class="billing-content">
                                                            <div class="fw-bold">Address #2</div>
                                                            <p>4262 Leverton Cove Road, Springfield, Massachusetts</p>
                                                        </div>
                                                        <div class="billing-edit align-self-center ms-auto">
                                                            <button class="btn btn-dark">Edit</button>
                                                        </div>
                                                    </div>
                                                </label>
                                                <label class="list-group-item">
                                                    <div class="d-flex w-100">
                                                        <div class="billing-radio me-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="billingAddress" id="billingAddress3">
                                                            </div>
                                                        </div>
                                                        <div class="billing-content">
                                                            <div class="fw-bold">Address #3</div>
                                                            <p>2692 Berkshire Circle, Knoxville, Tennessee</p>
                                                        </div>
                                                        <div class="billing-edit align-self-center ms-auto">
                                                            <button class="btn btn-dark">Edit</button>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>

                                            <button class="btn btn-secondary mt-4 add-address">Add Address</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info payment-info">
                                        <div class="info">
                                            <h6 class="">Payment Method</h6>
                                            <p>Changes to your <span class="text-success">Payment Method</span> information will take effect starting with scheduled payment and will be refelected on your next invoice.</p>

                                            <div class="list-group mt-4">

                                                <label class="list-group-item">
                                                    <div class="d-flex w-100">
                                                        <div class="billing-radio me-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod1">
                                                            </div>
                                                        </div>
                                                        <div class="payment-card">
                                                            <img src="../src/assets/img/card-mastercard.svg" class="align-self-center me-3" alt="americanexpress">
                                                        </div>
                                                        <div class="billing-content">
                                                            <div class="fw-bold">Mastercard</div>
                                                            <p>XXXX XXXX XXXX 9704</p>
                                                        </div>
                                                        <div class="billing-edit align-self-center ms-auto">
                                                            <button class="btn btn-dark">Edit</button>
                                                        </div>
                                                    </div>
                                                </label>
                                                <label class="list-group-item">
                                                    <div class="d-flex w-100">
                                                        <div class="billing-radio me-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod2" checked>
                                                            </div>
                                                        </div>
                                                        <div class="payment-card">
                                                            <img src="../src/assets/img/card-americanexpress.svg" class="align-self-center me-3" alt="americanexpress">
                                                        </div>
                                                        <div class="billing-content">
                                                            <div class="fw-bold">American Express</div>
                                                            <p>XXXX XXXX XXXX 310</p>
                                                        </div>
                                                        <div class="billing-edit align-self-center ms-auto">
                                                            <button class="btn btn-dark">Edit</button>
                                                        </div>
                                                    </div>
                                                </label>
                                                <label class="list-group-item">
                                                    <div class="d-flex w-100">
                                                        <div class="billing-radio me-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod3">
                                                            </div>
                                                        </div>
                                                        <div class="payment-card">
                                                            <img src="../src/assets/img/card-visa.svg" class="align-self-center me-3" alt="americanexpress">
                                                        </div>
                                                        <div class="billing-content">
                                                            <div class="fw-bold">Visa</div>
                                                            <p>XXXX XXXX XXXX 5264</p>
                                                        </div>
                                                        <div class="billing-edit align-self-center ms-auto">
                                                            <button class="btn btn-dark">Edit</button>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>

                                            <button class="btn btn-secondary mt-4 add-payment">Add Payment Method</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info payment-info">
                                        <div class="info">
                                            <h6 class="">Add Billing Address</h6>
                                            <p>Changes your New <span class="text-success">Billing</span> Information.</p>

                                            <div class="row mt-4">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">first Name</label>
                                                        <input type="text" class="form-control add-billing-address-input">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">last Name</label>
                                                        <input type="text" class="form-control add-billing-address-input">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Address</label>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">City</label>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Country</label>
                                                        <select class="form-select">
                                                            <option selected="">Choose...</option>
                                                            <option value="united-states">United States</option>
                                                            <option value="brazil">Brazil</option>
                                                            <option value="indonesia">Indonesia</option>
                                                            <option value="turkey">Turkey</option>
                                                            <option value="russia">Russia</option>
                                                            <option value="india">India</option>
                                                            <option value="germany">Germany</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">ZIP</label>
                                                        <input type="tel" class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <button class="btn btn-primary mt-4">Add</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info payment-info">
                                        <div class="info">
                                            <h6 class="">Add Payment Method</h6>
                                            <p>Changes your New <span class="text-success">Payment Method</span> Information.</p>

                                            <div class="row mt-4">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Card Brand</label>
                                                        <div class="invoice-action-currency">
                                                            <div class="dropdown selectable-dropdown cardName-select">
                                                                <a id="cardBrandDropdown" href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="../src/assets/img/card-mastercard.svg" class="flag-width" alt="flag"> <span class="selectable-text">Mastercard</span> <span class="selectable-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></span></a>
                                                                <div class="dropdown-menu" aria-labelledby="cardBrandDropdown">
                                                                    <a class="dropdown-item" data-img-value="../src/assets/img/card-mastercard.svg" data-value="GBP - British Pound" href="javascript:void(0);"><img src="../src/assets/img/card-mastercard.svg" class="flag-width" alt="flag"> Mastercard</a>
                                                                    <a class="dropdown-item" data-img-value="../src/assets/img/card-americanexpress.svg" data-value="IDR - Indonesian Rupiah" href="javascript:void(0);"><img src="../src/assets/img/card-americanexpress.svg" class="flag-width" alt="flag"> American Express</a>
                                                                    <a class="dropdown-item" data-img-value="../src/assets/img/card-visa.svg" data-value="USD - US Dollar" href="javascript:void(0);"><img src="../src/assets/img/card-visa.svg" class="flag-width" alt="flag"> Visa</a>
                                                                    <a class="dropdown-item" data-img-value="../src/assets/img/card-discover.svg" data-value="INR - Indian Rupee" href="javascript:void(0);"><img src="../src/assets/img/card-discover.svg" class="flag-width" alt="flag"> Discover</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Card Number</label>
                                                        <input type="text" class="form-control add-payment-method-input">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Holder Name</label>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">CVV/CVV2</label>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Card Expiry</label>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <button class="btn btn-primary mt-4">Add</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="tab-pane fade" id="animated-underline-preferences" role="tabpanel" aria-labelledby="animated-underline-preferences-tab">
                            <div class="row">
                                <div class="col-xl-6 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Choose Theme</h6>
                                            <div class="d-sm-flex justify-content-around">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" checked>
                                                    <label class="form-check-label" for="flexRadioDefault1">
                                                        <img class="ms-3" width="100" height="68" alt="settings-dark" src="../src/assets/img/settings-light.svg">
                                                    </label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                                    <label class="form-check-label" for="flexRadioDefault2">
                                                        <img class="ms-3" width="100" height="68" alt="settings-light" src="../src/assets/img/settings-dark.svg">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Activity data</h6>
                                            <p>Download your Summary, Task and Payment History Data</p>
                                            <div class="form-group mt-4">
                                                <button class="btn btn-primary">Download Data</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Public Profile</h6>
                                            <p>Your <span class="text-success">Profile</span> will be visible to anyone on the network.</p>
                                            <div class="form-group mt-4">
                                                <div class="switch form-switch-custom switch-inline form-switch-secondary mt-1">
                                                    <input class="switch-input" type="checkbox" role="switch" id="publicProfile" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Show my email</h6>
                                            <p>Your <span class="text-success">Email</span> will be visible to anyone on the network.</p>
                                            <div class="form-group mt-4">
                                                <div class="switch form-switch-custom switch-inline form-switch-secondary mt-1">
                                                    <input class="switch-input" type="checkbox" role="switch" id="showMyEmail">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Enable keyboard shortcuts</h6>
                                            <p>When enabled, press <code class="text-success">ctrl</code> for help</p>
                                            <div class="form-group mt-4">
                                                <div class="switch form-switch-custom switch-inline form-switch-secondary mt-1">
                                                    <input class="switch-input" type="checkbox" role="switch" id="EnableKeyboardShortcut">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Hide left navigation</h6>
                                            <p>Sidebar will be <span class="text-success">hidden</span> by default</p>
                                            <div class="form-group mt-4">
                                                <div class="switch form-switch-custom switch-inline form-switch-secondary mt-1">
                                                    <input class="switch-input" type="checkbox" role="switch" id="hideLeftNavigation">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Advertisements</h6>
                                            <p>Display <span class="text-success">Ads</span> on your dashboard</p>
                                            <div class="form-group mt-4">
                                                <div class="switch form-switch-custom switch-inline form-switch-secondary mt-1">
                                                    <input class="switch-input" type="checkbox" role="switch" id="advertisements">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Social Profile</h6>
                                            <p>Enable your <span class="text-success">social</span> profiles on this network</p>
                                            <div class="form-group mt-4">
                                                <div class="switch form-switch-custom switch-inline form-switch-secondary mt-1">
                                                    <input class="switch-input" type="checkbox" role="switch" id="socialprofile" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="animated-underline-contact" role="tabpanel" aria-labelledby="animated-underline-contact-tab">
                            <div class="alert alert-arrow-right alert-icon-right alert-light-warning alert-dismissible fade show mb-4" role="alert">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
                                <strong>Warning!</strong> Please proceed with caution. For any assistance - <a href="javascript:void(0);">Contact Us</a>
                            </div>

                            <div class="row">
                                <div class="col-xl-4 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Purge Cache</h6>
                                            <p>Remove the active resource from the cache without waiting for the predetermined cache expiry time.</p>
                                            <div class="form-group mt-4">
                                                <button class="btn btn-secondary btn-clear-purge">Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Deactivate Account</h6>
                                            <p>You will not be able to receive messages, notifications for up to 24 hours.</p>
                                            <div class="form-group mt-4">
                                                <div class="switch form-switch-custom switch-inline form-switch-success mt-1">
                                                    <input class="switch-input" type="checkbox" role="switch" id="socialformprofile-custom-switch-success">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info">
                                            <h6 class="">Delete Account</h6>
                                            <p>Once you delete the account, there is no going back. Please be certain.</p>
                                            <div class="form-group mt-4">
                                                <button class="btn btn-danger btn-delete-account">Delete my account</button>
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

    </div>    <!--  END CONTENT AREA  -->
@endsection
@push('js')
    <!--  BEGIN CUSTOM SCRIPTS FILE  -->
    <script src="{{asset('assetsAdmin')}}/src/plugins/src/filepond/filepond.min.js"></script>
    <script src="{{asset('assetsAdmin')}}/src/plugins/src/filepond/FilePondPluginFileValidateType.min.js"></script>
    <script src="{{asset('assetsAdmin')}}/src/plugins/src/filepond/FilePondPluginImageExifOrientation.min.js"></script>
    <script src="{{asset('assetsAdmin')}}/src/plugins/src/filepond/FilePondPluginImagePreview.min.js"></script>
    <script src="{{asset('assetsAdmin')}}/src/plugins/src/filepond/FilePondPluginImageCrop.min.js"></script>
    <script src="{{asset('assetsAdmin')}}/src/plugins/src/filepond/FilePondPluginImageResize.min.js"></script>
    <script src="{{asset('assetsAdmin')}}/src/plugins/src/filepond/FilePondPluginImageTransform.min.js"></script>
    <script src="{{asset('assetsAdmin')}}/src/plugins/src/filepond/filepondPluginFileValidateSize.min.js"></script>
    <script src="{{asset('assetsAdmin')}}/src/plugins/src/notification/snackbar/snackbar.min.js"></script>
    <script src="{{asset('assetsAdmin')}}/src/plugins/src/sweetalerts2/sweetalerts2.min.js"></script>
    <script src="{{asset('assetsAdmin')}}/src/assets/js/users/account-settings.js"></script>
    <!--  END CUSTOM SCRIPTS FILE  -->
@endpush
