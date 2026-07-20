@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'التقارير المالية' : 'Financial reports')

@push('css')
    <link rel="stylesheet" href="{{ asset('assetsAdmin/src/assets/css/academy-financial-reports.css') }}">
@endpush

@php
    $ar = app()->getLocale() === 'ar';
    $copy = $ar ? [
        'title' => 'مركز التقارير المالية', 'subtitle' => 'صورة كاملة للحسابات والحجوزات والملاعب في مكان واحد.',
        'billed' => 'إجمالي المستحق', 'collected' => 'إجمالي المحصل', 'remaining' => 'إجمالي المتبقي',
        'rate' => 'نسبة التحصيل', 'records' => 'سجل مالي', 'cancelled' => 'ملغي', 'currency' => 'ج.م',
        'filters' => 'تصفية التقرير', 'from' => 'من تاريخ', 'to' => 'إلى تاريخ', 'source' => 'مصدر التقرير',
        'payment' => 'حالة السداد', 'search' => 'بحث بالاسم أو الهاتف أو المرجع', 'apply' => 'تطبيق', 'reset' => 'إلغاء الفلاتر',
        'all' => 'الكل', 'subscriptions' => 'حسابات واشتراكات الطلاب', 'training' => 'حجوزات التدريبات', 'venues' => 'حجوزات الملاعب',
        'paid' => 'مدفوع', 'partial' => 'مدفوع جزئيًا', 'unpaid' => 'غير مدفوع', 'export' => 'تصدير CSV',
        'customer' => 'العميل / الطالب', 'service' => 'الخدمة', 'date' => 'التاريخ', 'amount' => 'المستحق',
        'paidAmount' => 'المحصل', 'remainingAmount' => 'المتبقي', 'method' => 'وسيلة الدفع', 'status' => 'الحالة',
        'reference' => 'المرجع', 'period' => 'الفترة', 'phone' => 'الهاتف', 'noData' => 'لا توجد بيانات مطابقة للفلاتر الحالية.',
        'details' => 'تفاصيل كاملة', 'summary' => 'ملخص المصدر', 'activeRecords' => 'السجلات المحتسبة',
    ] : [
        'title' => 'Financial reporting center', 'subtitle' => 'A complete view of academy accounts, bookings, and venues in one place.',
        'billed' => 'Total billed', 'collected' => 'Total collected', 'remaining' => 'Total outstanding',
        'rate' => 'Collection rate', 'records' => 'financial records', 'cancelled' => 'cancelled', 'currency' => 'EGP',
        'filters' => 'Report filters', 'from' => 'From date', 'to' => 'To date', 'source' => 'Report source',
        'payment' => 'Payment status', 'search' => 'Search by name, phone, or reference', 'apply' => 'Apply', 'reset' => 'Reset filters',
        'all' => 'All', 'subscriptions' => 'Student accounts & subscriptions', 'training' => 'Training bookings', 'venues' => 'Venue bookings',
        'paid' => 'Paid', 'partial' => 'Partially paid', 'unpaid' => 'Unpaid', 'export' => 'Export CSV',
        'customer' => 'Customer / student', 'service' => 'Service', 'date' => 'Date', 'amount' => 'Billed',
        'paidAmount' => 'Collected', 'remainingAmount' => 'Outstanding', 'method' => 'Payment method', 'status' => 'Status',
        'reference' => 'Reference', 'period' => 'Period', 'phone' => 'Phone', 'noData' => 'No records match the current filters.',
        'details' => 'Full details', 'summary' => 'Source summary', 'activeRecords' => 'included records',
    ];
    $paymentLabels = ['paid' => $copy['paid'], 'partial' => $copy['partial'], 'unpaid' => $copy['unpaid']];
    $sourceLabels = ['subscriptions' => $copy['subscriptions'], 'training' => $copy['training'], 'venues' => $copy['venues']];
    $money = fn ($value) => number_format((float) $value, 2);
    $queryFilters = array_filter($filters, fn ($value) => $value !== null && $value !== '' && $value !== 'all');
@endphp

@section('content')
    <div class="middle-content container-xxl p-0">
        <main class="financial-reports" dir="{{ $ar ? 'rtl' : 'ltr' }}">
            <header class="fr-hero">
                <div class="fr-hero-copy">
                    <button type="button" class="sidebarCollapse fr-menu" aria-label="{{ $ar ? 'فتح القائمة' : 'Open menu' }}">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div>
                        <span class="fr-kicker">HAGZZ BUSINESS</span>
                        <h1>{{ $copy['title'] }}</h1>
                        <p>{{ $copy['subtitle'] }}</p>
                    </div>
                </div>
                <div class="fr-hero-meta">
                    <strong>{{ number_format($summary['records']) }}</strong>
                    <span>{{ $copy['records'] }}</span>
                    <small>{{ number_format($summary['cancelled']) }} {{ $copy['cancelled'] }}</small>
                </div>
            </header>

            <section class="fr-kpis" aria-label="{{ $copy['summary'] }}">
                <article class="fr-kpi is-billed"><i class="fa-solid fa-file-invoice-dollar"></i><div><span>{{ $copy['billed'] }}</span><strong>{{ $money($summary['billed']) }}</strong><small>{{ $copy['currency'] }}</small></div></article>
                <article class="fr-kpi is-collected"><i class="fa-solid fa-circle-check"></i><div><span>{{ $copy['collected'] }}</span><strong>{{ $money($summary['collected']) }}</strong><small>{{ $copy['currency'] }}</small></div></article>
                <article class="fr-kpi is-remaining"><i class="fa-solid fa-hourglass-half"></i><div><span>{{ $copy['remaining'] }}</span><strong>{{ $money($summary['remaining']) }}</strong><small>{{ $copy['currency'] }}</small></div></article>
                <article class="fr-kpi is-rate"><i class="fa-solid fa-chart-line"></i><div><span>{{ $copy['rate'] }}</span><strong>{{ number_format($summary['collection_rate'], 1) }}%</strong><small>{{ $summary['collection_rate'] >= 80 ? ($ar ? 'ممتاز' : 'Excellent') : ($ar ? 'تحتاج متابعة' : 'Needs follow-up') }}</small></div></article>
            </section>

            <section class="fr-breakdown">
                @foreach($breakdown as $source => $totals)
                    <a class="fr-source-card" href="#{{ $source }}-report">
                        <div class="fr-source-head"><i class="fa-solid {{ $source === 'subscriptions' ? 'fa-user-graduate' : ($source === 'training' ? 'fa-ticket' : 'fa-futbol') }}"></i><strong>{{ $sourceLabels[$source] }}</strong></div>
                        <div class="fr-source-values"><span>{{ $copy['collected'] }} <b>{{ $money($totals['collected']) }}</b></span><span>{{ $copy['remaining'] }} <b>{{ $money($totals['remaining']) }}</b></span></div>
                        <small>{{ number_format($totals['records']) }} {{ $copy['activeRecords'] }}</small>
                    </a>
                @endforeach
            </section>

            <section class="fr-filter-panel">
                <div class="fr-section-heading"><div><span>{{ $copy['filters'] }}</span><h2>{{ $copy['details'] }}</h2></div><i class="fa-solid fa-sliders"></i></div>
                <form method="GET" action="{{ route('academy.report.overview') }}" class="fr-filter-form">
                    <label><span>{{ $copy['from'] }}</span><input type="date" name="start_date" value="{{ $filters['start_date'] }}"></label>
                    <label><span>{{ $copy['to'] }}</span><input type="date" name="end_date" value="{{ $filters['end_date'] }}"></label>
                    <label><span>{{ $copy['source'] }}</span><select name="source"><option value="all">{{ $copy['all'] }}</option>@foreach($sourceLabels as $value => $label)<option value="{{ $value }}" @selected($filters['source'] === $value)>{{ $label }}</option>@endforeach</select></label>
                    <label><span>{{ $copy['payment'] }}</span><select name="payment_status"><option value="all">{{ $copy['all'] }}</option>@foreach($paymentLabels as $value => $label)<option value="{{ $value }}" @selected($filters['payment_status'] === $value)>{{ $label }}</option>@endforeach</select></label>
                    <label class="fr-search"><span>{{ $copy['search'] }}</span><div><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="search" value="{{ $filters['search'] }}" maxlength="100"></div></label>
                    <div class="fr-filter-actions"><button type="submit"><i class="fa-solid fa-filter"></i>{{ $copy['apply'] }}</button><a href="{{ route('academy.report.overview') }}">{{ $copy['reset'] }}</a></div>
                </form>
            </section>

            <nav class="fr-source-tabs" aria-label="{{ $copy['source'] }}">
                @foreach(['all' => $copy['all']] + $sourceLabels as $value => $label)
                    <a class="{{ $filters['source'] === $value ? 'active' : '' }}" href="{{ route('academy.report.overview', array_merge($queryFilters, ['source' => $value])) }}">{{ $label }}</a>
                @endforeach
            </nav>

            @if(in_array($filters['source'], ['all', 'subscriptions'], true))
                <section class="fr-report-panel" id="subscriptions-report">
                    <header><div><i class="fa-solid fa-user-graduate"></i><div><h2>{{ $copy['subscriptions'] }}</h2><p>{{ $copy['details'] }}</p></div></div><a href="{{ route('academy.report.overview.export', array_merge(['type' => 'subscriptions'], $queryFilters)) }}"><i class="fa-solid fa-download"></i>{{ $copy['export'] }}</a></header>
                    <div class="fr-table-wrap"><table><thead><tr><th>#</th><th>{{ $copy['customer'] }}</th><th>{{ $copy['service'] }}</th><th>{{ $copy['period'] }}</th><th>{{ $copy['amount'] }}</th><th>{{ $copy['paidAmount'] }}</th><th>{{ $copy['remainingAmount'] }}</th><th>{{ $copy['status'] }}</th></tr></thead><tbody>
                    @forelse($subscriptions as $subscription)
                        @php $paid = (float) ($subscription->payments_sum_amount ?? 0); $remaining = max(0, (float) $subscription->amount - $paid); @endphp
                        <tr><td>{{ $subscription->id }}</td><td><strong>{{ $subscription->student?->name ?: '-' }}</strong><small>{{ $subscription->student?->phone ?: '-' }}</small></td><td>{{ $subscription->group?->name ?: '-' }}</td><td><strong>{{ $subscription->starts_on?->format('Y-m-d') }}</strong><small>{{ $subscription->ends_on?->format('Y-m-d') }}</small></td><td>{{ $money($subscription->amount) }}</td><td class="is-positive">{{ $money($paid) }}</td><td class="{{ $remaining > 0 ? 'is-negative' : '' }}">{{ $money($remaining) }}</td><td><span class="fr-status is-{{ $subscription->payment_status }}">{{ $paymentLabels[$subscription->payment_status] ?? $subscription->payment_status }}</span><small>{{ $subscription->status }}</small></td></tr>
                    @empty <tr><td colspan="8" class="fr-empty">{{ $copy['noData'] }}</td></tr> @endforelse
                    </tbody></table></div>{{ $subscriptions->links() }}
                </section>
            @endif

            @if(in_array($filters['source'], ['all', 'training'], true))
                <section class="fr-report-panel" id="training-report">
                    <header><div><i class="fa-solid fa-ticket"></i><div><h2>{{ $copy['training'] }}</h2><p>{{ $copy['details'] }}</p></div></div><a href="{{ route('academy.report.overview.export', array_merge(['type' => 'training'], $queryFilters)) }}"><i class="fa-solid fa-download"></i>{{ $copy['export'] }}</a></header>
                    <div class="fr-table-wrap"><table><thead><tr><th>{{ $copy['reference'] }}</th><th>{{ $copy['customer'] }}</th><th>{{ $copy['service'] }}</th><th>{{ $copy['date'] }}</th><th>{{ $copy['amount'] }}</th><th>{{ $copy['paidAmount'] }}</th><th>{{ $copy['remainingAmount'] }}</th><th>{{ $copy['method'] }}</th><th>{{ $copy['status'] }}</th></tr></thead><tbody>
                    @forelse($trainingBookings as $booking)
                        <tr><td><strong>{{ $booking->order_number ?: '#' . $booking->id }}</strong><small>{{ $booking->user_type }}</small></td><td><strong>{{ $booking->user?->name ?: '-' }}</strong><small>{{ $booking->user?->phone ?: '-' }}</small></td><td>{{ $booking->training?->name ?: '-' }}</td><td>{{ $booking->created_at?->format('Y-m-d') }}</td><td>{{ $money($booking->amount) }}</td><td class="is-positive">{{ $money($booking->collected_amount) }}</td><td class="{{ $booking->remaining_amount > 0 ? 'is-negative' : '' }}">{{ $money($booking->remaining_amount) }}</td><td>{{ $booking->payment_method_label }}</td><td>@if($booking->is_canceled)<span class="fr-status is-cancelled">{{ $ar ? 'ملغي' : 'Cancelled' }}</span>@else<span class="fr-status is-{{ $booking->payment_state }}">{{ $paymentLabels[$booking->payment_state] }}</span>@endif</td></tr>
                    @empty <tr><td colspan="9" class="fr-empty">{{ $copy['noData'] }}</td></tr> @endforelse
                    </tbody></table></div>{{ $trainingBookings->links() }}
                </section>
            @endif

            @if(in_array($filters['source'], ['all', 'venues'], true))
                <section class="fr-report-panel" id="venues-report">
                    <header><div><i class="fa-solid fa-futbol"></i><div><h2>{{ $copy['venues'] }}</h2><p>{{ $copy['details'] }}</p></div></div><a href="{{ route('academy.report.overview.export', array_merge(['type' => 'venues'], $queryFilters)) }}"><i class="fa-solid fa-download"></i>{{ $copy['export'] }}</a></header>
                    <div class="fr-table-wrap"><table><thead><tr><th>{{ $copy['reference'] }}</th><th>{{ $copy['customer'] }}</th><th>{{ $copy['service'] }}</th><th>{{ $copy['date'] }}</th><th>{{ $copy['amount'] }}</th><th>{{ $copy['paidAmount'] }}</th><th>{{ $copy['remainingAmount'] }}</th><th>{{ $copy['method'] }}</th><th>{{ $copy['status'] }}</th></tr></thead><tbody>
                    @forelse($venueBookings as $booking)
                        <tr><td><strong>{{ $booking->reference }}</strong><small>{{ $booking->source }}</small></td><td><strong>{{ $booking->customer?->name ?: '-' }}</strong><small>{{ $booking->customer?->phone ?: '-' }}</small></td><td><strong>{{ $booking->space?->venue?->name ?: '-' }}</strong><small>{{ $booking->space?->name ?: '-' }}</small></td><td><strong>{{ $booking->starts_at?->format('Y-m-d') }}</strong><small>{{ $booking->starts_at?->format('H:i') }} – {{ $booking->ends_at?->format('H:i') }}</small></td><td>{{ $money($booking->total_amount) }}</td><td class="is-positive">{{ $money($booking->paid_amount) }}</td><td class="{{ $booking->remaining_amount > 0 ? 'is-negative' : '' }}">{{ $money($booking->remaining_amount) }}</td><td>{{ $booking->payment_method }}</td><td>@if($booking->status === 'cancelled')<span class="fr-status is-cancelled">{{ $ar ? 'ملغي' : 'Cancelled' }}</span>@else<span class="fr-status is-{{ $booking->payment_status }}">{{ $paymentLabels[$booking->payment_status] }}</span><small>{{ $booking->status }}</small>@endif</td></tr>
                    @empty <tr><td colspan="9" class="fr-empty">{{ $copy['noData'] }}</td></tr> @endforelse
                    </tbody></table></div>{{ $venueBookings->links() }}
                </section>
            @endif
        </main>
    </div>
@endsection
