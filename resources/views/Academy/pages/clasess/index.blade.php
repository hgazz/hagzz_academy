@extends('Academy.Layouts.master')

@section('title', trans('admin.clasess.clasess'))

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
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.clasess.clasess') }}</li>
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
                    <span class="title-icon"><i class="fas fa-chalkboard-teacher"></i></span>
                    {{ trans('admin.clasess.clasess') }}
                </h1>
                <p class="heroui-header-subtitle">
                    {{ app()->getLocale() === 'ar' ? 'إدارة ومتابعة الحصص التدريبية المنظمة، التوقيت، وجداول الحضور' : 'Organize and track scheduled training sessions, timing, and attendance' }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('academy.training.create') }}" class="heroui-btn heroui-btn-light">
                    <i class="fas fa-plus-circle text-primary"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تدريب جديد' : 'New Training' }}</span>
                </a>
                <a href="{{ route('academy.class.create') }}" class="heroui-btn heroui-btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>{{ trans('admin.clasess.create') }}</span>
                </a>
            </div>
        </div>

        <!-- HeroUI Metric Stat Cards -->
        <div class="heroui-stat-grid">
            <div class="heroui-stat-card primary">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'إجمالي الحصص' : 'Total Sessions' }}</span>
                    <h2 class="stat-value">{{ number_format($metrics['total'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon primary">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>

            <div class="heroui-stat-card success">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'حصص اليوم' : "Today's Sessions" }}</span>
                    <h2 class="stat-value text-success">{{ number_format($metrics['today'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon success">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>

            <div class="heroui-stat-card info">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'الحصص القادمة' : 'Upcoming Sessions' }}</span>
                    <h2 class="stat-value text-info">{{ number_format($metrics['upcoming'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon info">
                    <i class="fas fa-clock"></i>
                </div>
            </div>

            <div class="heroui-stat-card secondary">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'الحصص المنتهية' : 'Completed Sessions' }}</span>
                    <h2 class="stat-value text-muted">{{ number_format($metrics['past'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon secondary">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        </div>

        <!-- HeroUI Smart Filter Panel -->
        <div class="heroui-filter-panel">
            <div class="row align-items-center g-3">
                <div class="col-lg-5 col-md-12 col-12">
                    <label for="filter_training_id" class="heroui-filter-label">
                        <i class="fas fa-filter text-primary"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تصفية حسب البرنامج التدريبي:' : 'Filter by Training Program:' }}</span>
                    </label>
                    <div class="heroui-select-wrap">
                        <select id="filter_training_id" class="heroui-select">
                            <option value="">{{ app()->getLocale() === 'ar' ? '✨ جميع البرامج التدريبية (الكل)' : '✨ All Training Programs' }}</option>
                            @foreach($trainings as $training)
                                @php
                                    $tName = is_array($training->name) ? ($training->name[app()->getLocale()] ?? reset($training->name)) : $training->name;
                                @endphp
                                <option value="{{ $training->id }}">{{ $tName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-5 col-md-12 col-12">
                    <label class="heroui-filter-label">
                        <i class="fas fa-calendar-alt text-primary"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الفترة الزمنية للحصص:' : 'Session Timeframe:' }}</span>
                    </label>
                    <div class="heroui-tabs-container">
                        <button type="button" class="heroui-tab-btn active" data-timeframe="all">
                            <span>{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}</span>
                            <span class="heroui-tab-badge">{{ $metrics['total'] ?? 0 }}</span>
                        </button>
                        <button type="button" class="heroui-tab-btn" data-timeframe="today">
                            <span>{{ app()->getLocale() === 'ar' ? 'اليوم' : 'Today' }}</span>
                            <span class="heroui-tab-badge">{{ $metrics['today'] ?? 0 }}</span>
                        </button>
                        <button type="button" class="heroui-tab-btn" data-timeframe="upcoming">
                            <span>{{ app()->getLocale() === 'ar' ? 'القادمة' : 'Upcoming' }}</span>
                            <span class="heroui-tab-badge">{{ $metrics['upcoming'] ?? 0 }}</span>
                        </button>
                        <button type="button" class="heroui-tab-btn" data-timeframe="past">
                            <span>{{ app()->getLocale() === 'ar' ? 'المنتهية' : 'Completed' }}</span>
                            <span class="heroui-tab-badge">{{ $metrics['past'] ?? 0 }}</span>
                        </button>
                    </div>
                    <input type="hidden" id="selected_timeframe" value="all">
                </div>

                <div class="col-lg-2 col-md-12 col-12 d-flex align-items-end justify-content-lg-end">
                    <button type="button" id="btn_reset_filters" class="heroui-btn heroui-btn-reset w-100">
                        <i class="fas fa-rotate-left"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset Filters' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- HeroUI Table Card -->
        <div class="heroui-card-table">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-hover w-100', 'id' => 'tclass-table']) !!}
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
    {!! $dataTable->scripts() !!}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function reloadTable() {
                if (window.LaravelDataTables && window.LaravelDataTables['tclass-table']) {
                    window.LaravelDataTables['tclass-table'].ajax.reload();
                }
            }

            // Training Filter Change
            $('#filter_training_id').on('change', function() {
                reloadTable();
            });

            // HeroUI Segmented Tabs Click
            $('.heroui-tab-btn').on('click', function(e) {
                e.preventDefault();
                $('.heroui-tab-btn').removeClass('active');
                $(this).addClass('active');

                var tf = $(this).data('timeframe');
                $('#selected_timeframe').val(tf);
                reloadTable();
            });

            // Reset Button
            $('#btn_reset_filters').on('click', function() {
                $('#filter_training_id').val('');
                $('.heroui-tab-btn').removeClass('active');
                $('.heroui-tab-btn[data-timeframe="all"]').addClass('active');
                $('#selected_timeframe').val('all');
                reloadTable();
            });
        });
    </script>
@endpush
