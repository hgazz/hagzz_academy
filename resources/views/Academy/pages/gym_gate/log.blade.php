@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'سجل دخول وخروج البوابة' : 'Gate Entry & Exit Log')

@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/heroui-theme.css') }}" rel="stylesheet">
    <style>
        .gate-log-card {
            background: #ffffff;
            border: 1px solid var(--heroui-border, #e2e8f0);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        .gate-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .gate-stat-grid {
                grid-template-columns: 1fr;
            }
        }
        .gate-stat-card {
            background: #ffffff;
            border: 1px solid var(--heroui-border, #e2e8f0);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            transition: transform 0.2s ease;
        }
        .gate-stat-card:hover {
            transform: translateY(-2px);
        }
        .gate-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .gate-stat-icon.teal { background: rgba(15, 118, 110, 0.12); color: #0f766e; }
        .gate-stat-icon.blue { background: rgba(30, 64, 175, 0.12); color: #1e40af; }
        .gate-stat-icon.red { background: rgba(220, 38, 38, 0.12); color: #dc2626; }

        .pulse-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            background: #dcfce7;
            color: #15803d;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse-green 1.6s infinite;
        }
        @keyframes pulse-green {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
        .date-filter-form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: nowrap;
        }
        .date-filter-form input[type="date"] {
            min-width: 140px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            font-size: 13px;
        }
        .date-filter-form .btn-search {
            white-space: nowrap !important;
            flex-shrink: 0;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 13px;
            font-weight: 600;
        }
    </style>
@endpush

@php
    $ar = app()->getLocale() === 'ar';
@endphp

@section('content')
<div class="middle-content container-xxl p-0 heroui-wrapper" dir="{{ $ar ? 'rtl' : 'ltr' }}">

    <!-- Breadcrumbs -->
    <div class="secondary-nav mb-3">
        <div class="breadcrumbs-container">
            <header class="header navbar navbar-expand-sm">
                <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </a>
                <div class="d-flex breadcrumb-content">
                    <div class="page-header">
                        <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('academy.gym-gate.scanner') }}">{{ $ar ? 'بوابة الدخول السريع' : 'Gate Scanner' }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $ar ? 'سجل البوابة' : 'Gate Log' }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </header>
        </div>
    </div>

    <!-- HeroUI Header Banner -->
    <div class="heroui-header-banner py-3 px-4 mb-3" style="border-radius: 12px;">
        <div class="d-flex align-items-center gap-3">
            <div class="title-icon" style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <h1 class="heroui-header-title m-0" style="font-size: 1.25rem;">{{ $ar ? 'سجل دخول وخروج البوابة' : 'Gate Entry & Exit Log' }}</h1>
                <p class="heroui-header-subtitle m-0 mt-1" style="font-size: 0.82rem; color: #64748b;">
                    {{ $ar ? 'متابعة حركة الدخول والخروج اللحظية للأعضاء وساعات التدريب في الصالة' : 'Real-time monitoring of member check-ins, exits, and gym session duration' }}
                    <span class="badge bg-light text-dark ms-2 border" style="font-family: monospace;">{{ $date }}</span>
                </p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center mt-2 mt-md-0">
            <form class="date-filter-form" method="GET">
                <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm">
                <button type="submit" class="btn btn-sm btn-primary btn-search">
                    <i class="fa-solid fa-filter me-1"></i> {{ $ar ? 'تصفية بالتاريخ' : 'Filter' }}
                </button>
            </form>
            @if($date !== today()->toDateString())
                <a href="{{ route('academy.gym-gate.log') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; white-space: nowrap;">
                    {{ $ar ? 'اليوم' : 'Today' }}
                </a>
            @endif
            <a href="{{ route('academy.gym-gate.scanner') }}" class="heroui-btn heroui-btn-primary py-2 px-3" style="background: #0f766e; border-color: #0f766e;">
                <i class="fa-solid fa-qrcode me-1"></i>
                <span>{{ $ar ? 'العودة لماسح البوابة' : 'Back to Scanner' }}</span>
            </a>
        </div>
    </div>

    <!-- Stat Metrics Cards -->
    <div class="gate-stat-grid">
        <div class="gate-stat-card">
            <div>
                <span class="text-muted d-block mb-1" style="font-size: 0.8rem; font-weight: 600;">{{ $ar ? 'إجمالي حركات الدخول' : 'Total Entries' }}</span>
                <h3 class="m-0 fw-bold" style="font-size: 1.5rem; color: #0f766e;">{{ number_format($stats['total']) }}</h3>
            </div>
            <div class="gate-stat-icon teal">
                <i class="fa-solid fa-door-open"></i>
            </div>
        </div>

        <div class="gate-stat-card">
            <div>
                <span class="text-muted d-block mb-1" style="font-size: 0.8rem; font-weight: 600;">{{ $ar ? 'داخل الصالة الآن' : 'Currently Inside' }}</span>
                <h3 class="m-0 fw-bold" style="font-size: 1.5rem; color: #1e40af;">{{ number_format($stats['inside']) }}</h3>
            </div>
            <div class="gate-stat-icon blue">
                <i class="fa-solid fa-person-walking"></i>
            </div>
        </div>

        <div class="gate-stat-card">
            <div>
                <span class="text-muted d-block mb-1" style="font-size: 0.8rem; font-weight: 600;">{{ $ar ? 'اشتراكات منتهية / مرفوضة' : 'Invalid / Expired' }}</span>
                <h3 class="m-0 fw-bold" style="font-size: 1.5rem; color: #dc2626;">{{ number_format($stats['invalid']) }}</h3>
            </div>
            <div class="gate-stat-icon red">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- Gate Entries Table Card -->
    <div class="gate-log-card mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead style="background: #f8fafc; border-bottom: 2px solid #edf2f7;">
                    <tr>
                        <th class="ps-3" style="width: 250px;">{{ $ar ? 'العضو' : 'Member' }}</th>
                        <th>{{ $ar ? 'وقت الدخول' : 'Entry Time' }}</th>
                        <th>{{ $ar ? 'وقت الخروج' : 'Exit Time' }}</th>
                        <th>{{ $ar ? 'مدة التمرين' : 'Duration' }}</th>
                        <th>{{ $ar ? 'حالة الاشتراك' : 'Subscription' }}</th>
                        <th>{{ $ar ? 'نقطة المسح' : 'Station' }}</th>
                        <th class="pe-3">{{ $ar ? 'طريقة التحقق' : 'Method' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                    <tr style="{{ !$entry->subscription_valid ? 'background-color: #fffbeb;' : '' }}">
                        <td class="ps-3 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $entry->student?->avatarUrl() }}"
                                     onerror="this.src='{{ $entry->student?->defaultImageUrl() }}'"
                                     style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0;"
                                     alt="">
                                <div>
                                    <div class="fw-bold text-dark">{{ $entry->student?->name ?? '-' }}</div>
                                    <small class="text-muted" style="font-size: 11px; direction: ltr; display: inline-block;">
                                        <i class="fa-solid fa-phone me-1"></i>{{ $entry->student?->phone ?: '—' }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 12px; font-family: monospace;">
                                <i class="fa-solid fa-arrow-right-to-bracket text-success me-1"></i>{{ $entry->entered_at?->format('h:i:s A') }}
                            </span>
                        </td>
                        <td>
                            @if($entry->exited_at)
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 12px; font-family: monospace;">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-danger me-1"></i>{{ $entry->exited_at->format('h:i:s A') }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($entry->duration_minutes !== null)
                                <span class="badge bg-light text-primary border fw-semibold px-2 py-1" style="font-size: 12px;">
                                    <i class="fa-regular fa-clock me-1"></i>{{ $entry->duration_minutes }} {{ $ar ? 'دقيقة' : 'min' }}
                                </span>
                            @elseif(!$entry->exited_at)
                                <span class="pulse-badge">
                                    <span class="pulse-dot"></span>
                                    <span>{{ $ar ? 'داخل الصالة الآن' : 'Inside' }}</span>
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($entry->subscription_valid)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                    <i class="fa-solid fa-check-circle me-1"></i>{{ $ar ? 'اشتراك سارٍ' : 'Active' }}
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                                    <i class="fa-solid fa-circle-xmark me-1"></i>{{ $ar ? 'منتهٍ / غير سارٍ' : 'Expired / Invalid' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 12px;">
                                <i class="fa-solid fa-location-dot me-1 text-secondary"></i>{{ $entry->station ?: ($ar ? 'الباب الرئيسي' : 'Main Door') }}
                            </span>
                        </td>
                        <td class="pe-3">
                            <span class="badge bg-light text-secondary border px-2 py-1 text-uppercase" style="font-size: 11px;">
                                @if($entry->scan_method === 'qr')
                                    <i class="fa-solid fa-qrcode me-1 text-primary"></i> QR
                                @elseif($entry->scan_method === 'barcode')
                                    <i class="fa-solid fa-barcode me-1 text-info"></i> Barcode
                                @else
                                    <i class="fa-solid fa-keyboard me-1 text-muted"></i> Manual
                                @endif
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <div style="font-size: 44px; margin-bottom: 12px; color: #cbd5e1;">
                                    <i class="fa-solid fa-clipboard-list"></i>
                                </div>
                                <h6 class="fw-bold text-secondary">{{ $ar ? 'لا توجد سجلات دخول مسجلة لهذا اليوم' : 'No entry records found for this date' }}</h6>
                                <p class="small text-muted mb-0">{{ $ar ? 'حركات الدخول التي تتم عبر الماسح ستظهر هنا تلقائياً ولحظياً.' : 'Check-in entries recorded via the gate scanner will appear here.' }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entries->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $entries->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
