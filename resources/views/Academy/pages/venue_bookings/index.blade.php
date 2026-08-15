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
                    <th>{{ app()->getLocale() === 'ar' ? 'المرجع' : 'Reference' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'العميل' : 'Customer' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'الملعب أو المساحة' : 'Space' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'التوقيت & الحالة' : 'Time & Status' }}</th>
                    <th class="text-nowrap">{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}</th>
                    <th class="text-nowrap">{{ app()->getLocale() === 'ar' ? 'المدفوع' : 'Paid' }}</th>
                    <th class="text-nowrap">{{ app()->getLocale() === 'ar' ? 'المتبقي' : 'Remaining' }}</th>
                    <th>{{ app()->getLocale() === 'ar' ? 'السداد' : 'Payment' }}</th>
                    <th class="text-center">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    @php
                        $remaining = $booking->remaining_amount;
                        $pStatus = $booking->payment_status;
                        $bStatus = $booking->status;

                        $statusBadgeClass = match($bStatus) {
                            'confirmed' => 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;',
                            'checked_in', 'completed' => 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;',
                            'pending' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
                            'cancelled' => 'background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5;',
                            'no_show' => 'background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;',
                            default => 'background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;',
                        };
                    @endphp
                    <tr>
                        <td class="fw-bold text-dark text-nowrap">
                            {{ $booking->reference }}
                            @if($booking->title)
                                <small class="d-block text-muted fw-normal">{{ $booking->title }}</small>
                            @endif
                        </td>
                        <td>
                            <strong class="d-block text-dark">{{ $booking->customer?->name ?: '-' }}</strong>
                            <small class="text-muted"><i class="fa-solid fa-phone me-1" style="font-size:10px;"></i>{{ $booking->customer?->phone ?: '-' }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $booking->space?->name ?: '-' }}</span>
                            <small class="d-block text-muted">{{ $booking->space?->venue?->name ?: '-' }}</small>
                        </td>
                        <td class="text-nowrap">
                            <div class="fw-semibold text-dark">{{ $booking->starts_at?->format('Y-m-d') }}</div>
                            <small class="text-muted">{{ $booking->starts_at?->format('H:i') }} - {{ $booking->ends_at?->format('H:i') }}</small>
                            <div class="mt-1">
                                <span style="display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:700; {{ $statusBadgeClass }}">
                                    {{ trans('admin.venues.statuses.'.$bStatus) ?: $bStatus }}
                                </span>
                            </div>
                        </td>
                        <td class="fw-bold text-dark text-nowrap">
                            {{ number_format($booking->total_amount, 2) }}
                        </td>
                        <td class="text-nowrap">
                            <span style="color:#047857; font-weight:700; font-size:13px;">
                                {{ number_format($booking->paid_amount, 2) }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            @if($remaining > 0)
                                <span style="display:inline-block; padding: 4px 10px; font-size: 13px; font-weight: 800; border-radius: 6px; background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;">
                                    {{ number_format($remaining, 2) }}
                                </span>
                            @else
                                <span style="display:inline-block; padding: 4px 8px; font-size: 12px; font-weight: 700; color: #059669;">
                                    0.00
                                </span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            @if($pStatus === 'paid')
                                <span style="display:inline-block; background-color: #d1fae5; color: #065f46; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 6px; border:1px solid #a7f3d0;">
                                    {{ app()->getLocale() === 'ar' ? 'مدفوع' : 'Paid' }}
                                </span>
                            @elseif($pStatus === 'partial')
                                <span style="display:inline-block; background-color: #fef3c7; color: #92400e; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 6px; border:1px solid #fde68a;">
                                    {{ app()->getLocale() === 'ar' ? 'مدفوع جزئياً' : 'Partial' }}
                                </span>
                            @else
                                <span style="display:inline-block; background-color: #fee2e2; color: #991b1b; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 6px; border:1px solid #fca5a5;">
                                    {{ app()->getLocale() === 'ar' ? 'غير مدفوع' : 'Unpaid' }}
                                </span>
                            @endif
                            @if($booking->payment_method)
                                <small class="d-block text-muted mt-1">{{ $booking->payment_method }}</small>
                            @endif
                        </td>
                        <td class="text-center text-nowrap">
                            <div class="d-inline-flex gap-1 align-items-center">
                                @if($remaining > 0 && $booking->status !== 'cancelled')
                                    <button type="button" 
                                            class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 fw-bold"
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
