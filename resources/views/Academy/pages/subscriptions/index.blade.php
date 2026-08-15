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

                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('admin.student_management.student') }}</th>
                                    <th>{{ trans('admin.student_management.group') }}</th>
                                    <th>{{ trans('admin.student_management.period') }}</th>
                                    <th class="text-nowrap">{{ trans('admin.student_management.amount') }}</th>
                                    <th class="text-nowrap">{{ trans('admin.student_management.paid') }}</th>
                                    <th class="text-nowrap">{{ app()->getLocale() === 'ar' ? 'الخصم' : 'Discount' }}</th>
                                    <th class="text-nowrap">{{ app()->getLocale() === 'ar' ? 'المتبقي' : 'Remaining' }}</th>
                                    <th>{{ trans('admin.student_management.method') }}</th>
                                    <th>{{ trans('admin.student_management.status') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('admin.student_management.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($subscriptions as $subscription)
                                    @php
                                        $paid = (float) $subscription->payments->sum('amount');
                                        $total = (float) $subscription->amount;
                                        $discount = (float) ($subscription->discount_amount ?? 0);
                                        $remaining = $subscription->remaining_amount;
                                        $pStatus = $subscription->payment_status;
                                    @endphp
                                    <tr>
                                        <td>{{ $subscription->id }}</td>
                                        <td>
                                            @if($subscription->student)
                                                <button type="button" class="student-profile-trigger fw-bold btn btn-link text-decoration-none p-0 text-start text-dark" data-student-profile-url="{{ route('academy.students.profile', $subscription->student) }}">
                                                    {{ $subscription->student->name }}
                                                </button>
                                                <small class="d-block text-muted">{{ $subscription->student->phone ?: '-' }}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $subscription->group?->name ?? '-' }}</td>
                                        <td class="text-nowrap">
                                            <div>{{ $subscription->starts_on?->format('Y-m-d') }}</div>
                                            <small class="text-muted">{{ $subscription->ends_on?->format('Y-m-d') }}</small>
                                        </td>
                                        <td class="fw-bold text-dark text-nowrap">{{ number_format($total, 2) }}</td>
                                        <td class="text-nowrap">
                                            <span style="color:#047857; font-weight:700; font-size:13px;">
                                                {{ number_format($paid, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            @if($discount > 0)
                                                <span style="display:inline-block; padding: 3px 8px; font-size: 12px; font-weight: 700; border-radius: 6px; background-color: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe;" title="{{ $subscription->discount_reason }} (اعتماد: {{ $subscription->discount_approved_by }})">
                                                    <i class="fa-solid fa-tag me-1" style="font-size: 10px;"></i>{{ number_format($discount, 2) }}
                                                </span>
                                                @if($subscription->discount_reason)
                                                    <small class="d-block text-muted" style="font-size:10px;">{{ Str::limit($subscription->discount_reason, 15) }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted" style="font-size: 12px;">-</span>
                                            @endif
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
                                        <td class="text-nowrap">{{ $subscription->payments->sortByDesc('paid_at')->first()?->method_label ?? '-' }}</td>
                                        <td class="text-nowrap">
                                            <span style="display:inline-block; background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; margin-bottom: 2px;">
                                                {{ trans('admin.student_management.' . $subscription->status) }}
                                            </span>
                                            <div>
                                                @if($pStatus === 'paid')
                                                    <span style="display:inline-block; background-color: #d1fae5; color: #065f46; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; border:1px solid #a7f3d0;">
                                                        {{ app()->getLocale() === 'ar' ? 'مدفوع' : 'Paid' }}
                                                    </span>
                                                @elseif($pStatus === 'partial')
                                                    <span style="display:inline-block; background-color: #fef3c7; color: #92400e; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; border:1px solid #fde68a;">
                                                        {{ app()->getLocale() === 'ar' ? 'مدفوع جزئياً' : 'Partial' }}
                                                    </span>
                                                @else
                                                    <span style="display:inline-block; background-color: #fee2e2; color: #991b1b; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; border:1px solid #fca5a5;">
                                                        {{ app()->getLocale() === 'ar' ? 'غير مدفوع' : 'Unpaid' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <div class="d-inline-flex gap-1 align-items-center">
                                                @if($remaining > 0 && $subscription->status !== 'cancelled')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 fw-bold"
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

                                                    <button type="button" 
                                                            class="btn btn-sm d-inline-flex align-items-center gap-1 fw-bold"
                                                            style="background:#7e22ce; color:#fff; border-color:#7e22ce;"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#subDiscountModal"
                                                            data-action="{{ route('academy.subscriptions.apply-discount', $subscription) }}"
                                                            data-student="{{ $subscription->student?->name }}"
                                                            data-group="{{ $subscription->group?->name }}"
                                                            data-remaining="{{ $remaining }}"
                                                            title="{{ app()->getLocale() === 'ar' ? 'اعتماد خصم للاشتراك' : 'Apply Discount' }}">
                                                        <i class="fa-solid fa-percent"></i>
                                                        <span>{{ app()->getLocale() === 'ar' ? 'خصم' : 'Discount' }}</span>
                                                    </button>
                                                @endif

                                                @if($discount > 0 && $subscription->status !== 'cancelled')
                                                    <form method="POST" action="{{ route('academy.subscriptions.remove-discount', $subscription) }}" class="d-inline" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من إلغاء واسترداد الخصم وإعادة المبلغ إلى المتبقي على الطالب؟' : 'Are you sure you want to reverse/refund this discount?' }}')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ app()->getLocale() === 'ar' ? 'استرداد / إلغاء الخصم' : 'Reverse Discount' }}">
                                                            <i class="fa-solid fa-rotate-left"></i>
                                                        </button>
                                                    </form>
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
                                    <tr><td colspan="11" class="text-center py-5 text-muted">{{ trans('admin.student_management.no_subscriptions_yet') }}</td></tr>
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

    <!-- Modal: Apply Student Subscription Discount -->
    <div class="modal fade" id="subDiscountModal" tabindex="-1" aria-labelledby="subDiscountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header" style="background:#7e22ce; color:#fff;">
                    <h5 class="modal-title fw-bold" id="subDiscountModalLabel">
                        <i class="fa-solid fa-percent me-2"></i> {{ app()->getLocale() === 'ar' ? 'اعتماد خصم لاشتراك الطالب' : 'Apply Student Subscription Discount' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="subDiscountForm" action="">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'الطالب:' : 'Student:' }}</span>
                                <strong id="discSubModalStudent">-</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'المجموعة:' : 'Group:' }}</span>
                                <strong id="discSubModalGroup">-</strong>
                            </div>
                            <div class="d-flex justify-content-between text-danger fw-bold">
                                <span>{{ app()->getLocale() === 'ar' ? 'المبلغ المتبقي المطلوب:' : 'Current Remaining:' }}</span>
                                <span id="discSubModalRemaining">0.00</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'قيمة الخصم المعتمد:' : 'Discount Amount:' }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0.01" class="form-control form-control-lg fw-bold" style="color:#7e22ce;" name="discount_amount" id="discSubModalAmountInput" required>
                                <span class="input-group-text fw-bold">EGP</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'سبب الخصم:' : 'Discount Reason:' }} <span class="text-danger">*</span></label>
                            <select class="form-select mb-2" onchange="if(this.value){ document.getElementById('discSubReasonInput').value = this.value; }">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر سبباً سريعاً أو اكتب بالأسفل --' : '-- Quick reason --' }}</option>
                                <option value="خصم إخوة / أقارب">{{ app()->getLocale() === 'ar' ? 'خصم إخوة / أقارب' : 'Siblings discount' }}</option>
                                <option value="خصم تفوق رياضي">{{ app()->getLocale() === 'ar' ? 'خصم تفوق رياضي' : 'Athletic excellence' }}</option>
                                <option value="منحة / تخفيض إداري">{{ app()->getLocale() === 'ar' ? 'منحة / تخفيض إداري' : 'Scholarship / Management waiver' }}</option>
                                <option value="عرض تجديد سنوي">{{ app()->getLocale() === 'ar' ? 'عرض تجديد سنوي' : 'Annual renewal offer' }}</option>
                            </select>
                            <input type="text" class="form-control" name="discount_reason" id="discSubReasonInput" placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب سبب اعتماد الخصم بالتفصيل...' : 'Reason for discount...' }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'اعتماد بواسطة (المسؤول):' : 'Approved By:' }}</label>
                            <input type="text" class="form-control" name="discount_approved_by" value="{{ auth('academy')->user()?->name ?: 'الإدارة' }}" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم المسؤول المعتمد للخصم' : 'Approver name' }}">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('admin.cancel') }}</button>
                        <button type="submit" class="btn fw-bold" style="background:#7e22ce; color:#fff;">
                            <i class="fa-solid fa-check me-1"></i> {{ app()->getLocale() === 'ar' ? 'اعتماد الخصم وتحديث الاشتراك' : 'Approve Discount' }}
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
            const collectModal = document.getElementById('collectSubPaymentModal');
            if (collectModal) {
                collectModal.addEventListener('show.bs.modal', function (event) {
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
            }

            const discModal = document.getElementById('subDiscountModal');
            if (discModal) {
                discModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const action = button.getAttribute('data-action');
                    const student = button.getAttribute('data-student');
                    const group = button.getAttribute('data-group');
                    const remaining = parseFloat(button.getAttribute('data-remaining') || '0');

                    document.getElementById('subDiscountForm').action = action;
                    document.getElementById('discSubModalStudent').textContent = student || '-';
                    document.getElementById('discSubModalGroup').textContent = group || '-';
                    document.getElementById('discSubModalRemaining').textContent = remaining.toFixed(2);

                    const input = document.getElementById('discSubModalAmountInput');
                    input.value = remaining.toFixed(2);
                    input.max = remaining;
                });
            }
        });
    </script>
    @endpush
@endsection
