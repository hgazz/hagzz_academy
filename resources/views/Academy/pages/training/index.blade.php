@extends('Academy.Layouts.master')

@section('title', trans('admin.training.training'))

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/src/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/custom_dt_miscellaneous.css') }}">
@endpush

@section('content')
    <div class="middle-content container-xxl p-0 heroui-wrapper">

        <!--  BEGIN BREADCRUMBS  -->
        <div class="secondary-nav mb-4">
            <div class="breadcrumbs-container" data-page-heading="Analytics">
                <header class="header navbar navbar-expand-sm">
                    <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-menu">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </a>
                    <div class="d-flex breadcrumb-content">
                        <div class="page-header">
                            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.training.training') }}</li>
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
                    <span class="title-icon"><i class="fas fa-dumbbell"></i></span>
                    {{ trans('admin.training.training') }}
                </h1>
                <p class="heroui-header-subtitle">
                    {{ app()->getLocale() === 'ar' ? 'إدارة البرامج والأنشطة التدريبية، المدربين، وجداول الحصص' : 'Manage training programs, coaches, schedules, and class assignments' }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('academy.class.index') }}" class="heroui-btn heroui-btn-light">
                    <i class="fas fa-layer-group text-primary"></i>
                    <span>{{ trans('admin.clasess.clasess') }}</span>
                </a>
                <a href="{{ route('academy.createBooking') }}" class="heroui-btn heroui-btn-light">
                    <i class="fas fa-user-plus text-success"></i>
                    <span>{{ trans('admin.training.booking') }}</span>
                </a>
                <a href="{{ route('academy.training.create') }}" class="heroui-btn heroui-btn-primary">
                    <i class="fas fa-plus"></i>
                    <span>{{ trans('admin.training.create') }}</span>
                </a>
            </div>
        </div>

        <!-- HeroUI Metric Stat Cards -->
        <div class="heroui-stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
            <div class="heroui-stat-card primary">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'إجمالي البرامج التدريبية' : 'Total Programs' }}</span>
                    <h2 class="stat-value">{{ number_format($metrics['total'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon primary">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>

            <div class="heroui-stat-card success">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'البرامج النشطة' : 'Active Programs' }}</span>
                    <h2 class="stat-value text-success">{{ number_format($metrics['active'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            <div class="heroui-stat-card secondary">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'البرامج غير النشطة' : 'Inactive Programs' }}</span>
                    <h2 class="stat-value text-muted">{{ number_format($metrics['inactive'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon secondary">
                    <i class="fas fa-pause-circle"></i>
                </div>
            </div>
        </div>

        <!-- HeroUI Table Card -->
        <div class="heroui-card-table">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-hover w-100', 'id' => 'training-table']) !!}
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
@endpush
