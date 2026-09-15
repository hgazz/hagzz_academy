@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'إدارة المصروفات وقيود الصرف وصافي الربح' : 'Expenses, Journal Entries & Net Profit')

@push('css')
    <link rel="stylesheet" href="{{ asset('assetsAdmin/src/assets/css/heroui-theme.css') }}">
    <style>
        .expense-card-metric {
            background: var(--heroui-surface, #ffffff);
            border: 1px solid var(--heroui-border, #e2e8f0);
            border-radius: var(--heroui-radius-lg, 18px);
            padding: 20px 24px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: var(--heroui-shadow-sm, 0 2px 8px rgba(0,0,0,0.04));
        }
        .expense-card-metric:hover {
            transform: translateY(-3px);
            box-shadow: var(--heroui-shadow-md, 0 8px 24px -4px rgba(0,0,0,0.08));
        }
        .expense-card-metric::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 4px;
            height: 100%;
        }
        .metric-revenue::before { background: #10b981; }
        .metric-expense::before { background: #ef4444; }
        .metric-profit::before { background: #6366f1; }
        .metric-coach::before { background: #06b6d4; }

        .jv-badge {
            font-family: monospace;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
            background: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
            border: 1px solid rgba(99, 102, 241, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .jv-badge:hover {
            background: rgba(99, 102, 241, 0.2);
            transform: scale(1.03);
        }

        .voucher-sheet {
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 24px;
            font-family: inherit;
        }
        .voucher-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .voucher-account-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
        }
    </style>
@endpush

@section('content')
@php($ar = app()->getLocale() === 'ar')

<div class="middle-content container-xxl p-0 heroui-wrapper">

    <!-- BREADCRUMBS -->
    <div class="secondary-nav mb-4">
        <div class="breadcrumbs-container">
            <header class="header navbar navbar-expand-sm">
                <a href="javascript:void(0);" class="btn-toggle sidebarCollapse"><i data-feather="menu"></i></a>
                <div class="d-flex breadcrumb-content">
                    <nav class="breadcrumb-style-one">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('academy.reports.financial') }}">{{ $ar ? 'التقارير المالية' : 'Financial Reports' }}</a></li>
                            <li class="breadcrumb-item active">{{ $ar ? 'إدارة المصروفات وقيود الصرف' : 'Expenses & Journal Entries' }}</li>
                        </ol>
                    </nav>
                </div>
            </header>
        </div>
    </div>

    <!-- HERO HEADER BANNER -->
    <div class="heroui-header-banner">
        <div>
            <h1 class="heroui-header-title">
                <span class="title-icon"><i class="fa-solid fa-scale-balanced text-primary"></i></span>
                {{ $ar ? 'المصروفات وقيود اليومية وصافي الأرباح' : 'Expenses, Journal Entries & Net Profit' }}
            </h1>
            <p class="heroui-header-subtitle">
                {{ $ar ? 'محرك مالي موحد يربط كافة إيرادات الأكاديمية (اشتراكات، حجوزات، ملاعب، معسكرات) بالمصروفات التشغيلية ومستحقات المدربين المسجلين والزائرين' : 'Unified financial engine linking all academy revenue streams with operational expenses, coach payables, and accounting vouchers' }}
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('academy.reports.financial') }}" class="heroui-btn heroui-btn-light">
                <i class="fa-solid fa-chart-pie text-info"></i>
                <span>{{ $ar ? 'التقرير المالي الشامل' : 'Full Financial Report' }}</span>
            </a>
            <button class="heroui-btn heroui-btn-light" data-bs-toggle="modal" data-bs-target="#newCategoryModal">
                <i class="fa-solid fa-folder-plus text-primary"></i>
                <span>{{ $ar ? 'تصنيف جديد' : 'New Category' }}</span>
            </button>
            <button class="heroui-btn heroui-btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>{{ $ar ? 'تسجيل قيد ومصروف جديد' : 'Record Expense & JV' }}</span>
            </button>
        </div>
    </div>

    <!-- FINANCIAL METRIC CARDS -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Collected Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="expense-card-metric metric-revenue">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">{{ $ar ? 'إجمالي الإيرادات المحصلة' : 'Total Revenue Collected' }}</span>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small">
                        <i class="fa-solid fa-check-circle me-1"></i> {{ $ar ? 'موحد 4 قنوات' : '4 Streams' }}
                    </span>
                </div>
                <h3 class="fw-bold text-success mb-1">{{ number_format($totalRevenue, 2) }} <small class="fs-6">{{ $academyCurrencySymbol }}</small></h3>
                <div class="small text-muted text-truncate mt-2" title="{{ $ar ? 'اشتراكات: ' . number_format($revenueStreams['subscriptions']['collected'], 2) . ' | حجوزات: ' . number_format($revenueStreams['trainings']['collected'], 2) . ' | ملاعب: ' . number_format($revenueStreams['venues']['collected'], 2) . ' | معسكرات: ' . number_format($revenueStreams['camps']['collected'], 2) : '' }}">
                    <i class="fa-solid fa-layer-group me-1 text-muted opacity-75"></i>
                    {{ $ar ? 'اشتراكات: ' . number_format($revenueStreams['subscriptions']['collected'], 0) . ' | حجوزات: ' . number_format($revenueStreams['trainings']['collected'], 0) : 'Subs: ' . number_format($revenueStreams['subscriptions']['collected'], 0) . ' | Bookings: ' . number_format($revenueStreams['trainings']['collected'], 0) }}
                </div>
            </div>
        </div>

        <!-- 2. Total Operational Expenses -->
        <div class="col-xl-3 col-md-6">
            <div class="expense-card-metric metric-expense">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">{{ $ar ? 'إجمالي المصروفات الصادرة' : 'Total Disbursed Expenses' }}</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 small">
                        <i class="fa-solid fa-arrow-trend-down me-1"></i> {{ $expenses->total() }} {{ $ar ? 'سند' : 'JVs' }}
                    </span>
                </div>
                <h3 class="fw-bold text-danger mb-1">{{ number_format($totalExpenses, 2) }} <small class="fs-6">{{ $academyCurrencySymbol }}</small></h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-receipt me-1 text-danger opacity-75"></i>
                    {{ $ar ? 'تشمل مستحقات المدربين والفروع والتشغيل' : 'Includes coaches, branches, and ops' }}
                </div>
            </div>
        </div>

        <!-- 3. Net Actual Profit / Margin -->
        <div class="col-xl-3 col-md-6">
            <div class="expense-card-metric metric-profit">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">{{ $ar ? 'صافي الربح الفعلي' : 'Net Operating Profit' }}</span>
                    <span class="badge {{ $netProfit >= 0 ? 'bg-primary' : 'bg-warning' }} bg-opacity-10 {{ $netProfit >= 0 ? 'text-primary' : 'text-warning' }} rounded-pill px-2 py-1 small">
                        {{ $profitMargin }}% {{ $ar ? 'هامش الربح' : 'Margin' }}
                    </span>
                </div>
                <h3 class="fw-bold {{ $netProfit >= 0 ? 'text-primary' : 'text-warning' }} mb-1">
                    {{ number_format($netProfit, 2) }} <small class="fs-6">{{ $academyCurrencySymbol }}</small>
                </h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid {{ $netProfit >= 0 ? 'fa-chart-line text-success' : 'fa-circle-exclamation text-warning' }} me-1"></i>
                    {{ $netProfit >= 0 ? ($ar ? 'أرباح تشغيلية إيجابية' : 'Healthy operating profit') : ($ar ? 'عجز تشغيلي يحتاج مراجعة' : 'Operating deficit') }}
                </div>
            </div>
        </div>

        <!-- 4. Quick Coach Dues / Status -->
        <div class="col-xl-3 col-md-6">
            <div class="expense-card-metric metric-coach">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">{{ $ar ? 'مدربو الأكاديمية' : 'Academy Coaches' }}</span>
                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-1 small">
                        <i class="fa-solid fa-user-tie me-1"></i> {{ $coaches->count() }} {{ $ar ? 'مدرب' : 'coaches' }}
                    </span>
                </div>
                <h3 class="fw-bold text-info mb-1">{{ $coaches->count() }} <small class="fs-6">{{ $ar ? 'مدرب مسجل' : 'Registered' }}</small></h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-building me-1 text-info opacity-75"></i>
                    {{ $branches->count() }} {{ $ar ? 'فروع ومقرات نشطة' : 'Active branches' }}
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER & AUDIT PANEL -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-filter text-primary"></i>
                    <span>{{ $ar ? 'تصفية وبحث القيود والمصروفات' : 'Filter & Search Expenses' }}</span>
                </h5>
                @if(request()->anyFilled(['period_type', 'category_id', 'coach_id', 'branch_id', 'expense_type', 'from_date', 'to_date']))
                    <a href="{{ route('academy.expenses.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                        <i class="fa-solid fa-rotate-left me-1"></i> {{ $ar ? 'إعادة ضبط الفلاتر' : 'Reset Filters' }}
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('academy.expenses.index') }}" class="row g-3">
                <!-- Coach Filter -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold text-muted">{{ $ar ? 'المدرب / المستفيد' : 'Coach / Payee' }}</label>
                    <select name="coach_id" class="form-select form-select-sm rounded-3">
                        <option value="">{{ $ar ? 'جميع المدربين والمستفيدين' : 'All Coaches & Payees' }}</option>
                        <option value="external" @selected(request('coach_id') === 'external')>
                            {{ $ar ? '⭐ مدربون خارجيون / زائرون (Guest / External)' : '⭐ Guest / External Coaches' }}
                        </option>
                        <optgroup label="{{ $ar ? 'المدربون المعتمدون بالأكاديمية' : 'Registered Coaches' }}">
                            @foreach($coaches as $coach)
                                <option value="{{ $coach->id }}" @selected(request('coach_id') == $coach->id)>
                                    {{ $coach->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <!-- Branch Filter -->
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold text-muted">{{ $ar ? 'الفرع / المقر' : 'Branch / Location' }}</label>
                    <select name="branch_id" class="form-select form-select-sm rounded-3">
                        <option value="">{{ $ar ? 'جميع الفروع' : 'All Branches' }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>
                                {{ $branch->address ?: ('فرع #' . $branch->id) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Expense Type -->
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold text-muted">{{ $ar ? 'طبيعة المصروف' : 'Expense Nature' }}</label>
                    <select name="expense_type" class="form-select form-select-sm rounded-3">
                        <option value="">{{ $ar ? 'جميع الأنواع' : 'All Types' }}</option>
                        <option value="general" @selected(request('expense_type') === 'general')>{{ $ar ? 'تشغيلي عام' : 'General Ops' }}</option>
                        <option value="coach" @selected(request('expense_type') === 'coach')>{{ $ar ? 'مستحقات مدرب' : 'Coach Dues' }}</option>
                        <option value="branch_rent" @selected(request('expense_type') === 'branch_rent')>{{ $ar ? 'إيجار فرع / ملعب' : 'Branch Rent' }}</option>
                        <option value="equipment" @selected(request('expense_type') === 'equipment')>{{ $ar ? 'أدوات ومعدات' : 'Equipment' }}</option>
                        <option value="maintenance" @selected(request('expense_type') === 'maintenance')>{{ $ar ? 'صيانة وإصلاحات' : 'Maintenance' }}</option>
                        <option value="utilities" @selected(request('expense_type') === 'utilities')>{{ $ar ? 'فواتير ومرافق' : 'Utilities' }}</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold text-muted">{{ $ar ? 'التصنيف المحاسبي' : 'Category' }}</label>
                    <select name="category_id" class="form-select form-select-sm rounded-3">
                        <option value="">{{ $ar ? 'جميع التصنيفات' : 'All Categories' }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- From Date -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold text-muted">{{ $ar ? 'الفترة الزمنية' : 'Date Range' }}</label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="from_date" class="form-control rounded-start-3" value="{{ request('from_date') }}" placeholder="{{ $ar ? 'من' : 'From' }}">
                        <input type="date" name="to_date" class="form-control rounded-end-3" value="{{ request('to_date') }}" placeholder="{{ $ar ? 'إلى' : 'To' }}">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold rounded-3">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> {{ $ar ? 'تطبيق الفلترة' : 'Apply Filters' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- RECORDED EXPENSES & JOURNAL ENTRIES TABLE -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom border-light">
            <div>
                <h5 class="card-title m-0 fw-bold d-flex align-items-center gap-2">
                    <i class="fa-solid fa-book text-primary"></i>
                    <span>{{ $ar ? 'دفتر قيود المصروفات وسندات الصرف' : 'Expense Vouchers & Journal Register' }}</span>
                </h5>
                <span class="small text-muted">{{ $ar ? 'كل عملية مسجلة تمثل قيداً محاسبياً دقيقاً ومستنداً مالياً' : 'Each record corresponds to a verified accounting journal voucher' }}</span>
            </div>
            <span class="badge bg-light text-dark border px-3 py-2 fs-6 rounded-pill fw-bold">
                {{ $expenses->total() }} {{ $ar ? 'سند مقيد' : 'Records' }}
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light">
                        <tr class="text-muted small">
                            <th class="ps-4">#</th>
                            <th>{{ $ar ? 'رقم السند / القيد' : 'JV Voucher #' }}</th>
                            <th>{{ $ar ? 'بيان وتفاصيل المصروف' : 'Title & Description' }}</th>
                            <th>{{ $ar ? 'المستفيد / الحساب المدين' : 'Payee / Debit Account' }}</th>
                            <th>{{ $ar ? 'الفرع / الموقع' : 'Branch / Location' }}</th>
                            <th>{{ $ar ? 'المبلغ المسدد' : 'Paid Amount' }}</th>
                            <th>{{ $ar ? 'وسيلة الدفع / الحساب الدائن' : 'Payment Method / Credit' }}</th>
                            <th>{{ $ar ? 'التاريخ' : 'Date' }}</th>
                            <th>{{ $ar ? 'المعتمد' : 'Authorized By' }}</th>
                            <th class="pe-4 text-center">{{ $ar ? 'الإجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                            @php
                                $journalEntry = \App\Services\AcademyFinancialEngine::buildJournalEntry($exp);
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted small">{{ $loop->iteration + ($expenses->currentPage() - 1) * $expenses->perPage() }}</td>
                                
                                <!-- JV Badge -->
                                <td>
                                    <span class="jv-badge" data-bs-toggle="modal" data-bs-target="#voucherModal{{ $exp->id }}" title="{{ $ar ? 'انقر لعرض سند القيد المحاسبي' : 'Click to view official accounting voucher' }}">
                                        <i class="fa-solid fa-stamp"></i>
                                        <span>{{ $journalEntry['entry_number'] }}</span>
                                    </span>
                                </td>

                                <!-- Title & Category -->
                                <td>
                                    <div class="fw-bold text-dark">{{ $exp->title }}</div>
                                    <div class="small text-muted d-flex align-items-center gap-1">
                                        <i class="fa-solid {{ $exp->category?->icon ?: 'fa-receipt' }} text-primary opacity-75"></i>
                                        <span>{{ $exp->category?->name ?: ($ar ? 'عام' : 'General') }}</span>
                                        @if($exp->period_type && $exp->period_type !== 'monthly')
                                            <span class="badge bg-light text-muted border ms-1">{{ ucfirst($exp->period_type) }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Payee / Debit Account -->
                                <td>
                                    @if($exp->coach_id && $exp->coach)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-user-tie me-1"></i> {{ $exp->coach->name }}
                                        </span>
                                    @elseif($exp->is_external_coach)
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-user-ninja me-1"></i> {{ $ar ? 'مدرب خارجي / زائر' : 'External / Guest Coach' }}
                                        </span>
                                    @elseif($exp->expense_type === 'branch_rent')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-building me-1"></i> {{ $ar ? 'إيجار / مرافق مقر' : 'Branch Rent' }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-layer-group me-1 text-muted"></i> {{ $journalEntry['debit_account'] }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Branch -->
                                <td>
                                    @if($exp->branch)
                                        <span class="small fw-semibold text-dark">
                                            <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                            {{ $exp->branch->address ?: ('فرع #' . $exp->branch->id) }}
                                        </span>
                                    @else
                                        <span class="small text-muted">{{ $ar ? 'المقر العام' : 'Headquarters' }}</span>
                                    @endif
                                </td>

                                <!-- Amount -->
                                <td>
                                    <div class="fw-bold text-dark fs-6">
                                        {{ number_format($exp->amount, 2) }} <small class="text-muted">{{ $exp->currency }}</small>
                                    </div>
                                    @if($exp->currency !== ($exp->base_currency ?: $academyCurrencyCode))
                                        <div class="small text-danger fw-semibold">
                                            = {{ number_format($exp->base_amount, 2) }} {{ $exp->base_currency ?: $academyCurrencyCode }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Payment Method / Credit Account -->
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 small">
                                        @if($exp->payment_method === 'bank_transfer')
                                            <i class="fa-solid fa-building-columns text-primary me-1"></i> {{ $ar ? 'تحويل بنكي' : 'Bank' }}
                                        @elseif($exp->payment_method === 'card')
                                            <i class="fa-solid fa-credit-card text-success me-1"></i> {{ $ar ? 'شبكة / بطاقة' : 'POS' }}
                                        @elseif($exp->payment_method === 'online')
                                            <i class="fa-solid fa-globe text-info me-1"></i> {{ $ar ? 'إلكتروني' : 'Online' }}
                                        @else
                                            <i class="fa-solid fa-money-bill-wave text-secondary me-1"></i> {{ $ar ? 'خزينة / نقدي' : 'Cash' }}
                                        @endif
                                    </span>
                                </td>

                                <!-- Date -->
                                <td class="small text-muted">{{ $exp->expense_date?->format('Y-m-d') ?: '-' }}</td>

                                <!-- Approved By -->
                                <td class="small">
                                    <i class="fa-solid fa-user-check text-success me-1"></i>
                                    {{ $exp->approved_by ?: ($exp->creator?->name ?: '-') }}
                                </td>

                                <!-- Actions -->
                                <td class="pe-4 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <!-- Voucher details button -->
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#voucherModal{{ $exp->id }}" title="{{ $ar ? 'عرض سند القيد المحاسبي' : 'View Accounting Voucher' }}">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </button>

                                        @if($exp->receipt_image)
                                            <a href="{{ asset('storage/' . $exp->receipt_image) }}" target="_blank" class="btn btn-sm btn-outline-info" title="{{ $ar ? 'معاينة الإيصال' : 'View Receipt' }}">
                                                <i class="fa-solid fa-paperclip"></i>
                                            </a>
                                        @endif

                                        <form method="POST" action="{{ route('academy.expenses.destroy', $exp->id) }}" onsubmit="return confirm('{{ $ar ? 'هل أنت متأكد من حذف هذا المصروف والقيد المحاسبي المرتبط به؟' : 'Are you sure you want to delete this expense voucher?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ $ar ? 'حذف' : 'Delete' }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- VOUCHER MODAL FOR THIS EXPENSE -->
                            <div class="modal fade" id="voucherModal{{ $exp->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-md">
                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                        <div class="modal-header bg-primary text-white p-3">
                                            <h6 class="modal-title fw-bold m-0 d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-stamp"></i>
                                                <span>{{ $ar ? 'سند صرف وقيد محاسبي رسمي' : 'Official Payment & Journal Voucher' }}</span>
                                            </h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-light">
                                            <div class="voucher-sheet shadow-sm">
                                                <div class="text-center pb-3 border-bottom mb-3">
                                                    <h5 class="fw-bold text-dark mb-1">{{ auth('academy')->user()?->academy?->name ?? 'Hagzz Academy' }}</h5>
                                                    <span class="badge bg-primary px-3 py-1">{{ $journalEntry['entry_number'] }}</span>
                                                    <div class="small text-muted mt-1">{{ $ar ? 'سند قيد يومية معتمد' : 'Authorized Journal Voucher' }}</div>
                                                </div>

                                                <div class="voucher-row">
                                                    <span class="text-muted small">{{ $ar ? 'تاريخ الاستحقاق والصرف:' : 'Voucher Date:' }}</span>
                                                    <span class="fw-bold text-dark">{{ $journalEntry['date'] }}</span>
                                                </div>

                                                <div class="voucher-row">
                                                    <span class="text-muted small">{{ $ar ? 'المبلغ الإجمالي:' : 'Total Amount:' }}</span>
                                                    <span class="fw-bold text-danger fs-5">{{ number_format($journalEntry['amount'], 2) }} {{ $journalEntry['currency'] }}</span>
                                                </div>

                                                <div class="voucher-row">
                                                    <span class="text-muted small">{{ $ar ? 'البيان / الوصف:' : 'Description:' }}</span>
                                                    <span class="fw-bold text-dark">{{ $exp->title }}</span>
                                                </div>

                                                <!-- Debit Account Box -->
                                                <div class="voucher-account-box my-3">
                                                    <div class="small text-muted mb-1">{{ $ar ? 'من حـ/ (المدين - وجه الصرف):' : 'Debit Account (DR):' }}</div>
                                                    <div class="fw-bold text-primary fs-6">
                                                        <i class="fa-solid fa-circle-arrow-left me-1"></i>
                                                        {{ $journalEntry['debit_account'] }}
                                                    </div>
                                                </div>

                                                <!-- Credit Account Box -->
                                                <div class="voucher-account-box mb-3">
                                                    <div class="small text-muted mb-1">{{ $ar ? 'إلى حـ/ (الدائن - وسيلة الدفع):' : 'Credit Account (CR):' }}</div>
                                                    <div class="fw-bold text-success fs-6">
                                                        <i class="fa-solid fa-circle-arrow-right me-1"></i>
                                                        {{ $journalEntry['credit_account'] }}
                                                    </div>
                                                </div>

                                                @if($exp->notes)
                                                    <div class="small text-muted bg-white p-2 rounded border mb-3">
                                                        <strong>{{ $ar ? 'ملاحظات:' : 'Notes:' }}</strong> {{ $exp->notes }}
                                                    </div>
                                                @endif

                                                <div class="d-flex justify-content-between pt-2 border-top text-muted small">
                                                    <div>
                                                        <div>{{ $ar ? 'المسؤول / المدخل:' : 'Recorded By:' }}</div>
                                                        <div class="fw-bold text-dark">{{ $exp->creator?->name ?: '-' }}</div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div>{{ $ar ? 'المعتمد / المصرح:' : 'Authorized By:' }}</div>
                                                        <div class="fw-bold text-success">{{ $exp->approved_by ?: '-' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-white p-3">
                                            <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">{{ $ar ? 'إغلاق' : 'Close' }}</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                                <i class="fa-solid fa-print me-1"></i> {{ $ar ? 'طباعة السند' : 'Print Voucher' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <div class="my-4">
                                        <i class="fa-solid fa-scale-balanced fa-3x text-muted opacity-50 mb-3 d-block"></i>
                                        <h6 class="fw-bold text-dark">{{ $ar ? 'لا يوجد قيود أو مصروفات مسجلة بهذه الفلاتر' : 'No expenses or journal vouchers found' }}</h6>
                                        <p class="small text-muted">{{ $ar ? 'يمكنك تسجيل مصروف جديد وربطه بالمدربين أو الفروع بكل سهولة' : 'You can record an expense and link it to coaches or branches' }}</p>
                                        <button class="btn btn-primary btn-sm mt-2 rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                            <i class="fa-solid fa-plus me-1"></i> {{ $ar ? 'تسجيل مصروف وقيد صرف' : 'Record Expense & JV' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($expenses->hasPages())
                <div class="p-3 border-top border-light d-flex justify-content-center">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: ADD EXPENSE & GENERATE JOURNAL VOUCHER (قيد محاسبي معتمد) -->
<!-- ========================================================================= -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form method="POST" action="{{ route('academy.expenses.store') }}" enctype="multipart/form-data" id="expenseForm">
                @csrf
                <div class="modal-header bg-primary text-white p-4">
                    <div>
                        <h5 class="modal-title fw-bold m-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-file-signature"></i>
                            <span>{{ $ar ? 'تسجيل مصروف وإنشاء قيد محاسبي' : 'Record Expense & Journal Entry' }}</span>
                        </h5>
                        <p class="small text-white-50 m-0 mt-1">
                            {{ $ar ? 'ربط المصروف بالمدرب أو الفرع لضمان تكامل التقارير المالية وميزان المراجعة' : 'Link expense directly to coaches or branches for unified financial reporting' }}
                        </p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-light">
                    <div class="row g-3">
                        <!-- Expense Title -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'عنوان / بيان المصروف' : 'Expense Title' }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="modal_title" class="form-control rounded-3" placeholder="{{ $ar ? 'مثال: مستحقات تدريب شهر سبتمبر - كابتن أحمد / صيانة إنارة الملعب' : 'e.g. September Training Dues - Coach Ahmed / Pitch Maintenance' }}" required>
                        </div>

                        <!-- Category -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'التصنيف المحاسبي' : 'Category' }} <span class="text-danger">*</span></label>
                            <select name="category_id" id="modal_category" class="form-select rounded-3" required>
                                <option value="">{{ $ar ? '-- اختر التصنيف --' : '-- Select Category --' }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" data-name="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Expense Nature / Type -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'طبيعة المصروف' : 'Expense Nature' }} <span class="text-danger">*</span></label>
                            <select name="expense_type" id="modal_expense_type" class="form-select rounded-3" required>
                                <option value="general">{{ $ar ? 'مصروف تشغيلي عام' : 'General Operational' }}</option>
                                <option value="coach">{{ $ar ? 'مستحقات / أتعاب مدرب' : 'Coach Dues / Compensation' }}</option>
                                <option value="branch_rent">{{ $ar ? 'إيجار فرع / ملعب' : 'Branch Rent / Facility' }}</option>
                                <option value="equipment">{{ $ar ? 'شراء أدوات ومعدات' : 'Equipment & Tools' }}</option>
                                <option value="maintenance">{{ $ar ? 'صيانة وإصلاحات' : 'Maintenance & Repairs' }}</option>
                                <option value="utilities">{{ $ar ? 'فواتير ومرافق (كهرباء/مياه/إنترنت)' : 'Utilities' }}</option>
                            </select>
                        </div>

                        <!-- Coach Selector (Shown when coach or general) -->
                        <div class="col-md-4" id="coach_wrapper">
                            <label class="form-label fw-bold small text-dark d-flex justify-content-between">
                                <span>{{ $ar ? 'المدرب المستفيد' : 'Coach Payee' }}</span>
                                <span class="form-check form-switch p-0 m-0">
                                    <input class="form-check-input" type="checkbox" id="toggle_external_coach">
                                    <label class="form-check-label small text-warning fw-bold ms-1" for="toggle_external_coach">{{ $ar ? 'مدرب خارجي / زائر؟' : 'External/Guest?' }}</label>
                                </span>
                            </label>
                            <!-- Registered Coaches Select -->
                            <div id="reg_coach_box">
                                <select name="coach_id" id="modal_coach_id" class="form-select rounded-3">
                                    <option value="">{{ $ar ? '-- اختر المدرب المسجل --' : '-- Select Registered Coach --' }}</option>
                                    @foreach($coaches as $c)
                                        <option value="{{ $c->id }}" data-name="{{ $c->name }}">{{ $c->name }} ({{ $c->compensation_type ?? 'عقد' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- External Coach notice/input -->
                            <div id="ext_coach_box" class="d-none">
                                <input type="text" class="form-control rounded-3 border-warning bg-warning bg-opacity-10 text-dark fw-bold" value="{{ $ar ? 'مدرب خارجي / زائر (Guest Coach)' : 'Guest / External Coach' }}" readonly>
                                <span class="small text-muted d-block mt-1">{{ $ar ? 'سيتم تسجيل القيد لحساب أتعاب مدرب خارجي دون التأثير على عقود المدربين الدائمين' : 'Recorded as external coach fee without mixing with permanent coach contracts' }}</span>
                            </div>
                        </div>

                        <!-- Branch Selector -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'الفرع / الموقع المخصص' : 'Allocated Branch' }}</label>
                            <select name="branch_id" id="modal_branch_id" class="form-select rounded-3">
                                <option value="">{{ $ar ? 'المقر العام / غير محدد' : 'General / Unallocated' }}</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->address ?: ('فرع #' . $b->id) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Amount & Currency -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'المبلغ المنصرف' : 'Amount Disbursed' }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" id="exp_amount" name="amount" class="form-control rounded-3 fw-bold fs-6" placeholder="0.00" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'عملة الصرف' : 'Payment Currency' }} <span class="text-danger">*</span></label>
                            <select id="exp_currency" name="currency" class="form-select rounded-3 fw-bold" required>
                                <option value="{{ $academyCurrencyCode }}" selected>{{ $academyCurrencyCode }} ({{ $academyCurrencySymbol }}) - {{ $ar ? 'العملة الأساسية' : 'Base Currency' }}</option>
                                <option value="USD">USD ($) - {{ $ar ? 'دولار أمريكي' : 'US Dollar' }}</option>
                                <option value="EUR">EUR (€) - {{ $ar ? 'يورو أوروبي' : 'Euro' }}</option>
                                <option value="EGP">EGP (ج.م) - {{ $ar ? 'جنيه مصري' : 'Egyptian Pound' }}</option>
                                <option value="SAR">SAR (ر.س) - {{ $ar ? 'ريال سعودي' : 'Saudi Riyal' }}</option>
                                <option value="QAR">QAR (ر.ق) - {{ $ar ? 'ريال قطري' : 'Qatari Riyal' }}</option>
                                <option value="AED">AED (د.إ) - {{ $ar ? 'درهم إماراتي' : 'UAE Dirham' }}</option>
                                <option value="KWD">KWD (د.ك) - {{ $ar ? 'دينار كويتي' : 'Kuwaiti Dinar' }}</option>
                                <option value="BHD">BHD (د.ب) - {{ $ar ? 'دينار بحريني' : 'Bahraini Dinar' }}</option>
                                <option value="OMR">OMR (ر.ع) - {{ $ar ? 'ريال عماني' : 'Omani Rial' }}</option>
                            </select>
                        </div>

                        <!-- FX EQUIVALENT -->
                        <div class="col-md-4 d-none" id="fx_wrapper">
                            <label class="form-label fw-bold small text-primary">
                                <i class="fa-solid fa-calculator me-1"></i>
                                {{ $ar ? 'المقابل بـ '.$academyCurrencySymbol : 'Equivalent in '.$academyCurrencyCode }} <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" min="0.01" id="exp_base_amount" name="base_amount" class="form-control rounded-3 fw-bold border-primary" placeholder="0.00">
                        </div>

                        <!-- Payment Method / Credit Account -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'طريقة السداد / الحساب الدائن' : 'Payment Method / Credit Acc' }} <span class="text-danger">*</span></label>
                            <select name="payment_method" id="modal_payment_method" class="form-select rounded-3" required>
                                <option value="cash" selected>{{ $ar ? 'الصندوق / الخزينة (نقدي)' : 'Cash / Academy Treasury' }}</option>
                                <option value="bank_transfer">{{ $ar ? 'تحويل بنكي / حساب البنك' : 'Bank Transfer' }}</option>
                                <option value="card">{{ $ar ? 'شبكة / جهاز نقاط البيع / بطاقة' : 'POS / Card Machine' }}</option>
                                <option value="online">{{ $ar ? 'بوابة الدفع الإلكتروني' : 'Online Payment Gateway' }}</option>
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'تاريخ السند' : 'Voucher Date' }} <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Period Type -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'دورة المصروف' : 'Period Type' }} <span class="text-danger">*</span></label>
                            <select name="period_type" class="form-select rounded-3" required>
                                <option value="daily">{{ $ar ? 'يومي (Daily)' : 'Daily' }}</option>
                                <option value="monthly" selected>{{ $ar ? 'شهري (Monthly)' : 'Monthly' }}</option>
                                <option value="quarterly">{{ $ar ? 'ربع سنوي (Quarterly)' : 'Quarterly' }}</option>
                                <option value="annual">{{ $ar ? 'سنوي (Annual)' : 'Annual' }}</option>
                            </select>
                        </div>

                        <!-- Approver -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'المعتمد / المصرح بالصرف' : 'Authorized Approver' }}</label>
                            <input type="text" name="approved_by" class="form-control rounded-3" value="{{ auth('academy')->user()?->name }}" placeholder="{{ $ar ? 'اسم المدير أو المعتمد' : 'Approver Name' }}">
                        </div>

                        <!-- Receipt Attachment -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'إرفاق سند / إيصال الصرف' : 'Attach Receipt / Voucher' }}</label>
                            <input type="file" name="receipt_image" class="form-control rounded-3" accept="image/*">
                        </div>

                        <!-- Notes -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">{{ $ar ? 'ملاحظات وتفاصيل إضافية' : 'Notes & Details' }}</label>
                            <input type="text" name="notes" id="modal_notes" class="form-control rounded-3" placeholder="{{ $ar ? 'أي تفاصيل محاسبية إضافية...' : 'Any additional voucher notes...' }}">
                        </div>

                        <!-- LIVE ACCOUNTING JOURNAL ENTRY PREVIEW -->
                        <div class="col-12 mt-3">
                            <div class="card border-primary border-opacity-25 rounded-3 bg-white p-3 shadow-sm">
                                <div class="small fw-bold text-primary mb-2 d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>{{ $ar ? 'معاينة القيد المحاسبي المزدوج (Double-Entry Journal Preview):' : 'Double-Entry Accounting Journal Preview:' }}</span>
                                </div>
                                <div class="row g-2 text-center text-md-start">
                                    <div class="col-md-6">
                                        <div class="p-2 bg-light rounded-2 border">
                                            <span class="small text-muted d-block">{{ $ar ? 'من حـ/ المدين (DR):' : 'Debit Account (DR):' }}</span>
                                            <strong class="text-primary" id="preview_debit">{{ $ar ? 'مصروفات عامة' : 'General Expenses' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2 bg-light rounded-2 border">
                                            <span class="small text-muted d-block">{{ $ar ? 'إلى حـ/ الدائن (CR):' : 'Credit Account (CR):' }}</span>
                                            <strong class="text-success" id="preview_credit">{{ $ar ? 'الصندوق / الخزينة (نقدي)' : 'Cash / Treasury' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ $ar ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                        <i class="fa-solid fa-check-circle me-1"></i> {{ $ar ? 'اعتماد وحفظ السند' : 'Authorize & Save Voucher' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: ADD EXPENSE CATEGORY -->
<!-- ========================================================================= -->
<div class="modal fade" id="newCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form method="POST" action="{{ route('academy.expenses.categories.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white p-4">
                    <h5 class="modal-title fw-bold m-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-folder-plus"></i>
                        <span>{{ $ar ? 'إضافة تصنيف مصروفات جديد' : 'Add Expense Category' }}</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">{{ $ar ? 'اسم التصنيف بالعربية' : 'Category Name (Arabic)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control rounded-3" placeholder="مثال: رسوم اتحادات وتراخيص" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">{{ $ar ? 'اسم التصنيف بالإنجليزية' : 'Category Name (English)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control rounded-3" placeholder="e.g. Federation & License Fees" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">{{ $ar ? 'رمز الأيقونة (FontAwesome)' : 'Icon Class' }}</label>
                        <input type="text" name="icon" class="form-control rounded-3" placeholder="fa-receipt" value="fa-receipt">
                    </div>
                </div>
                <div class="modal-footer bg-white p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ $ar ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">{{ $ar ? 'حفظ التصنيف' : 'Save Category' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const isArabic = {{ $ar ? 'true' : 'false' }};
    const currencySelect = document.getElementById('exp_currency');
    const fxWrapper = document.getElementById('fx_wrapper');
    const baseAmountInput = document.getElementById('exp_base_amount');
    const expAmountInput = document.getElementById('exp_amount');
    const baseCurrencyCode = "{{ $academyCurrencyCode }}";

    // Dynamic Elements for Journal Preview
    const previewDebit = document.getElementById('preview_debit');
    const previewCredit = document.getElementById('preview_credit');
    const expenseTypeSelect = document.getElementById('modal_expense_type');
    const coachSelect = document.getElementById('modal_coach_id');
    const toggleExternal = document.getElementById('toggle_external_coach');
    const regCoachBox = document.getElementById('reg_coach_box');
    const extCoachBox = document.getElementById('ext_coach_box');
    const categorySelect = document.getElementById('modal_category');
    const paymentMethodSelect = document.getElementById('modal_payment_method');

    function toggleFX() {
        if (currencySelect.value !== baseCurrencyCode) {
            fxWrapper.classList.remove('d-none');
            baseAmountInput.setAttribute('required', 'required');
        } else {
            fxWrapper.classList.add('d-none');
            baseAmountInput.removeAttribute('required');
            baseAmountInput.value = expAmountInput.value;
        }
    }

    function updateJournalPreview() {
        // 1. Debit Account
        if (toggleExternal && toggleExternal.checked) {
            previewDebit.textContent = isArabic ? 'أتعاب مدرب خارجي / زائر' : 'External / Guest Coach Fee';
        } else if (coachSelect && coachSelect.value) {
            const opt = coachSelect.options[coachSelect.selectedIndex];
            previewDebit.textContent = (isArabic ? 'مستحقات المدرب: ' : 'Coach Dues: ') + (opt.dataset.name || opt.text);
        } else if (categorySelect && categorySelect.value) {
            const opt = categorySelect.options[categorySelect.selectedIndex];
            previewDebit.textContent = (isArabic ? 'مصروف: ' : 'Expense: ') + (opt.dataset.name || opt.text);
        } else {
            previewDebit.textContent = isArabic ? 'مصروفات عامة' : 'General Expenses';
        }

        // 2. Credit Account
        const method = paymentMethodSelect ? paymentMethodSelect.value : 'cash';
        if (method === 'bank_transfer') {
            previewCredit.textContent = isArabic ? 'البنك / الحساب البنكي' : 'Bank Transfer';
        } else if (method === 'card') {
            previewCredit.textContent = isArabic ? 'شبكة / جهاز نقاط البيع' : 'POS / Card Machine';
        } else if (method === 'online') {
            previewCredit.textContent = isArabic ? 'بوابة الدفع الإلكتروني' : 'Online Payment Gateway';
        } else {
            previewCredit.textContent = isArabic ? 'الصندوق / الخزينة (نقدي)' : 'Cash / Treasury';
        }
    }

    if (currencySelect) currencySelect.addEventListener('change', toggleFX);
    if (expAmountInput) {
        expAmountInput.addEventListener('input', function () {
            if (currencySelect.value === baseCurrencyCode) {
                baseAmountInput.value = expAmountInput.value;
            }
        });
    }

    // Toggle External Coach
    if (toggleExternal) {
        toggleExternal.addEventListener('change', function () {
            if (this.checked) {
                regCoachBox.classList.add('d-none');
                extCoachBox.classList.remove('d-none');
                coachSelect.value = 'external';
            } else {
                regCoachBox.classList.remove('d-none');
                extCoachBox.classList.add('d-none');
                coachSelect.value = '';
            }
            updateJournalPreview();
        });
    }

    if (expenseTypeSelect) {
        expenseTypeSelect.addEventListener('change', function () {
            if (this.value === 'coach') {
                document.getElementById('coach_wrapper').style.display = 'block';
            }
            updateJournalPreview();
        });
    }

    if (coachSelect) coachSelect.addEventListener('change', updateJournalPreview);
    if (categorySelect) categorySelect.addEventListener('change', updateJournalPreview);
    if (paymentMethodSelect) paymentMethodSelect.addEventListener('change', updateJournalPreview);

    toggleFX();
    updateJournalPreview();
});
</script>
@endsection
