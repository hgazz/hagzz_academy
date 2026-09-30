@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'سجل حضور وانصراف الكوادر والمدربين' : 'Staff & Coach Attendance')

@php
    $ar = app()->getLocale() === 'ar';
    $facType = \App\Support\FacilityTerminology::currentType();
    $termTrainer = facility_term('coach', $facType, false);
    $termTrainers = facility_term('coach', $facType, true);
@endphp

@section('content')
<style>
    /* Adaptive Typography & Theme Variables */
    .att-text-main { color: #0f172a; }
    .att-text-muted { color: #64748b; }
    .att-label {
        font-weight: 700;
        font-size: 12.5px;
        color: #334155;
        margin-bottom: 4px;
        display: block;
    }
    
    body.dark .att-text-main, .dark .att-text-main { color: #f8fafc !important; }
    body.dark .att-text-muted, .dark .att-text-muted { color: #cbd5e1 !important; }
    body.dark .att-label, .dark .att-label { color: #e2e8f0 !important; }

    /* Stat Cards */
    .att-stat-card {
        background: #fff;
        border-radius: 14px;
        padding: 16px 18px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.04);
        border: 1px solid #eef2f0;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    body.dark .att-stat-card, .dark .att-stat-card {
        background: #0e1726 !important;
        border-color: #1b2e4b !important;
        box-shadow: 0 4px 14px rgba(0,0,0,0.25) !important;
    }
    .att-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.07);
    }
    .att-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .icon-green { background: #ecfdf5; color: #059669; }
    .icon-blue { background: #eff6ff; color: #2563eb; }
    .icon-purple { background: #f5f3ff; color: #7c3aed; }
    .icon-amber { background: #fffbeb; color: #d97706; }
    .icon-teal { background: #f0fdfa; color: #0d9488; }

    body.dark .icon-green { background: rgba(5,150,105,0.18); color: #34d399; }
    body.dark .icon-blue { background: rgba(37,99,235,0.18); color: #60a5fa; }
    body.dark .icon-purple { background: rgba(124,58,237,0.18); color: #a78bfa; }
    body.dark .icon-amber { background: rgba(217,119,6,0.18); color: #fbbf24; }
    body.dark .icon-teal { background: rgba(13,148,136,0.18); color: #2dd4bf; }

    /* Compact Modal Styles */
    .att-modal-dialog {
        max-width: 520px;
        margin: 1.75rem auto;
    }
    .att-modal-content {
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    body.dark .att-modal-content, .dark .att-modal-content {
        background-color: #0e1726 !important;
        border-color: #1b2e4b !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5) !important;
    }
    body.dark #manualAttendanceModal .modal-header,
    .dark #manualAttendanceModal .modal-header {
        background: #152238 !important;
        border-bottom-color: #1b2e4b !important;
    }
    body.dark #manualAttendanceModal .modal-footer,
    .dark #manualAttendanceModal .modal-footer {
        background: #152238 !important;
        border-top-color: #1b2e4b !important;
    }
    body.dark #manualAttendanceModal .form-control,
    body.dark #manualAttendanceModal .form-select,
    .dark #manualAttendanceModal .form-control,
    .dark #manualAttendanceModal .form-select {
        background-color: #1b2e4b !important;
        color: #ffffff !important;
        border-color: #253b5c !important;
    }
    body.dark #manualAttendanceModal .form-control:focus,
    body.dark #manualAttendanceModal .form-select:focus,
    .dark #manualAttendanceModal .form-control:focus,
    .dark #manualAttendanceModal .form-select:focus {
        border-color: #009688 !important;
        box-shadow: 0 0 0 0.25rem rgba(0, 150, 136, 0.25) !important;
    }
    body.dark #manualAttendanceModal .btn-light,
    .dark #manualAttendanceModal .btn-light {
        background-color: #1b2e4b !important;
        color: #e2e8f0 !important;
        border-color: #253b5c !important;
    }

    /* Badge Helper */
    .att-badge-time {
        background: rgba(148, 163, 184, 0.12);
        color: inherit;
        border: 1px solid rgba(148, 163, 184, 0.25);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
    }
</style>

<div class="middle-content container-xxl p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="mb-1 fw-bold att-text-main">
                <i class="fa-solid fa-user-clock text-success me-2"></i>
                {{ $ar ? 'حضور وانصراف الكوادر و' . $termTrainers : 'Staff & ' . $termTrainers . ' Attendance' }}
            </h3>
            <p class="att-text-muted mb-0 fs-7">
                {{ $ar ? 'متابعة الحضور والانصراف اللحظي، شاشة الـ Live QR الذكية المتغيرة، وحساب ساعات العمل' : 'Real-time attendance tracking, dynamic rotating QR kiosk, and work duration log' }}
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('academy.staff-attendance.live-screen') }}" target="_blank" class="btn btn-dark fw-bold shadow-sm d-flex align-items-center gap-2" style="border-radius:10px;">
                <i class="fa-solid fa-qrcode text-warning fa-lg"></i>
                <span>{{ $ar ? 'شاشة الاستقبال (Live QR)' : 'Launch Reception Live QR' }}</span>
                <span class="badge bg-danger ms-1 animate__animated animate__pulse animate__infinite">LIVE</span>
            </a>
            <button type="button" class="btn btn-outline-success fw-bold d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#manualAttendanceModal" style="border-radius:10px;">
                <i class="fa-solid fa-plus me-1"></i> {{ $ar ? 'تسجيل يدوي' : 'Manual Entry' }}
            </button>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl">
            <div class="att-stat-card">
                <div class="att-stat-icon icon-green"><i class="fa-solid fa-user-check"></i></div>
                <div>
                    <span class="att-text-muted fs-7 d-block fw-semibold">{{ $ar ? 'إجمالي الحاضرين' : 'Total Present' }}</span>
                    <strong class="fs-4 att-text-main">{{ $stats['total_present'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="att-stat-card">
                <div class="att-stat-icon icon-teal"><i class="fa-solid fa-building-user"></i></div>
                <div>
                    <span class="att-text-muted fs-7 d-block fw-semibold">{{ $ar ? 'متواجدون حالياً' : 'Currently In' }}</span>
                    <strong class="fs-4 text-teal" style="color:#0d9488;">{{ $stats['currently_in'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="att-stat-card">
                <div class="att-stat-icon icon-blue"><i class="fa-solid fa-person-walking-arrow-right"></i></div>
                <div>
                    <span class="att-text-muted fs-7 d-block fw-semibold">{{ $ar ? 'تم الانصراف' : 'Checked Out' }}</span>
                    <strong class="fs-4 text-primary">{{ $stats['checked_out'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="att-stat-card">
                <div class="att-stat-icon icon-amber"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <div>
                    <span class="att-text-muted fs-7 d-block fw-semibold">{{ $ar ? 'تأخير' : 'Late' }}</span>
                    <strong class="fs-4 text-warning">{{ $stats['total_late'] }}</strong>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl">
            <div class="att-stat-card">
                <div class="att-stat-icon icon-purple"><i class="fa-solid fa-stopwatch"></i></div>
                <div>
                    <span class="att-text-muted fs-7 d-block fw-semibold">{{ $ar ? 'ساعات العمل اليوم' : 'Total Hours' }}</span>
                    <strong class="fs-4" style="color:#7c3aed;">{{ $stats['total_hours'] }} <small class="fs-7">{{ $ar ? 'ساعة' : 'hrs' }}</small></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('academy.staff-attendance.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label fs-7 fw-bold att-text-muted mb-1">{{ $ar ? 'تاريخ الحضور' : 'Attendance Date' }}</label>
                    <input type="date" class="form-control form-control-sm" name="date" value="{{ $date }}">
                </div>
                @if($branches->count() > 1)
                    <div class="col-md-3">
                        <label class="form-label fs-7 fw-bold att-text-muted mb-1">{{ $ar ? 'الفرع' : 'Branch' }}</label>
                        <select class="form-select form-select-sm" name="branch_id">
                            <option value="">{{ $ar ? 'جميع الفروع' : 'All Branches' }}</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->address ?: 'فرع #'.$b->id }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-3">
                    <label class="form-label fs-7 fw-bold att-text-muted mb-1">{{ $ar ? 'تصنيف الكادر' : 'Staff Category' }}</label>
                    <select class="form-select form-select-sm" name="staff_type">
                        <option value="all" {{ $staffType === 'all' ? 'selected' : '' }}>{{ $ar ? 'الكل (موظفين و' . $termTrainers . ')' : 'All Staff & ' . $termTrainers }}</option>
                        <option value="employee" {{ $staffType === 'employee' ? 'selected' : '' }}>{{ $ar ? 'فريق العمل الإداري' : 'Administrative Staff' }}</option>
                        <option value="coach" {{ $staffType === 'coach' ? 'selected' : '' }}>{{ $termTrainers }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2 pt-md-3">
                    <button type="submit" class="btn btn-success btn-sm flex-grow-1 fw-bold" style="border-radius:8px;">
                        <i class="fa-solid fa-filter me-1"></i> {{ $ar ? 'تصفية' : 'Filter' }}
                    </button>
                    <a href="{{ route('academy.staff-attendance.index') }}" class="btn btn-light btn-sm" style="border-radius:8px;">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Log Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold att-text-main">
                <i class="fa-solid fa-list-check me-2 text-primary"></i>
                {{ $ar ? 'سجل الحضور والانصراف ليوم: ' . Carbon\Carbon::parse($date)->locale($ar ? 'ar' : 'en')->isoFormat('LL') : 'Attendance Log: ' . $date }}
            </h5>
            <span class="badge bg-secondary-subtle att-text-main border fw-bold">{{ $attendances->total() }} {{ $ar ? 'سجل' : 'records' }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead>
                    <tr>
                        <th>{{ $ar ? 'الاسم والبيانات' : 'Name & Role' }}</th>
                        <th>{{ $ar ? 'التصنيف' : 'Category' }}</th>
                        <th>{{ $ar ? 'الفرع' : 'Branch' }}</th>
                        <th>{{ $ar ? 'وقت الحضور' : 'Check-In' }}</th>
                        <th>{{ $ar ? 'وقت الانصراف' : 'Check-Out' }}</th>
                        <th>{{ $ar ? 'مدة العمل' : 'Duration' }}</th>
                        <th>{{ $ar ? 'وسيلة التحقق' : 'Verification' }}</th>
                        <th>{{ $ar ? 'الحالة' : 'Status' }}</th>
                        <th class="text-end">{{ $ar ? 'إجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width:34px; height:34px; font-size:12px; background: {{ $att->staff_type === 'coach' ? '#059669' : '#3b82f6' }};">
                                        {{ mb_substr($att->staff_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <strong class="d-block att-text-main">{{ $att->staff_name }}</strong>
                                        <small class="att-text-muted">{{ $att->staff_phone ?: '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($att->staff_type === 'coach')
                                    <span class="badge" style="background:rgba(5,150,105,0.15); color:#059669; border:1px solid rgba(5,150,105,0.3);">
                                        <i class="fa-solid fa-whistle me-1"></i> {{ $termTrainer }}
                                    </span>
                                @else
                                    <span class="badge" style="background:rgba(37,99,235,0.15); color:#2563eb; border:1px solid rgba(37,99,235,0.3);">
                                        <i class="fa-solid fa-id-badge me-1"></i> {{ $ar ? 'موظف / إداري' : 'Employee' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="att-text-main fw-semibold">
                                    <i class="fa-solid fa-location-dot att-text-muted me-1"></i>
                                    {{ $att->branch?->address ?: ($ar ? 'الفرع الرئيسي' : 'Main Branch') }}
                                </span>
                            </td>
                            <td>
                                <span class="att-badge-time">
                                    <i class="fa-solid fa-arrow-down text-success me-1"></i>
                                    {{ $att->check_in_at ? $att->check_in_at->format('H:i A') : '-' }}
                                </span>
                            </td>
                            <td>
                                @if($att->check_out_at)
                                    <span class="att-badge-time">
                                        <i class="fa-solid fa-arrow-up text-primary me-1"></i>
                                        {{ $att->check_out_at->format('H:i A') }}
                                    </span>
                                @else
                                    <span class="badge bg-success text-white">
                                        <i class="fa-solid fa-circle-dot me-1"></i> {{ $ar ? 'متواجد' : 'On Site' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong class="att-text-main">{{ $att->duration_formatted }}</strong>
                            </td>
                            <td>
                                @if($att->verification_method === 'dynamic_qr')
                                    <span class="badge" style="background:rgba(124,58,237,0.15); color:#7c3aed; border:1px solid rgba(124,58,237,0.3);" title="تم المسح عبر الكود اللحظي المتجدد بالفرع">
                                        <i class="fa-solid fa-qrcode me-1"></i> {{ $ar ? 'QR ديناميكي' : 'Dynamic QR' }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle att-text-muted border">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> {{ $ar ? 'يدوي' : 'Manual' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($att->status === 'present')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $ar ? 'حاضر' : 'Present' }}</span>
                                @elseif($att->status === 'late')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">{{ $ar ? 'متأخر' : 'Late' }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $att->status }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('academy.staff-attendance.destroy', $att) }}" onsubmit="return confirm('{{ $ar ? 'هل أنت متأكد من حذف هذا السجل؟' : 'Delete this record?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="{{ $ar ? 'حذف' : 'Delete' }}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 att-text-muted">
                                <i class="fa-solid fa-calendar-xmark fa-2x mb-2 d-block opacity-50"></i>
                                {{ $ar ? 'لا توجد تسجيلات حضور في هذا اليوم حتى الآن.' : 'No attendance entries for this date yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
            <div class="card-footer py-3">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Compact & High-Contrast Manual Attendance Entry -->
<div class="modal fade" id="manualAttendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered att-modal-dialog">
        <div class="modal-content att-modal-content border-0">
            
            <div class="modal-header py-3 px-4">
                <h6 class="modal-title fw-bold att-text-main d-flex align-items-center gap-2 m-0 fs-6">
                    <i class="fa-solid fa-pen-to-square text-success"></i>
                    <span>{{ $ar ? 'تسجيل حضور / انصراف يدوي' : 'Record Manual Attendance' }}</span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="POST" action="{{ route('academy.staff-attendance.manual') }}">
                @csrf
                <div class="modal-body p-3 px-4">
                    
                    <!-- Row 1: Staff Selection + Branch -->
                    <div class="row g-2 mb-2">
                        <div class="col-md-7 col-12">
                            <label class="att-label">{{ $ar ? 'اختر الموظف أو ' . $termTrainer : 'Select Staff or ' . $termTrainer }} <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" name="staff_target" required style="border-radius:8px;">
                                <option value="">{{ $ar ? '-- حدد الاسم --' : '-- Choose --' }}</option>
                                <optgroup label="{{ $ar ? 'الكادر الإداري والموظفين' : 'Administrative Staff' }}">
                                    @foreach($employees as $emp)
                                        <option value="employee:{{ $emp->id }}">{{ $emp->name }} ({{ $emp->phone ?: '-' }})</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ $termTrainers }}">
                                    @foreach($coaches as $c)
                                        <option value="coach:{{ $c->id }}">{{ $c->name }} ({{ $termTrainer }})</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-md-5 col-12">
                            <label class="att-label">{{ $ar ? 'الفرع' : 'Branch' }}</label>
                            <select class="form-select form-select-sm" name="branch_id" style="border-radius:8px;">
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->address ?: 'فرع #'.$b->id }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Date + Status -->
                    <div class="row g-2 mb-2">
                        <div class="col-md-6 col-12">
                            <label class="att-label">{{ $ar ? 'التاريخ' : 'Date' }} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" name="attendance_date" value="{{ date('Y-m-d') }}" required style="border-radius:8px;">
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="att-label">{{ $ar ? 'الحالة' : 'Status' }}</label>
                            <select class="form-select form-select-sm" name="status" style="border-radius:8px;">
                                <option value="present">{{ $ar ? 'حاضر في الموعد' : 'Present (On Time)' }}</option>
                                <option value="late">{{ $ar ? 'متأخر' : 'Late' }}</option>
                                <option value="excused">{{ $ar ? 'مستأذن / عذر معتمد' : 'Excused' }}</option>
                                <option value="absent">{{ $ar ? 'غائب' : 'Absent' }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Check-in + Check-out -->
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="att-label">{{ $ar ? 'وقت الحضور' : 'Check-In' }} <span class="text-danger">*</span></label>
                            <input type="time" class="form-control form-control-sm" name="check_in_time" value="09:00" required style="border-radius:8px;">
                        </div>
                        <div class="col-6">
                            <label class="att-label">{{ $ar ? 'وقت الانصراف (اختياري)' : 'Check-Out (Opt)' }}</label>
                            <input type="time" class="form-control form-control-sm" name="check_out_time" style="border-radius:8px;">
                        </div>
                    </div>

                    <!-- Row 4: Notes -->
                    <div class="mb-1">
                        <label class="att-label">{{ $ar ? 'ملاحظات' : 'Notes' }}</label>
                        <input type="text" class="form-control form-control-sm" name="notes" placeholder="{{ $ar ? 'أي ملاحظات إضافية...' : 'Optional notes...' }}" style="border-radius:8px;">
                    </div>

                </div>
                
                <div class="modal-footer py-2 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal" style="border-radius:8px;">{{ $ar ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-success btn-sm px-3 fw-bold" style="border-radius:8px;">{{ $ar ? 'حفظ السجل' : 'Save Record' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
