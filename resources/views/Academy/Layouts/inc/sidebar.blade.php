@php
    $workspace ??= \App\Support\PartnerWorkspace::forUser(auth('academy')->user(), Request::route()?->getName());
    $isArabic = session('locale', app()->getLocale()) === 'ar';
    $navLocale = $isArabic ? 'ar' : 'en';
    $nav = fn(string $key) => trans('partner_navigation.' . $key, [], $navLocale);
    $workflow = fn(string $key) => trans('partner_workflow.' . $key, [], $navLocale);
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
        padding: 10px 10px 64px !important;
    }
    #sidebar > .menu-categories::-webkit-scrollbar { width: 5px; }
    #sidebar > .menu-categories::-webkit-scrollbar-track { background: transparent; }
    #sidebar > .menu-categories::-webkit-scrollbar-thumb { background: rgba(27, 85, 226, .32); border-radius: 10px; }
    #sidebar .workspace-type span { color: #1b55e2; }
    #sidebar .workflow-number { flex: 0 0 28px; width: 28px; height: 28px; margin-inline-end: 9px; display: inline-flex !important; align-items: center; justify-content: center; border-radius: 8px; background: rgba(27, 85, 226, .08); color: #1b55e2; font-size: 12px; }
    #sidebar .workflow-lane { padding: 10px 8px 4px; color: #7c849b; font-size: 11px; font-weight: 700; }
    #sidebar .navigation-section { padding: 17px 10px 6px; list-style: none; }
    #sidebar .navigation-section:first-of-type { padding-top: 13px; }
    #sidebar .navigation-section span { color: #7c849b; font-size: 11px; font-weight: 700; letter-spacing: normal; text-transform: uppercase; white-space: nowrap; }
    #sidebar .menu { margin: 0 0 3px; }
    #sidebar .menu > a {
        min-height: 43px; margin: 0; padding: 9px 11px; border: 1px solid transparent; border-radius: 10px;
        color: #3b4254; transition: background .16s ease, color .16s ease, border-color .16s ease;
    }
    #sidebar .menu > a:hover { background: rgba(27, 85, 226, .055); color: #1b55e2; }
    #sidebar .menu > a > div:first-child { flex: 1 1 auto; min-width: 0; overflow: hidden; display: flex; align-items: center; }
    #sidebar .menu > a > div:first-child > span { display: block; min-width: 0; white-space: normal; overflow-wrap: break-word; line-height: 1.65; font-size: 13px; font-weight: 600; }
    #sidebar .menu-icon {
        width: 30px; min-width: 30px; height: 30px; margin-inline-end: 9px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center; align-self: flex-start; color: #626b7f; background: rgba(148, 163, 184, .11); font-size: 15px;
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
    #sidebar .menu > a:focus-visible { outline: 2px solid #1b55e2; outline-offset: 2px; }
    #sidebar .menu-chevron { color: #8b93a6; font-size: 11px; transition: transform .2s ease; }
    #sidebar a[aria-expanded="true"] .menu-chevron { transform: rotate(90deg); }
    #sidebar .sidebar-scroll-controls {
        position: absolute; inset-inline: 0; bottom: 0; z-index: 20; height: 48px; display: flex; align-items: center; justify-content: center; gap: 6px;
        padding: 8px 12px; border-top: 1px solid rgba(15, 23, 42, .07); background: rgba(255, 255, 255, .96); backdrop-filter: blur(12px);
    }
    /* When scroll controls are hidden, keep bottom safe-area via a pseudo-element on the list instead */
    #sidebar .sidebar-scroll-controls[hidden] { display: none !important; }
    #sidebar > .menu-categories::after {
        content: ''; display: block; height: 16px; flex-shrink: 0;
    }
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
    body.dark #sidebar .workspace-type span, .dark #sidebar .workspace-type span { color: #a7c5ff; }
    body.dark #sidebar .workflow-number, .dark #sidebar .workflow-number { background: rgba(56, 103, 214, .25); color: #a7c5ff; }
    body.dark #sidebar .workflow-lane, .dark #sidebar .workflow-lane { color: #b7c4d7; }
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
            <li class="navigation-section workspace-type"><span>{{ $workflow('types.' . $workspace['type']) }}</span></li>
            @if($workspace['showDashboard'])
                <li class="menu {{ Request::routeIs('academy.index') ? 'active' : '' }}"><a href="{{ route('academy.index') }}" class="dropdown-toggle"><div><i class="fa-solid fa-chart-pie menu-icon" aria-hidden="true"></i><span>{{ $nav('dashboard') }}</span></div></a></li>
            @endif
            <li class="menu {{ Request::routeIs('academy.operating-guide') ? 'active' : '' }}"><a href="{{ route('academy.operating-guide') }}" class="dropdown-toggle"><div><i class="fa-solid fa-book-open menu-icon" aria-hidden="true"></i><span>{{ $workflow('title') }}</span></div></a></li>
            <li class="navigation-section"><span>{{ $workflow('menu_heading') }}</span></li>
            @foreach($workspace['sections'] as $section)
                <li class="menu workflow-section {{ $section['active'] ? 'active' : '' }}">
                    <a href="#workflow-{{ $section['id'] }}" data-bs-toggle="collapse" data-no-loader="true" aria-controls="workflow-{{ $section['id'] }}" aria-expanded="{{ $section['active'] ? 'true' : 'false' }}" class="dropdown-toggle {{ $section['active'] ? '' : 'collapsed' }}">
                        <div><span class="workflow-number" aria-hidden="true">{{ $loop->iteration }}</span><span>{{ $workflow('sections.' . $section['id']) }}</span></div>
                        <div><i class="fa-solid fa-chevron-right menu-chevron" aria-hidden="true"></i></div>
                    </a>
                    <ul class="collapse submenu list-unstyled {{ $section['active'] ? 'show' : '' }}" id="workflow-{{ $section['id'] }}" data-bs-parent="#accordionExample">
                        @php($previousLane = null)
                        @foreach($section['items'] as $page)
                            @if($workspace['type'] === 'hybrid' && $page['lane'] !== 'shared' && $page['lane'] !== $previousLane)
                                <li class="workflow-lane">{{ $workflow('lanes.' . $page['lane']) }}</li>
                            @endif
                            @php($previousLane = $page['lane'])
                            <li class="menu {{ $page['active'] ? 'active' : '' }}">
                                <a href="{{ route($page['route']) }}" class="dropdown-toggle" @if($page['newTab'] ?? false) target="_blank" rel="noopener noreferrer" @endif @if($page['active']) aria-current="page" @endif>
                                    <div><i class="fa-solid fa-{{ $page['icon'] }} menu-icon" aria-hidden="true"></i><span>{{ trans($page['title'], [], $navLocale) }}</span></div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
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
        const activeItem = menu.querySelector('.submenu .menu.active') || menu.querySelector('.menu.active');
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
