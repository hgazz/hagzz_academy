@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'فواتير اشتراك منصة Hagzz' : 'Hagzz Platform Subscription Invoices')

@section('content')
@php
    $ar = app()->getLocale() === 'ar';
@endphp

<style>
    .billing-text-main { color: #0f172a; }
    .billing-text-muted { color: #64748b; }
    body.dark .billing-text-main, .dark .billing-text-main { color: #f8fafc !important; }
    body.dark .billing-text-muted, .dark .billing-text-muted { color: #cbd5e1 !important; }
</style>

<div class="middle-content container-xxl p-0">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="mb-1 fw-bold billing-text-main">
                <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>
                {{ $ar ? 'فواتير اشتراك منصة Hagzz' : 'Hagzz Platform Invoices' }}
            </h3>
            <p class="billing-text-muted mb-0 fs-7">
                {{ $ar ? 'مراجعة المبالغ المستحقة والمدفوعة لحساب الأكاديمية وطباعة الفواتير الرسمية المعتمدة.' : 'Review billed and paid amounts for your facility and print authorized invoices.' }}
            </p>
        </div>
    </div>

    <!-- Invoices Table Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold billing-text-main">
                <i class="fa-solid fa-receipt me-2 text-success"></i>
                {{ $ar ? 'سجل الفواتير الصادرة' : 'Issued Invoices Log' }}
            </h6>
            <span class="badge bg-secondary-subtle billing-text-main border fw-bold">{{ $invoices->total() }} {{ $ar ? 'فاتورة' : 'invoices' }}</span>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                <thead>
                    <tr>
                        <th>{{ $ar ? 'رقم الفاتورة' : 'Invoice #' }}</th>
                        <th>{{ $ar ? 'الخطة والفترة' : 'Plan & Period' }}</th>
                        <th>{{ $ar ? 'الإجمالي' : 'Total' }}</th>
                        <th>{{ $ar ? 'المدفوع' : 'Paid' }}</th>
                        <th>{{ $ar ? 'المتبقي' : 'Balance' }}</th>
                        <th>{{ $ar ? 'الحالة' : 'Status' }}</th>
                        <th class="text-end">{{ $ar ? 'خيارات الطباعة' : 'Print Options' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="fw-bold billing-text-main">
                                <i class="fa-solid fa-file-invoice text-muted me-1"></i>
                                {{ $invoice->invoice_number }}
                            </td>
                            <td>
                                <strong class="d-block billing-text-main">{{ $invoice->subscription?->plan?->name ?: ($ar ? 'اشتراك المنصة' : 'Platform Subscription') }}</strong>
                                <small class="billing-text-muted">
                                    {{ $invoice->period_starts_at?->format('Y-m-d') }} — {{ $invoice->period_ends_at?->format('Y-m-d') }}
                                </small>
                            </td>
                            <td>
                                <strong class="billing-text-main">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency_code }}</strong>
                            </td>
                            <td class="text-success fw-bold">
                                {{ number_format($invoice->paid_amount, 2) }}
                            </td>
                            <td>
                                @if($invoice->balance > 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold">
                                        {{ number_format($invoice->balance, 2) }}
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        {{ $ar ? 'مسددة بالكامل' : 'Settled' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($invoice->status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $ar ? 'مدفوعة' : 'Paid' }}</span>
                                @elseif($invoice->status === 'void')
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $ar ? 'ملغاة' : 'Void' }}</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">{{ $invoice->status }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    @foreach(['a4' => 'A4', 'a5' => 'A5', 'pos' => 'POS'] as $paper => $label)
                                        <a class="btn btn-outline-primary" target="_blank" href="{{ route('academy.invoices.platform.print', ['invoice' => $invoice, 'paper' => $paper]) }}">
                                            <i class="fa-solid fa-print me-1"></i> {{ $label }}
                                        </a>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 billing-text-muted">
                                <i class="fa-solid fa-file-circle-check fa-2x mb-2 d-block opacity-50"></i>
                                {{ $ar ? 'لا توجد فواتير اشتراك مسجلة لحسابك حتى الآن.' : 'No subscription invoices issued for your account yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="card-footer py-3">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
