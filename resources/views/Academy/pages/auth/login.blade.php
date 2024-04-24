<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>{{ trans('admin.partner') . ' | '   . trans('admin.auth.login') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assetsAdmin/logo/Tab icon.svg') }}"/>
    <link href="{{ asset('assetsAdmin/layouts/vertical-light-menu/css/light/loader.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assetsAdmin/layouts/vertical-light-menu/css/dark/loader.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('assetsAdmin/layouts/vertical-light-menu/loader.js') }}"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{ asset('assetsAdmin/src/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>

    <link href="{{ asset('assetsAdmin/layouts/vertical-light-menu/css/light/plugins.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assetsAdmin/src/assets/css/light/authentication/auth-boxed.css') }}" rel="stylesheet" type="text/css"/>

    <link href="{{ asset('assetsAdmin/layouts/vertical-light-menu/css/dark/plugins.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assetsAdmin/src/assets/css/dark/authentication/auth-boxed.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->

</head>
<body class="form">

<!-- BEGIN LOADER -->
<div id="load_screen">
    <div class="loader">
        <div class="loader-content">
            <div class="spinner-grow align-self-center"></div>
        </div>
    </div>
</div>
<!--  END LOADER -->

<div class="auth-container d-flex">

    <div class="container mx-auto align-self-center">

        <div class="row">

            <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center mx-auto">
                <div class="card mt-3 mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h2>{{ trans('admin.auth.sign_in') }}</h2>
                                    <p>{{ trans('admin.auth.enter_your_email_and_password') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <img src="{{ asset('assetsAdmin/logo/Icon-Black.svg') }}" class="img-thumbnail w-100 h100" alt="hagzz">
                                </div>
                            </div>


                            @if(session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session()->get('error') }}
                            </div>
                            @endif

                            <form action="{{ route('academy.login') }}" method="post">
                                @csrf
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">{{ trans('admin.auth.email') }}</label>
                                        <input type="email" id="email" class="form-control" name="email" value={{ old('email') }}>
                                        @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-4">
                                        <label for="password" class="form-label">{{ trans('admin.auth.password') }}</label>
                                        <input id="password" type="password" class="form-control" name="password">
                                        @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <div class="form-check form-check-primary form-check-inline">

                                            <input class="form-check-input me-3" type="checkbox" id="form-check-default"
                                                   name="remember" @if(old('remember')) checked @endif>
                                            <label class="form-check-label" for="form-check-default">
                                                {{ trans('admin.auth.remember_me') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-4">
                                        <button class="btn btn-secondary w-100" type="submit">{{ trans('admin.auth.sign_in') }}</button>
                                    </div>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<script src="{{ asset('assetsAdmin/ar/src/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- END GLOBAL MANDATORY SCRIPTS -->

</body>
</html>
