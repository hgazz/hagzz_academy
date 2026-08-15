@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'تقفيل الوردية الحالية' : 'Close Current Shift')

@section('content')
<div class="middle-content container-xxl p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h3 class="mb-1 fw-bold">{{ app()->getLocale() === 'ar' ? 'تقفيل الوردية الحالية وصندوق الكاش' : 'Reconcile & Close Shift' }}</h3>
            <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'حساب تحصيلات الوردية الحالية ومطابقة الكاش الفعلي بالدرج' : 'Calculate current shift totals and balance the cash drawer' }}</p>
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
            <!-- Left Column: Metrics Summary -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fa-solid fa-chart-pie me-2 text-primary"></i> {{ app()->getLocale() === 'ar' ? 'تحصيلات الوردية المسجلة بالنظام' : 'System Recorded Collections' }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-success bg-opacity-10 border border-success border-opacity-25">
                                    <span class="text-muted d-block mb-1 fs-6">{{ app()->getLocale() === 'ar' ? 'مقبوضات الكاش (نقداً)' : 'Cash Collected' }}</span>
                                    <h3 class="fw-bold text-success mb-0" id="sysCashValue">{{ number_format($metrics['cash'], 2) }} <small class="fs-6">EGP</small></h3>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-primary bg-opacity-10 border border-primary border-opacity-25">
                                    <span class="text-muted d-block mb-1 fs-6">{{ app()->getLocale() === 'ar' ? 'البطاقات / فيزا (POS)' : 'Card / POS' }}</span>
                                    <h3 class="fw-bold text-primary mb-0">{{ number_format($metrics['card'], 2) }} <small class="fs-6">EGP</small></h3>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-info bg-opacity-10 border border-info border-opacity-25">
                                    <span class="text-muted d-block mb-1 fs-6">{{ app()->getLocale() === 'ar' ? 'إنستا باي (InstaPay)' : 'InstaPay' }}</span>
                                    <h3 class="fw-bold text-info mb-0">{{ number_format($metrics['instapay'], 2) }} <small class="fs-6">EGP</small></h3>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded bg-warning bg-opacity-10 border border-warning border-opacity-25">
                                    <span class="text-muted d-block mb-1 fs-6">{{ app()->getLocale() === 'ar' ? 'فوري / تحويلات أخرى' : 'Fawry / Transfers' }}</span>
                                    <h3 class="fw-bold text-warning mb-0">{{ number_format($metrics['fawry'] + $metrics['bank_transfer'] + $metrics['other'], 2) }} <small class="fs-6">EGP</small></h3>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fs-6 text-muted">{{ app()->getLocale() === 'ar' ? 'إجمالي الخصومات والتسويات المعتمدة:' : 'Approved Discounts:' }}</span>
                            <span class="fw-bold fs-6" style="color:#7e22ce;">- {{ number_format($metrics['discounts'], 2) }} EGP</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background:#f0fdf4;">
                            <span class="fs-5 fw-bold text-dark">{{ app()->getLocale() === 'ar' ? 'إجمالي التحصيلات الكلية للوردية:' : 'Total Shift Collections:' }}</span>
                            <span class="fs-4 fw-bold text-success">{{ number_format($metrics['total_collected'], 2) }} EGP</span>
                        </div>
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
                            <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'اسم / عنوان الوردية:' : 'Shift Title:' }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="shift_title" value="{{ app()->getLocale() === 'ar' ? 'وردية ' . now()->format('Y-m-d') . ' (' . (now()->hour < 16 ? 'صباحية' : 'مسائية') . ')' : 'Shift ' . now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'بداية الوردية:' : 'Started At:' }}</label>
                                <input type="datetime-local" class="form-control" name="started_at" value="{{ $startedAt->format('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'نهاية الوردية:' : 'Closed At:' }}</label>
                                <input type="datetime-local" class="form-control" name="closed_at" value="{{ $closedAt->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded mb-3">
                            <label class="form-label fw-bold fs-6 text-dark">{{ app()->getLocale() === 'ar' ? 'الكاش الفعلي الموجود في الدرج (العد اليدوي):' : 'Actual Cash in Drawer:' }} <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <input type="number" step="0.01" min="0" class="form-control fw-bold text-success fs-4" name="actual_cash_counted" id="actualCashInput" placeholder="0.00" required oninput="calculateDifference()">
                                <span class="input-group-text fw-bold">EGP</span>
                            </div>
                            <div class="mt-2 text-end">
                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" onclick="document.getElementById('actualCashInput').value = {{ $metrics['cash'] }}; calculateDifference();">
                                    {{ app()->getLocale() === 'ar' ? 'مطابقة مع كاش النظام تلقائياً' : 'Match System Cash' }}
                                </button>
                            </div>
                        </div>

                        <div id="diffAlert" class="alert alert-secondary py-2 mb-3 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">{{ app()->getLocale() === 'ar' ? 'حالة المطابقة / الفارق:' : 'Difference status:' }}</span>
                            <strong id="diffText" class="fs-6">-</strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'اسم مستلم الوردية التالية (اختياري):' : 'Next Shift Receiver:' }}</label>
                            <input type="text" class="form-control" name="next_shift_receiver" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم الموظف أو المشرف المستلم...' : 'Employee receiving next shift...' }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'ملاحظات تسليم الوردية:' : 'Shift Notes:' }}</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'أي ملاحظات خاصة بالوردية أو صندوق الكاش...' : 'Any handover notes...' }}"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">
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
