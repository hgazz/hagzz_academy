@extends('Academy.Layouts.master')

@section('title', 'Edit Subscription')

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
                    <div class="card-header"><h4 class="mb-0">Record Payment</h4></div>
                    <div class="card-body">
                        <form action="{{ route('academy.subscriptions.payments.store', $subscription) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Amount</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Paid At</label>
                                <input type="date" name="paid_at" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Method</label>
                                <select name="method" class="form-select">
                                    @foreach(['cash', 'bank_transfer', 'card', 'online', 'other'] as $method)
                                        <option value="{{ $method }}">{{ str_replace('_', ' ', ucfirst($method)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Reference</label>
                                <input type="text" name="reference" class="form-control">
                            </div>
                            <button class="btn btn-success w-100">Save Payment</button>
                        </form>
                    </div>
                    <div class="card-body border-top">
                        <h5>Payments</h5>
                        @forelse($subscription->payments as $payment)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ $payment->paid_at?->format('Y-m-d') }}</span>
                                <strong>{{ number_format($payment->amount, 2) }}</strong>
                            </div>
                        @empty
                            <p class="text-muted">No payments yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
