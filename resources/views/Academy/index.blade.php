@extends('Academy.Layouts.master')

@section('title', trans('admin.dashboard'))

@push('css')
    <link href="{{ asset('assetsAdmin/src/plugins/src/apex/apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assetsAdmin/src/plugins/src/flatpickr/flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assetsAdmin/src/assets/css/academy-dashboard-modern.css') }}?v={{ time() }}" rel="stylesheet" type="text/css">
    <style>
        .hybrid-venue-strip{display:grid;grid-template-columns:1.2fr repeat(4,minmax(110px,.7fr));gap:10px;padding:14px;margin-bottom:15px;border:1px solid #bae6fd;border-radius:8px;background:#f0f9ff}.hybrid-venue-title{display:flex;align-items:center;gap:10px}.hybrid-venue-title i{display:grid;place-items:center;width:40px;height:40px;border-radius:8px;background:#0f766e;color:#fff}.hybrid-venue-title strong,.hybrid-venue-item strong{display:block;color:#102a43}.hybrid-venue-title span,.hybrid-venue-item span{display:block;color:#64748b;font-size:11px}.hybrid-venue-item{padding:9px;border-inline-start:1px solid #bae6fd}.hybrid-venue-links{display:flex;gap:7px;margin-top:5px}.hybrid-venue-links a{color:#0f766e;font-size:11px;font-weight:700}@media(max-width:991px){.hybrid-venue-strip{grid-template-columns:repeat(2,1fr)}.hybrid-venue-title{grid-column:1/-1}.hybrid-venue-item{border-inline-start:0;border-top:1px solid #bae6fd}}@media(max-width:575px){.hybrid-venue-strip{grid-template-columns:1fr}}
    </style>
@endpush

@php
    $isArabic = app()->getLocale() === 'ar';
    $copy = [
        'welcome' => $isArabic ? 'مرحباً بعودتك' : 'Welcome back',
        'overview' => $isArabic ? 'إليك ملخص أداء أكاديميتك وأهم ما يحتاج إلى متابعتك اليوم.' : 'Here is your academy performance and what needs attention today.',
        'today' => $isArabic ? 'اليوم' : 'Today',
        'students' => $isArabic ? 'الطلاب النشطون' : 'Active students',
        'customers' => $isArabic ? 'عملاء التطبيق' : 'App customers',
        'trainings' => $isArabic ? 'التدريبات' : 'Trainings',
        'activeTrainings' => $isArabic ? 'تدريب نشط' : 'active trainings',
        'bookings' => $isArabic ? 'الحجوزات' : 'Bookings',
        'bookingRevenue' => $isArabic ? 'إيرادات الحجوزات' : 'Booking revenue',
        'subscriptions' => $isArabic ? 'الاشتراكات النشطة' : 'Active subscriptions',
        'subscriptionRevenue' => $isArabic ? 'تحصيل الاشتراكات' : 'Subscription collections',
        'outstanding' => $isArabic ? 'مبالغ متبقية' : 'Outstanding',
        'attendance' => $isArabic ? 'معدل الحضور' : 'Attendance rate',
        'sessionsToday' => $isArabic ? 'جلسات اليوم' : 'Today sessions',
        'coaches' => $isArabic ? 'المدربون' : 'Coaches',
        'groups' => $isArabic ? 'المجموعات' : 'Groups',
        'followers' => $isArabic ? 'المتابعون' : 'Followers',
        'last30' => $isArabic ? 'مقارنةً بالـ 30 يوم السابقة' : 'vs previous 30 days',
        'financialPerformance' => $isArabic ? 'الأداء المالي والحجوزات' : 'Revenue and bookings',
        'financialHint' => $isArabic ? 'أداء الأكاديمية خلال آخر 12 شهراً' : 'Academy performance over the last 12 months',
        'attendanceBreakdown' => $isArabic ? 'تفاصيل الحضور' : 'Attendance breakdown',
        'present' => $isArabic ? 'حاضر' : 'Present',
        'late' => $isArabic ? 'متأخر' : 'Late',
        'absent' => $isArabic ? 'غائب' : 'Absent',
        'excused' => $isArabic ? 'بعذر' : 'Excused',
        'topTrainings' => $isArabic ? 'التدريبات الأكثر حجزاً' : 'Top booked trainings',
        'expiring' => $isArabic ? 'اشتراكات تنتهي قريباً' : 'Expiring subscriptions',
        'expiringHint' => $isArabic ? 'خلال الأربعة عشر يوماً القادمة' : 'Within the next 14 days',
        'recent' => $isArabic ? 'أحدث الحجوزات' : 'Recent bookings',
        'recentHint' => $isArabic ? 'آخر عمليات الحجز في أكاديميتك' : 'Latest bookings for your academy',
        'filter' => $isArabic ? 'تصفية نتائج الحجوزات' : 'Filter booking results',
        'from' => $isArabic ? 'من تاريخ' : 'From',
        'to' => $isArabic ? 'إلى تاريخ' : 'To',
        'apply' => $isArabic ? 'تطبيق' : 'Apply',
        'balance' => $isArabic ? 'قيمة الحجوزات' : 'Booking value',
        'refunds' => $isArabic ? 'الحجوزات المستردة' : 'Refunded bookings',
        'refundAmount' => $isArabic ? 'قيمة المستردات' : 'Refund amount',
        'bookingCount' => $isArabic ? 'عدد الحجوزات' : 'Booking count',
        'paid' => $isArabic ? 'مدفوع' : 'Paid',
        'pending' => $isArabic ? 'معلق' : 'Pending',
        'canceled' => $isArabic ? 'ملغى' : 'Canceled',
        'viewAll' => $isArabic ? 'عرض الكل' : 'View all',
        'noData' => $isArabic ? 'لا توجد بيانات بعد' : 'No data yet',
        'currency' => $isArabic ? 'ج.م' : 'EGP',
        'endsOn' => $isArabic ? 'ينتهي في' : 'Ends',
        'quickActions' => $isArabic ? 'إجراءات سريعة' : 'Quick actions',
    ];
@endphp

@section('content')
    <div class="middle-content container-xxl p-0 academy-dashboard" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <div class="dashboard-topbar">
            <button type="button" class="dashboard-menu-toggle btn-toggle sidebarCollapse" aria-label="Toggle menu">
                <i data-feather="menu"></i>
            </button>
            <div>
                <h1>{{ trans('admin.dashboard') }}</h1>
                <p>{{ $copy['today'] }}، {{ now()->locale(app()->getLocale())->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <section class="welcome-strip">
            <div class="welcome-copy">
                <span>{{ $copy['welcome'] }}</span>
                <h2>{{ $dashboard['ownerName'] ?: $dashboard['academyName'] }}</h2>
                <p><b>{{ $dashboard['academyName'] }}</b> · {{ $copy['overview'] }}</p>
            </div>
            <div class="welcome-actions">
                <a href="{{ route('academy.students.create') }}" class="dashboard-action dashboard-action-secondary">
                    <i data-feather="user-plus"></i><span>{{ $isArabic ? 'إضافة طالب' : 'Add student' }}</span>
                </a>
                <a href="{{ route('academy.training.create') }}" class="dashboard-action dashboard-action-primary">
                    <i data-feather="plus-circle"></i><span>{{ $isArabic ? 'إضافة تدريب' : 'Add training' }}</span>
                </a>
            </div>
        </section>

        @if(($dashboard['dashboardMode'] ?? 'academy') === 'hybrid' && $dashboard['venue'])
            <section class="hybrid-venue-strip">
                <div class="hybrid-venue-title"><i><span data-feather="map"></span></i><div><strong>{{ $isArabic ? 'تشغيل الملاعب' : 'Venue operations' }}</strong><span>{{ $isArabic ? 'ملخص سريع لنشاط الملاعب' : 'Quick venue activity summary' }}</span><div class="hybrid-venue-links"><a href="{{ route('academy.venue-bookings.calendar') }}">{{ $isArabic ? 'التقويم' : 'Calendar' }}</a><a href="{{ route('academy.venue-bookings.create') }}">{{ $isArabic ? 'حجز جديد' : 'New booking' }}</a></div></div></div>
                <div class="hybrid-venue-item"><span>{{ $isArabic ? 'حجوزات اليوم' : 'Today bookings' }}</span><strong>{{ number_format($dashboard['venue']['todayBookings']) }}</strong></div>
                <div class="hybrid-venue-item"><span>{{ $isArabic ? 'تحصيل اليوم' : 'Today collected' }}</span><strong>{{ number_format($dashboard['venue']['todayCollected'],2) }}</strong></div>
                <div class="hybrid-venue-item"><span>{{ $isArabic ? 'المساحات النشطة' : 'Active spaces' }}</span><strong>{{ number_format($dashboard['venue']['spaces']) }}</strong></div>
                <div class="hybrid-venue-item"><span>{{ $isArabic ? 'المبالغ المتبقية' : 'Outstanding' }}</span><strong>{{ number_format($dashboard['venue']['outstanding'],2) }}</strong></div>
            </section>
        @endif

        <section class="metric-grid">
            <article class="metric-card metric-students">
                <div class="metric-icon"><i data-feather="users"></i></div>
                <div><span>{{ $copy['students'] }}</span><strong>{{ number_format($dashboard['activeStudents']) }}</strong><small>{{ $dashboard['activeGroups'] }} {{ $copy['groups'] }}</small></div>
            </article>
            <article class="metric-card metric-bookings">
                <div class="metric-icon"><i data-feather="calendar"></i></div>
                <div><span>{{ $copy['bookings'] }}</span><strong>{{ number_format($dashboard['totalBookings']) }}</strong><small class="{{ $dashboard['bookingTrend'] >= 0 ? 'trend-up' : 'trend-down' }}"><i data-feather="{{ $dashboard['bookingTrend'] >= 0 ? 'trending-up' : 'trending-down' }}"></i> {{ abs($dashboard['bookingTrend']) }}% {{ $copy['last30'] }}</small></div>
            </article>
            <article class="metric-card metric-revenue">
                <div class="metric-icon"><i data-feather="credit-card"></i></div>
                <div><span>{{ $copy['bookingRevenue'] }}</span><strong>{{ number_format($dashboard['totalRevenue'], 0) }}</strong><small>{{ $copy['currency'] }}</small></div>
            </article>
            <article class="metric-card metric-subscriptions">
                <div class="metric-icon"><i data-feather="repeat"></i></div>
                <div><span>{{ $copy['subscriptions'] }}</span><strong>{{ number_format($dashboard['activeSubscriptions']) }}</strong><small>{{ number_format($dashboard['subscriptionRevenue'], 0) }} {{ $copy['currency'] }} {{ $copy['paid'] }}</small></div>
            </article>
            <article class="metric-card metric-outstanding">
                <div class="metric-icon"><i data-feather="alert-circle"></i></div>
                <div><span>{{ $copy['outstanding'] }}</span><strong>{{ number_format($dashboard['outstandingSubscriptions'], 0) }}</strong><small>{{ $copy['currency'] }}</small></div>
            </article>
            <article class="metric-card metric-attendance">
                <div class="metric-icon"><i data-feather="check-circle"></i></div>
                <div><span>{{ $copy['attendance'] }}</span><strong>{{ $dashboard['attendanceRate'] }}%</strong><small>{{ $dashboard['todaySessions'] }} {{ $copy['sessionsToday'] }}</small></div>
            </article>
        </section>

        <section class="operational-strip">
            <a href="{{ route('academy.training.index') }}"><i data-feather="activity"></i><span>{{ $copy['trainings'] }}</span><strong>{{ $dashboard['activeTrainings'] }}/{{ $dashboard['totalTrainings'] }}</strong></a>
            <a href="{{ route('academy.coach') }}"><i data-feather="award"></i><span>{{ $copy['coaches'] }}</span><strong>{{ $dashboard['totalCoaches'] }}</strong></a>
            <a href="{{ route('academy.users.index') }}"><i data-feather="smartphone"></i><span>{{ $copy['customers'] }}</span><strong>{{ $dashboard['uniqueCustomers'] }}</strong></a>
            <a href="{{ route('academy.profile.index') }}"><i data-feather="heart"></i><span>{{ $copy['followers'] }}</span><strong>{{ $dashboard['followers'] }}</strong></a>
        </section>

        <section class="dashboard-grid dashboard-grid-main">
            <article class="dashboard-panel dashboard-panel-wide">
                <header class="panel-header">
                    <div><h3>{{ $copy['financialPerformance'] }}</h3><p>{{ $copy['financialHint'] }}</p></div>
                    <span class="panel-badge">{{ $dashboard['activeTrainings'] }} {{ $copy['activeTrainings'] }}</span>
                </header>
                <div id="financialChart" class="chart-slot chart-slot-large"></div>
            </article>
            <article class="dashboard-panel">
                <header class="panel-header">
                    <div><h3>{{ $copy['attendanceBreakdown'] }}</h3><p>{{ $copy['last30'] }}</p></div>
                </header>
                <div id="attendanceChart" class="chart-slot chart-slot-large"></div>
            </article>
        </section>

        <section class="dashboard-grid dashboard-grid-secondary">
            <article class="dashboard-panel">
                <header class="panel-header">
                    <div><h3>{{ $copy['topTrainings'] }}</h3><p>{{ $isArabic ? 'مرتبة حسب عدد الحجوزات' : 'Ranked by bookings' }}</p></div>
                    <a href="{{ route('academy.training.index') }}" class="panel-link">{{ $copy['viewAll'] }}</a>
                </header>
                <div id="trainingsChart" class="chart-slot"></div>
            </article>
            <article class="dashboard-panel quick-panel">
                <header class="panel-header"><div><h3>{{ $copy['quickActions'] }}</h3><p>{{ $isArabic ? 'الوصول السريع إلى مهام الأكاديمية اليومية' : 'Shortcuts to daily academy work' }}</p></div></header>
                <div class="quick-grid">
                    <a href="{{ route('academy.attendance.create') }}"><i data-feather="user-check"></i><span>{{ $isArabic ? 'تسجيل الحضور' : 'Take attendance' }}</span></a>
                    <a href="{{ route('academy.subscriptions.create') }}"><i data-feather="file-plus"></i><span>{{ $isArabic ? 'اشتراك جديد' : 'New subscription' }}</span></a>
                    <a href="{{ route('academy.groups.create') }}"><i data-feather="grid"></i><span>{{ $isArabic ? 'إنشاء مجموعة' : 'Create group' }}</span></a>
                    <a href="{{ route('academy.createBooking') }}"><i data-feather="calendar"></i><span>{{ $isArabic ? 'حجز مباشر' : 'Direct booking' }}</span></a>
                    <a href="{{ route('academy.student-reports.index') }}"><i data-feather="bar-chart-2"></i><span>{{ $isArabic ? 'تقارير الطلاب' : 'Student reports' }}</span></a>
                    <a href="{{ route('academy.calendar.index') }}"><i data-feather="clock"></i><span>{{ $isArabic ? 'جدول الأكاديمية' : 'Academy calendar' }}</span></a>
                </div>
            </article>
        </section>

        <section class="filter-panel">
            <div class="filter-heading"><div class="filter-icon"><i data-feather="sliders"></i></div><div><h3>{{ $copy['filter'] }}</h3><p>{{ $isArabic ? 'راجع نتائج الحجوزات لفترة زمنية محددة.' : 'Review booking results for a selected period.' }}</p></div></div>
            <form id="filterForm" class="filter-form">
                <label><span>{{ $copy['from'] }}</span><input type="date" class="form-control" id="start_date"></label>
                <label><span>{{ $copy['to'] }}</span><input type="date" class="form-control" id="end_date"></label>
                <button type="button" id="filter" class="dashboard-action dashboard-action-primary"><i data-feather="filter"></i><span>{{ $copy['apply'] }}</span></button>
            </form>
            <div class="filtered-metrics">
                <div><span>{{ $copy['balance'] }}</span><strong id="total_booking_balance">0</strong></div>
                <div><span>{{ $copy['refunds'] }}</span><strong id="total_booking_refund_count">0</strong></div>
                <div><span>{{ $copy['refundAmount'] }}</span><strong id="total_booking_refund_amount">0</strong></div>
                <div><span>{{ $copy['bookingCount'] }}</span><strong id="total_booking_count">0</strong></div>
            </div>
        </section>

        <section class="dashboard-grid dashboard-grid-main mt-4">
            <article class="dashboard-panel">
                <header class="panel-header">
                    <div>
                        <h3>{{ $isArabic ? 'تطور المصروفات الشهرية' : 'Monthly Expenses Trend' }}</h3>
                        <p>{{ $isArabic ? 'تتبع المصروفات التشغيلية خلال آخر 12 شهراً' : 'Track operational expenses over last 12 months' }}</p>
                    </div>
                    <a href="{{ route('academy.expenses.index') }}" class="panel-link"><i class="fa-solid fa-plus-circle me-1"></i> {{ $isArabic ? 'إدارة المصروفات' : 'Manage Expenses' }}</a>
                </header>
                <div id="expensesChart" class="chart-slot chart-slot-large"></div>
            </article>

            <article class="dashboard-panel">
                <header class="panel-header">
                    <div>
                        <h3>{{ $isArabic ? 'توزيع المصروفات حسب التصنيف' : 'Expense Categories Distribution' }}</h3>
                        <p>{{ $isArabic ? 'نسبة كل بند من إجمالي المصروفات' : 'Cost breakdown by category' }}</p>
                    </div>
                </header>
                <div id="expenseCategoryChart" class="chart-slot chart-slot-large"></div>
            </article>
        </section>

        <!-- EXPIRING SUBSCRIBERS 10-0 DAYS COUNTDOWN SECTION -->
        <section class="dashboard-grid dashboard-grid-main mt-4">
            <article class="dashboard-panel">
                <header class="panel-header">
                    <div>
                        <h3>{{ $isArabic ? 'تنازلي انتهاء اشتراكات الطلاب (10 أيام إلى اليوم)' : 'Subscribers Expiration Countdown (10 Days to Today)' }}</h3>
                        <p>{{ $isArabic ? 'عدد الاشتراكات المقاربة على الانتهاء مصنفة حسب الأيام المتبقية' : 'Active subscriptions categorized by days remaining' }}</p>
                    </div>
                    <span class="panel-badge bg-danger text-white">{{ $isArabic ? 'تنبيه انتهاء' : 'Expiring Alert' }}</span>
                </header>
                <div id="expiringCountdownChart" class="chart-slot chart-slot-large"></div>
            </article>

            <article class="dashboard-panel data-panel">
                <header class="panel-header">
                    <div>
                        <h3>{{ $isArabic ? 'قائمة الطلاب المقاربين على الانتهاء' : 'Expiring Subscribers List' }}</h3>
                        <p>{{ $isArabic ? 'تواصل سريع عبر واتساب للتذكير بالتجديد' : 'Quick WhatsApp reminder to renew' }}</p>
                    </div>
                    <a href="{{ route('academy.subscriptions.index') }}" class="panel-link">{{ $copy['viewAll'] }}</a>
                </header>
                <div class="subscription-list">
                    @forelse($dashboard['expiringSubscriptions'] as $sub)
                        @php
                            $daysLeft = now()->startOfDay()->diffInDays($sub->ends_on, false);
                            $studentPhone = preg_replace('/[^0-9]/', '', $sub->student?->phone ?: '');
                            $studentName = $sub->student?->name ?: ($isArabic ? 'مشترك' : 'Subscriber');
                            $groupName = $sub->group?->name ?: '';
                            $waMessage = rawurlencode($isArabic 
                                ? "مرحباً {$studentName}، نود تذكيرك بقرب انتهاء اشتراكك في مجموعة {$groupName} بتاريخ {$sub->ends_on?->format('Y-m-d')}. يرجى التواصل للتجديد." 
                                : "Hello {$studentName}, reminder that your subscription for {$groupName} expires on {$sub->ends_on?->format('Y-m-d')}.");
                        @endphp
                        <div class="subscription-item d-flex align-items-center justify-content-between p-3 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="subscription-icon text-warning bg-warning bg-opacity-10 p-2 rounded-circle">
                                    <i data-feather="clock"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-dark mb-1">{{ $studentName }}</strong>
                                    <small class="text-muted"><i class="fa-solid fa-users me-1"></i> {{ $groupName }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $daysLeft <= 2 ? 'bg-danger' : 'bg-warning text-dark' }} px-2 py-1 mb-1 d-block">
                                    {{ $daysLeft === 0 ? ($isArabic ? 'ينتهي اليوم' : 'Today') : ($daysLeft . ' ' . ($isArabic ? 'أيام متبقية' : 'days left')) }}
                                </span>
                                @if($studentPhone)
                                    <a href="https://wa.me/{{ $studentPhone }}?text={{ $waMessage }}" target="_blank" class="btn btn-sm btn-success py-1 px-2 text-white fw-bold mt-1">
                                        <i class="fa-brands fa-whatsapp me-1"></i> {{ $isArabic ? 'تذكير' : 'Remind' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">{{ $copy['noData'] }}</div>
                    @endforelse
                </div>
            </article>
        </section>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assetsAdmin/src/plugins/src/apex/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assetsAdmin/src/plugins/src/flatpickr/flatpickr.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const finiteNumbers = values => Array.from(values || [], value => {
                const number = Number(value);
                return Number.isFinite(number) ? number : 0;
            });
            const data = {
                labels: @json($dashboard['monthLabels']),
                bookings: finiteNumbers(@json($dashboard['monthlyBookings'])),
                bookingRevenue: finiteNumbers(@json($dashboard['monthlyBookingRevenue'])),
                subscriptionRevenue: finiteNumbers(@json($dashboard['monthlySubscriptionRevenue'])),
                attendance: finiteNumbers(@json($dashboard['attendanceStatuses'])),
                trainingLabels: @json($dashboard['topTrainings']->pluck('name')),
                trainingBookings: finiteNumbers(@json($dashboard['topTrainings']->pluck('bookings')))
            };
            const labels = {
                bookings: @json($copy['bookings']), bookingRevenue: @json($copy['bookingRevenue']),
                subscriptionRevenue: @json($copy['subscriptionRevenue']), present: @json($copy['present']),
                late: @json($copy['late']), absent: @json($copy['absent']), excused: @json($copy['excused']),
                attendance: @json($copy['attendance']), currency: @json($copy['currency'])
            };
            const dark = document.body.classList.contains('dark');
            const text = dark ? '#cbd5e1' : '#64748b';
            const grid = dark ? '#293446' : '#e8edf4';
            const common = { fontFamily: 'Cairo, Nunito, sans-serif', foreColor: text, toolbar: { show: false }, animations: { enabled: true, easing: 'easeinout', speed: 550 } };
            const noData = { text: @json($copy['noData']), align: 'center', verticalAlign: 'middle', style: { color: text } };

            const financialElement = document.querySelector('#financialChart');
            const financialTotal = data.bookings.reduce((sum, v) => sum + v, 0) +
                                   data.bookingRevenue.reduce((sum, v) => sum + v, 0) +
                                   data.subscriptionRevenue.reduce((sum, v) => sum + v, 0);
            if (financialTotal > 0) {
                new ApexCharts(financialElement, {
                    chart: { ...common, type: 'line', height: 460 },
                    series: [
                        { name: labels.bookings, type: 'column', data: data.bookings },
                        { name: labels.bookingRevenue, type: 'area', data: data.bookingRevenue },
                        { name: labels.subscriptionRevenue, type: 'line', data: data.subscriptionRevenue }
                    ],
                    colors: ['#2563eb', '#14b8a6', '#7c3aed'], stroke: { width: [0, 3, 3], curve: 'smooth' },
                    fill: { type: ['solid', 'gradient', 'solid'], gradient: { opacityFrom: .3, opacityTo: .04 } },
                    plotOptions: { bar: { borderRadius: 5, columnWidth: '38%' } }, dataLabels: { enabled: false },
                    grid: { borderColor: grid, strokeDashArray: 4, padding: { top: 30, right: 50, bottom: 65, left: 50 } },
                    xaxis: { categories: data.labels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { rotate: -45, rotateAlways: true, minHeight: 70, style: { colors: text, fontSize: '11px', fontWeight: 600 } } },
                    yaxis: [
                        { min: 0, forceNiceScale: true },
                        { opposite: true, min: 0, labels: { formatter: value => Math.round(value).toLocaleString() } }
                    ],
                    legend: { position: 'top', horizontalAlign: 'center', offsetY: -5, fontSize: '13px', fontWeight: 700, itemMargin: { horizontal: 15, vertical: 8 } },
                    tooltip: { shared: true, intersect: false }, noData
                }).render();
            } else {
                financialElement.classList.add('empty-state');
                financialElement.style.minHeight = '460px';
                financialElement.style.display = 'grid';
                financialElement.style.placeItems = 'center';
                financialElement.textContent = @json($copy['noData']);
            }

            const attendanceElement = document.querySelector('#attendanceChart');
            const attendanceTotal = data.attendance.reduce((sum, value) => sum + value, 0);
            if (attendanceTotal > 0) {
                new ApexCharts(attendanceElement, {
                    chart: { ...common, type: 'donut', height: 380 }, series: data.attendance,
                    labels: [labels.present, labels.late, labels.absent, labels.excused],
                    colors: ['#14b8a6', '#f59e0b', '#ef4444', '#64748b'], stroke: { width: 0 }, dataLabels: { enabled: false },
                    legend: { position: 'bottom' }, plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, label: labels.attendance, formatter: chart => chart.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString() } } } } }, noData
                }).render();
            } else {
                attendanceElement.classList.add('empty-state');
                attendanceElement.style.minHeight = '380px';
                attendanceElement.style.display = 'grid';
                attendanceElement.style.placeItems = 'center';
                attendanceElement.textContent = @json($copy['noData']);
            }

            const trainingsElement = document.querySelector('#trainingsChart');
            const trainingsTotal = data.trainingBookings.reduce((sum, v) => sum + v, 0);
            if (trainingsTotal > 0 && data.trainingLabels.length > 0) {
                new ApexCharts(trainingsElement, {
                    chart: { ...common, type: 'bar', height: 380 }, series: [{ name: labels.bookings, data: data.trainingBookings }],
                    colors: ['#f97316'], plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '52%' } },
                    dataLabels: { enabled: true, textAnchor: 'start', style: { colors: ['#ffffff'], fontSize: '11px', fontWeight: 700 }, formatter: val => val > 0 ? val : '', offsetX: 5 },
                    grid: { borderColor: grid, strokeDashArray: 4, padding: { top: 20, right: 40, bottom: 20, left: 110 } },
                    xaxis: { categories: data.trainingLabels, min: 0, forceNiceScale: true },
                    yaxis: { labels: { show: true, align: 'left', style: { colors: text, fontSize: '13px', fontWeight: 700 } } },
                    noData
                }).render();
            } else {
                trainingsElement.classList.add('empty-state');
                trainingsElement.style.minHeight = '380px';
                trainingsElement.style.display = 'grid';
                trainingsElement.style.placeItems = 'center';
                trainingsElement.textContent = @json($copy['noData']);
            }

            const extraData = {
                expenses: finiteNumbers(@json($dashboard['monthlyExpenses'])),
                expenseCategories: @json($dashboard['expenseCategories']),
                expenseCategoryTotals: finiteNumbers(@json($dashboard['expenseCategoryTotals'])),
                expiringCountdownLabels: @json($dashboard['expiringCountdownLabels']),
                expiringCountdownCounts: finiteNumbers(@json($dashboard['expiringCountdownCounts']))
            };

            // Expenses Monthly Trend Chart
            const expensesElement = document.querySelector('#expensesChart');
            if (expensesElement) {
                const totalExpensesSum = extraData.expenses.reduce((a, b) => a + b, 0);
                if (totalExpensesSum > 0) {
                    new ApexCharts(expensesElement, {
                        chart: { ...common, type: 'area', height: 460 },
                        series: [{ name: @json($isArabic ? 'إجمالي المصروفات' : 'Total Expenses'), data: extraData.expenses }],
                        colors: ['#ef4444'],
                        stroke: { curve: 'smooth', width: 3 },
                        fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.04 } },
                        dataLabels: { enabled: false },
                        grid: { borderColor: grid, strokeDashArray: 4, padding: { top: 30, right: 50, bottom: 65, left: 50 } },
                        xaxis: { categories: data.labels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { rotate: -45, rotateAlways: true, minHeight: 70, style: { colors: text, fontSize: '11px', fontWeight: 600 } } },
                        yaxis: { min: 0, forceNiceScale: true, labels: { formatter: value => Math.round(value).toLocaleString() + ' ' + labels.currency } },
                        noData
                    }).render();
                } else {
                    expensesElement.classList.add('empty-state');
                    expensesElement.style.minHeight = '460px';
                    expensesElement.style.display = 'grid';
                    expensesElement.style.placeItems = 'center';
                    expensesElement.textContent = @json($copy['noData']);
                }
            }

            // Expense Categories Donut Chart
            const categoryElement = document.querySelector('#expenseCategoryChart');
            if (categoryElement) {
                const totalCatExpenses = extraData.expenseCategoryTotals.reduce((a, b) => a + b, 0);
                if (totalCatExpenses > 0) {
                    new ApexCharts(categoryElement, {
                        chart: { ...common, type: 'donut', height: 460 },
                        series: extraData.expenseCategoryTotals,
                        labels: extraData.expenseCategories,
                        colors: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#ec4899', '#6366f1'],
                        stroke: { width: 0 },
                        dataLabels: { enabled: false },
                        legend: { position: 'bottom' },
                        plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, label: @json($isArabic ? 'المصروفات' : 'Expenses'), formatter: chart => chart.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString() + ' ' + labels.currency } } } } },
                        noData
                    }).render();
                } else {
                    categoryElement.classList.add('empty-state');
                    categoryElement.style.minHeight = '460px';
                    categoryElement.style.display = 'grid';
                    categoryElement.style.placeItems = 'center';
                    categoryElement.textContent = @json($copy['noData']);
                }
            }

            // Expiring Subscriptions Countdown Chart (10 to 0 days)
            const countdownElement = document.querySelector('#expiringCountdownChart');
            if (countdownElement) {
                const totalExpiringSum = extraData.expiringCountdownCounts.reduce((a, b) => a + b, 0);
                if (totalExpiringSum > 0) {
                    new ApexCharts(countdownElement, {
                        chart: { ...common, type: 'bar', height: 460 },
                        series: [{ name: @json($isArabic ? 'عدد الطلاب' : 'Subscribers Count'), data: extraData.expiringCountdownCounts }],
                        colors: ['#f43f5e'],
                        plotOptions: { bar: { borderRadius: 6, columnWidth: '45%', distributed: true } },
                        dataLabels: { enabled: true, style: { colors: ['#ffffff'], fontSize: '11px', fontWeight: 700 } },
                        grid: { borderColor: grid, strokeDashArray: 4, padding: { top: 25, right: 35, bottom: 65, left: 35 } },
                        xaxis: { categories: extraData.expiringCountdownLabels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { rotate: -40, rotateAlways: true, minHeight: 70, style: { colors: text, fontSize: '11px', fontWeight: 600 } } },
                        yaxis: { min: 0, forceNiceScale: true },
                        legend: { show: false },
                        noData
                    }).render();
                } else {
                    countdownElement.classList.add('empty-state');
                    countdownElement.style.minHeight = '460px';
                    countdownElement.style.display = 'grid';
                    countdownElement.style.placeItems = 'center';
                    countdownElement.textContent = @json($copy['noData']);
                }
            }

            flatpickr('#start_date', { dateFormat: 'Y-m-d' });
            flatpickr('#end_date', { dateFormat: 'Y-m-d' });
            const button = document.getElementById('filter');
            button.addEventListener('click', async function () {
                button.disabled = true;
                const params = new URLSearchParams({ start_date: document.getElementById('start_date').value, end_date: document.getElementById('end_date').value });
                try {
                    const response = await fetch(`{{ route('academy.filter-bookings') }}?${params}`, { headers: { Accept: 'application/json' } });
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    const result = await response.json();
                    document.getElementById('total_booking_balance').textContent = `${Number(result.total_booking_balance || 0).toLocaleString()} ${labels.currency}`;
                    document.getElementById('total_booking_refund_count').textContent = Number(result.total_booking_refund_count || 0).toLocaleString();
                    document.getElementById('total_booking_refund_amount').textContent = `${Number(result.total_booking_refund_amount || 0).toLocaleString()} ${labels.currency}`;
                    document.getElementById('total_booking_count').textContent = Number(result.total_booking_count || 0).toLocaleString();
                } catch (error) {
                    console.error('[Hagzz Academy Dashboard] Booking filter failed', error);
                } finally { button.disabled = false; }
            });
            button.click();
            if (window.feather) feather.replace();
        });
    </script>
@endpush
