@extends('Academy.Layouts.master')

@section('title', trans('admin.user.user'))

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/src/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/custom_dt_miscellaneous.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsAdmin/src/assets/css/heroui-theme.css') }}">
    <style>
        #user-table td {
            vertical-align: middle;
        }

        #user-table .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            object-fit: cover;
            object-position: center;
            border: 2px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
            background: #e8eef0;
        }

        #user-table th:nth-child(2),
        #user-table td:nth-child(2) {
            width: 64px !important;
            min-width: 64px;
            text-align: center;
        }
    </style>
@endpush

@section('content')
@php
    $ar = app()->getLocale() === 'ar';
@endphp

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
                            <li class="breadcrumb-item active">{{ trans('admin.user.user') }}</li>
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
                <span class="title-icon"><i class="fa-solid fa-users text-primary"></i></span>
                {{ trans('admin.user.user') }}
            </h1>
            <p class="heroui-header-subtitle">
                {{ $ar ? 'سجل عملاء ومستخدمي تطبيق احجز المشتركين والحاجزين لخدمات وتدريبات الأكاديمية' : 'Directory of app users and customers registered with or booking through this academy' }}
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('academy.students.index') }}" class="heroui-btn heroui-btn-light">
                <i class="fa-solid fa-user-graduate text-success"></i>
                <span>{{ $ar ? 'سجل طلاب الأكاديمية' : 'Academy Students' }}</span>
            </a>
            <a href="{{ route('academy.team.index') }}" class="heroui-btn heroui-btn-light">
                <i class="fa-solid fa-users-gear text-primary"></i>
                <span>{{ $ar ? 'طاقم العمل والصلاحيات' : 'Team & Permissions' }}</span>
            </a>
        </div>
    </div>

    <!-- USERS DATATABLE CARD -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom border-light">
            <div>
                <h5 class="card-title m-0 fw-bold d-flex align-items-center gap-2">
                    <i class="fa-solid fa-address-book text-primary"></i>
                    <span>{{ $ar ? 'بيانات وحسابات العملاء' : 'Customer Accounts List' }}</span>
                </h5>
                <span class="small text-muted">{{ $ar ? 'قائمة المستخدمين المرتبطين بحجوزات واشتراكات الأكاديمية' : 'List of users linked to academy bookings and student subscriptions' }}</span>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-hover align-middle mb-0 dataTable']) !!}
            </div>
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
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('assetsAdmin/confirmationDelete.js') }}"></script>
    {!! $dataTable->scripts() !!}
@endpush
