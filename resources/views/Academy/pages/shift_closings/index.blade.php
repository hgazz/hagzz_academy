@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'تقفيل الوردية وصندوق الكاش (Z-Report)' : 'Shift Closings & Cash Register')

@section('content')
<div class="middle-content container-xxl p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h3 class="mb-1 fw-bold">{{ app()->getLocale() === 'ar' ? 'تقارير تقفيل الوردية وصندوق الكاش' : 'Shift Closings & Cash Register' }}</h3>
            <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'متابعة مقبوضات الورديات، مطابقة الكاش الفعلي، وطباعة إيصالات Z-Report' : 'Daily cashier shift reconciliations and Z-Reports' }}</p>
        </div>
        <div>
            <a href="{{ route('academy.shift-closings.create') }}" class="btn btn-primary fw-bold">
                <i class="fa-solid fa-cash-register me-1"></i> {{ app()->getLocale() === 'ar' ? 'تقفيل وردية جديدة الآن' : 'Close Current Shift' }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'الوردية' : 'Shift' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'المسؤول' : 'Cashier' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'الفترة' : 'Time Window' }}</th>
                    <th class="text-nowrap">{{ app()->getLocale() === 'ar' ? 'كاش النظام' : 'System Cash' }}</th>
                    <th class="text-nowrap">{{ app()->getLocale() === 'ar' ? 'الكاش الفعلي' : 'Actual Cash' }}</th>
                    <th class="text-nowrap">{{ app()->getLocale() === 'ar' ? 'الفارق' : 'Difference' }}</th>
                    <th class="text-nowrap">{{ app()->getLocale() === 'ar' ? 'إجمالي التحصيل' : 'Total Collected' }}</th>
                    <th class="text-center">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($closings as $closing)
                    @php
                        $diff = (float) $closing->cash_difference;
                    @endphp
                    <tr>
                        <td class="fw-bold">#{{ $closing->id }}</td>
                        <td>
                            <strong class="d-block text-dark">{{ $closing->shift_title }}</strong>
                            @if($closing->next_shift_receiver)
                                <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'المستلم:' : 'Receiver:' }} {{ $closing->next_shift_receiver }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $closing->closed_by_name }}</span>
                        </td>
                        <td class="text-nowrap">
                            <div>{{ $closing->closed_at->format('Y-m-d') }}</div>
                            <small class="text-muted">{{ $closing->started_at->format('H:i') }} - {{ $closing->closed_at->format('H:i') }}</small>
                        </td>
                        <td class="text-nowrap fw-bold text-dark">
                            {{ number_format($closing->total_cash_system, 2) }} EGP
                        </td>
                        <td class="text-nowrap fw-bold text-success">
                            {{ number_format($closing->actual_cash_counted, 2) }} EGP
                        </td>
                        <td class="text-nowrap">
                            @if($diff == 0)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">
                                    {{ app()->getLocale() === 'ar' ? 'مطابق تماماً' : 'Balanced' }}
                                </span>
                            @elseif($diff > 0)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1">
                                    + {{ number_format($diff, 2) }} ({{ app()->getLocale() === 'ar' ? 'زيادة' : 'Surplus' }})
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1 fw-bold">
                                    {{ number_format($diff, 2) }} ({{ app()->getLocale() === 'ar' ? 'عجز' : 'Shortage' }})
                                </span>
                            @endif
                        </td>
                        <td class="text-nowrap fw-bold" style="color:#0f766e; font-size:14px;">
                            {{ number_format($closing->total_collected_system, 2) }} EGP
                        </td>
                        <td class="text-center text-nowrap">
                            <a href="{{ route('academy.shift-closings.show', $closing) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-receipt"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'تقرير Z-Report' : 'View Report' }}</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-cash-register fa-2x mb-2 d-block text-secondary"></i>
                            {{ app()->getLocale() === 'ar' ? 'لا توجد تقارير تقفيل ورديات مسجلة حتى الآن.' : 'No shift closings recorded yet.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $closings->links() }}
    </div>
</div>
@endsection
