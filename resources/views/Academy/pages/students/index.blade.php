@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.students'))

@push('css')
    <style>
        .student-table td, .student-table th {
            vertical-align: middle;
        }

        .student-table .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            cursor: pointer;
            margin: 0;
        }

        .student-row-selected td {
            background-color: rgba(0, 150, 136, 0.08) !important;
        }

        .student-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center;
            border: 2px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.15);
            background: #e8eef0;
        }

        .student-tools {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
        }

        .student-import-box {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #e4e7ec;
            border-radius: 8px;
            background: #f8fafc;
        }

        .student-import-box input[type="file"] {
            max-width: 280px;
        }

        .student-filter-box {
            padding: 14px;
            margin-bottom: 18px;
            border: 1px solid #e4e7ec;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        /* Floating Bulk Action Bar */
        .student-bulk-bar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: rgba(15, 23, 42, 0.94);
            backdrop-filter: blur(10px);
            color: #ffffff;
            padding: 10px 22px;
            border-radius: 50px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1060;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            opacity: 0;
            pointer-events: none;
            max-width: 92vw;
            flex-wrap: wrap;
        }

        .student-bulk-bar.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        /* Redesigned Card Footer & Pagination */
        .student-card-footer {
            background-color: #ffffff;
            border-top: 1px solid #edf2f7;
            padding: 16px 20px;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .students-pagination-wrapper .pagination {
            margin-bottom: 0;
            gap: 4px;
            flex-wrap: wrap;
        }

        .students-pagination-wrapper .page-item .page-link {
            border-radius: 8px !important;
            border: 1px solid #e2e8f0;
            color: #334155;
            padding: 7px 14px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        .students-pagination-wrapper .page-item.active .page-link {
            background-color: #009688;
            border-color: #009688;
            color: #ffffff !important;
            box-shadow: 0 3px 8px rgba(0, 150, 136, 0.35);
        }

        .students-pagination-wrapper .page-item:not(.active) .page-link:hover {
            background-color: #f1f5f9;
            color: #009688;
            border-color: #cbd5e1;
        }

        .students-pagination-wrapper .page-item.disabled .page-link {
            background-color: #f8fafc;
            color: #94a3b8;
            border-color: #e2e8f0;
        }

        .bottom-view-spacer {
            padding-bottom: 70px;
        }
    </style>
@endpush

@section('content')
    <div class="middle-content container-xxl p-0 heroui-wrapper bottom-view-spacer">

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
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.student_management.students') }}</li>
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
                    <span class="title-icon"><i class="fas fa-user-graduate"></i></span>
                    {{ trans('admin.student_management.students') }}
                </h1>
                <p class="heroui-header-subtitle">
                    {{ app()->getLocale() === 'ar' ? 'إدارة ومتابعة سجلات المشتركين، عضوياتهم، الحضور، والتواصل المباشر' : 'Manage and track student profiles, memberships, attendance, and direct messaging' }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('academy.whatsapp.compose') }}" class="heroui-btn heroui-btn-light text-success">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'إرسال جماعي' : 'Bulk WhatsApp' }}</span>
                </a>
                <a href="{{ route('academy.students.export') }}" class="heroui-btn heroui-btn-light">
                    <i class="fa-solid fa-file-excel text-success"></i>
                    <span>{{ trans('admin.student_management.export_excel') }}</span>
                </a>
                <a href="{{ route('academy.students.template') }}" class="heroui-btn heroui-btn-light">
                    <i class="fa-solid fa-download text-muted"></i>
                    <span>{{ trans('admin.student_management.download_students_template') }}</span>
                </a>
                <a href="{{ route('academy.students.print') }}" target="_blank" class="heroui-btn heroui-btn-light">
                    <i class="fa-solid fa-print"></i>
                    <span>{{ trans('admin.student_management.print_pdf') }}</span>
                </a>
                <a href="{{ route('academy.students.create') }}" class="heroui-btn heroui-btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    <span>{{ trans('admin.student_management.add_student') }}</span>
                </a>
            </div>
        </div>

        <!-- HeroUI Metric Stat Cards -->
        <div class="heroui-stat-grid">
            <div class="heroui-stat-card primary">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'إجمالي المشتركين' : 'Total Students' }}</span>
                    <h2 class="stat-value">{{ number_format($metrics['total'] ?? $students->total()) }}</h2>
                </div>
                <div class="heroui-stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <div class="heroui-stat-card success">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'المشتركون النشطون' : 'Active Students' }}</span>
                    <h2 class="stat-value text-success">{{ number_format($metrics['active'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon success">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>

            <div class="heroui-stat-card secondary">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'غير النشطين' : 'Inactive Students' }}</span>
                    <h2 class="stat-value text-muted">{{ number_format($metrics['inactive'] ?? 0) }}</h2>
                </div>
                <div class="heroui-stat-icon secondary">
                    <i class="fas fa-user-xmark"></i>
                </div>
            </div>

            <div class="heroui-stat-card info">
                <div class="heroui-stat-content">
                    <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'التوزيع (ذكور / إناث)' : 'Gender Ratio (M / F)' }}</span>
                    <h2 class="stat-value text-info fs-4">
                        <i class="fas fa-mars text-primary fs-6 me-1"></i>{{ $metrics['male'] ?? 0 }}
                        <span class="text-muted mx-1">/</span>
                        <i class="fas fa-venus text-danger fs-6 me-1"></i>{{ $metrics['female'] ?? 0 }}
                    </h2>
                </div>
                <div class="heroui-stat-icon info">
                    <i class="fas fa-venus-mars"></i>
                </div>
            </div>
        </div>

        @if(session('import_summary'))
            @php $summary = session('import_summary'); @endphp
            <div class="heroui-filter-panel mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-excel text-success"></i> {{ trans('admin.student_management.import_results') }}
                    </h5>
                    <span class="heroui-chip heroui-chip-default">{{ trans('admin.student_management.import_total_rows') }}: {{ $summary['total'] }}</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 text-center">
                            <span class="text-success fw-bold d-block mb-1">{{ trans('admin.student_management.import_created') }}</span>
                            <h4 class="text-success fw-bolder mb-0">{{ $summary['created'] }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3 text-center">
                            <span class="text-primary fw-bold d-block mb-1">{{ trans('admin.student_management.import_updated') }}</span>
                            <h4 class="text-primary fw-bolder mb-0">{{ $summary['updated'] }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 {{ $summary['skipped'] > 0 ? 'bg-danger bg-opacity-10 border border-danger border-opacity-25' : 'bg-secondary bg-opacity-10 border border-secondary border-opacity-25' }} rounded-3 text-center">
                            <span class="{{ $summary['skipped'] > 0 ? 'text-danger' : 'text-muted' }} fw-bold d-block mb-1">{{ trans('admin.student_management.import_skipped') }}</span>
                            <h4 class="{{ $summary['skipped'] > 0 ? 'text-danger' : 'text-muted' }} fw-bolder mb-0">{{ $summary['skipped'] }}</h4>
                        </div>
                    </div>
                </div>

                @if(!empty($summary['errors']))
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-danger rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#importErrorsCollapse" aria-expanded="false">
                            <i class="fa fa-circle-exclamation me-1"></i> {{ trans('admin.student_management.import_errors_list') }} ({{ count($summary['errors']) }})
                        </button>
                        <div class="collapse mt-2" id="importErrorsCollapse">
                            <div class="table-responsive bg-white rounded-3 border">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 80px;">{{ trans('admin.student_management.import_row') }}</th>
                                            <th>{{ trans('admin.student_management.name') }}</th>
                                            <th>{{ trans('admin.student_management.import_reason') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($summary['errors'] as $err)
                                            <tr>
                                                <td class="text-center fw-bold">{{ $err['row'] }}</td>
                                                <td>{{ $err['name'] }}</td>
                                                <td class="text-danger">{{ $err['reason'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- HeroUI Quick Import Box -->
        <div class="heroui-filter-panel mb-3 py-3">
            <form action="{{ route('academy.students.import') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-wrap align-items-center justify-content-between gap-3 m-0">
                @csrf
                <div class="d-flex align-items-center gap-2">
                    <div class="heroui-stat-icon primary" style="width: 38px; height: 38px; font-size: 16px;">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div>
                        <strong class="d-block" style="font-size: 14px;">{{ trans('admin.student_management.import_students') }}</strong>
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'رفع ملف Excel أو CSV لإضافة أو تحديث المشتركين' : 'Upload Excel/CSV file to bulk create or update students' }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <input type="file" name="students_file" class="form-control form-control-sm" style="max-width: 320px; border-radius: var(--heroui-radius-md);" accept=".xlsx,.xls,.csv" required>
                    <button class="heroui-btn heroui-btn-primary py-2 px-3">
                        <i class="fa-solid fa-upload me-1"></i> {{ trans('admin.student_management.upload_students_file') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- HeroUI Smart Filter Panel -->
        <div class="heroui-filter-panel mb-4">
            <form method="GET" action="{{ route('academy.students.index') }}">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-4 col-md-12">
                        <label class="heroui-filter-label">
                            <i class="fa fa-search text-primary"></i>
                            <span>{{ trans('admin.student_management.search_placeholder') }}</span>
                        </label>
                        <input type="text" name="search" class="heroui-select" value="{{ request('search') }}" placeholder="{{ trans('admin.student_management.search_placeholder') }}...">
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <label class="heroui-filter-label">
                            <i class="fa fa-arrow-down-short-wide text-primary"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'الترتيب' : 'Sort' }}</span>
                        </label>
                        <div class="heroui-select-wrap">
                            <select name="sort" class="heroui-select">
                                <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>{{ trans('admin.student_management.sort_latest') }}</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ trans('admin.student_management.sort_oldest') }}</option>
                                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>{{ trans('admin.student_management.sort_name_asc') }}</option>
                                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>{{ trans('admin.student_management.sort_name_desc') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <label class="heroui-filter-label">
                            <i class="fa fa-circle-check text-primary"></i>
                            <span>{{ trans('admin.student_management.status') }}</span>
                        </label>
                        <div class="heroui-select-wrap">
                            <select name="status" class="heroui-select">
                                <option value="">{{ trans('admin.student_management.all_statuses') }}</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ trans('admin.student_management.active') }}</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ trans('admin.student_management.inactive') }}</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>{{ trans('admin.student_management.suspended') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <label class="heroui-filter-label">
                            <i class="fa fa-venus-mars text-primary"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'الجنس' : 'Gender' }}</span>
                        </label>
                        <div class="heroui-select-wrap">
                            <select name="gender" class="heroui-select">
                                <option value="">{{ trans('admin.student_management.all_genders') }}</option>
                                <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>{{ trans('admin.student_management.male') }}</option>
                                <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>{{ trans('admin.student_management.female') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12 col-sm-6 d-flex gap-2 align-items-end" style="height: 100%;">
                        <button type="submit" class="heroui-btn heroui-btn-primary flex-fill py-2">
                            <i class="fa fa-filter"></i> {{ trans('admin.student_management.filter') }}
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'gender']) || (request()->filled('sort') && request('sort') !== 'latest'))
                            <a href="{{ route('academy.students.index') }}" class="heroui-btn heroui-btn-reset py-2" title="{{ trans('admin.student_management.reset') }}">
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
                <table class="table student-table heroui-table mb-0">
                    <thead>
                    <tr>
                        <th style="width: 45px;" class="text-center">
                            <input type="checkbox" id="selectAllStudents" class="form-check-input" title="{{ trans('admin.student_management.select_all') }}">
                        </th>
                        <th style="width: 60px;">#</th>
                        <th style="width: 70px;">{{ trans('admin.banners.image') }}</th>
                        <th>{{ trans('admin.student_management.name') }}</th>
                        <th>{{ trans('admin.student_management.phone') }}</th>
                        <th>{{ trans('admin.student_management.guardian') }}</th>
                        <th>{{ trans('admin.student_management.status') }}</th>
                        <th class="text-center">{{ trans('admin.student_management.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($students as $student)
                        <tr id="student-row-{{ $student->id }}">
                            <td class="text-center">
                                <input type="checkbox" value="{{ $student->id }}" data-name="{{ $student->name }}" class="form-check-input student-select-checkbox">
                            </td>
                            <td class="fw-bold text-muted">{{ $students->firstItem() ? ($students->firstItem() + $loop->index) : ($loop->iteration) }}</td>
                            <td>
                                <img
                                    src="{{ $student->avatarUrl() }}"
                                    alt="{{ $student->name }}"
                                    class="student-avatar"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ $student->defaultImageUrl() }}';"
                                >
                            </td>
                            <td>
                                <button type="button" class="student-profile-trigger btn btn-link p-0 fw-bold text-decoration-none text-dark d-flex align-items-center gap-1" data-student-profile-url="{{ route('academy.students.profile', $student) }}">
                                    <span>{{ $student->name }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-muted" style="font-size: 11px;"></i>
                                </button>
                                @if($student->membership_number)
                                    <span class="heroui-time-chip mt-1" style="font-size: 11px;">
                                        <i class="fa fa-id-badge text-primary me-1"></i>{{ $student->membership_number }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($student->phone)
                                    <span class="fw-bold text-dark d-block">{{ $student->phone }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($student->guardian_name)
                                    <span class="fw-bold text-dark d-block">{{ $student->guardian_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                                @if($student->guardian_phone)
                                    <small class="text-muted"><i class="fa fa-phone me-1"></i>{{ $student->guardian_phone }}</small>
                                @endif
                            </td>
                            <td>
                                @if($student->status === 'active')
                                    <span class="heroui-chip heroui-chip-success">
                                        <span class="chip-dot pulse"></span>
                                        {{ trans('admin.student_management.active') }}
                                    </span>
                                @else
                                    <span class="heroui-chip heroui-chip-default">
                                        <span class="chip-dot"></span>
                                        {{ trans('admin.student_management.' . $student->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    @if($student->phone || $student->guardian_phone)
                                        <a href="#" class="btn btn-sm btn-outline-success rounded-circle js-whatsapp-direct p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" data-phone="{{ $student->phone ?: $student->guardian_phone }}" data-name="{{ $student->name }}" title="{{ app()->getLocale() === 'ar' ? 'فتح واتساب' : 'Open WhatsApp' }}">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('academy.students.card', $student) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="{{ app()->getLocale() === 'ar' ? 'الكارت' : 'Card' }}">
                                        <i class="fa-regular fa-id-card"></i>
                                    </a>
                                    <a href="{{ route('academy.students.edit', $student) }}" class="btn btn-sm btn-outline-warning rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="{{ trans('admin.student_management.edit') }}">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('academy.students.destroy', $student) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" onclick="return confirm('{{ trans('admin.student_management.delete_student_confirm') }}')" title="{{ trans('admin.student_management.delete') }}">
                                            <i class="fa fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="fa-regular fa-folder-open text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <h6 class="fw-bold">{{ trans('admin.student_management.no_students_yet') }}</h6>
                                    <p class="small text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'لا توجد بيانات مطابقة لخيارات البحث المحددة' : 'No student records match your current filters' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Card Footer & Pagination -->
            @if($students->total() > 0)
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4 pt-3 border-top">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <span class="text-muted fw-bold">
                            <i class="fa-solid fa-users text-primary me-1"></i>
                            {{ trans('admin.student_management.showing_results', [
                                'from' => $students->firstItem() ?? 0,
                                'to' => $students->lastItem() ?? 0,
                                'total' => $students->total()
                            ]) }}
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <small class="text-muted">{{ trans('admin.student_management.per_page') }}:</small>
                            <select class="form-select form-select-sm" style="width: auto; border-radius: var(--heroui-radius-sm);" onchange="window.location.href = this.value">
                                @foreach([20, 50, 100] as $size)
                                    <option value="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}" {{ request('per_page', 20) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="students-pagination-wrapper">
                        {{ $students->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Floating Bulk Actions Toolbar -->
    <div id="studentBulkBar" class="student-bulk-bar">
        <span class="badge bg-primary px-3 py-2 rounded-pill fs-6 fw-bold">
            <i class="fa fa-users me-1"></i>
            <span id="bulkSelectedCount">0</span>
        </span>
        <div class="vr bg-white opacity-25"></div>
        <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
            <i class="fa fa-trash-can me-1"></i> {{ trans('admin.student_management.bulk_delete') }}
        </button>
        <div class="dropdown d-inline-block">
            <button class="btn btn-secondary btn-sm rounded-pill px-3 dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-arrows-rotate me-1"></i> {{ trans('admin.student_management.bulk_status') }}
            </button>
            <ul class="dropdown-menu shadow">
                <li><a class="dropdown-item js-bulk-status-btn" href="#" data-status="active"><i class="fa fa-circle-check text-success me-2"></i>{{ trans('admin.student_management.active') }}</a></li>
                <li><a class="dropdown-item js-bulk-status-btn" href="#" data-status="inactive"><i class="fa fa-circle-xmark text-secondary me-2"></i>{{ trans('admin.student_management.inactive') }}</a></li>
                <li><a class="dropdown-item js-bulk-status-btn" href="#" data-status="suspended"><i class="fa fa-ban text-warning me-2"></i>{{ trans('admin.student_management.suspended') }}</a></li>
            </ul>
        </div>
        <button type="button" id="clearBulkSelection" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="fa fa-xmark me-1"></i> {{ trans('admin.student_management.clear_selection') }}
        </button>
    </div>

    <!-- Bulk Delete Confirmation Modal -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white fw-bold" id="bulkDeleteModalLabel">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ trans('admin.student_management.bulk_delete') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="bulkDeleteForm" action="{{ route('academy.students.bulk-delete') }}" method="POST">
                    @csrf
                    <div class="modal-body py-4 text-center">
                        <div class="mb-3">
                            <i class="fa-solid fa-trash-can text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ trans('admin.student_management.bulk_delete_confirm') }}</h5>
                        <p class="text-muted mb-0">
                            {{ app()->getLocale() === 'ar' ? 'سيتم حذف' : 'You are about to delete' }}
                            <strong class="text-danger fs-5" id="modalBulkCount">0</strong>
                            {{ app()->getLocale() === 'ar' ? 'طالب بشكل نهائي. لا يمكن التراجع عن هذا الإجراء!' : 'students permanently. This action cannot be undone!' }}
                        </p>
                        <div id="bulkDeleteInputsContainer"></div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('admin.student_management.cancel') }}</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">
                            <i class="fa-solid fa-trash-can me-1"></i> {{ trans('admin.student_management.bulk_delete') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Bulk Status Form -->
    <form id="bulkStatusForm" action="{{ route('academy.students.bulk-status') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="status" id="bulkStatusInput">
        <div id="bulkStatusInputsContainer"></div>
    </form>

    @include('Academy.pages.students._profile_modal')
    @include('Academy.components.whatsapp-direct-modal')
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAllStudents');
    const studentCheckboxes = document.querySelectorAll('.student-select-checkbox');
    const bulkBar = document.getElementById('studentBulkBar');
    const bulkCountSpan = document.getElementById('bulkSelectedCount');
    const modalBulkCount = document.getElementById('modalBulkCount');
    const clearSelectionBtn = document.getElementById('clearBulkSelection');
    const bulkDeleteInputsContainer = document.getElementById('bulkDeleteInputsContainer');
    const bulkStatusForm = document.getElementById('bulkStatusForm');
    const bulkStatusInput = document.getElementById('bulkStatusInput');
    const bulkStatusInputsContainer = document.getElementById('bulkStatusInputsContainer');

    function updateBulkState() {
        const checkedBoxes = document.querySelectorAll('.student-select-checkbox:checked');
        const count = checkedBoxes.length;

        studentCheckboxes.forEach(cb => {
            const row = document.getElementById('student-row-' + cb.value);
            if (row) {
                if (cb.checked) {
                    row.classList.add('student-row-selected');
                } else {
                    row.classList.remove('student-row-selected');
                }
            }
        });

        if (count > 0) {
            const textTemplate = @json(trans('admin.student_management.selected_count', ['count' => ':count']));
            bulkCountSpan.textContent = textTemplate.replace(':count', count);
            modalBulkCount.textContent = count;
            bulkBar.classList.add('show');
        } else {
            bulkBar.classList.remove('show');
        }

        if (selectAllCheckbox) {
            if (count === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (count === studentCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }

        // Update hidden inputs for delete
        if (bulkDeleteInputsContainer) {
            bulkDeleteInputsContainer.innerHTML = '';
            checkedBoxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                bulkDeleteInputsContainer.appendChild(input);
            });
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const isChecked = this.checked;
            studentCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });
            updateBulkState();
        });
    }

    studentCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkState);
    });

    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function () {
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            studentCheckboxes.forEach(cb => {
                cb.checked = false;
            });
            updateBulkState();
        });
    }

    // Bulk status change handlers
    document.querySelectorAll('.js-bulk-status-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const status = this.getAttribute('data-status');
            const checkedBoxes = document.querySelectorAll('.student-select-checkbox:checked');
            if (checkedBoxes.length === 0) return;

            bulkStatusInput.value = status;
            bulkStatusInputsContainer.innerHTML = '';
            checkedBoxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                bulkStatusInputsContainer.appendChild(input);
            });

            bulkStatusForm.submit();
        });
    });
});
</script>
@endpush
