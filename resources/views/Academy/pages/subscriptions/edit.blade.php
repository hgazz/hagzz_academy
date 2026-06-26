@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.edit_subscription'))

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-lg-8 layout-spacing">
                <form action="{{ route('academy.subscriptions.update', $subscription) }}" method="POST">
                    @method('PUT')
                    @include('Academy.pages.subscriptions.partials._form')
                </form>
            </div>
            <div class="col-lg-4 layout-spacing">
                <div class="card">
                    <div class="card-header"><h4 class="mb-0">{{ trans('admin.student_management.record_payment') }}</h4></div>
                    <div class="card-body">
                        <form action="{{ route('academy.subscriptions.payments.store', $subscription) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ trans('admin.student_management.amount') }}</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ trans('admin.student_management.paid_at') }}</label>
                                <input type="date" name="paid_at" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ trans('admin.student_management.method') }}</label>
                                <select name="method" id="subscription_payment_method" class="form-select">
                                    @foreach(['cash', 'instapay', 'fawry', 'app_online', 'other'] as $method)
                                        <option value="{{ $method }}" @selected(old('method', 'cash') === $method)>{{ trans('admin.payment_methods.' . $method) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 d-none" id="subscription_payment_method_other_wrap">
                                <label class="form-label">{{ trans('admin.payment_method_other') }}</label>
                                <input type="text" name="method_other" id="subscription_payment_method_other" class="form-control"
                                       value="{{ old('method_other') }}" placeholder="{{ trans('admin.payment_method_other_placeholder') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ trans('admin.student_management.reference') }}</label>
                                <input type="text" name="reference" class="form-control">
                            </div>
                            <button class="btn btn-success w-100">{{ trans('admin.student_management.save_payment') }}</button>
                        </form>
                    </div>
                    <div class="card-body border-top">
                        <h5>{{ trans('admin.student_management.payments') }}</h5>
                        @forelse($subscription->payments as $payment)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>
                                    {{ $payment->paid_at?->format('Y-m-d') }}
                                    <small class="d-block text-muted">{{ $payment->method_label }}</small>
                                </span>
                                <strong>{{ number_format($payment->amount, 2) }}</strong>
                            </div>
                        @empty
                            <p class="text-muted">{{ trans('admin.student_management.no_payments_yet') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const methodSelect = document.getElementById('subscription_payment_method');
            const otherWrap = document.getElementById('subscription_payment_method_other_wrap');
            const otherInput = document.getElementById('subscription_payment_method_other');

            function toggleOtherMethod() {
                if (!methodSelect || !otherWrap || !otherInput) {
                    return;
                }

                const isOther = methodSelect.value === 'other';
                otherWrap.classList.toggle('d-none', !isOther);
                otherInput.required = isOther;

                if (!isOther) {
                    otherInput.value = '';
                }
            }

            if (methodSelect) {
                methodSelect.addEventListener('change', toggleOtherMethod);
                toggleOtherMethod();
            }
        });
    </script>
@endpush
