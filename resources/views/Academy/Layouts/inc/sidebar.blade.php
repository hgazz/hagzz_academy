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
    .sidebar-wrapper { height: 100vh !important; }
    #sidebar { height: 100%; overflow: hidden !important; display: flex; flex-direction: column; position: relative; }
    #sidebar .theme-brand { flex: 0 0 auto; z-index: 5; background: inherit; }
    #sidebar > .shadow-bottom { flex: 0 0 auto; }
    #sidebar > .menu-categories {
        flex: 1 1 auto; min-height: 0; overflow-y: auto !important; overflow-x: hidden !important;
        overscroll-behavior: contain; scroll-behavior: smooth; scrollbar-gutter: stable;
        scrollbar-width: thin; scrollbar-color: rgba(27, 85, 226, .45) transparent;
        padding-bottom: 18px !important;
    }
    #sidebar > .menu-categories::-webkit-scrollbar { width: 6px; }
    #sidebar > .menu-categories::-webkit-scrollbar-track { background: transparent; }
    #sidebar > .menu-categories::-webkit-scrollbar-thumb { background: rgba(27, 85, 226, .42); border-radius: 10px; }
    #sidebar .menu-icon { width: 22px; min-width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; color: rgba(14, 23, 38, .72); font-size: 18px; margin-inline-end: 12px; }
    #sidebar .menu.active > a .menu-icon, #sidebar .submenu .menu.active > a .menu-icon { color: #1b55e2; }
    #sidebar .submenu .menu-icon { font-size: 15px; width: 19px; min-width: 19px; }
    #sidebar .menu-chevron { color: rgba(14, 23, 38, .5); font-size: 13px; transition: transform .2s ease; }
    #sidebar a[aria-expanded="true"] .menu-chevron { transform: rotate(90deg); }
    #sidebar .navigation-section { padding: 18px 22px 7px; list-style: none; }
    #sidebar .navigation-section span { color: #888ea8; font-size: 10px; font-weight: 800; letter-spacing: .075em; text-transform: uppercase; white-space: nowrap; }
    #sidebar .menu > a { border-radius: 10px; margin-inline: 10px; min-width: 0; }
    #sidebar .menu > a > div:first-child { flex: 1 1 auto; min-width: 0; overflow: hidden; }
    #sidebar .menu > a > div:first-child > span {
        display: block; flex: 1 1 auto; min-width: 0; white-space: nowrap;
        transition: transform .55s cubic-bezier(.22, .75, .25, 1); will-change: transform;
    }
    #sidebar .menu > a > div:first-child > span.sidebar-label-overflow { cursor: help; }
    #sidebar .menu.active > a { box-shadow: inset 3px 0 0 #1b55e2; }
    [dir="rtl"] #sidebar .menu.active > a { box-shadow: inset -3px 0 0 #1b55e2; }
    #sidebar .sidebar-scroll-controls {
        position: static; flex: 0 0 auto; align-self: flex-end; z-index: 20; display: flex; gap: 7px;
        margin: 8px 14px 12px;
        padding: 6px; border: 1px solid rgba(27, 85, 226, .16); border-radius: 14px;
        background: rgba(255, 255, 255, .94); box-shadow: 0 8px 24px rgba(31, 45, 61, .16); backdrop-filter: blur(8px);
    }
    #sidebar .sidebar-scroll-controls[hidden] { display: none !important; }
    #sidebar .sidebar-scroll-button {
        width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;
        border: 0; border-radius: 9px; background: #eef2ff; color: #1b55e2; cursor: pointer;
        transition: transform .18s ease, opacity .18s ease, background .18s ease;
    }
    #sidebar .sidebar-scroll-button:hover { background: #1b55e2; color: #fff; transform: translateY(-1px); }
    #sidebar .sidebar-scroll-button:disabled { opacity: .28; cursor: default; transform: none; }
    body.dark #sidebar .sidebar-scroll-controls, .dark #sidebar .sidebar-scroll-controls { background: rgba(20, 30, 50, .94); border-color: rgba(136, 142, 168, .25); }
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

                <li class="navigation-section"><span>{{ $isArabic ? 'إدارة الأكاديمية' : 'Academy management' }}</span></li>
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
                <li class="menu {{ $whatsappActive ? 'active' : '' }}"><a href="{{ route('academy.whatsapp.index') }}" class="dropdown-toggle"><div><i class="fa-brands fa-whatsapp menu-icon"></i><span>{{ $isArabic ? 'رسائل ومحادثات WhatsApp' : 'WhatsApp messages' }}</span></div></a></li>
                <li class="menu {{ Request::routeIs('academy.coach') || Request::routeIs('academy.coach.*') ? 'active' : '' }}"><a href="{{ route('academy.coach') }}" class="dropdown-toggle"><div><i class="fa-solid fa-user-tie menu-icon"></i><span>{{ trans('admin.coaches.coaches') }}</span></div></a></li>
                <li class="menu {{ Request::routeIs('academy.users.*') ? 'active' : '' }}"><a href="{{ route('academy.users.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-users menu-icon"></i><span>{{ trans('admin.profile.user') }}</span></div></a></li>
                <li class="menu {{ Request::routeIs('academy.gallery.*') ? 'active' : '' }}"><a href="{{ route('academy.gallery.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-images menu-icon"></i><span>{{ trans('admin.gallery.gallery') }}</span></div></a></li>
            @endunless

            <li class="navigation-section"><span>{{ $isArabic ? 'الفواتير والتقارير' : 'Billing & reports' }}</span></li>
            <li class="menu {{ Request::routeIs('academy.billing-invoices.*') ? 'active' : '' }}"><a href="{{ route('academy.billing-invoices.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-file-invoice-dollar menu-icon"></i><span>{{ $isArabic ? 'فواتير اشتراك Hagzz' : 'Hagzz invoices' }}</span></div></a></li>
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

            <li class="navigation-section"><span>{{ $isArabic ? 'الحساب والمنصة' : 'Account & platform' }}</span></li>
            @unless($isVenueOnly)
                <li class="menu {{ Request::routeIs('academy.address.*') ? 'active' : '' }}"><a href="{{ route('academy.address.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-map-location-dot menu-icon"></i><span>{{ trans('admin.address.address') }}</span></div></a></li>
            @endunless
            <li class="menu {{ Request::routeIs('academy.notification.*') ? 'active' : '' }}"><a href="{{ route('academy.notification.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-bell menu-icon"></i><span>{{ trans('admin.notifications.notifications') }}</span></div></a></li>
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
        const prepareMovingLabels = function () {
            menu.querySelectorAll('.menu > a > div:first-child > span').forEach(function (label) {
                const link = label.closest('a');
                if (!link || link.dataset.labelMotionReady === 'true') return;
                link.dataset.labelMotionReady = 'true';
                const moveLabel = function () {
                    label.style.transform = 'none';
                    const overflow = Math.ceil(label.scrollWidth - label.clientWidth);
                    label.classList.toggle('sidebar-label-overflow', overflow > 2);
                    if (overflow > 2) {
                        const direction = document.documentElement.dir === 'rtl' ? 1 : -1;
                        label.style.transform = 'translateX(' + (direction * Math.min(overflow + 10, 180)) + 'px)';
                        link.title = label.textContent.trim();
                    }
                };
                const resetLabel = function () { label.style.transform = 'none'; };
                link.addEventListener('mouseenter', moveLabel);
                link.addEventListener('mouseleave', resetLabel);
                link.addEventListener('focusin', moveLabel);
                link.addEventListener('focusout', resetLabel);
            });
        };
        menu.querySelectorAll('.collapse').forEach(function (collapse) {
            collapse.addEventListener('shown.bs.collapse', function () { updateControls(); revealActiveItem(); prepareMovingLabels(); });
            collapse.addEventListener('hidden.bs.collapse', updateControls);
        });
        window.addEventListener('resize', function () { updateControls(); prepareMovingLabels(); }, { passive: true });
        prepareMovingLabels();
        requestAnimationFrame(function () { revealActiveItem(); updateControls(); });
    });
</script>
