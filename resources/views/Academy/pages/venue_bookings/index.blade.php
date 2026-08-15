@extends('Academy.Layouts.master')

@section('title', trans('admin.venues.bookings'))

@section('content')
<div class="middle-content container-xxl p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h3 class="mb-1 fw-bold">{{ trans('admin.venues.bookings') }}</h3>
            <p class="text-muted mb-0">{{ trans('admin.venues.bookings_hint') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('academy.venue-bookings.calendar') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-calendar-days me-1"></i> {{ app()->getLocale() === 'ar' ? 'تقويم الحجوزات' : 'Bookings Calendar' }}
            </a>
            <a href="{{ route('academy.venue-bookings.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-calendar-plus me-1"></i> {{ trans('admin.venues.add_booking') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-info me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ trans('admin.venues.reference') }}</th>
                    <th>{{ trans('admin.venues.customer') }}</th>
                    <th>{{ trans('admin.venues.space') }}</th>
                    <th>{{ trans('admin.venues.time') }} & {{ trans('admin.status') }}</th>
                    <th>{{ trans('admin.venues.total') }}</th>
                    <th>{{ trans('admin.venues.paid') ?? 'المدفوع' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'المتبقي' : 'Remaining' }}</th>
                    <th>{{ trans('admin.venues.payment') }}</th>
                    <th class="text-center">{{ trans('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    @php
                        $remaining = $booking->remaining_amount;
                        $pStatus = $booking->payment_status;
                        $bStatus = $booking->status;

                        $statusBadgeClass = match($bStatus) {
                            'confirmed' => 'bg-primary text-white',
                            'checked_in', 'completed' => 'bg-success text-white',
                            'pending' => 'bg-warning text-dark',
                            'cancelled' => 'bg-danger text-white',
                            'no_show' => 'bg-secondary text-white',
                            default => 'bg-light text-dark',
                        };

                        $paymentBadgeClass = match($pStatus) {
                            'paid' => 'badge bg-success',
                            'partial' => 'badge bg-warning text-dark',
                            'unpaid' => 'badge bg-danger',
                            default => 'badge bg-secondary',
                        };
                    @endphp
                    <tr>
                        <td class="fw-bold text-dark">
                            {{ $booking->reference }}
                            @if($booking->title)
                                <small class="d-block text-muted">{{ $booking->title }}</small>
                            @endif
                        </td>
                        <td>
                            <strong class="d-block">{{ $booking->customer?->name ?: '-' }}</strong>
                            <small class="text-muted"><i class="fa-solid fa-phone me-1" style="font-size:10px;"></i>{{ $booking->customer?->phone ?: '-' }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $booking->space?->name ?: '-' }}</span>
                            <small class="d-block text-muted">{{ $booking->space?->venue?->name ?: '-' }}</small>
                        </td>
                        <td>
                            <div>{{ $booking->starts_at?->format('Y-m-d') }}</div>
                            <small class="text-muted">{{ $booking->starts_at?->format('H:i') }} - {{ $booking->ends_at?->format('H:i') }}</small>
                            <div class="mt-1">
                                <span class="badge {{ $statusBadgeClass }}" style="font-size: 11px;">
                                    {{ trans('admin.venues.statuses.'.$bStatus) ?: $bStatus }}
                                </span>
                            </div>
                        </td>
                        <td class="fw-bold">
                            {{ number_format($booking->total_amount, 2) }}
                        </td>
                        <td class="text-success fw-bold">
                            {{ number_format($booking->paid_amount, 2) }}
                        </td>
                        <td>
                            @if($remaining > 0)
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 fs-6 fw-bold">
                                    {{ number_format($remaining, 2) }}
                                </span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 fw-bold">
                                    0.00
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $paymentBadgeClass }}">
                                {{ trans('admin.venues.payment_states.'.$pStatus) ?: ($pStatus === 'paid' ? 'مدفوع' : ($pStatus === 'partial' ? 'مدفوع جزئياً' : 'غير مدفوع')) }}
                            </span>
                            @if($booking->payment_method)
                                <small class="d-block text-muted mt-1">{{ $booking->payment_method }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1 align-items-center">
                                @if($remaining > 0 && $booking->status !== 'cancelled')
                                    <button type="button" 
                                            class="btn btn-sm btn-success d-inline-flex align-items-center gap-1"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#collectPaymentModal"
                                            data-action="{{ route('academy.venue-bookings.collect-payment', $booking) }}"
                                            data-ref="{{ $booking->reference }}"
                                            data-customer="{{ $booking->customer?->name }}"
                                            data-total="{{ number_format($booking->total_amount, 2) }}"
                                            data-paid="{{ number_format($booking->paid_amount, 2) }}"
                                            data-remaining="{{ $remaining }}"
                                            title="{{ app()->getLocale() === 'ar' ? 'تحصيل دفعة متبقية' : 'Collect Remaining Payment' }}">
                                        <i class="fa-solid fa-hand-holding-dollar"></i>
                                        <span>{{ app()->getLocale() === 'ar' ? 'تحصيل' : 'Collect' }}</span>
                                    </button>
                                @endif

                                <a class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" 
                                   target="_blank" 
                                   href="{{ route('academy.invoices.venues.print', ['booking' => $booking, 'paper' => 'a4']) }}" 
                                   title="{{ app()->getLocale() === 'ar' ? 'طباعة وإرسال الفاتورة' : 'Print & Send Invoice' }}">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'الفاتورة' : 'Invoice' }}</span>
                                </a>

                                <a class="btn btn-sm btn-outline-primary" 
                                   href="{{ route('academy.venue-bookings.edit', $booking) }}" 
                                   title="{{ trans('admin.edit') }}">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                @if($booking->status !== 'cancelled')
                                    <form method="POST" action="{{ route('academy.venue-bookings.destroy', $booking) }}" class="d-inline" onsubmit="return confirm('{{ trans('admin.venues.cancel_booking_confirm') ?: 'هل أنت متأكد من إلغاء هذا الحجز؟' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="{{ trans('admin.venues.cancel_booking') }}">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-calendar-xmark fa-2x mb-2 d-block text-secondary"></i>
                            {{ trans('admin.venues.empty_bookings') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $bookings->links() }}
    </div>
</div>

<!-- Modal: Collect Payment -->
<div class="modal fade" id="collectPaymentModal" tabindex="-1" aria-labelledby="collectPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="collectPaymentModalLabel">
                    <i class="fa-solid fa-cash-register me-2"></i> {{ app()->getLocale() === 'ar' ? 'تحصيل دفعة مالية للحجز' : 'Collect Booking Payment' }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="collectPaymentForm" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="bg-light p-3 rounded mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'رقم المرجع / الحجز:' : 'Reference:' }}</span>
                            <strong id="modalRef">-</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'العميل:' : 'Customer:' }}</span>
                            <strong id="modalCustomer">-</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ app()->getLocale() === 'ar' ? 'إجمالي الحجز:' : 'Total Amount:' }}</span>
                            <span id="modalTotal" class="fw-bold">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-success">
                            <span>{{ app()->getLocale() === 'ar' ? 'المسدد سابقاً:' : 'Paid Amount:' }}</span>
                            <span id="modalPaid" class="fw-bold">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between fs-6 text-danger fw-bold">
                            <span>{{ app()->getLocale() === 'ar' ? 'المبلغ المتبقي المطلوب:' : 'Remaining Balance:' }}</span>
                            <span id="modalRemaining">0.00</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'المبلغ المحصل الآن:' : 'Amount Collected Now:' }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" class="form-control form-control-lg fw-bold text-success" name="amount" id="modalAmountInput" required>
                            <span class="input-group-text fw-bold">EGP</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'طريقة التحصيل / الدفع:' : 'Payment Method:' }} <span class="text-danger">*</span></label>
                        <select class="form-select" name="payment_method" id="modalPaymentMethod" required>
                            <option value="cash">{{ app()->getLocale() === 'ar' ? 'كاش (نقداً)' : 'Cash' }}</option>
                            <option value="card">{{ app()->getLocale() === 'ar' ? 'بطاقة بنكية / فيزا / مدى' : 'Card / POS' }}</option>
                            <option value="instapay">{{ app()->getLocale() === 'ar' ? 'إنستا باي (InstaPay)' : 'InstaPay' }}</option>
                            <option value="fawry">{{ app()->getLocale() === 'ar' ? 'فوري (Fawry)' : 'Fawry' }}</option>
                            <option value="bank_transfer">{{ app()->getLocale() === 'ar' ? 'تحويل بنكي' : 'Bank Transfer' }}</option>
                            <option value="sadad">{{ app()->getLocale() === 'ar' ? 'سداد / STC Pay / Apple Pay' : 'Sadad / Apple Pay' }}</option>
                            <option value="other">{{ app()->getLocale() === 'ar' ? 'طريقة أخرى' : 'Other' }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'ملاحظات الدفعة (اختياري):' : 'Payment Notes (Optional):' }}</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'ملاحظات إضافية على الدفعة...' : 'Additional notes...' }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('admin.cancel') }}</button>
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="fa-solid fa-check me-1"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد التحصيل وتحديث الفاتورة' : 'Confirm Collection' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('collectPaymentModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const action = button.getAttribute('data-action');
            const ref = button.getAttribute('data-ref');
            const customer = button.getAttribute('data-customer');
            const total = button.getAttribute('data-total');
            const paid = button.getAttribute('data-paid');
            const remaining = parseFloat(button.getAttribute('data-remaining') || '0');

            document.getElementById('collectPaymentForm').action = action;
            document.getElementById('modalRef').textContent = ref;
            document.getElementById('modalCustomer').textContent = customer || '-';
            document.getElementById('modalTotal').textContent = total;
            document.getElementById('modalPaid').textContent = paid;
            document.getElementById('modalRemaining').textContent = remaining.toFixed(2);

            const input = document.getElementById('modalAmountInput');
            input.value = remaining.toFixed(2);
            input.max = remaining;
        });
    });
</script>
@endpush
@endsection
