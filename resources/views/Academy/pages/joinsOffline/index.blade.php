@extends('Academy.Layouts.master')

@section('title', trans('admin.bookings.offline_bookings'))

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/src/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/custom_dt_miscellaneous.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/assets/css/heroui-theme.css') }}">
@endpush

@section('content')
    <div class="middle-content container-xxl p-0 heroui-wrapper">

        <!--  BEGIN BREADCRUMBS  -->
        <div class="secondary-nav mb-4">
            <div class="breadcrumbs-container" data-page-heading="Analytics">
                <header class="header navbar navbar-expand-sm">
                    <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </a>
                    <div class="d-flex breadcrumb-content">
                        <div class="page-header">
                            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('academy.report.joins') }}">{{ trans('admin.bookings.bookings') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.bookings.offline_bookings') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </header>
            </div>
        </div>
        <!--  END BREADCRUMBS  -->

        <!-- HeroUI Header Banner -->
        <div class="heroui-header-banner">
            <div>
                <h1 class="heroui-header-title">
                    <span class="title-icon"><i class="fas fa-store"></i></span>
                    {{ trans('admin.bookings.offline_bookings') }}
                </h1>
                <p class="heroui-header-subtitle">
                    {{ app()->getLocale() === 'ar' ? 'سجل الحجوزات والاشتراكات المباشرة في مقر الأكاديمية والمقبوضات النقدية' : 'Direct in-person registrations and on-premise payment records' }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('academy.report.joins') }}" class="heroui-btn heroui-btn-light">
                    <i class="fas fa-ticket-alt text-primary"></i>
                    <span>{{ trans('admin.bookings.bookings') }}</span>
                </a>
                <a href="{{ route('academy.report.join.export') }}" class="heroui-btn heroui-btn-light">
                    <i class="fa-solid fa-file-excel text-success"></i>
                    <span>{{ trans('admin.export') }}</span>
                </a>
            </div>
        </div>

        <!-- HeroUI Metric Stat Cards -->
        <div class="heroui-stat-grid">
            <div class="heroui-stat-card primary">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'إجمالي الحجوزات المباشرة' : 'Total Offline Bookings' }}</span>
                    <h2 class="stat-value">{{ number_format($metrics['total'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon primary">
                    <i class="fas fa-cash-register"></i>
                </div>
            </div>

            <div class="heroui-stat-card success">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'حجوزات اليوم المباشرة' : "Today's Direct" }}</span>
                    <h2 class="stat-value text-success">{{ number_format($metrics['today'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon success">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>

            <div class="heroui-stat-card info">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'مشتركون مسجلون' : 'Registered Users' }}</span>
                    <h2 class="stat-value text-info">{{ number_format($metrics['with_user'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon info">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>

            <div class="heroui-stat-card secondary">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'حجوزات ضيوف' : 'Guest Bookings' }}</span>
                    <h2 class="stat-value text-muted">{{ number_format($metrics['guest'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon secondary">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
        </div>

        <!-- HeroUI Smart Filter & View Switcher -->
        <div class="heroui-filter-panel">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold fs-6">{{ app()->getLocale() === 'ar' ? 'نوع الحجوزات المعروضة:' : 'Display Bookings:' }}</span>
                </div>
                <div class="heroui-tabs-container m-0">
                    <a href="{{ route('academy.report.joins') }}" class="heroui-tab-btn text-decoration-none">
                        <i class="fas fa-list-check"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'جميع الحجوزات' : 'All Bookings' }}</span>
                    </a>
                    <a href="{{ route('academy.report.offline-joins') }}" class="heroui-tab-btn active text-decoration-none">
                        <i class="fas fa-store"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الحجوزات المباشرة' : 'Offline Bookings' }}</span>
                        <span class="heroui-tab-badge">{{ $metrics['total'] ?? 0 }}</span>
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('academy.report.joins-offline-filter') }}" class="mt-2">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5 col-sm-6">
                        <label class="heroui-filter-label">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            <span>{{ trans('admin.start_date') }}</span>
                        </label>
                        <input type="date" name="start_date" class="heroui-select" value="{{ old('start_date', request('start_date')) }}" required>
                    </div>
                    <div class="col-md-5 col-sm-6">
                        <label class="heroui-filter-label">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            <span>{{ trans('admin.end_date') }}</span>
                        </label>
                        <input type="date" name="end_date" class="heroui-select" value="{{ old('end_date', request('end_date')) }}" required>
                    </div>
                    <div class="col-md-2 col-sm-12 d-flex gap-2">
                        <button type="submit" class="heroui-btn heroui-btn-primary flex-fill py-2">
                            <i class="fa fa-filter"></i> {{ trans('admin.apply') }}
                        </button>
                        @if(request()->has('start_date') || request()->has('end_date'))
                            <a href="{{ route('academy.report.offline-joins') }}" class="heroui-btn heroui-btn-reset py-2" title="{{ trans('admin.reset') }}">
                                <i class="fa fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- HeroUI Card Table -->
        <div class="heroui-card-table">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table heroui-table dt-table-hover dataTable', 'id' => 'join-table']) !!}
            </div>
        </div>

    </div>
@endsection

@push('js')
    <script src="{{ asset('assetsAdmin/src/plugins/src/table/datatable/datatables.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="/vendor/datatables/buttons.server-side.js"></script>
    <script src="{{ asset('assetsAdmin/confirmationDelete.js') }}"></script>
    {!! $dataTable->scripts() !!}
@endpush
