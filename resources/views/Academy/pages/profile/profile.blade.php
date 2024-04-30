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

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link " id="animated-underline-profile-tab" data-bs-toggle="tab" href="#animated-underline-profile" role="tab" aria-controls="animated-underline-home" aria-selected="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg> Contract</button>
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
                                                                <img src="{{$user->logo}}" class="mt-2" width="50" height="50">
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
                                                                            <input type="text" class="form-control" name="name" id="fullName" placeholder="Name" value="{{$user->name ?? ''}}">
                                                                            @error('name')
                                                                            <span class="text-danger">{{$message}}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="profession">{{trans('admin.profile.owner_name')}}</label>
                                                                            <input type="text" class="form-control" name="owner_name" id="profession" placeholder="owner_name" value="{{$user->owner_name ?? ''}}">
                                                                            @error('owner_name')
                                                                            <span class="text-danger">{{$message}}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="address">{{trans('admin.profile.email')}}</label>
                                                                            <input type="email" class="form-control" name="email" id="address" placeholder="Email" value="{{$user->email ?? ''}}" >
                                                                            @error('email')
                                                                            <span class="text-danger">{{$message}}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="location">{{trans('admin.profile.phone')}}</label>
                                                                            <input type="tel" name="phone" class="form-control" id="location" placeholder="Phone" value="{{$user->phone ?? ''}}">
                                                                            @error('phone')
                                                                            <span class="text-danger">{{$message}}</span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-11 mx-auto">
                                                                        <div class="row">
                                                                            <div class="col-md-6 mt-2">
                                                                                <div class="input-group social-fb mb-3">
                                                                                    <span class="input-group-text me-3" id="fb"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></span>
                                                                                    <input type="text" class="form-control" name="facebook" placeholder="Facebook Username" aria-label="Username" aria-describedby="fb" value="{{$user->facebook ?? ''}}">
                                                                                    @error('facebook')
                                                                                    <span class="text-danger">{{$message}}</span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-6 mt-2">
                                                                                <div class="input-group social-github mb-3">
                                                                                    <span class="input-group-text me-3" id="instagram">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></span>
                                                                                    <input type="text" class="form-control" name="instagram" placeholder="Instagram Username" aria-label="Username" aria-describedby="github" value="{{$user->instagram ?? ''}}">
                                                                                    @error('instagram')
                                                                                    <span class="text-danger">{{$message}}</span>
                                                                                    @enderror
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
                            <div class="row ">
                                <div class="col-xl-6 col-lg-12 col-md-12 layout-spacing mx-auto">
                                    <div class="section general-info payment-info">
                                        <div class="info">
                                            <h6 class="">Contract Data</h6>
                                            <div class="list-group mt-4">
                                                <label class="list-group-item">
                                                    <div class="d-flex w-100">
                                                        <div class="billing-content">
                                                            <div class="fw-bold">Contract Date</div>
                                                            <p>{{$user->contract_date ?? 'null'}}</p>
                                                        </div>
                                                    </div>
                                                </label>

                                                <label class="list-group-item">
                                                    <div class="d-flex w-100">
                                                        <div class="billing-content">
                                                            <div class="fw-bold">Contract Number</div>
                                                            <p>{{$user->contract_number ?? 'null'}}</p>
                                                        </div>

                                                    </div>
                                                </label>
                                                <label class="list-group-item">
                                                    <div class="d-flex w-100">
                                                        <div class="billing-content">
                                                            <div class="fw-bold">Account Manager</div>
                                                            <p>{{$user->account_manager ?? 'null'}}</p>
                                                        </div>
                                                    </div>
                                                </label>
                                                <label class="list-group-item">
                                                    <div class="d-flex w-100">
                                                        <div class="billing-content">
                                                            <div class="fw-bold">Status</div>
                                                            <p>{{$user->status ?? 'null'}}</p>
                                                        </div>
                                                    </div>
                                                </label>
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
