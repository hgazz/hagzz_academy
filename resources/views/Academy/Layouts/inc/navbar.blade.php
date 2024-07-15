<div class="header-container container-xxl">
    <header class="header navbar navbar-expand-sm expand-header justify-content-between">

        <ul class="navbar-item theme-brand flex-row  text-center">
            <li class="nav-item theme-logo">
                <a href="{{ route('academy.index') }}">
                    <img src="{{ asset('assetsAdmin/logo/Icon-Black.svg') }}"  class="bg-transparent" alt="hagzz">
                </a>
            </li>
            <li class="nav-item theme-text">
                <a href="{{ route('academy.index') }}" class="nav-link"> {{ trans('admin.bokit') }} </a>
            </li>
        </ul>


        <ul class="navbar-item flex-row ms-lg-auto ms-0 action-area gap-lg-3 gap-md-2 gap-sm-1">

            <li class="nav-item dropdown language-dropdown">
                <a href="javascript:void(0)" class="nav-link dropdown-toggle" id="language-dropdown" data-bs-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    <img
                        src="{{ app()->getLocale() === 'en' ? asset('assetsAdmin/src/assets/img/1x1/us.svg') : asset('assetsAdmin/src/assets/img/1x1/qa.svg') }}"
                        class="flag-width" alt="flag">
                    {{ LaravelLocalization::getCurrentLocaleNative() }}
                </a>
                <div class="dropdown-menu position-absolute" aria-labelledby="language-dropdown">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        @if($properties['native']==="English")
                            <a class="dropdown-item d-flex"
                               href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                <img src="{{ asset('assetsAdmin/translate.svg') }}" class="flag-width" alt="flag">
                                <span class="align-self-center">{{ $properties['native'] }}</span></a>
                        @elseif($properties['native']==="العربية")
                            <a class="dropdown-item d-flex"
                               href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                <img src="{{ asset('assetsAdmin/src/assets/img/1x1/qa.svg') }}" class="flag-width" alt="flag">
                                <span class="align-self-center">{{ $properties['native'] }}</span></a>
                        @endif
                    @endforeach
                </div>
            </li>

            <li class="nav-item theme-toggle-item">
                <a href="javascript:void(0);" class="nav-link theme-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon dark-mode"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun light-mode"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                </a>
            </li>

            <li class="nav-item dropdown notification-dropdown">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="notificationDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><span class="badge badge-success"></span>
                </a>

                <div class="dropdown-menu position-absolute" aria-labelledby="notificationDropdown">
                    <div class="notification-scroll">
                        <div class="drodpown-title notification mt-2">
                            <h6 class="d-flex justify-content-between"><span class="align-self-center">{{ trans('admin.notifications.notifications') }}</span> <span class="badge badge-secondary">{{ auth('academy')->user()->unreadNotifications->count() }}</span></h6>
                        </div>
                       @foreach(auth()->user()->unreadNotifications as $notification)
                           @include('Academy.Layouts.inc.notifications.' .  $notification->type, ['notification' => $notification])
                       @endforeach
{{--                        <div class="drodpown-title notification mt-2">--}}
{{--                            <a class="d-flex justify-content-between" href="javascript:void(0)">--}}
{{--                                <h6 class="d-flex justify-content-between">{{ trans('admin.notifications.view_all') }}</h6>--}}
{{--                            </a>--}}
{{--                        </div>--}}
                    </div>
                </div>

            </li>

            <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar-container">
                        <div class="avatar avatar-sm avatar-indicators avatar-online">
                            <img alt="avatar" src="{{ auth()->user('academy')->logo }}" width="40px" height="40px" class="rounded-circle">
                        </div>
                    </div>
                </a>

                <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                    <div class="user-profile-section">
                        <div class="media mx-auto">
                            <div class="emoji me-2">
                                &#x1F44B;
                            </div>
                            <div class="media-body">
                                <h5>{{ auth('academy')->user()->owner_name }}</h5>
                                <p>{{ auth('academy')->user()->commercial_name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <a href="{{route('academy.profile.index')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> <span>{{trans('admin.profile.profile')}}</span>
                        </a>
                    </div>
                    <form method="POST" action="{{ route('academy.logout') }}" id="logout-form">
                        @csrf
                        <div class="dropdown-item" onclick="document.getElementById('logout-form').submit();">
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     class="feather feather-log-out">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span>{{ trans('admin.auth.logout') }}</span>
                            </a>
                        </div>
                    </form>
                </div>

            </li>
        </ul>
    </header>
</div>
