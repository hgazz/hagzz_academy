@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'تقفيل الوردية الحالية' : 'Close Current Shift')

@section('content')
<style>
    .metric-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .metric-cash { border-color: #10b981; background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%); }
    .metric-card-pos { border-color: #3b82f6; background: linear-gradient(180deg, #ffffff 0%, #eff6ff 100%); }
    .metric-instapay { border-color: #8b5cf6; background: linear-gradient(180deg, #ffffff 0%, #f5f3ff 100%); }
    .metric-other { border-color: #f59e0b; background: linear-gradient(180deg, #ffffff 0%, #fffbeb 100%); }

    .metric-title {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .metric-value {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.1;
        margin: 0;
    }
    .metric-currency {
        font-size: 14px;
        font-weight: 700;
        opacity: 0.85;
    }
</style>

<div class="middle-content container-xxl p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-dark">{{ app()->getLocale() === 'ar' ? 'تقفيل الوردية الحالية ومطابقة الكاش' : 'Reconcile & Close Shift' }}</h3>
            <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'حساب تحصيلات الوردية الحالية، مراجعة العمليات، ومطابقة الكاش الفعلي بالدرج' : 'Calculate current shift totals and balance the cash drawer' }}</p>
        </div>
        <div>
            <a href="{{ route('academy.shift-closings.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-right me-1"></i> {{ app()->getLocale() === 'ar' ? 'سجل الورديات السابقة' : 'Past Shifts' }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('academy.shift-closings.store') }}">
        @csrf
        <div class="row g-4">
            <!-- Left Column: Metrics & Detailed Transactions -->
            <div class="col-lg-7">
                <!-- 4 High-Contrast Metrics Cards -->
                <div class="row g-3 mb-4">
                    <!-- Cash -->
                    <div class="col-sm-6">
                        <div class="metric-card metric-cash">
                            <div class="metric-title text-success" style="color:#065f46 !important;">
                                <i class="fa-solid fa-money-bill-wave fa-lg"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'مقبوضات الكاش (نقداً)' : 'Cash Collected' }}</span>
                            </div>
                            <div class="metric-value text-success" style="color:#047857 !important;" id="sysCashValue">
                                {{ number_format($metrics['cash'], 2) }} <span class="metric-currency">EGP</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cards / POS -->
                    <div class="col-sm-6">
                        <div class="metric-card metric-card-pos">
                            <div class="metric-title text-primary" style="color:#1e40af !important;">
                                <i class="fa-solid fa-credit-card fa-lg"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'البطاقات / فيزا (POS)' : 'Card / POS' }}</span>
                            </div>
                            <div class="metric-value text-primary" style="color:#1d4ed8 !important;">
                                {{ number_format($metrics['card'], 2) }} <span class="metric-currency">EGP</span>
                            </div>
                        </div>
                    </div>

                    <!-- InstaPay -->
                    <div class="col-sm-6">
                        <div class="metric-card metric-instapay">
                            <div class="metric-title" style="color:#5b21b6 !important;">
                                <i class="fa-solid fa-mobile-screen-button fa-lg"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'إنستا باي (InstaPay)' : 'InstaPay' }}</span>
                            </div>
                            <div class="metric-value" style="color:#6d28d9 !important;">
                                {{ number_format($metrics['instapay'], 2) }} <span class="metric-currency">EGP</span>
                            </div>
                        </div>
                    </div>

                    <!-- Other / Fawry / Bank -->
                    <div class="col-sm-6">
                        <div class="metric-card metric-other">
                            <div class="metric-title" style="color:#92400e !important;">
                                <i class="fa-solid fa-building-columns fa-lg"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'فوري / تحويلات أخرى' : 'Fawry / Transfers' }}</span>
                            </div>
                            <div class="metric-value" style="color:#b45309 !important;">
                                {{ number_format($metrics['fawry'] + $metrics['bank_transfer'] + $metrics['other'], 2) }} <span class="metric-currency">EGP</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shift Overall Summary -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                            <span class="fs-6 text-muted fw-bold">{{ app()->getLocale() === 'ar' ? 'إجمالي الخصومات والتسويات المعتمدة:' : 'Approved Discounts:' }}</span>
                            <span class="fw-bold fs-6" style="color:#7e22ce;">- {{ number_format($metrics['discounts'], 2) }} EGP</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background:#f0fdf4; border: 1px solid #bbf7d0;">
                            <span class="fs-5 fw-bold text-dark">{{ app()->getLocale() === 'ar' ? 'إجمالي التحصيلات الكلية للوردية:' : 'Total Shift Collections:' }}</span>
                            <span class="fs-4 fw-bold text-success">{{ number_format($metrics['total_collected'], 2) }} EGP</span>
                        </div>
                    </div>
                </div>

                <!-- Detailed Transactions Table -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fa-solid fa-list-check me-2 text-primary"></i> 
                            {{ app()->getLocale() === 'ar' ? 'تفاصيل عمليات وتحصيلات الوردية (' . count($metrics['transactions']) . ' عملية)' : 'Shift Transactions Breakdown' }}
                        </h5>
                    </div>
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>{{ app()->getLocale() === 'ar' ? 'الوقت' : 'Time' }}</th>
                                    <th>{{ app()->getLocale() === 'ar' ? 'البيان / العميل' : 'Customer / Details' }}</th>
                                    <th>{{ app()->getLocale() === 'ar' ? 'طريقة الدفع' : 'Method' }}</th>
                                    <th class="text-end">{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($metrics['transactions'] as $tx)
                                    <tr>
                                        <td class="text-nowrap text-muted">
                                            <i class="fa-solid fa-clock me-1" style="font-size: 11px;"></i>
                                            {{ $tx['time'] ? Carbon\Carbon::parse($tx['time'])->format('H:i') : '-' }}
                                        </td>
                                        <td>
                                            <strong class="d-block text-dark">{{ $tx['customer'] }}</strong>
                                            <small class="text-muted">{{ $tx['type_label'] }} · {{ $tx['ref'] }}</small>
                                        </td>
                                        <td>
                                            @if($tx['method_key'] === 'cash')
                                                <span class="badge" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;">
                                                    <i class="fa-solid fa-money-bill me-1"></i> {{ $tx['method_label'] }}
                                                </span>
                                            @elseif($tx['method_key'] === 'card')
                                                <span class="badge" style="background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe;">
                                                    <i class="fa-solid fa-credit-card me-1"></i> {{ $tx['method_label'] }}
                                                </span>
                                            @elseif($tx['method_key'] === 'instapay')
                                                <span class="badge" style="background:#f3e8ff; color:#7e22ce; border:1px solid #e9d5ff;">
                                                    <i class="fa-solid fa-mobile-screen me-1"></i> {{ $tx['method_label'] }}
                                                </span>
                                            @elseif($tx['method_key'] === 'discount')
                                                <span class="badge" style="background:#faf5ff; color:#7e22ce; border:1px dashed #d8b4fe;">
                                                    <i class="fa-solid fa-tag me-1"></i> {{ $tx['method_label'] }}
                                                </span>
                                            @else
                                                <span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a;">
                                                    {{ $tx['method_label'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end text-nowrap">
                                            @if($tx['amount'] > 0)
                                                <strong class="text-success fs-6">+ {{ number_format($tx['amount'], 2) }}</strong>
                                            @endif
                                            @if($tx['discount'] > 0)
                                                <small class="d-block fw-bold" style="color:#7e22ce;">- {{ number_format($tx['discount'], 2) }} (خصم)</small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            {{ app()->getLocale() === 'ar' ? 'لا توجد عمليات مسجلة خلال فترة هذه الوردية حتى الآن.' : 'No transactions recorded during this shift window yet.' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Shift Details & Cash Count -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fa-solid fa-cash-register me-2 text-success"></i> {{ app()->getLocale() === 'ar' ? 'بيانات ومطابقة الوردية' : 'Reconciliation Details' }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">{{ app()->getLocale() === 'ar' ? 'اسم / عنوان الوردية:' : 'Shift Title:' }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="shift_title" value="{{ app()->getLocale() === 'ar' ? 'وردية ' . now()->format('Y-m-d') . ' (' . (now()->hour < 16 ? 'صباحية' : 'مسائية') . ')' : 'Shift ' . now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark">{{ app()->getLocale() === 'ar' ? 'بداية الوردية:' : 'Started At:' }}</label>
                                <input type="datetime-local" class="form-control" name="started_at" value="{{ $startedAt->format('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark">{{ app()->getLocale() === 'ar' ? 'نهاية الوردية:' : 'Closed At:' }}</label>
                                <input type="datetime-local" class="form-control" name="closed_at" value="{{ $closedAt->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded mb-3 border">
                            <label class="form-label fw-bold fs-6 text-dark">{{ app()->getLocale() === 'ar' ? 'الكاش الفعلي الموجود في الدرج (العد اليدوي):' : 'Actual Cash in Drawer:' }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <input type="number" step="0.01" min="0" class="form-control fw-bold text-success fs-4 bg-white" name="actual_cash_counted" id="actualCashInput" placeholder="0.00" required oninput="calculateDifference()">
                                <span class="input-group-text fw-bold">EGP</span>
                            </div>
                            <div class="mt-2 text-end">
                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold" onclick="document.getElementById('actualCashInput').value = {{ $metrics['cash'] }}; calculateDifference();">
                                    {{ app()->getLocale() === 'ar' ? 'مطابقة مع كاش النظام تلقائياً' : 'Match System Cash' }}
                                </button>
                            </div>
                        </div>

                        <div id="diffAlert" class="alert alert-secondary py-2 mb-3 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">{{ app()->getLocale() === 'ar' ? 'حالة المطابقة / الفارق:' : 'Difference status:' }}</span>
                            <strong id="diffText" class="fs-6">-</strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">{{ app()->getLocale() === 'ar' ? 'اسم مستلم الوردية التالية (اختياري):' : 'Next Shift Receiver:' }}</label>
                            <input type="text" class="form-control" name="next_shift_receiver" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم الموظف أو المشرف المستلم...' : 'Employee receiving next shift...' }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-dark">{{ app()->getLocale() === 'ar' ? 'ملاحظات تسليم الوردية:' : 'Shift Notes:' }}</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'أي ملاحظات خاصة بالوردية أو صندوق الكاش...' : 'Any handover notes...' }}"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">
                            <i class="fa-solid fa-lock me-1"></i> {{ app()->getLocale() === 'ar' ? 'إغلاق الوردية وإصدار تقرير Z-Report' : 'Confirm Close & Generate Z-Report' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('js')
<script>
    const sysCash = {{ (float) $metrics['cash'] }};
    const isAr = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};

    function calculateDifference() {
        const val = parseFloat(document.getElementById('actualCashInput').value) || 0;
        const diff = (val - sysCash).toFixed(2);
        const diffAlert = document.getElementById('diffAlert');
        const diffText = document.getElementById('diffText');

        if (val === 0 && !document.getElementById('actualCashInput').value) {
            diffAlert.className = 'alert alert-secondary py-2 mb-3 d-flex justify-content-between align-items-center';
            diffText.textContent = '-';
            return;
        }

        if (diff == 0) {
            diffAlert.className = 'alert alert-success py-2 mb-3 d-flex justify-content-between align-items-center';
            diffText.textContent = isAr ? '✅ مطابق تماماً (0.00 EGP)' : '✅ Balanced (0.00 EGP)';
        } else if (diff > 0) {
            diffAlert.className = 'alert alert-primary py-2 mb-3 d-flex justify-content-between align-items-center';
            diffText.textContent = isAr ? '➕ زيادة في الكاش: +' + diff + ' EGP' : '➕ Surplus: +' + diff + ' EGP';
        } else {
            diffAlert.className = 'alert alert-danger py-2 mb-3 d-flex justify-content-between align-items-center';
            diffText.textContent = isAr ? '⚠️ عجز في الكاش: ' + diff + ' EGP' : '⚠️ Shortage: ' + diff + ' EGP';
        }
    }
</script>
@endpush
@endsection
