@php
    $servicesActive = Request::routeIs('academy.training.*', 'academy.calendar.*', 'academy.class.*');
    $studentsActive = Request::routeIs('academy.students.*', 'academy.groups.*', 'academy.competitions.*', 'academy.attendance.*', 'academy.subscriptions.*', 'academy.student-reports.*');
    $reportsActive = Request::routeIs('academy.report.*');
    $venueActive = Request::routeIs('academy.venues.*', 'academy.venue-spaces.*', 'academy.venue-bookings.*');
    $whatsappActive = Request::routeIs('academy.whatsapp.*');
    $hasVenueModule = auth('academy')->user()?->hasVenueModule();
    $isVenueOnly = auth('academy')->user()?->business_type === 'venue';
    $isArabic = app()->getLocale() === 'ar';
@endphp

<style>
    .sidebar-wrapper { height: 100vh !important; border-inline-end: 1px solid rgba(15, 23, 42, .07); }
    #sidebar { height: 100%; overflow: hidden !important; display: flex; flex-direction: column; position: relative; }
    #sidebar .theme-brand { flex: 0 0 auto; min-height: 68px; z-index: 5; background: inherit; border-bottom: 1px solid rgba(15, 23, 42, .07); }
    #sidebar > .shadow-bottom { display: none; }
    #sidebar > .menu-categories {
        flex: 1 1 auto; min-height: 0; overflow-y: auto !important; overflow-x: hidden !important;
        overscroll-behavior: contain; scroll-behavior: smooth; scrollbar-gutter: stable;
        scrollbar-width: thin; scrollbar-color: rgba(27, 85, 226, .45) transparent;
        padding: 10px 10px 62px !important;
    }
    #sidebar > .menu-categories::-webkit-scrollbar { width: 5px; }
    #sidebar > .menu-categories::-webkit-scrollbar-track { background: transparent; }
    #sidebar > .menu-categories::-webkit-scrollbar-thumb { background: rgba(27, 85, 226, .32); border-radius: 10px; }
    #sidebar .navigation-section { padding: 17px 10px 6px; list-style: none; }
    #sidebar .navigation-section:first-of-type { padding-top: 13px; }
    #sidebar .navigation-section span { color: #7c849b; font-size: 10px; font-weight: 800; letter-spacing: .055em; text-transform: uppercase; white-space: nowrap; }
    #sidebar .menu { margin: 0 0 3px; }
    #sidebar .menu > a {
        min-height: 43px; margin: 0; padding: 9px 11px; border: 1px solid transparent; border-radius: 10px;
        color: #3b4254; transition: background .16s ease, color .16s ease, border-color .16s ease;
    }
    #sidebar .menu > a:hover { background: rgba(27, 85, 226, .055); color: #1b55e2; }
    #sidebar .menu > a > div:first-child { flex: 1 1 auto; min-width: 0; overflow: hidden; display: flex; align-items: center; }
    #sidebar .menu > a > div:first-child > span { display: block; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 13px; font-weight: 600; }
    #sidebar .menu-icon {
        width: 30px; min-width: 30px; height: 30px; margin-inline-end: 9px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center; color: #626b7f; background: rgba(148, 163, 184, .11); font-size: 15px;
    }
    #sidebar .menu.active > a { color: #1749c9; background: rgba(27, 85, 226, .10); border-color: rgba(27, 85, 226, .13); box-shadow: none; }
    #sidebar .menu.active > a .menu-icon { color: #fff; background: #1b55e2; box-shadow: 0 4px 10px rgba(27, 85, 226, .24); }
    #sidebar .submenu { margin: 3px 16px 7px 0; padding-inline-start: 8px; border-inline-start: 1px solid rgba(27, 85, 226, .16); }
    [dir="ltr"] #sidebar .submenu { margin: 3px 0 7px 16px; }
    #sidebar .submenu .menu { margin-bottom: 1px; }
    #sidebar .submenu .menu > a { min-height: 36px; padding-block: 5px; border-radius: 8px; }
    #sidebar .submenu .menu-icon { width: 24px; min-width: 24px; height: 24px; font-size: 12px; background: transparent; }
    #sidebar .submenu .menu.active > a .menu-icon { color: #1b55e2; background: rgba(27, 85, 226, .10); box-shadow: none; }
    #sidebar .submenu .menu > a > div:first-child > span { font-size: 12px; font-weight: 600; }
    #sidebar .menu-chevron { color: #8b93a6; font-size: 11px; transition: transform .2s ease; }
    #sidebar a[aria-expanded="true"] .menu-chevron { transform: rotate(90deg); }
    #sidebar .sidebar-scroll-controls {
        position: absolute; inset-inline: 0; bottom: 0; z-index: 20; height: 48px; display: flex; align-items: center; justify-content: center; gap: 6px;
        padding: 8px 12px; border-top: 1px solid rgba(15, 23, 42, .07); background: rgba(255, 255, 255, .96); backdrop-filter: blur(12px);
    }
    #sidebar .sidebar-scroll-controls[hidden] { display: none !important; }
    #sidebar .sidebar-scroll-button {
        flex: 1 1 50%; max-width: 92px; height: 30px; display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid rgba(27, 85, 226, .12); border-radius: 8px; background: rgba(27, 85, 226, .055); color: #1b55e2; cursor: pointer;
        transition: opacity .16s ease, background .16s ease, color .16s ease;
    }
    #sidebar .sidebar-scroll-button:hover { background: #1b55e2; color: #fff; }
    #sidebar .sidebar-scroll-button:disabled { opacity: .24; cursor: default; }
    body.dark .sidebar-wrapper, .dark .sidebar-wrapper, body.dark #sidebar .theme-brand, .dark #sidebar .theme-brand { border-color: rgba(148, 163, 184, .12); }
    body.dark #sidebar .menu > a, .dark #sidebar .menu > a { color: #cbd5e1; }
    body.dark #sidebar .menu-icon, .dark #sidebar .menu-icon { color: #aab4c5; background: rgba(148, 163, 184, .09); }
    body.dark #sidebar .menu.active > a, .dark #sidebar .menu.active > a { color: #8eb0ff; background: rgba(56, 103, 214, .18); border-color: rgba(94, 134, 228, .20); }
    body.dark #sidebar .menu.active > a .menu-icon, .dark #sidebar .menu.active > a .menu-icon { color: #fff; background: #3867d6; }
    body.dark #sidebar .sidebar-scroll-controls, .dark #sidebar .sidebar-scroll-controls { background: rgba(20, 30, 50, .96); border-color: rgba(136, 142, 168, .16); }
    .sidebar-closed #sidebar .sidebar-scroll-controls { display: none !important; }
</style>

<div class="sidebar-wrapper sidebar-theme">
    <nav id="sidebar">
        <div class="navbar-nav theme-brand flex-row text-center">
            <div class="nav-logo">
                <div class="nav-item theme-logo"><a href="{{ route('academy.index') }}"><img src="{{ asset('assetsAdmin/logo/Icon-Black.svg') }}" class="navbar-logo" alt="logo"></a></div>
                <div class="nav-item theme-text"><a href="{{ route('academy.index') }}" class="nav-link">{{ trans('admin.bokit') }}</a></div>
            </div>
            <div class="nav-item sidebar-toggle"><div class="btn-toggle sidebarCollapse"><i class="fa-solid fa-angles-left"></i></div></div>
        </div>
        <div class="shadow-bottom"></div>

        <ul class="list-unstyled menu-categories" id="accordionExample">
            <li class="menu {{ Request::routeIs('academy.index') ? 'active' : '' }}"><a href="{{ route('academy.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-chart-pie menu-icon"></i><span>{{ trans('admin.dashboard') }}</span></div></a></li>

            <li class="navigation-section"><span>{{ $isArabic ? 'التشغيل اليومي' : 'Daily operations' }}</span></li>
            @if($hasVenueModule)
                <li class="menu {{ $venueActive ? 'active' : '' }}">
                    <a href="#venue-management" data-bs-toggle="collapse" aria-expanded="{{ $venueActive ? 'true' : 'false' }}" class="dropdown-toggle {{ $venueActive ? '' : 'collapsed' }}"><div><i class="fa-solid fa-futbol menu-icon"></i><span>{{ trans('admin.venues.menu') }}</span></div><div><i class="fa-solid fa-chevron-right menu-chevron"></i></div></a>
                    <ul class="collapse submenu list-unstyled {{ $venueActive ? 'show' : '' }}" id="venue-management" data-bs-parent="#accordionExample">
                        <li class="menu {{ Request::routeIs('academy.venue-bookings.calendar') ? 'active' : '' }}"><a href="{{ route('academy.venue-bookings.calendar') }}" class="dropdown-toggle"><div><i class="fa-solid fa-calendar-days menu-icon"></i><span>{{ $isArabic ? 'تقويم الحجوزات' : 'Booking calendar' }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.venue-bookings.*') && !Request::routeIs('academy.venue-bookings.calendar') ? 'active' : '' }}"><a href="{{ route('academy.venue-bookings.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-calendar-check menu-icon"></i><span>{{ trans('admin.venues.bookings') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.venues.*') ? 'active' : '' }}"><a href="{{ route('academy.venues.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-location-dot menu-icon"></i><span>{{ trans('admin.venues.locations') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.venue-spaces.*') ? 'active' : '' }}"><a href="{{ route('academy.venue-spaces.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-table-cells-large menu-icon"></i><span>{{ trans('admin.venues.spaces') }}</span></div></a></li>
                    </ul>
                </li>
            @endif

            @unless($isVenueOnly)
                <li class="menu {{ Request::routeIs('academy.createBooking') ? 'active' : '' }}"><a href="{{ route('academy.createBooking') }}" class="dropdown-toggle"><div><i class="fa-solid fa-calendar-plus menu-icon"></i><span>{{ trans('admin.add_booking') }}</span></div></a></li>
                <li class="menu {{ $servicesActive ? 'active' : '' }}">
                    <a href="#services" data-bs-toggle="collapse" aria-expanded="{{ $servicesActive ? 'true' : 'false' }}" class="dropdown-toggle {{ $servicesActive ? '' : 'collapsed' }}"><div><i class="fa-solid fa-dumbbell menu-icon"></i><span>{{ trans('admin.services_management') }}</span></div><div><i class="fa-solid fa-chevron-right menu-chevron"></i></div></a>
                    <ul class="collapse submenu list-unstyled {{ $servicesActive ? 'show' : '' }}" id="services" data-bs-parent="#accordionExample">
                        <li class="menu {{ Request::routeIs('academy.calendar.*') ? 'active' : '' }}"><a href="{{ route('academy.calendar.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-calendar-days menu-icon"></i><span>{{ trans('admin.training.calendar') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.training.*') ? 'active' : '' }}"><a href="{{ route('academy.training.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-person-running menu-icon"></i><span>{{ trans('admin.training.training') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.class.*') ? 'active' : '' }}"><a href="{{ route('academy.class.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-people-roof menu-icon"></i><span>{{ $isArabic ? 'الحصص والمواعيد' : 'Classes & sessions' }}</span></div></a></li>
                    </ul>
                </li>

                <li class="navigation-section"><span>{{ $isArabic ? 'الأشخاص والفريق' : 'People & team' }}</span></li>
                <li class="menu {{ $studentsActive ? 'active' : '' }}">
                    <a href="#students-management" data-bs-toggle="collapse" aria-expanded="{{ $studentsActive ? 'true' : 'false' }}" class="dropdown-toggle {{ $studentsActive ? '' : 'collapsed' }}"><div><i class="fa-solid fa-graduation-cap menu-icon"></i><span>{{ trans('admin.student_management.menu') }}</span></div><div><i class="fa-solid fa-chevron-right menu-chevron"></i></div></a>
                    <ul class="collapse submenu list-unstyled {{ $studentsActive ? 'show' : '' }}" id="students-management" data-bs-parent="#accordionExample">
                        <li class="menu {{ Request::routeIs('academy.students.*') ? 'active' : '' }}"><a href="{{ route('academy.students.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-user-graduate menu-icon"></i><span>{{ trans('admin.student_management.students') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.groups.*') ? 'active' : '' }}"><a href="{{ route('academy.groups.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-people-group menu-icon"></i><span>{{ trans('admin.student_management.groups') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.subscriptions.*') ? 'active' : '' }}"><a href="{{ route('academy.subscriptions.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-receipt menu-icon"></i><span>{{ trans('admin.student_management.subscriptions') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.attendance.*') && !Request::routeIs('academy.attendance.scanner') ? 'active' : '' }}"><a href="{{ route('academy.attendance.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-clipboard-check menu-icon"></i><span>{{ trans('admin.student_management.attendance') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.attendance.scanner') ? 'active' : '' }}"><a href="{{ route('academy.attendance.scanner') }}" class="dropdown-toggle"><div><i class="fa-solid fa-qrcode menu-icon"></i><span>{{ $isArabic ? 'ماسح الحضور' : 'Attendance scanner' }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.competitions.*') ? 'active' : '' }}"><a href="{{ route('academy.competitions.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-trophy menu-icon"></i><span>{{ trans('admin.student_management.competitions') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.student-reports.*') ? 'active' : '' }}"><a href="{{ route('academy.student-reports.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-chart-line menu-icon"></i><span>{{ trans('admin.student_management.reports') }}</span></div></a></li>
                    </ul>
                </li>
                <li class="menu {{ Request::routeIs('academy.coach') || Request::routeIs('academy.coach.*') ? 'active' : '' }}"><a href="{{ route('academy.coach') }}" class="dropdown-toggle"><div><i class="fa-solid fa-user-tie menu-icon"></i><span>{{ trans('admin.coaches.coaches') }}</span></div></a></li>
                <li class="menu {{ Request::routeIs('academy.users.*') ? 'active' : '' }}"><a href="{{ route('academy.users.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-users menu-icon"></i><span>{{ trans('admin.profile.user') }}</span></div></a></li>
            @endunless

            <li class="navigation-section"><span>{{ $isArabic ? 'التواصل والمحتوى' : 'Communication & content' }}</span></li>
            @unless($isVenueOnly)
                <li class="menu {{ $whatsappActive ? 'active' : '' }}"><a href="{{ route('academy.whatsapp.index') }}" class="dropdown-toggle"><div><i class="fa-brands fa-whatsapp menu-icon"></i><span>{{ $isArabic ? 'مركز واتساب' : 'WhatsApp centre' }}</span></div></a></li>
                <li class="menu {{ Request::routeIs('academy.gallery.*') ? 'active' : '' }}"><a href="{{ route('academy.gallery.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-images menu-icon"></i><span>{{ trans('admin.gallery.gallery') }}</span></div></a></li>
            @endunless
            <li class="menu {{ Request::routeIs('academy.notification.*') ? 'active' : '' }}"><a href="{{ route('academy.notification.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-bell menu-icon"></i><span>{{ trans('admin.notifications.notifications') }}</span></div></a></li>

            <li class="navigation-section"><span>{{ $isArabic ? 'المالية والتقارير' : 'Finance & reports' }}</span></li>
            @unless($isVenueOnly)
                <li class="menu {{ $reportsActive ? 'active' : '' }}">
                    <a href="#report" data-bs-toggle="collapse" aria-expanded="{{ $reportsActive ? 'true' : 'false' }}" class="dropdown-toggle {{ $reportsActive ? '' : 'collapsed' }}"><div><i class="fa-solid fa-chart-column menu-icon"></i><span>{{ trans('admin.report') }}</span></div><div><i class="fa-solid fa-chevron-right menu-chevron"></i></div></a>
                    <ul class="collapse submenu list-unstyled {{ $reportsActive ? 'show' : '' }}" id="report" data-bs-parent="#accordionExample">
                        <li class="menu {{ Request::routeIs('academy.report.overview*') ? 'active' : '' }}"><a href="{{ route('academy.report.overview') }}" class="dropdown-toggle"><div><i class="fa-solid fa-chart-line menu-icon"></i><span>{{ $isArabic ? 'النظرة المالية الشاملة' : 'Financial overview' }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.report.settlement.index') ? 'active' : '' }}"><a href="{{ route('academy.report.settlement.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-scale-balanced menu-icon"></i><span>{{ trans('admin.settlement.Settlements') }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.report.transaction.index') ? 'active' : '' }}"><a href="{{ route('academy.report.transaction.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-money-bill-transfer menu-icon"></i><span>{{ $isArabic ? 'فواتير ومدفوعات الحجوزات' : 'Booking invoices & payments' }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.report.joins') ? 'active' : '' }}"><a href="{{ route('academy.report.joins') }}" class="dropdown-toggle"><div><i class="fa-solid fa-ticket menu-icon"></i><span>{{ $isArabic ? 'تفاصيل حجوزات التدريبات' : 'Training booking details' }}</span></div></a></li>
                        <li class="menu {{ Request::routeIs('academy.report.offline-joins') ? 'active' : '' }}"><a href="{{ route('academy.report.offline-joins') }}" class="dropdown-toggle"><div><i class="fa-solid fa-cash-register menu-icon"></i><span>{{ $isArabic ? 'الحجوزات المسجلة يدويًا' : 'Manually entered bookings' }}</span></div></a></li>
                    </ul>
                </li>
            @endunless
            <li class="menu {{ Request::routeIs('academy.billing-invoices.*') ? 'active' : '' }}"><a href="{{ route('academy.billing-invoices.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-file-invoice-dollar menu-icon"></i><span>{{ $isArabic ? 'فواتير اشتراك Hagzz' : 'Hagzz invoices' }}</span></div></a></li>

            <li class="navigation-section"><span>{{ $isArabic ? 'إعدادات المنشأة' : 'Business settings' }}</span></li>
            @unless($isVenueOnly)
                <li class="menu {{ Request::routeIs('academy.address.*') ? 'active' : '' }}"><a href="{{ route('academy.address.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-map-location-dot menu-icon"></i><span>{{ trans('admin.address.address') }}</span></div></a></li>
            @endunless
            <li class="menu {{ Request::routeIs('academy.terms.*') ? 'active' : '' }}"><a href="{{ route('academy.terms.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-file-shield menu-icon"></i><span>{{ trans('admin.terms.terms') }}</span></div></a></li>
        </ul>

        <div class="sidebar-scroll-controls" aria-label="{{ $isArabic ? 'التمرير داخل القائمة' : 'Sidebar scrolling' }}">
            <button type="button" class="sidebar-scroll-button" data-scroll-direction="-1" title="{{ $isArabic ? 'تمرير لأعلى' : 'Scroll up' }}" aria-label="{{ $isArabic ? 'تمرير لأعلى' : 'Scroll up' }}"><i class="fa-solid fa-chevron-up"></i></button>
            <button type="button" class="sidebar-scroll-button" data-scroll-direction="1" title="{{ $isArabic ? 'تمرير لأسفل' : 'Scroll down' }}" aria-label="{{ $isArabic ? 'تمرير لأسفل' : 'Scroll down' }}"><i class="fa-solid fa-chevron-down"></i></button>
        </div>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menu = document.querySelector('#sidebar > .menu-categories');
        const controls = document.querySelector('#sidebar > .sidebar-scroll-controls');
        if (!menu || !controls || menu.dataset.scrollReady === 'true') return;
        menu.dataset.scrollReady = 'true';
        const storageKey = 'hagzz-partner-sidebar-scroll';
        const buttons = controls.querySelectorAll('[data-scroll-direction]');
        const savedPosition = Number(sessionStorage.getItem(storageKey));
        if (Number.isFinite(savedPosition) && savedPosition > 0) menu.scrollTop = savedPosition;
        const activeItem = menu.querySelector('.menu.active');
        const revealActiveItem = function () {
            if (!activeItem) return;
            const menuRect = menu.getBoundingClientRect();
            const activeRect = activeItem.getBoundingClientRect();
            if (activeRect.top < menuRect.top + 10) menu.scrollTop -= (menuRect.top + 10 - activeRect.top);
            else if (activeRect.bottom > menuRect.bottom - 10) menu.scrollTop += (activeRect.bottom - menuRect.bottom + 10);
        };
        const updateControls = function () {
            const maxScroll = Math.max(0, menu.scrollHeight - menu.clientHeight);
            buttons[0].disabled = menu.scrollTop <= 2;
            buttons[1].disabled = menu.scrollTop >= maxScroll - 2;
            controls.hidden = maxScroll <= 2;
        };
        buttons.forEach(function (button) { button.addEventListener('click', function () { menu.scrollBy({ top: Number(button.dataset.scrollDirection) * Math.max(220, menu.clientHeight * .55), behavior: 'smooth' }); }); });
        menu.addEventListener('scroll', function () { sessionStorage.setItem(storageKey, String(Math.round(menu.scrollTop))); updateControls(); }, { passive: true });
        menu.querySelectorAll('.collapse').forEach(function (collapse) {
            collapse.addEventListener('shown.bs.collapse', function () { updateControls(); revealActiveItem(); });
            collapse.addEventListener('hidden.bs.collapse', updateControls);
        });
        window.addEventListener('resize', updateControls, { passive: true });
        requestAnimationFrame(function () { revealActiveItem(); updateControls(); });
    });
</script>
