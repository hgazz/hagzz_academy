@extends('Academy.Layouts.master')

@section('title', trans('admin.training.calendar'))

@push('css')
    <link href="{{ asset('assetsAdmin/src/plugins/src/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assetsAdmin/src/assets/css/academy-calendar-modern.css') }}" rel="stylesheet">
@endpush

@php
    $isArabic = app()->getLocale() === 'ar';
    $copy = [
        'title' => $isArabic ? 'جدول الأكاديمية' : 'Academy calendar',
        'subtitle' => $isArabic ? 'تابع مواعيد التدريبات والجلسات الأسبوعية من مكان واحد.' : 'Manage weekly trainings and sessions in one place.',
        'today' => $isArabic ? 'اليوم' : 'Today',
        'month' => $isArabic ? 'شهر' : 'Month',
        'week' => $isArabic ? 'أسبوع' : 'Week',
        'day' => $isArabic ? 'يوم' : 'Day',
        'list' => $isArabic ? 'قائمة' : 'List',
        'trainings' => $isArabic ? 'التدريبات النشطة' : 'Active trainings',
        'weeklySessions' => $isArabic ? 'جلسات أسبوعية' : 'Weekly sessions',
        'todaySessions' => $isArabic ? 'جلسات اليوم' : 'Today sessions',
        'bookings' => $isArabic ? 'إجمالي الحجوزات' : 'Total bookings',
        'todaySchedule' => $isArabic ? 'جدول اليوم' : 'Today schedule',
        'todayHint' => $isArabic ? 'الجلسات المجدولة لهذا اليوم' : 'Sessions scheduled for today',
        'noToday' => $isArabic ? 'لا توجد جلسات مجدولة اليوم.' : 'No sessions scheduled today.',
        'details' => $isArabic ? 'تفاصيل التدريب' : 'Training details',
        'coach' => $isArabic ? 'المدرب' : 'Coach',
        'sport' => $isArabic ? 'الرياضة' : 'Sport',
        'location' => $isArabic ? 'الموقع' : 'Location',
        'time' => $isArabic ? 'الوقت' : 'Time',
        'capacity' => $isArabic ? 'الحجوزات والسعة' : 'Bookings and capacity',
        'level' => $isArabic ? 'المستوى' : 'Level',
        'edit' => $isArabic ? 'تعديل التدريب' : 'Edit training',
        'close' => $isArabic ? 'إغلاق' : 'Close',
        'addTraining' => $isArabic ? 'إضافة تدريب' : 'Add training',
        'attendance' => $isArabic ? 'تسجيل الحضور' : 'Take attendance',
        'calendar' => $isArabic ? 'التقويم' : 'Calendar',
        'notSpecified' => $isArabic ? 'غير محدد' : 'Not specified',
        'now' => $isArabic ? 'الوقت الآن' : 'Current time',
        'live' => $isArabic ? 'مباشر' : 'Live',
        'previous' => $isArabic ? 'السابق' : 'Previous',
        'next' => $isArabic ? 'التالي' : 'Next',
        'goNow' => $isArabic ? 'الانتقال إلى الساعة الحالية' : 'Go to current time',
    ];
@endphp

@section('content')
    <div class="middle-content container-xxl p-0 academy-calendar-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <header class="calendar-page-header">
            <div class="calendar-title-group">
                <button type="button" class="calendar-menu-toggle btn-toggle sidebarCollapse" aria-label="Toggle menu">
                    <i data-feather="menu"></i>
                </button>
                <div>
                    <span>{{ $copy['calendar'] }}</span>
                    <h1>{{ $copy['title'] }}</h1>
                    <p>{{ $copy['subtitle'] }}</p>
                </div>
            </div>
            <div class="calendar-header-actions">
                <div class="calendar-live-clock">
                    <div class="live-clock-icon"><i data-feather="clock"></i></div>
                    <div>
                        <span id="liveDate">{{ now()->locale(app()->getLocale())->translatedFormat('l، d F') }}</span>
                        <strong id="liveClock">{{ now()->format('h:i:s A') }}</strong>
                    </div>
                    <small><i></i>{{ $copy['live'] }}</small>
                </div>
                <a href="{{ route('academy.attendance.create') }}" class="calendar-action calendar-action-secondary">
                    <i data-feather="user-check"></i><span>{{ $copy['attendance'] }}</span>
                </a>
                <a href="{{ route('academy.training.create') }}" class="calendar-action calendar-action-primary">
                    <i data-feather="plus"></i><span>{{ $copy['addTraining'] }}</span>
                </a>
            </div>
        </header>

        <section class="calendar-metrics">
            <article>
                <span class="metric-label is-blue"><i data-feather="activity"></i>{{ $copy['trainings'] }}</span>
                <strong>{{ number_format($calendarSummary['trainings']) }}</strong>
            </article>
            <article>
                <span class="metric-label is-purple"><i data-feather="repeat"></i>{{ $copy['weeklySessions'] }}</span>
                <strong>{{ number_format($calendarSummary['weeklySessions']) }}</strong>
            </article>
            <article>
                <span class="metric-label is-teal"><i data-feather="calendar"></i>{{ $copy['todaySessions'] }}</span>
                <strong>{{ number_format($calendarSummary['todaySessions']) }}</strong>
            </article>
            <article>
                <span class="metric-label is-orange"><i data-feather="users"></i>{{ $copy['bookings'] }}</span>
                <strong>{{ number_format($calendarSummary['bookings']) }}</strong>
            </article>
        </section>

        <section class="calendar-workspace">
            <aside class="calendar-sidebar">
                <div class="sidebar-heading">
                    <div class="sidebar-date">{{ now()->locale(app()->getLocale())->translatedFormat('d') }}<small>{{ now()->locale(app()->getLocale())->translatedFormat('M') }}</small></div>
                    <div><h2>{{ $copy['todaySchedule'] }}</h2><p>{{ $copy['todayHint'] }}</p></div>
                </div>
                <div class="today-session-list">
                    @forelse($calendarSummary['todayTrainings'] as $training)
                        <button type="button" class="today-session" data-training-id="{{ $training['id'] }}">
                            <span class="session-color" style="--event-color: {{ $training['color'] }}"></span>
                            <span class="session-copy">
                                <strong>{{ $training['title'] }}</strong>
                                <small><i data-feather="clock"></i>{{ \Carbon\Carbon::parse($training['startTime'])->format('h:i A') }} - {{ \Carbon\Carbon::parse($training['endTime'])->format('h:i A') }}</small>
                                <small><i data-feather="user"></i>{{ $training['coach'] }}</small>
                            </span>
                            <i data-feather="{{ $isArabic ? 'chevron-left' : 'chevron-right' }}"></i>
                        </button>
                    @empty
                        <div class="calendar-empty"><i data-feather="coffee"></i><p>{{ $copy['noToday'] }}</p></div>
                    @endforelse
                </div>
                <div class="calendar-legend">
                    <span><i class="legend-dot is-active"></i>{{ $copy['trainings'] }}</span>
                    <span><i class="legend-dot is-now"></i>{{ $copy['today'] }}</span>
                </div>
            </aside>

            <div class="calendar-surface">
                <div class="calendar-controlbar">
                    <div class="calendar-navigation" aria-label="{{ $copy['calendar'] }}">
                        <button type="button" data-calendar-action="prev" title="{{ $copy['previous'] }}" aria-label="{{ $copy['previous'] }}">
                            <i data-feather="{{ $isArabic ? 'chevron-right' : 'chevron-left' }}"></i>
                        </button>
                        <button type="button" data-calendar-action="today" class="calendar-today-button">
                            <i data-feather="calendar"></i><span>{{ $copy['today'] }}</span>
                        </button>
                        <button type="button" data-calendar-action="next" title="{{ $copy['next'] }}" aria-label="{{ $copy['next'] }}">
                            <i data-feather="{{ $isArabic ? 'chevron-left' : 'chevron-right' }}"></i>
                        </button>
                    </div>
                    <h2 id="calendarRangeTitle"></h2>
                    <div class="calendar-view-actions">
                        <button type="button" data-calendar-view="dayGridMonth" title="{{ $copy['month'] }}"><i data-feather="calendar"></i><span>{{ $copy['month'] }}</span></button>
                        <button type="button" data-calendar-view="timeGridWeek" title="{{ $copy['week'] }}"><i data-feather="columns"></i><span>{{ $copy['week'] }}</span></button>
                        <button type="button" data-calendar-view="timeGridDay" title="{{ $copy['day'] }}"><i data-feather="clock"></i><span>{{ $copy['day'] }}</span></button>
                        <button type="button" data-calendar-view="listWeek" title="{{ $copy['list'] }}"><i data-feather="list"></i><span>{{ $copy['list'] }}</span></button>
                        <button type="button" id="goToCurrentTime" class="go-now-button" title="{{ $copy['goNow'] }}"><i data-feather="crosshair"></i></button>
                    </div>
                </div>
                <div id="academyCalendar"></div>
            </div>
        </section>
    </div>

    <div class="training-drawer-backdrop" id="trainingDrawerBackdrop"></div>
    <aside class="training-drawer" id="trainingDrawer" dir="{{ $isArabic ? 'rtl' : 'ltr' }}" aria-hidden="true">
        <header>
            <div><span>{{ $copy['details'] }}</span><h2 id="drawerTitle">-</h2></div>
            <button type="button" id="drawerClose" aria-label="{{ $copy['close'] }}"><i data-feather="x"></i></button>
        </header>
        <div class="drawer-time-card">
            <i data-feather="clock"></i>
            <div><span>{{ $copy['time'] }}</span><strong id="drawerTime">-</strong></div>
        </div>
        <dl class="drawer-details">
            <div><dt><i data-feather="user"></i>{{ $copy['coach'] }}</dt><dd id="drawerCoach">-</dd></div>
            <div><dt><i data-feather="award"></i>{{ $copy['sport'] }}</dt><dd id="drawerSport">-</dd></div>
            <div><dt><i data-feather="map-pin"></i>{{ $copy['location'] }}</dt><dd id="drawerLocation">-</dd></div>
            <div><dt><i data-feather="users"></i>{{ $copy['capacity'] }}</dt><dd id="drawerCapacity">-</dd></div>
            <div><dt><i data-feather="bar-chart"></i>{{ $copy['level'] }}</dt><dd id="drawerLevel">-</dd></div>
        </dl>
        <a href="#" id="drawerEditLink" class="calendar-action calendar-action-primary drawer-edit"><i data-feather="edit-2"></i><span>{{ $copy['edit'] }}</span></a>
    </aside>
@endsection

@push('js')
    <script src="{{ asset('assetsAdmin/src/plugins/src/fullcalendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('assetsAdmin/src/plugins/src/fullcalendar/locales-all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sourceEvents = @json($events);
            const dayIndexes = { sunday: 0, monday: 1, tuesday: 2, wednesday: 3, thursday: 4, friday: 5, saturday: 6 };
            const eventMap = new Map(sourceEvents.map(event => [String(event.id), event]));
            const recurringEvents = sourceEvents.flatMap(event =>
                (event.days || []).map(day => ({
                    id: String(event.id),
                    title: event.title,
                    daysOfWeek: [dayIndexes[String(day).toLowerCase()]],
                    startTime: event.startTime,
                    endTime: event.endTime,
                    backgroundColor: event.color,
                    borderColor: event.color,
                    textColor: '#ffffff',
                    extendedProps: event
                })).filter(event => Number.isInteger(event.daysOfWeek[0]))
            );

            const drawer = document.getElementById('trainingDrawer');
            const backdrop = document.getElementById('trainingDrawerBackdrop');
            const timeFormatter = new Intl.DateTimeFormat(@json($isArabic ? 'ar-EG' : 'en-US'), { hour: '2-digit', minute: '2-digit' });
            const clockFormatter = new Intl.DateTimeFormat(@json($isArabic ? 'ar-EG' : 'en-US'), { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateFormatter = new Intl.DateTimeFormat(@json($isArabic ? 'ar-EG' : 'en-US'), { weekday: 'long', day: '2-digit', month: 'long' });

            function updateLiveClock() {
                const now = new Date();
                document.getElementById('liveClock').textContent = clockFormatter.format(now);
                document.getElementById('liveDate').textContent = dateFormatter.format(now);
            }

            function currentScrollTime() {
                const now = new Date();
                now.setMinutes(now.getMinutes() - 60);
                const hours = String(Math.max(0, now.getHours())).padStart(2, '0');
                return `${hours}:${String(now.getMinutes()).padStart(2, '0')}:00`;
            }

            function formatTime(value) {
                if (!value) return @json($copy['notSpecified']);
                return timeFormatter.format(new Date(`2000-01-01T${value}`));
            }

            function openDrawer(event) {
                if (!event) return;
                document.getElementById('drawerTitle').textContent = event.title || @json($copy['notSpecified']);
                document.getElementById('drawerTime').textContent = `${formatTime(event.startTime)} - ${formatTime(event.endTime)}`;
                document.getElementById('drawerCoach').textContent = event.coach || @json($copy['notSpecified']);
                document.getElementById('drawerSport').textContent = event.sport || @json($copy['notSpecified']);
                document.getElementById('drawerLocation').textContent = event.location || @json($copy['notSpecified']);
                document.getElementById('drawerCapacity').textContent = `${Number(event.bookings || 0).toLocaleString()} / ${Number(event.capacity || 0).toLocaleString()}`;
                document.getElementById('drawerLevel').textContent = event.level || @json($copy['notSpecified']);
                document.getElementById('drawerEditLink').href = event.editUrl;
                drawer.classList.add('is-open');
                backdrop.classList.add('is-open');
                drawer.setAttribute('aria-hidden', 'false');
            }

            function closeDrawer() {
                drawer.classList.remove('is-open');
                backdrop.classList.remove('is-open');
                drawer.setAttribute('aria-hidden', 'true');
            }

            document.querySelectorAll('.today-session').forEach(button => {
                button.addEventListener('click', () => openDrawer(eventMap.get(button.dataset.trainingId)));
            });
            document.getElementById('drawerClose').addEventListener('click', closeDrawer);
            backdrop.addEventListener('click', closeDrawer);
            document.addEventListener('keydown', event => { if (event.key === 'Escape') closeDrawer(); });

            const calendar = new FullCalendar.Calendar(document.getElementById('academyCalendar'), {
                locale: @json($isArabic ? 'ar' : 'en'),
                direction: @json($isArabic ? 'rtl' : 'ltr'),
                initialView: window.innerWidth < 768 ? 'listWeek' : 'timeGridWeek',
                firstDay: 6,
                nowIndicator: true,
                scrollTime: currentScrollTime(),
                scrollTimeReset: false,
                allDaySlot: false,
                slotMinTime: '06:00:00',
                slotMaxTime: '24:00:00',
                slotDuration: '00:30:00',
                expandRows: true,
                height: 'auto',
                stickyHeaderDates: true,
                dayMaxEvents: 3,
                eventDisplay: 'block',
                events: recurringEvents,
                headerToolbar: false,
                buttonText: {
                    today: @json($copy['today']),
                    month: @json($copy['month']),
                    week: @json($copy['week']),
                    day: @json($copy['day']),
                    list: @json($copy['list'])
                },
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: 'short' },
                slotLabelFormat: { hour: '2-digit', minute: '2-digit', meridiem: 'short' },
                eventContent: function (arg) {
                    const props = arg.event.extendedProps;
                    const wrapper = document.createElement('div');
                    wrapper.className = 'calendar-event-content';
                    const time = document.createElement('span');
                    time.className = 'calendar-event-time calendar-event-meta';
                    if (window.feather?.icons?.clock) time.innerHTML = window.feather.icons.clock.toSvg();
                    time.append(document.createTextNode(arg.timeText));
                    const title = document.createElement('strong');
                    title.textContent = arg.event.title;
                    const meta = document.createElement('small');
                    meta.className = 'calendar-event-meta';
                    if (window.feather?.icons?.user) meta.innerHTML = window.feather.icons.user.toSvg();
                    meta.append(document.createTextNode(props.coach || ''));
                    const location = document.createElement('small');
                    location.className = 'calendar-event-meta';
                    if (window.feather?.icons?.['map-pin']) location.innerHTML = window.feather.icons['map-pin'].toSvg();
                    location.append(document.createTextNode(props.location || ''));
                    wrapper.append(time, title, meta, location);
                    return { domNodes: [wrapper] };
                },
                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    openDrawer(eventMap.get(String(info.event.id)));
                },
                datesSet: function (info) {
                    document.getElementById('calendarRangeTitle').textContent = info.view.title;
                    document.querySelectorAll('[data-calendar-view]').forEach(button => {
                        button.classList.toggle('is-active', button.dataset.calendarView === info.view.type);
                    });
                },
                windowResize: function (info) {
                    if (window.innerWidth < 768 && info.view.type !== 'listWeek') calendar.changeView('listWeek');
                }
            });
            calendar.render();

            document.querySelectorAll('[data-calendar-action]').forEach(button => {
                button.addEventListener('click', () => calendar[button.dataset.calendarAction]());
            });
            document.querySelectorAll('[data-calendar-view]').forEach(button => {
                button.addEventListener('click', () => calendar.changeView(button.dataset.calendarView));
            });
            document.getElementById('goToCurrentTime').addEventListener('click', function () {
                calendar.today();
                if (window.innerWidth >= 768 && !calendar.view.type.startsWith('timeGrid')) {
                    calendar.changeView('timeGridDay');
                }
                calendar.scrollToTime(currentScrollTime());
            });

            updateLiveClock();
            window.setInterval(updateLiveClock, 1000);
            if (window.feather) feather.replace();
        });
    </script>
@endpush
