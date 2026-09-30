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
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                                    </div>
                                    @if(session('freeze_whatsapp_url'))
                                        <a href="{{ session('freeze_whatsapp_url') }}" target="_blank" class="btn btn-sm btn-success fw-bold text-white shadow-sm">
                                            <i class="fa-brands fa-whatsapp me-1"></i> {{ app()->getLocale() === 'ar' ? 'إرسال إشعار التجميد للمشترك عبر واتساب 📲' : 'Send WhatsApp Notice 📲' }}
                                        </a>
                                    @endif
                                </div>
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
                                            @if($subscription->status === 'frozen')
                                                <span style="display:inline-block; background:#e0f2fe; color:#0284c7; border:1px solid #7dd3fc; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; margin-bottom: 2px;" title="{{ $subscription->freeze_reason }} ({{ app()->getLocale() === 'ar' ? 'حتى' : 'until' }}: {{ optional($subscription->frozen_until)->format('Y-m-d') }})">
                                                    <i class="fa-solid fa-snowflake me-1"></i> {{ app()->getLocale() === 'ar' ? 'مجمد' : 'Frozen' }}
                                                </span>
                                            @else
                                                <span style="display:inline-block; background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; margin-bottom: 2px;">
                                                    {{ trans('admin.student_management.' . $subscription->status) }}
                                                </span>
                                            @endif
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
                                                            onclick="openSubCollectModal(this)"
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
                                                            onclick="openSubDiscountModal(this)"
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

                                                {{-- زر التجميد وإلغاء التجميد --}}
                                                @if($subscription->status === 'frozen')
                                                    <form method="POST" action="{{ route('academy.subscriptions.unfreeze', $subscription) }}" class="d-inline" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من إلغاء التجميد وإعادة تفعيل الاشتراك فوراً؟' : 'Are you sure you want to unfreeze and reactivate this subscription?' }}')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info text-white d-inline-flex align-items-center gap-1 fw-bold" title="{{ app()->getLocale() === 'ar' ? 'إلغاء التجميد وتفعيل الاشتراك' : 'Unfreeze Subscription' }}">
                                                            <i class="fa-solid fa-play"></i>
                                                            <span>{{ app()->getLocale() === 'ar' ? 'تفعيل' : 'Unfreeze' }}</span>
                                                        </button>
                                                    </form>
                                                @elseif($subscription->status === 'active')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 fw-bold"
                                                            onclick="openSubFreezeModal(this)"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#freezeSubModal"
                                                            data-action="{{ route('academy.subscriptions.freeze', $subscription) }}"
                                                            data-student="{{ $subscription->student?->name }}"
                                                            data-group="{{ $subscription->group?->name }}"
                                                            data-ends="{{ $subscription->ends_on?->format('Y-m-d') }}"
                                                            title="{{ app()->getLocale() === 'ar' ? 'تجميد الاشتراك وتمديد المدة تلقائياً' : 'Freeze Subscription & Extend Period' }}">
                                                        <i class="fa-solid fa-snowflake"></i>
                                                        <span>{{ app()->getLocale() === 'ar' ? 'تجميد' : 'Freeze' }}</span>
                                                    </button>
                                                @endif

                                                @php
                                                    $sPhone = preg_replace('/\D+/', '', (string) ($subscription->student?->phone ?: $subscription->student?->guardian_phone));
                                                    if ($sPhone && str_starts_with($sPhone, '0')) $sPhone = '2' . $sPhone;
                                                    $subPublicUrl = route('invoices.public.view', ['type' => 'student_subscription', 'id' => $subscription->id]);
                                                    $subRemText = "مرحباً بك ولي أمر الطالب " . ($subscription->student?->name ?: '') . " 👋\n"
                                                        . "نود تذكيركم ببيانات اشتراك الطالب:\n"
                                                        . "🏅 المجموعة: " . ($subscription->group?->name ?: '-') . "\n"
                                                        . "📅 فترة الاشتراك: " . optional($subscription->starts_on)->format('Y-m-d') . " إلى " . optional($subscription->ends_on)->format('Y-m-d') . "\n"
                                                        . ($remaining > 0 ? "💰 المبلغ المتبقي المطلوب: " . number_format($remaining, 2) . " EGP\n" : "✅ الاشتراك مسدد بالكامل\n")
                                                        . "📄 رابط الفاتورة الإلكترونية والتفاصيل:\n" . $subPublicUrl . "\n\nشكراً لثقتكم بنا! 🌟";
                                                    $subRemWaLink = 'https://api.whatsapp.com/send?phone=' . $sPhone . '&text=' . urlencode($subRemText);
                                                @endphp

                                                @if($sPhone && $subscription->status !== 'cancelled')
                                                    <a class="btn btn-sm btn-outline-success" target="_blank" href="{{ $subRemWaLink }}" title="{{ app()->getLocale() === 'ar' ? 'إرسال تذكير بالاشتراك عبر واتساب' : 'Send WhatsApp Reminder' }}">
                                                        <i class="fa-brands fa-whatsapp"></i>
                                                    </a>
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
                <form method="POST" id="collectSubPaymentForm" action="#">
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
                <form method="POST" id="subDiscountForm" action="#">
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

    <!-- Modal: Freeze Student Subscription -->
    <div class="modal fade" id="freezeSubModal" tabindex="-1" aria-labelledby="freezeSubModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold" id="freezeSubModalLabel">
                        <i class="fa-solid fa-snowflake me-2"></i> {{ app()->getLocale() === 'ar' ? 'تجميد الاشتراك وتمديد المدة تلقائياً' : 'Freeze Subscription & Extend Period' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="freezeSubForm" action="#">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'المشترك:' : 'Member:' }}</span>
                                <strong id="freezeSubModalStudent">-</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'الباقة / المجموعة:' : 'Plan / Group:' }}</span>
                                <strong id="freezeSubModalGroup">-</strong>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ app()->getLocale() === 'ar' ? 'تاريخ الانتهاء الحالي:' : 'Current End Date:' }}</span>
                                <span id="freezeSubModalCurrentEnds" class="fw-bold text-dark">-</span>
                            </div>
                            <div class="d-flex justify-content-between text-info fw-bold">
                                <span>{{ app()->getLocale() === 'ar' ? 'تاريخ الانتهاء الجديد المتوقع:' : 'New Extended End Date:' }}</span>
                                <span id="freezeSubModalNewEnds" class="fw-bold">-</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'عدد أيام التجميد:' : 'Freeze Duration (Days):' }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="1" min="1" max="365" class="form-control form-control-lg fw-bold text-info" name="freeze_days" id="freezeSubDaysInput" value="30" required oninput="calculateExtendedEndDate()">
                                <span class="input-group-text fw-bold">{{ app()->getLocale() === 'ar' ? 'يوماً' : 'Days' }}</span>
                            </div>
                            <small class="text-muted d-block mt-1">
                                {{ app()->getLocale() === 'ar' ? 'يُضاف هذا العدد تلقائياً إلى تاريخ نهاية الاشتراك الحالي دون خسارة أي يوم مدفوع.' : 'This duration is automatically added to the current expiry date.' }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'سبب التجميد:' : 'Freeze Reason:' }} <span class="text-danger">*</span></label>
                            <select class="form-select mb-2" onchange="if(this.value){ document.getElementById('freezeSubReasonInput').value = this.value; }">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر سبباً سريعاً أو اكتب بالأسفل --' : '-- Quick reason --' }}</option>
                                <option value="إجازة سنوية / عطلة">{{ app()->getLocale() === 'ar' ? 'إجازة سنوية / عطلة' : 'Annual vacation / Holiday' }}</option>
                                <option value="عذر صحي / إصابة أو عملية">{{ app()->getLocale() === 'ar' ? 'عذر صحي / إصابة أو عملية' : 'Medical condition / Injury' }}</option>
                                <option value="سفر خارج البلاد">{{ app()->getLocale() === 'ar' ? 'سفر خارج البلاد' : 'Travel / Overseas trip' }}</option>
                                <option value="ظروف دراسة / امتحانات">{{ app()->getLocale() === 'ar' ? 'ظروف دراسة / امتحانات' : 'Study / Exams' }}</option>
                                <option value="ظروف عمل طارئة">{{ app()->getLocale() === 'ar' ? 'ظروف عمل طارئة' : 'Work circumstances' }}</option>
                            </select>
                            <input type="text" class="form-control" name="freeze_reason" id="freezeSubReasonInput" placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب سبب التجميد بالتفصيل...' : 'Detailed reason for freeze...' }}" required>
                        </div>

                        <div class="alert alert-info py-2 px-3 mb-0 small border-0" style="background-color: #f0f9ff; color: #0369a1; border-left: 4px solid #0284c7 !important;">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'سيتم إيقاف صلاحية الدخول وتسجيل الحضور طوال فترة التجميد، ويمكنك استئناف وتفعيل الاشتراك في أي وقت.' : 'Check-in and entry access will be suspended during freeze. You can unfreeze anytime.' }}
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('admin.cancel') }}</button>
                        <button type="submit" class="btn btn-info text-white fw-bold">
                            <i class="fa-solid fa-snowflake me-1"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد تجميد الاشتراك' : 'Confirm Freeze' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('Academy.pages.students._profile_modal')

    @push('js')
    <script>
        let currentSubEndsDateStr = null;

        function openSubFreezeModal(button) {
            const action = button.getAttribute('data-action');
            const student = button.getAttribute('data-student');
            const group = button.getAttribute('data-group');
            currentSubEndsDateStr = button.getAttribute('data-ends');

            const form = document.getElementById('freezeSubForm');
            if (form) form.action = action;
            const fStud = document.getElementById('freezeSubModalStudent');
            if (fStud) fStud.textContent = student || '-';
            const fGrp = document.getElementById('freezeSubModalGroup');
            if (fGrp) fGrp.textContent = group || '-';
            const fEnds = document.getElementById('freezeSubModalCurrentEnds');
            if (fEnds) fEnds.textContent = currentSubEndsDateStr || '-';

            const daysInput = document.getElementById('freezeSubDaysInput');
            if (daysInput) daysInput.value = 30;

            const reasonInput = document.getElementById('freezeSubReasonInput');
            if (reasonInput) reasonInput.value = '';

            calculateExtendedEndDate();
        }

        function calculateExtendedEndDate() {
            const daysInput = document.getElementById('freezeSubDaysInput');
            const newEndsEl = document.getElementById('freezeSubModalNewEnds');
            if (!daysInput || !newEndsEl || !currentSubEndsDateStr) return;

            const days = parseInt(daysInput.value, 10);
            if (isNaN(days) || days < 1) {
                newEndsEl.textContent = '-';
                return;
            }

            const baseDate = new Date(currentSubEndsDateStr);
            if (isNaN(baseDate.getTime())) {
                newEndsEl.textContent = '-';
                return;
            }

            baseDate.setDate(baseDate.getDate() + days);
            const yyyy = baseDate.getFullYear();
            const mm = String(baseDate.getMonth() + 1).padStart(2, '0');
            const dd = String(baseDate.getDate()).padStart(2, '0');
            newEndsEl.textContent = `${yyyy}-${mm}-${dd} (+${days} يوم)`;
        }

        function openSubCollectModal(button) {
            const action = button.getAttribute('data-action');
            const student = button.getAttribute('data-student');
            const group = button.getAttribute('data-group');
            const total = button.getAttribute('data-total');
            const paid = button.getAttribute('data-paid');
            const remaining = parseFloat(button.getAttribute('data-remaining') || '0');

            const form = document.getElementById('collectSubPaymentForm');
            if (form) form.action = action;
            const subStud = document.getElementById('subModalStudent');
            if (subStud) subStud.textContent = student || '-';
            const subGrp = document.getElementById('subModalGroup');
            if (subGrp) subGrp.textContent = group || '-';
            const subTot = document.getElementById('subModalTotal');
            if (subTot) subTot.textContent = total;
            const subP = document.getElementById('subModalPaid');
            if (subP) subP.textContent = paid;
            const subRem = document.getElementById('subModalRemaining');
            if (subRem) subRem.textContent = remaining.toFixed(2);

            const input = document.getElementById('subModalAmountInput');
            if (input) {
                input.value = remaining.toFixed(2);
                input.max = remaining;
            }
        }

        function openSubDiscountModal(button) {
            const action = button.getAttribute('data-action');
            const student = button.getAttribute('data-student');
            const group = button.getAttribute('data-group');
            const remaining = parseFloat(button.getAttribute('data-remaining') || '0');

            const form = document.getElementById('subDiscountForm');
            if (form) form.action = action;
            const discStud = document.getElementById('discSubModalStudent');
            if (discStud) discStud.textContent = student || '-';
            const discGrp = document.getElementById('discSubModalGroup');
            if (discGrp) discGrp.textContent = group || '-';
            const discRem = document.getElementById('discSubModalRemaining');
            if (discRem) discRem.textContent = remaining.toFixed(2);

            const input = document.getElementById('discSubModalAmountInput');
            if (input) {
                input.value = remaining.toFixed(2);
                input.max = remaining;
            }
        }
    </script>
    @endpush
@endsection
