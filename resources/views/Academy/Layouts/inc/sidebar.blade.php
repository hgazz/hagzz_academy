@php
    $servicesActive = Request::routeIs('academy.training.*') || Request::routeIs('academy.calendar.*') || Request::routeIs('academy.class.*');
    $studentsActive = Request::routeIs('academy.students.*')
        || Request::routeIs('academy.groups.*')
        || Request::routeIs('academy.competitions.*')
        || Request::routeIs('academy.attendance.*')
        || Request::routeIs('academy.subscriptions.*')
        || Request::routeIs('academy.student-reports.*');
    $reportsActive = Request::routeIs('academy.report.*');
    $venueActive = Request::routeIs('academy.venues.*') || Request::routeIs('academy.venue-spaces.*') || Request::routeIs('academy.venue-bookings.*');
    $hasVenueModule = auth('academy')->user()?->hasVenueModule();
    $isVenueOnly = auth('academy')->user()?->business_type === 'venue';
@endphp

<style>
    #sidebar .menu-icon {
        width: 22px;
        min-width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: rgba(14, 23, 38, .72);
        font-size: 18px;
        margin-inline-end: 12px;
    }

    #sidebar .menu.active > a .menu-icon,
    #sidebar .submenu .menu.active > a .menu-icon {
        color: #1b55e2;
    }

    #sidebar .submenu .menu-icon {
        font-size: 15px;
        width: 19px;
        min-width: 19px;
    }

    #sidebar .menu-chevron {
        color: rgba(14, 23, 38, .5);
        font-size: 13px;
    }
</style>

<!--  BEGIN SIDEBAR  -->
<div class="sidebar-wrapper sidebar-theme">
    <nav id="sidebar">
        <div class="navbar-nav theme-brand flex-row text-center">
            <div class="nav-logo">
                <div class="nav-item theme-logo">
                    <a href="{{ route('academy.index') }}">
                        <img src="{{ asset('assetsAdmin/logo/Icon-Black.svg') }}" class="navbar-logo" alt="logo">
                    </a>
                </div>
                <div class="nav-item theme-text">
                    <a href="{{ route('academy.index') }}" class="nav-link">{{ trans('admin.bokit') }}</a>
                </div>
            </div>
            <div class="nav-item sidebar-toggle">
                <div class="btn-toggle sidebarCollapse">
                    <i class="fa-solid fa-angles-left"></i>
                </div>
            </div>
        </div>

        <div class="shadow-bottom"></div>

        <ul class="list-unstyled menu-categories" id="accordionExample">
            <li class="menu {{ Request::routeIs('academy.index') ? 'active' : '' }}">
                <a href="{{ route('academy.index') }}" aria-expanded="{{ Request::routeIs('academy.index') ? 'true' : 'false' }}" class="dropdown-toggle {{ Request::routeIs('academy.index') ? '' : 'collapsed' }}">
                    <div>
                        <i class="fa-solid fa-chart-pie menu-icon"></i>
                        <span>{{ trans('admin.dashboard') }}</span>
                    </div>
                </a>
            </li>

            @if($hasVenueModule)
                <li class="menu {{ $venueActive ? 'active' : '' }}">
                    <a href="#venue-management" data-bs-toggle="collapse" aria-expanded="{{ $venueActive ? 'true' : 'false' }}" class="dropdown-toggle {{ $venueActive ? '' : 'collapsed' }}">
                        <div><i class="fa-solid fa-futbol menu-icon"></i><span>{{ trans('admin.venues.menu') }}</span></div>
                        <div><i class="fa-solid fa-chevron-right menu-chevron"></i></div>
                    </a>
                    <ul class="collapse submenu list-unstyled {{ $venueActive ? 'show' : '' }}" id="venue-management" data-bs-parent="#accordionExample">
                        <li class="menu {{ Request::routeIs('academy.venues.*') ? 'active' : '' }}"><a href="{{ route('academy.venues.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-location-dot menu-icon"></i><span>{{ trans('admin.venues.locations') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.venue-spaces.*') ? 'active' : '' }}"><a href="{{ route('academy.venue-spaces.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-table-cells-large menu-icon"></i><span>{{ trans('admin.venues.spaces') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.venue-bookings.*') ? 'active' : '' }}"><a href="{{ route('academy.venue-bookings.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-calendar-check menu-icon"></i><span>{{ trans('admin.venues.bookings') }}</span></div></a></li>
                    </ul>
                </li>
            @endif

            @unless($isVenueOnly)
            <li class="menu {{ Request::routeIs('academy.address.*') ? 'active' : '' }}">
                <a href="#addresses-menu" data-bs-toggle="collapse" aria-expanded="{{ Request::routeIs('academy.address.*') ? 'true' : 'false' }}" class="dropdown-toggle {{ Request::routeIs('academy.address.*') ? '' : 'collapsed' }}">
                    <div>
                        <i class="fa-solid fa-location-dot menu-icon"></i>
                        <span>{{ trans('admin.address.address') }}</span>
                    </div>
                    <div><i class="fa-solid fa-chevron-right menu-chevron"></i></div>
                </a>
                <ul class="collapse submenu list-unstyled {{ Request::routeIs('academy.address.*') ? 'show' : '' }}" id="addresses-menu" data-bs-parent="#accordionExample">
                    <li class="menu {{ Request::routeIs('academy.address.*') ? 'active' : '' }}">
                        <a href="{{ route('academy.address.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-map-location-dot menu-icon"></i>
                                <span>{{ trans('admin.address.address') }}</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu {{ $servicesActive ? 'active' : '' }}">
                <a href="#services" data-bs-toggle="collapse" aria-expanded="{{ $servicesActive ? 'true' : 'false' }}" class="dropdown-toggle {{ $servicesActive ? '' : 'collapsed' }}">
                    <div>
                        <i class="fa-solid fa-dumbbell menu-icon"></i>
                        <span>{{ trans('admin.services_management') }}</span>
                    </div>
                    <div><i class="fa-solid fa-chevron-right menu-chevron"></i></div>
                </a>
                <ul class="collapse submenu list-unstyled {{ $servicesActive ? 'show' : '' }}" id="services" data-bs-parent="#accordionExample">
                    <li class="menu {{ Request::routeIs('academy.training.*') ? 'active' : '' }}">
                        <a href="{{ route('academy.training.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-person-running menu-icon"></i>
                                <span>{{ trans('admin.training.training') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.calendar.*') ? 'active' : '' }}">
                        <a href="{{ route('academy.calendar.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-calendar-days menu-icon"></i>
                                <span>{{ trans('admin.training.calendar') }}</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu {{ Request::routeIs('academy.createBooking') ? 'active' : '' }}">
                <a href="{{ route('academy.createBooking') }}" aria-expanded="false" class="dropdown-toggle">
                    <div>
                        <i class="fa-solid fa-calendar-plus menu-icon"></i>
                        <span>{{ trans('admin.add_booking') }}</span>
                    </div>
                </a>
            </li>

            <li class="menu {{ $studentsActive ? 'active' : '' }}">
                <a href="#students-management" data-bs-toggle="collapse" aria-expanded="{{ $studentsActive ? 'true' : 'false' }}" class="dropdown-toggle {{ $studentsActive ? '' : 'collapsed' }}">
                    <div>
                        <i class="fa-solid fa-graduation-cap menu-icon"></i>
                        <span>{{ trans('admin.student_management.menu') }}</span>
                    </div>
                    <div><i class="fa-solid fa-chevron-right menu-chevron"></i></div>
                </a>
                <ul class="collapse submenu list-unstyled {{ $studentsActive ? 'show' : '' }}" id="students-management" data-bs-parent="#accordionExample">
                    <li class="menu {{ Request::routeIs('academy.students.*') ? 'active' : '' }}">
                        <a href="{{ route('academy.students.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-user-graduate menu-icon"></i>
                                <span>{{ trans('admin.student_management.students') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.groups.*') ? 'active' : '' }}">
                        <a href="{{ route('academy.groups.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-people-group menu-icon"></i>
                                <span>{{ trans('admin.student_management.groups') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.competitions.*') ? 'active' : '' }}">
                        <a href="{{ route('academy.competitions.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-trophy menu-icon"></i>
                                <span>{{ trans('admin.student_management.competitions') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.attendance.*') ? 'active' : '' }}">
                        <a href="{{ route('academy.attendance.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-clipboard-check menu-icon"></i>
                                <span>{{ trans('admin.student_management.attendance') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.subscriptions.*') ? 'active' : '' }}">
                        <a href="{{ route('academy.subscriptions.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-receipt menu-icon"></i>
                                <span>{{ trans('admin.student_management.subscriptions') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.student-reports.*') ? 'active' : '' }}">
                        <a href="{{ route('academy.student-reports.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-chart-line menu-icon"></i>
                                <span>{{ trans('admin.student_management.reports') }}</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu {{ Request::routeIs('academy.coach') || Request::routeIs('academy.coach.*') ? 'active' : '' }}">
                <a href="{{ route('academy.coach') }}" aria-expanded="{{ Request::routeIs('academy.coach.*') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div>
                        <i class="fa-solid fa-user-tie menu-icon"></i>
                        <span>{{ trans('admin.coaches.coaches') }}</span>
                    </div>
                </a>
            </li>

            <li class="menu {{ Request::routeIs('academy.gallery.*') ? 'active' : '' }}">
                <a href="{{ route('academy.gallery.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div>
                        <i class="fa-solid fa-images menu-icon"></i>
                        <span>{{ trans('admin.gallery.gallery') }}</span>
                    </div>
                </a>
            </li>

            <li class="menu {{ Request::routeIs('academy.users.*') ? 'active' : '' }}">
                <a href="{{ route('academy.users.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div>
                        <i class="fa-solid fa-users menu-icon"></i>
                        <span>{{ trans('admin.profile.user') }}</span>
                    </div>
                </a>
            </li>

            @endunless
            <li class="menu {{ Request::routeIs('academy.notification.*') ? 'active' : '' }}">
                <a href="{{ route('academy.notification.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div>
                        <i class="fa-solid fa-bell menu-icon"></i>
                        <span>{{ trans('admin.notifications.notifications') }}</span>
                    </div>
                </a>
            </li>

            <li class="menu {{ Request::routeIs('academy.terms.*') ? 'active' : '' }}">
                <a href="{{ route('academy.terms.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div>
                        <i class="fa-solid fa-file-shield menu-icon"></i>
                        <span>{{ trans('admin.terms.terms') }}</span>
                    </div>
                </a>
            </li>

            @unless($isVenueOnly)
            <li class="menu {{ $reportsActive ? 'active' : '' }}">
                <a href="#report" data-bs-toggle="collapse" aria-expanded="{{ $reportsActive ? 'true' : 'false' }}" class="dropdown-toggle {{ $reportsActive ? '' : 'collapsed' }}">
                    <div>
                        <i class="fa-solid fa-chart-column menu-icon"></i>
                        <span>{{ trans('admin.report') }}</span>
                    </div>
                    <div><i class="fa-solid fa-chevron-right menu-chevron"></i></div>
                </a>
                <ul class="collapse submenu list-unstyled {{ $reportsActive ? 'show' : '' }}" id="report" data-bs-parent="#accordionExample">
                    <li class="menu {{ Request::routeIs('academy.report.settlement.index') ? 'active' : '' }}">
                        <a href="{{ route('academy.report.settlement.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-scale-balanced menu-icon"></i>
                                <span>{{ trans('admin.settlement.Settlements') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.report.transaction.index') ? 'active' : '' }}">
                        <a href="{{ route('academy.report.transaction.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-money-bill-transfer menu-icon"></i>
                                <span>{{ trans('admin.transaction') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.report.joins') ? 'active' : '' }}">
                        <a href="{{ route('academy.report.joins') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-ticket menu-icon"></i>
                                <span>{{ trans('admin.bookings.bookings') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.report.offline-joins') ? 'active' : '' }}">
                        <a href="{{ route('academy.report.offline-joins') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-cash-register menu-icon"></i>
                                <span>{{ trans('admin.bookings.offline_bookings') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.report.coach') ? 'active' : '' }}">
                        <a href="{{ route('academy.report.coach') }}" aria-expanded="false" class="dropdown-toggle">
                            <div>
                                <i class="fa-solid fa-medal menu-icon"></i>
                                <span>{{ trans('admin.Coaches') }}</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
            @endunless
        </ul>
    </nav>
</div>
<!--  END SIDEBAR  -->
