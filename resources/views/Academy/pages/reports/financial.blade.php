@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'مركز التقارير المالية والتشغيلية' : 'Financial & Operations Center')

@push('css')
    <link rel="stylesheet" href="{{ asset('assetsAdmin/src/assets/css/academy-financial-reports.css') }}">
    <style>
        .fr-nav-tabs {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            border-bottom: 2px solid var(--fr-line, #e4ebe7);
            padding-bottom: 8px;
            overflow-x: auto;
        }
        .fr-nav-tab {
            background: #fff;
            border: 1px solid var(--fr-line, #e4ebe7);
            border-radius: 12px;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 700;
            color: var(--fr-muted, #68766f);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .fr-nav-tab.active {
            background: var(--fr-green, #0e5a3f);
            color: #fff;
            border-color: var(--fr-green, #0e5a3f);
            box-shadow: 0 4px 12px rgba(14, 90, 63, 0.2);
        }
        .fr-nav-tab:hover:not(.active) {
            background: #f0f7f3;
            color: var(--fr-green, #0e5a3f);
        }
        .fr-tab-pane {
            display: none;
            margin-top: 18px;
        }
        .fr-tab-pane.active {
            display: block;
        }
        .fr-badge-type {
            background: #edf6f1;
            color: #0e5a3f;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }
        .fr-progress-bar {
            height: 8px;
            background: #e4ebe7;
            border-radius: 999px;
            overflow: hidden;
            margin-top: 4px;
        }
        .fr-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            border-radius: 999px;
        }
        .fr-print-header {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .middle-content, .middle-content * {
                visibility: visible;
            }
            .middle-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .fr-filter-panel, .fr-filter-actions, .sidebarCollapse, .secondary-nav, .fr-nav-tabs, .btn-print-hide, .fr-menu {
                display: none !important;
            }
            .fr-hero {
                background: #0b402f !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 15px !important;
                border-radius: 0 !important;
            }
            .fr-tab-pane {
                display: block !important;
                page-break-after: always;
            }
            .fr-tab-pane:last-child {
                page-break-after: auto;
            }
            .fr-print-header {
                display: block;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 2px solid #000;
            }
            .fr-table-wrap {
                overflow: visible !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            th, td {
                border: 1px solid #ddd !important;
                padding: 8px !important;
                font-size: 11px !important;
            }
            th {
                background: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
@endpush

@php
    $ar = app()->getLocale() === 'ar';
    $copy = $ar ? [
        'title' => 'مركز التقارير المالية والتشغيلية', 
        'subtitle' => 'صورة مفصلة ومطبوخة لكل فرع، المدربين، مستحقات الطلاب، المجموعات، المعسكرات، البطولات، أنواع الرياضة وطرق التحصيل.',
        'billed' => 'إجمالي المستحق', 'collected' => 'إجمالي المحصل', 'remaining' => 'إجمالي المتبقي',
        'rate' => 'نسبة التحصيل', 'records' => 'سجل مالي', 'cancelled' => 'ملغي', 'currency' => 'ج.م',
        'filters' => 'تصفية التصفية المتقدمة', 'from' => 'من تاريخ', 'to' => 'إلى تاريخ', 'source' => 'مصدر التقرير',
        'branch' => 'الفرع / المقر', 'sport' => 'الرياضة / اللعبة', 'payment_method' => 'طريقة التحصيل',
        'payment' => 'حالة السداد', 'search' => 'بحث بالاسم، الهاتف، المرجع', 'apply' => 'تطبيق الفلترة', 'reset' => 'إلغاء الفلاتر',
        'print' => 'طباعة التقرير', 'all' => 'الكل / جميع الفروع', 'subscriptions' => 'حسابات واشتراكات الطلاب', 
        'training' => 'حجوزات التدريبات', 'venues' => 'حجوزات الملاعب', 'camps' => 'معسكرات تدريبية',
        'paid' => 'مدفوع', 'partial' => 'مدفوع جزئيًا', 'unpaid' => 'غير مدفوع', 'export' => 'تصدير CSV',
        'customer' => 'العميل / الطالب', 'service' => 'الخدمة / المجموعة', 'date' => 'التاريخ', 'amount' => 'المستحق',
        'paidAmount' => 'المحصل', 'remainingAmount' => 'المتبقي', 'method' => 'وسيلة الدفع', 'status' => 'الحالة',
        'reference' => 'المرجع', 'phone' => 'الهاتف', 'noData' => 'لا توجد بيانات مطابقة للفلاتر الحالية.',

        // Tabs & Headers
        'tab_branches' => 'تقرير الفروع والمجمع',
        'tab_groups' => 'تقرير المجموعات (حسب الرياضة والفرع)',
        'tab_coaches' => 'معدل تشغيل المدربين والمستحقات',
        'tab_dues' => 'مستحقات الطلاب (الدفع الجزئي)',
        'tab_camps' => 'تقرير المعسكرات المالي',
        'tab_competitions' => 'تقرير المباريات والبطولات',
        'tab_sports' => 'تقارير الرياضات',
        'tab_payments' => 'طرق التحصيل',

        // Section Headers
        'branches_head' => 'التقرير المالي المفصل للفروع والتقرير المجمع',
        'branches_sub' => 'عرض الإيرادات والتحصيلات والمصروفات لكل فرع على حدة والفرع المجمع لجميع الفروع',
        'combined_branch' => 'المجمع الشامل (كل الفروع)',
        'direct_expenses' => 'المصروفات المباشرة',
        'net_income' => 'صافي الدخل (الأرباح)',

        'groups_head' => 'التقرير المالي والتشغيلي للمجموعات التدريبية',
        'groups_sub' => 'عرض التفاصيل المالية والتشغيلية لكل مجموعة مقسمة طبقاً للرياضة والفرع',
        'group_name' => 'اسم المجموعة',
        'capacity_enrolled' => 'الطلاب / السعة',
        'fill_rate' => 'نسبة الإشغال (%)',

        'coaches_head' => 'تقرير معدل تشغيل المدربين ومستحقاتهم',
        'coaches_sub' => 'معدل إشغال طاقات المدربين، إجمالي الدخل المحقق، نظام الاستحقاق (مرتب / نسبة) وتكلفة كل مدرب',
        'coach_name' => 'اسم المدرب',
        'sports_assigned' => 'الرياضات المسندة',
        'groups_count' => 'المجموعات/التدريبات',
        'comp_system' => 'نظام الاستحقاق',
        'coach_cost' => 'مستحق المدرب (التكلفة)',
        'net_profit' => 'صافي الربح المحقق',

        'dues_head' => 'تقرير مستحقات الطلاب (الدفع الجزئي والغير مدفوع)',
        'dues_sub' => 'حصر جميع المبالغ المتبقية على الطلاب للمتابعة الفورية والتواصل',
        'student_name' => 'اسم الطالب / العميل',
        'dues_remaining' => 'المتبقي على الطالب',

        'camps_head' => 'التقرير المالي والتشغيلي للمعسكرات التدريبية',
        'camps_sub' => 'تحليل الإيرادات والمكاسب والمصروفات الإجمالية والصافية لجميع المعسكرات',
        'camp_title' => 'عنوان المعسكر',
        'camp_location' => 'المكان والدولة',
        'camp_dates' => 'الفترة والتاريخ',
        'camp_profit' => 'صافي أرباح المعسكر',

        'comp_head' => 'تقرير البطولات والمنافسات والمباريات',
        'comp_sub' => 'سجل كافة المباريات والبطولات التي خاضها فريق الأكاديمية والنتائج واللاعبين المشاركين',
        'comp_date' => 'التاريخ والوقت',
        'home_team' => 'فريق الأكاديمية (المجموعة)',
        'opponent' => 'الفريق المنافس',
        'venue' => 'الملعب / المكان',
        'score' => 'النتيجة',
        'players_count' => 'اللاعبين المشاركين',
        'notes' => 'ملاحظات / المركز',

        'sports_head' => 'تقارير الأداء المالي حسب أنواع الرياضة',
        'sports_sub' => 'تحليل الإيرادات والاشتراكات المقسمة حسب كل لعبة رياضية في الأكاديمية',
        'sport_name' => 'اسم الرياضة / اللعبة',
        'active_trainings' => 'عدد التدريبات النشطة',

        'payments_head' => 'تقرير مالي حسب وسيلة وطريقة التحصيل',
        'payments_sub' => 'تحليل المبالغ المحصلة مقسمة حسب كاش، فيزا/شبكة، تحويل بنكي، ودفع أونلاين',
        'pm_method' => 'طريقة التحصيل / الوسيلة',
        'pm_total' => 'إجمالي المحصل بهذه الوسيلة',
        'official_report' => 'تقرير رسمي معتمد',
        'period_label' => 'الفترة',
    ] : [
        'title' => 'Financial & Operations Center', 
        'subtitle' => 'Comprehensive detailed reports for branches, coaches, student dues, groups, camps, matches, sports & payment methods.',
        'billed' => 'Total Billed', 'collected' => 'Total Collected', 'remaining' => 'Total Outstanding',
        'rate' => 'Collection Rate', 'records' => 'Records', 'cancelled' => 'Cancelled', 'currency' => 'EGP',
        'filters' => 'Advanced Report Filters', 'from' => 'From Date', 'to' => 'To Date', 'source' => 'Report Source',
        'branch' => 'Branch / Location', 'sport' => 'Sport / Game', 'payment_method' => 'Collection Method',
        'payment' => 'Payment Status', 'search' => 'Search name, phone, ref', 'apply' => 'Apply Filters', 'reset' => 'Reset Filters',
        'print' => 'Print Report', 'all' => 'All / Combined', 'subscriptions' => 'Student Subscriptions', 
        'training' => 'Training Bookings', 'venues' => 'Venue Bookings', 'camps' => 'Training Camps',
        'paid' => 'Paid', 'partial' => 'Partially Paid', 'unpaid' => 'Unpaid', 'export' => 'Export CSV',
        'customer' => 'Customer / Student', 'service' => 'Service / Group', 'date' => 'Date', 'amount' => 'Billed',
        'paidAmount' => 'Collected', 'remainingAmount' => 'Outstanding', 'method' => 'Payment Method', 'status' => 'Status',
        'reference' => 'Reference', 'phone' => 'Phone', 'noData' => 'No records found matching current filters.',

        // Tabs & Headers
        'tab_branches' => 'Branches & Combined',
        'tab_groups' => 'Groups Report (by Sport & Branch)',
        'tab_coaches' => 'Coach Utilization & Cost',
        'tab_dues' => 'Student Dues (Partial/Unpaid)',
        'tab_camps' => 'Camps Financial Report',
        'tab_competitions' => 'Matches & Competitions',
        'tab_sports' => 'Sports Breakdown',
        'tab_payments' => 'Collection Methods',

        // Section Headers
        'branches_head' => 'Detailed Branch & Combined Financial Report',
        'branches_sub' => 'Revenue, collections, direct expenses and net profit breakdown per branch and combined total',
        'combined_branch' => 'Combined Total (All Branches)',
        'direct_expenses' => 'Direct Expenses',
        'net_income' => 'Net Income (Profit)',

        'groups_head' => 'Training Groups Financial & Operational Report',
        'groups_sub' => 'Financial and operational performance per group broken down by sport and branch',
        'group_name' => 'Group Name',
        'capacity_enrolled' => 'Enrolled / Capacity',
        'fill_rate' => 'Fill Rate (%)',

        'coaches_head' => 'Coach Utilization & Payroll Report',
        'coaches_sub' => 'Capacity utilization, generated revenue, compensation model (Salary/Percentage) & coach cost',
        'coach_name' => 'Coach Name',
        'sports_assigned' => 'Assigned Sports',
        'groups_count' => 'Groups / Classes',
        'comp_system' => 'Compensation System',
        'coach_cost' => 'Coach Cost',
        'net_profit' => 'Net Revenue Contribution',

        'dues_head' => 'Student Outstanding Dues Report (Partial & Unpaid)',
        'dues_sub' => 'List of all outstanding balances owed by students for immediate follow-up',
        'student_name' => 'Student / Customer Name',
        'dues_remaining' => 'Outstanding Amount',

        'camps_head' => 'Camps Financial & Operational Report',
        'camps_sub' => 'Revenue, expenses and net profit breakdown for training camps',
        'camp_title' => 'Camp Title',
        'camp_location' => 'Location & Country',
        'camp_dates' => 'Period & Dates',
        'camp_profit' => 'Net Camp Profit',

        'comp_head' => 'Matches & Competitions Report',
        'comp_sub' => 'History of matches and tournaments played by academy teams with scores and player counts',
        'comp_date' => 'Date & Time',
        'home_team' => 'Academy Team / Group',
        'opponent' => 'Opponent Team',
        'venue' => 'Venue / Field',
        'score' => 'Score',
        'players_count' => 'Participating Players',
        'notes' => 'Notes / Ranking',

        'sports_head' => 'Financial Performance by Sport',
        'sports_sub' => 'Revenue and subscription breakdown per sport in the academy',
        'sport_name' => 'Sport / Game Name',
        'active_trainings' => 'Active Trainings Count',

        'payments_head' => 'Financial Report by Payment Collection Method',
        'payments_sub' => 'Revenue breakdown by Cash, Card/POS, Bank Transfer and Online payment',
        'pm_method' => 'Collection Method',
        'pm_total' => 'Total Collected by Method',
        'official_report' => 'Certified Official Report',
        'period_label' => 'Period',
    ];

    $money = fn ($value) => number_format((float) $value, 2);
@endphp

@section('content')
    <div class="middle-content container-xxl p-0">
        <main class="financial-reports" dir="{{ $ar ? 'rtl' : 'ltr' }}">

            <!-- Printable Official Header -->
            <div class="fr-print-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2>{{ auth('academy')->user()->name ?? 'HAGZZ BUSINESS ACADEMY' }}</h2>
                        <p style="margin:4px 0 0; color:#555;">{{ $copy['title'] }} — {{ now()->format('Y-m-d H:i') }}</p>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-weight: bold; display: block;">{{ $copy['official_report'] }}</span>
                        <small>{{ $copy['period_label'] }}: {{ $filters['start_date'] ?: ($ar ? 'البداية' : 'Start') }} {{ $ar ? 'إلى' : 'to' }} {{ $filters['end_date'] ?: ($ar ? 'الآن' : 'Now') }}</small>
                    </div>
                </div>
            </div>

            <!-- Hero Header -->
            <header class="fr-hero">
                <div class="fr-hero-copy">
                    <button type="button" class="sidebarCollapse fr-menu" aria-label="Open menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div>
                        <span class="fr-kicker">HAGZZ PARTNER PORTAL</span>
                        <h1>{{ $copy['title'] }}</h1>
                        <p>{{ $copy['subtitle'] }}</p>
                    </div>
                </div>
                <div class="fr-hero-meta">
                    <strong>{{ number_format($summary['records']) }}</strong>
                    <span>{{ $copy['records'] }}</span>
                    <button type="button" onclick="window.print()" class="btn btn-light btn-sm mt-2 btn-print-hide fw-bold">
                        <i class="fa-solid fa-print me-1"></i> {{ $copy['print'] }}
                    </button>
                </div>
            </header>

            <!-- Global KPIs -->
            <section class="fr-kpis">
                <article class="fr-kpi is-billed">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <div>
                        <span>{{ $copy['billed'] }}</span>
                        <strong>{{ $money($summary['billed']) }}</strong>
                        <small>{{ $copy['currency'] }}</small>
                    </div>
                </article>
                <article class="fr-kpi is-collected">
                    <i class="fa-solid fa-circle-check"></i>
                    <div>
                        <span>{{ $copy['collected'] }}</span>
                        <strong>{{ $money($summary['collected']) }}</strong>
                        <small>{{ $copy['currency'] }}</small>
                    </div>
                </article>
                <article class="fr-kpi is-remaining">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <div>
                        <span>{{ $copy['remaining'] }}</span>
                        <strong>{{ $money($summary['remaining']) }}</strong>
                        <small>{{ $copy['currency'] }}</small>
                    </div>
                </article>
                <article class="fr-kpi is-rate">
                    <i class="fa-solid fa-chart-line"></i>
                    <div>
                        <span>{{ $copy['rate'] }}</span>
                        <strong>{{ number_format($summary['collection_rate'], 1) }}%</strong>
                        <small>{{ $summary['collection_rate'] >= 80 ? ($ar ? 'ممتاز' : 'Excellent') : ($ar ? 'تحتاج متابعة' : 'Needs follow-up') }}</small>
                    </div>
                </article>
            </section>

            <!-- Filter Panel -->
            <section class="fr-filter-panel btn-print-hide">
                <div class="fr-section-heading">
                    <div><span>{{ $copy['filters'] }}</span><h2>{{ $ar ? 'خيارات التصفية المتقدمة' : 'Advanced Filters' }}</h2></div>
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <form method="GET" action="{{ route('academy.report.overview') }}" class="fr-filter-form">
                    <label>
                        <span>{{ $copy['from'] }}</span>
                        <input type="date" name="start_date" value="{{ $filters['start_date'] }}">
                    </label>
                    <label>
                        <span>{{ $copy['to'] }}</span>
                        <input type="date" name="end_date" value="{{ $filters['end_date'] }}">
                    </label>
                    <label>
                        <span>{{ $copy['branch'] }}</span>
                        <select name="branch_id">
                            <option value="">{{ $copy['all'] }}</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" @selected($filters['branch_id'] == $b->id)>
                                    {{ $b->address ?: ($ar ? 'فرع #' : 'Branch #') . $b->id }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>{{ $copy['sport'] }}</span>
                        <select name="sport_id">
                            <option value="">{{ $copy['all'] }}</option>
                            @foreach($sports as $s)
                                <option value="{{ $s->id }}" @selected($filters['sport_id'] == $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>{{ $copy['payment_method'] }}</span>
                        <select name="payment_method">
                            <option value="">{{ $copy['all'] }}</option>
                            <option value="cash" @selected($filters['payment_method'] == 'cash')>{{ $ar ? 'كاش (نقداً)' : 'Cash' }}</option>
                            <option value="card" @selected($filters['payment_method'] == 'card')>{{ $ar ? 'بطاقة / شبكة فيزا' : 'Card / POS' }}</option>
                            <option value="bank_transfer" @selected($filters['payment_method'] == 'bank_transfer')>{{ $ar ? 'تحويل بنكي' : 'Bank Transfer' }}</option>
                            <option value="online" @selected($filters['payment_method'] == 'online')>{{ $ar ? 'دفع أونلاين' : 'Online Payment' }}</option>
                            <option value="other" @selected($filters['payment_method'] == 'other')>{{ $ar ? 'أخرى' : 'Other' }}</option>
                        </select>
                    </label>
                    <label>
                        <span>{{ $copy['source'] }}</span>
                        <select name="source">
                            <option value="all">{{ $copy['all'] }}</option>
                            <option value="subscriptions" @selected($filters['source'] == 'subscriptions')>{{ $copy['subscriptions'] }}</option>
                            <option value="training" @selected($filters['source'] == 'training')>{{ $copy['training'] }}</option>
                            <option value="venues" @selected($filters['source'] == 'venues')>{{ $copy['venues'] }}</option>
                            <option value="camps" @selected($filters['source'] == 'camps')>{{ $copy['camps'] }}</option>
                        </select>
                    </label>
                    <label>
                        <span>{{ $copy['payment'] }}</span>
                        <select name="payment_status">
                            <option value="all">{{ $copy['all'] }}</option>
                            <option value="paid" @selected($filters['payment_status'] == 'paid')>{{ $copy['paid'] }}</option>
                            <option value="partial" @selected($filters['payment_status'] == 'partial')>{{ $copy['partial'] }}</option>
                            <option value="unpaid" @selected($filters['payment_status'] == 'unpaid')>{{ $copy['unpaid'] }}</option>
                        </select>
                    </label>
                    <label class="fr-search">
                        <span>{{ $copy['search'] }}</span>
                        <div>
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="search" name="search" value="{{ $filters['search'] }}" maxlength="100">
                        </div>
                    </label>
                    <div class="fr-filter-actions">
                        <button type="submit"><i class="fa-solid fa-check"></i> {{ $copy['apply'] }}</button>
                        <a href="{{ route('academy.report.overview') }}">{{ $copy['reset'] }}</a>
                    </div>
                </form>
            </section>

            <!-- Multi-Tab Navigation Header -->
            <div class="fr-nav-tabs btn-print-hide">
                <button type="button" class="fr-nav-tab active" onclick="switchTab('branches-tab', this)">
                    <i class="fa-solid fa-building"></i> {{ $copy['tab_branches'] }}
                </button>
                <button type="button" class="fr-nav-tab" onclick="switchTab('groups-tab', this)">
                    <i class="fa-solid fa-users-rectangle"></i> {{ $copy['tab_groups'] }}
                </button>
                <button type="button" class="fr-nav-tab" onclick="switchTab('coaches-tab', this)">
                    <i class="fa-solid fa-user-ninja"></i> {{ $copy['tab_coaches'] }}
                </button>
                <button type="button" class="fr-nav-tab" onclick="switchTab('student-dues-tab', this)">
                    <i class="fa-solid fa-file-invoice-dollar"></i> {{ $copy['tab_dues'] }}
                </button>
                <button type="button" class="fr-nav-tab" onclick="switchTab('camps-report-tab', this)">
                    <i class="fa-solid fa-campground"></i> {{ $copy['tab_camps'] }}
                </button>
                <button type="button" class="fr-nav-tab" onclick="switchTab('competitions-tab', this)">
                    <i class="fa-solid fa-trophy"></i> {{ $copy['tab_competitions'] }}
                </button>
                <button type="button" class="fr-nav-tab" onclick="switchTab('sports-tab', this)">
                    <i class="fa-solid fa-volleyball"></i> {{ $copy['tab_sports'] }}
                </button>
                <button type="button" class="fr-nav-tab" onclick="switchTab('payments-tab', this)">
                    <i class="fa-solid fa-wallet"></i> {{ $copy['tab_payments'] }}
                </button>
            </div>

            <!-- TAB 1: Branch Financial Reports -->
            <div id="branches-tab" class="fr-tab-pane active">
                <article class="fr-report-panel">
                    <header>
                        <div>
                            <i class="fa-solid fa-building"></i>
                            <div>
                                <h2>{{ $copy['branches_head'] }}</h2>
                                <p>{{ $copy['branches_sub'] }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="window.print()" class="btn btn-outline-success btn-sm btn-print-hide fw-bold">
                            <i class="fa-solid fa-print me-1"></i> {{ $copy['print'] }}
                        </button>
                    </header>
                    <div class="fr-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ $copy['branch'] }}</th>
                                    <th>{{ $copy['billed'] }}</th>
                                    <th>{{ $copy['collected'] }}</th>
                                    <th>{{ $copy['remaining'] }}</th>
                                    <th>{{ $copy['direct_expenses'] }}</th>
                                    <th>{{ $copy['net_income'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background: #e8f5ed; font-weight: bold; border-bottom: 2px solid #0e5a3f;">
                                    <td><i class="fa-solid fa-globe text-success me-1"></i> <strong>{{ $copy['combined_branch'] }}</strong></td>
                                    <td><strong>{{ $money($branchReportData['combined']['billed']) }} {{ $copy['currency'] }}</strong></td>
                                    <td class="is-positive"><strong>{{ $money($branchReportData['combined']['collected']) }} {{ $copy['currency'] }}</strong></td>
                                    <td class="is-negative"><strong>{{ $money($branchReportData['combined']['remaining']) }} {{ $copy['currency'] }}</strong></td>
                                    <td><strong>{{ $money($branchReportData['combined']['expenses']) }} {{ $copy['currency'] }}</strong></td>
                                    <td style="color: #0e5a3f; font-size: 14px;"><strong>{{ $money($branchReportData['combined']['net_income']) }} {{ $copy['currency'] }}</strong></td>
                                </tr>

                                @forelse($branchReportData['items'] as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item['name'] }}</strong>
                                            <small>{{ $item['city'] }} {{ $item['area'] ? ' - '.$item['area'] : '' }}</small>
                                        </td>
                                        <td>{{ $money($item['billed']) }} {{ $copy['currency'] }}</td>
                                        <td class="is-positive">{{ $money($item['collected']) }} {{ $copy['currency'] }}</td>
                                        <td class="is-negative">{{ $money($item['remaining']) }} {{ $copy['currency'] }}</td>
                                        <td>{{ $money($item['expenses']) }} {{ $copy['currency'] }}</td>
                                        <td>
                                            <strong class="{{ $item['net_income'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $money($item['net_income']) }} {{ $copy['currency'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="fr-empty">{{ $copy['noData'] }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <!-- TAB 2: Training Groups Financial Report -->
            <div id="groups-tab" class="fr-tab-pane">
                <article class="fr-report-panel">
                    <header>
                        <div>
                            <i class="fa-solid fa-users-rectangle"></i>
                            <div>
                                <h2>{{ $copy['groups_head'] }}</h2>
                                <p>{{ $copy['groups_sub'] }}</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 btn-print-hide">
                            <a href="{{ route('academy.report.overview.export', 'groups') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-file-csv me-1"></i> {{ $copy['export'] }}
                            </a>
                            <button type="button" onclick="window.print()" class="btn btn-outline-success btn-sm fw-bold">
                                <i class="fa-solid fa-print me-1"></i> {{ $copy['print'] }}
                            </button>
                        </div>
                    </header>
                    <div class="fr-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ $copy['group_name'] }}</th>
                                    <th>{{ $copy['sport'] }}</th>
                                    <th>{{ $copy['branch'] }}</th>
                                    <th>{{ $copy['coach_name'] }}</th>
                                    <th>{{ $copy['capacity_enrolled'] }}</th>
                                    <th>{{ $copy['fill_rate'] }}</th>
                                    <th>{{ $copy['billed'] }}</th>
                                    <th>{{ $copy['collected'] }}</th>
                                    <th>{{ $copy['remaining'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupsReportData['items'] as $gItem)
                                    <tr>
                                        <td>
                                            <strong>{{ $gItem['name'] }}</strong>
                                            <small><i class="fa-solid fa-calendar-days me-1"></i>{{ $gItem['days'] }}</small>
                                        </td>
                                        <td><span class="fr-badge-type">{{ $gItem['sport_name'] }}</span></td>
                                        <td>{{ $gItem['branch_name'] }}</td>
                                        <td>{{ $gItem['coach_name'] }}</td>
                                        <td>
                                            <strong>{{ $gItem['enrolled'] }}</strong> / {{ $gItem['capacity'] }}
                                        </td>
                                        <td>
                                            <strong>{{ $gItem['fill_rate'] }}%</strong>
                                            <div class="fr-progress-bar">
                                                <div class="fr-progress-fill" style="width: {{ min(100, $gItem['fill_rate']) }}%;"></div>
                                            </div>
                                        </td>
                                        <td>{{ $money($gItem['billed']) }} {{ $copy['currency'] }}</td>
                                        <td class="is-positive">{{ $money($gItem['collected']) }} {{ $copy['currency'] }}</td>
                                        <td class="is-negative fw-bold">{{ $money($gItem['remaining']) }} {{ $copy['currency'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="fr-empty">{{ $copy['noData'] }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <!-- TAB 3: Coach Utilization & Payroll Report -->
            <div id="coaches-tab" class="fr-tab-pane">
                <article class="fr-report-panel">
                    <header>
                        <div>
                            <i class="fa-solid fa-user-ninja"></i>
                            <div>
                                <h2>{{ $copy['coaches_head'] }}</h2>
                                <p>{{ $copy['coaches_sub'] }}</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 btn-print-hide">
                            <a href="{{ route('academy.report.overview.export', 'coaches') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-file-csv me-1"></i> {{ $copy['export'] }}
                            </a>
                            <button type="button" onclick="window.print()" class="btn btn-outline-success btn-sm fw-bold">
                                <i class="fa-solid fa-print me-1"></i> {{ $copy['print'] }}
                            </button>
                        </div>
                    </header>
                    <div class="fr-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ $copy['coach_name'] }}</th>
                                    <th>{{ $copy['sports_assigned'] }}</th>
                                    <th>{{ $copy['groups_count'] }}</th>
                                    <th>{{ $copy['capacity_enrolled'] }}</th>
                                    <th>{{ $copy['fill_rate'] }}</th>
                                    <th>{{ $copy['collected'] }}</th>
                                    <th>{{ $copy['comp_system'] }}</th>
                                    <th>{{ $copy['coach_cost'] }}</th>
                                    <th>{{ $copy['net_profit'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coachReportData['items'] as $coach)
                                    <tr>
                                        <td>
                                            <strong>{{ $coach['name'] }}</strong>
                                            <small><i class="fa-solid fa-phone me-1"></i>{{ $coach['phone'] }}</small>
                                        </td>
                                        <td><span class="fr-badge-type">{{ $coach['sports_list'] ?: ($ar ? 'عام' : 'General') }}</span></td>
                                        <td><strong>{{ $coach['assigned_groups_count'] }}</strong></td>
                                        <td>
                                            {{ $coach['enrolled_students_count'] }} / {{ $coach['capacity_sum'] }}
                                        </td>
                                        <td>
                                            <strong>{{ $coach['utilization_rate'] }}%</strong>
                                            <div class="fr-progress-bar">
                                                <div class="fr-progress-fill" style="width: {{ min(100, $coach['utilization_rate']) }}%;"></div>
                                            </div>
                                        </td>
                                        <td class="is-positive">{{ $money($coach['total_collected']) }} {{ $copy['currency'] }}</td>
                                        <td>
                                            <span class="badge {{ $coach['compensation_type'] === 'session' ? 'bg-primary' : ($coach['compensation_type'] === 'percentage' ? 'text-white' : 'bg-success') }}" style="{{ $coach['compensation_type'] === 'percentage' ? 'background:#7e22ce !important;' : '' }}">
                                                {{ $coach['compensation_label'] }}
                                            </span>
                                        </td>
                                        <td class="text-danger fw-bold">{{ $money($coach['coach_cost']) }} {{ $copy['currency'] }}</td>
                                        <td>
                                            <strong class="{{ $coach['net_revenue'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $money($coach['net_revenue']) }} {{ $copy['currency'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="fr-empty">{{ $copy['noData'] }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <!-- TAB 4: Student Outstanding Dues Report -->
            <div id="student-dues-tab" class="fr-tab-pane">
                <article class="fr-report-panel">
                    <header>
                        <div>
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <div>
                                <h2>{{ $copy['dues_head'] }}</h2>
                                <p>{{ $copy['dues_sub'] }}</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 btn-print-hide">
                            <a href="{{ route('academy.report.overview.export', 'student_dues') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-file-csv me-1"></i> {{ $copy['export'] }}
                            </a>
                            <button type="button" onclick="window.print()" class="btn btn-outline-success btn-sm fw-bold">
                                <i class="fa-solid fa-print me-1"></i> {{ $copy['print'] }}
                            </button>
                        </div>
                    </header>
                    <div class="fr-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ $copy['reference'] }}</th>
                                    <th>{{ $copy['student_name'] }}</th>
                                    <th>{{ $copy['phone'] }}</th>
                                    <th>{{ $copy['service'] }}</th>
                                    <th>{{ $copy['date'] }}</th>
                                    <th>{{ $copy['status'] }}</th>
                                    <th>{{ $copy['billed'] }}</th>
                                    <th>{{ $copy['paidAmount'] }}</th>
                                    <th>{{ $copy['dues_remaining'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($studentDuesReportData['items'] as $due)
                                    <tr>
                                        <td>
                                            <span class="fr-badge-type">{{ $due['source_label'] }}</span>
                                            <small>{{ $due['reference'] }}</small>
                                        </td>
                                        <td><strong>{{ $due['student_name'] }}</strong></td>
                                        <td>{{ $due['phone'] }}</td>
                                        <td>{{ $due['service_name'] }}</td>
                                        <td>{{ $due['date'] }}</td>
                                        <td>
                                            <span class="fr-status {{ $due['payment_status'] === 'مدفوع جزئياً' || $due['payment_status'] === 'Partially paid' ? 'is-partial' : 'is-unpaid' }}">
                                                {{ $due['payment_status'] }}
                                            </span>
                                        </td>
                                        <td>{{ $money($due['amount']) }} {{ $copy['currency'] }}</td>
                                        <td class="is-positive">{{ $money($due['paid_amount']) }} {{ $copy['currency'] }}</td>
                                        <td class="is-negative fw-bold" style="font-size: 13px;">
                                            {{ $money($due['remaining_amount']) }} {{ $copy['currency'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="fr-empty">{{ $copy['noData'] }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <!-- TAB 5: Camps Financial Report -->
            <div id="camps-report-tab" class="fr-tab-pane">
                <article class="fr-report-panel">
                    <header>
                        <div>
                            <i class="fa-solid fa-campground"></i>
                            <div>
                                <h2>{{ $copy['camps_head'] }}</h2>
                                <p>{{ $copy['camps_sub'] }}</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 btn-print-hide">
                            <a href="{{ route('academy.report.overview.export', 'camps_report') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-file-csv me-1"></i> {{ $copy['export'] }}
                            </a>
                            <button type="button" onclick="window.print()" class="btn btn-outline-success btn-sm fw-bold">
                                <i class="fa-solid fa-print me-1"></i> {{ $copy['print'] }}
                            </button>
                        </div>
                    </header>
                    <div class="fr-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ $copy['camp_title'] }}</th>
                                    <th>{{ $copy['sport'] }}</th>
                                    <th>{{ $copy['camp_location'] }}</th>
                                    <th>{{ $copy['camp_dates'] }}</th>
                                    <th>{{ $copy['capacity_enrolled'] }}</th>
                                    <th>{{ $copy['billed'] }}</th>
                                    <th>{{ $copy['collected'] }}</th>
                                    <th>{{ $copy['direct_expenses'] }}</th>
                                    <th>{{ $copy['camp_profit'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campsReportData['items'] as $cItem)
                                    <tr>
                                        <td><strong>{{ $cItem['title'] }}</strong></td>
                                        <td><span class="fr-badge-type">{{ $cItem['sport_name'] }}</span></td>
                                        <td>{{ $cItem['location'] }}</td>
                                        <td>{{ $cItem['dates'] }}</td>
                                        <td><strong>{{ $cItem['enrolled'] }}</strong> / {{ $cItem['capacity'] }}</td>
                                        <td>{{ $money($cItem['billed']) }} {{ $copy['currency'] }}</td>
                                        <td class="is-positive">{{ $money($cItem['collected']) }} {{ $copy['currency'] }}</td>
                                        <td class="text-danger">{{ $money($cItem['expenses']) }} {{ $copy['currency'] }}</td>
                                        <td>
                                            <strong class="{{ $cItem['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:13px;">
                                                {{ $money($cItem['net_profit']) }} {{ $copy['currency'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="fr-empty">{{ $copy['noData'] }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <!-- TAB 6: Matches & Competitions Report -->
            <div id="competitions-tab" class="fr-tab-pane">
                <article class="fr-report-panel">
                    <header>
                        <div>
                            <i class="fa-solid fa-trophy"></i>
                            <div>
                                <h2>{{ $copy['comp_head'] }}</h2>
                                <p>{{ $copy['comp_sub'] }}</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 btn-print-hide">
                            <a href="{{ route('academy.report.overview.export', 'competitions') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-file-csv me-1"></i> {{ $copy['export'] }}
                            </a>
                            <button type="button" onclick="window.print()" class="btn btn-outline-success btn-sm fw-bold">
                                <i class="fa-solid fa-print me-1"></i> {{ $copy['print'] }}
                            </button>
                        </div>
                    </header>
                    <div class="fr-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ $copy['comp_date'] }}</th>
                                    <th>{{ $copy['sport'] }}</th>
                                    <th>{{ $copy['home_team'] }}</th>
                                    <th>{{ $copy['opponent'] }}</th>
                                    <th>{{ $copy['venue'] }}</th>
                                    <th>{{ $copy['score'] }}</th>
                                    <th>{{ $copy['status'] }}</th>
                                    <th>{{ $copy['players_count'] }}</th>
                                    <th>{{ $copy['notes'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($competitionsReportData['items'] as $compItem)
                                    <tr>
                                        <td>
                                            <strong>{{ $compItem['date'] }}</strong>
                                            <small>{{ $compItem['time'] }}</small>
                                        </td>
                                        <td><span class="fr-badge-type">{{ $compItem['sport_name'] }}</span></td>
                                        <td><strong class="text-primary">{{ $compItem['home_team'] }}</strong></td>
                                        <td><strong class="text-secondary">{{ $compItem['opponent'] }}</strong></td>
                                        <td>{{ $compItem['venue'] }}</td>
                                        <td>
                                            <span class="badge bg-dark px-2 py-1" style="font-size:12px;">{{ $compItem['score'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $compItem['status'] === 'مكتملة' || $compItem['status'] === 'Completed' ? 'bg-success' : 'bg-info' }}">
                                                {{ $compItem['status'] }}
                                            </span>
                                        </td>
                                        <td><strong>{{ $compItem['players_count'] }}</strong></td>
                                        <td><small>{{ $compItem['notes'] }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="fr-empty">{{ $copy['noData'] }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <!-- TAB 7: Sports Breakdown Report -->
            <div id="sports-tab" class="fr-tab-pane">
                <article class="fr-report-panel">
                    <header>
                        <div>
                            <i class="fa-solid fa-volleyball"></i>
                            <div>
                                <h2>{{ $copy['sports_head'] }}</h2>
                                <p>{{ $copy['sports_sub'] }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="window.print()" class="btn btn-outline-success btn-sm btn-print-hide fw-bold">
                            <i class="fa-solid fa-print me-1"></i> {{ $copy['print'] }}
                        </button>
                    </header>
                    <div class="fr-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ $copy['sport_name'] }}</th>
                                    <th>{{ $copy['active_trainings'] }}</th>
                                    <th>{{ $copy['customer'] }}</th>
                                    <th>{{ $copy['billed'] }}</th>
                                    <th>{{ $copy['collected'] }}</th>
                                    <th>{{ $copy['remaining'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sportsReportData['items'] as $sportRow)
                                    <tr>
                                        <td><strong><i class="fa-solid fa-trophy text-warning me-1"></i> {{ $sportRow['name'] }}</strong></td>
                                        <td><strong>{{ $sportRow['trainings_count'] }}</strong></td>
                                        <td><strong>{{ $sportRow['students_count'] }}</strong></td>
                                        <td>{{ $money($sportRow['total_billed']) }} {{ $copy['currency'] }}</td>
                                        <td class="is-positive">{{ $money($sportRow['total_collected']) }} {{ $copy['currency'] }}</td>
                                        <td class="is-negative">{{ $money($sportRow['total_remaining']) }} {{ $copy['currency'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="fr-empty">{{ $copy['noData'] }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <!-- TAB 8: Payment Collection Methods Breakdown -->
            <div id="payments-tab" class="fr-tab-pane">
                <article class="fr-report-panel">
                    <header>
                        <div>
                            <i class="fa-solid fa-wallet"></i>
                            <div>
                                <h2>{{ $copy['payments_head'] }}</h2>
                                <p>{{ $copy['payments_sub'] }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="window.print()" class="btn btn-outline-success btn-sm btn-print-hide fw-bold">
                            <i class="fa-solid fa-print me-1"></i> {{ $copy['print'] }}
                        </button>
                    </header>
                    <div class="fr-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ $copy['pm_method'] }}</th>
                                    <th>{{ $copy['subscriptions'] }}</th>
                                    <th>{{ $copy['training'] }}</th>
                                    <th>{{ $copy['venues'] }}</th>
                                    <th>{{ $copy['pm_total'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentMethodReportData as $methodKey => $pm)
                                    <tr>
                                        <td>
                                            <strong style="color: {{ $pm['color'] }};">
                                                <i class="fa-solid {{ $pm['icon'] }} me-1"></i> {{ $pm['label'] }}
                                            </strong>
                                        </td>
                                        <td>{{ $money($pm['sub_paid']) }} {{ $copy['currency'] }}</td>
                                        <td>{{ $money($pm['inv_paid']) }} {{ $copy['currency'] }}</td>
                                        <td>{{ $money($pm['venue_paid']) }} {{ $copy['currency'] }}</td>
                                        <td class="is-positive fw-bold" style="font-size: 14px;">
                                            {{ $money($pm['total_collected']) }} {{ $copy['currency'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

        </main>
    </div>
@endsection

@push('js')
    <script>
        function switchTab(tabId, btn) {
            document.querySelectorAll('.fr-tab-pane').forEach(function(pane) {
                pane.classList.remove('active');
            });
            document.querySelectorAll('.fr-nav-tab').forEach(function(tab) {
                tab.classList.remove('active');
            });

            const targetPane = document.getElementById(tabId);
            if (targetPane) {
                targetPane.classList.add('active');
            }
            if (btn) {
                btn.classList.add('active');
            }
        }
    </script>
@endpush
