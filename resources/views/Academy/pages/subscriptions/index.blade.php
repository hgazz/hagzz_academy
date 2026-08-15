@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.subscriptions'))

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0 fw-bold">{{ trans('admin.student_management.subscriptions') }}</h3>
                        <a href="{{ route('academy.subscriptions.create') }}" class="btn btn-primary">
                            <i class="fa-solid fa-plus me-1"></i> {{ trans('admin.student_management.add_subscription') }}
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
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

                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('admin.student_management.student') }}</th>
                                    <th>{{ trans('admin.student_management.group') }}</th>
                                    <th>{{ trans('admin.student_management.period') }}</th>
                                    <th>{{ trans('admin.student_management.amount') }}</th>
                                    <th>{{ trans('admin.student_management.paid') }}</th>
                                    <th>{{ app()->getLocale() === 'ar' ? 'المتبقي' : 'Remaining' }}</th>
                                    <th>{{ trans('admin.student_management.method') }}</th>
                                    <th>{{ trans('admin.student_management.status') }}</th>
                                    <th class="text-center">{{ trans('admin.student_management.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($subscriptions as $subscription)
                                    @php
                                        $paid = (float) $subscription->payments->sum('amount');
                                        $total = (float) $subscription->amount;
                                        $remaining = max(0, $total - $paid);
                                        $pStatus = $subscription->payment_status;
                                        $paymentBadge = match($pStatus) {
                                            'paid' => 'badge bg-success',
                                            'partial' => 'badge bg-warning text-dark',
                                            'unpaid' => 'badge bg-danger',
                                            default => 'badge bg-secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $subscription->id }}</td>
                                        <td>
                                            @if($subscription->student)
                                                <button type="button" class="student-profile-trigger fw-bold btn btn-link text-decoration-none p-0 text-start" data-student-profile-url="{{ route('academy.students.profile', $subscription->student) }}">
                                                    {{ $subscription->student->name }}
                                                </button>
                                                <small class="d-block text-muted">{{ $subscription->student->phone ?: '-' }}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $subscription->group?->name ?? '-' }}</td>
                                        <td>
                                            <div>{{ $subscription->starts_on?->format('Y-m-d') }}</div>
                                            <small class="text-muted">{{ $subscription->ends_on?->format('Y-m-d') }}</small>
                                        </td>
                                        <td class="fw-bold">{{ number_format($total, 2) }}</td>
                                        <td class="text-success fw-bold">{{ number_format($paid, 2) }}</td>
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
                                        <td>{{ $subscription->payments->sortByDesc('paid_at')->first()?->method_label ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-info text-white mb-1 d-block">{{ trans('admin.student_management.' . $subscription->status) }}</span>
                                            <span class="{{ $paymentBadge }}">{{ trans('admin.student_management.' . $pStatus) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1 align-items-center">
                                                @if($remaining > 0 && $subscription->status !== 'cancelled')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success d-inline-flex align-items-center gap-1"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#collectSubPaymentModal"
                                                            data-action="{{ route('academy.subscriptions.payments.store', $subscription) }}"
                                                            data-id="{{ $subscription->id }}"
                                                            data-student="{{ $subscription->student?->name }}"
                                                            data-group="{{ $subscription->group?->name }}"
                                                            data-total="{{ number_format($total, 2) }}"
                                                            data-paid="{{ number_format($paid, 2) }}"
                                                            data-remaining="{{ $remaining }}"
                                                            title="{{ app()->getLocale() === 'ar' ? 'تحصيل دفعة متبقية' : 'Collect Payment' }}">
                                                        <i class="fa-solid fa-hand-holding-dollar"></i>
                                                        <span>{{ app()->getLocale() === 'ar' ? 'تحصيل' : 'Collect' }}</span>
                                                    </button>
                                                @endif

                                                <a href="{{ route('academy.invoices.students.print', ['subscription' => $subscription, 'paper' => 'a4']) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" 
                                                   title="{{ app()->getLocale()==='ar'?'طباعة الفاتورة':'Print invoice' }}">
                                                    <i class="fa-solid fa-receipt"></i>
                                                    <span>{{ app()->getLocale()==='ar'?'الفاتورة':'Invoice' }}</span>
                                                </a>

                                                <a href="{{ route('academy.subscriptions.edit', $subscription) }}" class="btn btn-sm btn-outline-primary" title="{{ trans('admin.student_management.edit') }}">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>

                                                <form action="{{ route('academy.subscriptions.destroy', $subscription) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ trans('admin.student_management.delete_subscription_confirm') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="{{ trans('admin.student_management.delete') }}">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="text-center py-5 text-muted">{{ trans('admin.student_management.no_subscriptions_yet') }}</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $subscriptions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Collect Student Subscription Payment -->
    <div class="modal fade" id="collectSubPaymentModal" tabindex="-1" aria-labelledby="collectSubPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="collectSubPaymentModalLabel">
                        <i class="fa-solid fa-cash-register me-2"></i> {{ app()->getLocale() === 'ar' ? 'تحصيل دفعة لاشتراك الطالب' : 'Collect Subscription Payment' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="collectSubPaymentForm" action="">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'الطالب:' : 'Student:' }}</span>
                                <strong id="subModalStudent">-</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'المجموعة:' : 'Group:' }}</span>
                                <strong id="subModalGroup">-</strong>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ app()->getLocale() === 'ar' ? 'قيمة الاشتراك:' : 'Subscription Amount:' }}</span>
                                <span id="subModalTotal" class="fw-bold">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 text-success">
                                <span>{{ app()->getLocale() === 'ar' ? 'المدفوع سابقاً:' : 'Paid Amount:' }}</span>
                                <span id="subModalPaid" class="fw-bold">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between fs-6 text-danger fw-bold">
                                <span>{{ app()->getLocale() === 'ar' ? 'المبلغ المتبقي المطلوب:' : 'Remaining Balance:' }}</span>
                                <span id="subModalRemaining">0.00</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'المبلغ المحصل الآن:' : 'Amount Collected Now:' }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0.01" class="form-control form-control-lg fw-bold text-success" name="amount" id="subModalAmountInput" required>
                                <span class="input-group-text fw-bold">EGP</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'تاريخ الدفعة:' : 'Payment Date:' }} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="paid_at" value="{{ now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'طريقة التحصيل / الدفع:' : 'Payment Method:' }} <span class="text-danger">*</span></label>
                            <select class="form-select" name="method" required>
                                <option value="cash">{{ app()->getLocale() === 'ar' ? 'كاش (نقداً)' : 'Cash' }}</option>
                                <option value="card">{{ app()->getLocale() === 'ar' ? 'بطاقة بنكية / فيزا / مدى' : 'Card / POS' }}</option>
                                <option value="instapay">{{ app()->getLocale() === 'ar' ? 'إنستا باي (InstaPay)' : 'InstaPay' }}</option>
                                <option value="fawry">{{ app()->getLocale() === 'ar' ? 'فوري (Fawry)' : 'Fawry' }}</option>
                                <option value="bank_transfer">{{ app()->getLocale() === 'ar' ? 'تحويل بنكي' : 'Bank Transfer' }}</option>
                                <option value="sadad">{{ app()->getLocale() === 'ar' ? 'سداد / STC Pay' : 'Sadad / STC Pay' }}</option>
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
                            <i class="fa-solid fa-check me-1"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد التحصيل وتحديث الاشتراك' : 'Confirm Collection' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('Academy.pages.students._profile_modal')

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('collectSubPaymentModal');
            if (!modal) return;

            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const action = button.getAttribute('data-action');
                const student = button.getAttribute('data-student');
                const group = button.getAttribute('data-group');
                const total = button.getAttribute('data-total');
                const paid = button.getAttribute('data-paid');
                const remaining = parseFloat(button.getAttribute('data-remaining') || '0');

                document.getElementById('collectSubPaymentForm').action = action;
                document.getElementById('subModalStudent').textContent = student || '-';
                document.getElementById('subModalGroup').textContent = group || '-';
                document.getElementById('subModalTotal').textContent = total;
                document.getElementById('subModalPaid').textContent = paid;
                document.getElementById('subModalRemaining').textContent = remaining.toFixed(2);

                const input = document.getElementById('subModalAmountInput');
                input.value = remaining.toFixed(2);
                input.max = remaining;
            });
        });
    </script>
    @endpush
@endsection
