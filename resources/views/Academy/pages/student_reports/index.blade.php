@extends('Academy.Layouts.master')

@section('title', 'Student Reports')

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            @foreach([
                ['label' => 'Active Students', 'value' => $activeStudentsCount],
                ['label' => 'Active Groups', 'value' => $activeGroupsCount],
                ['label' => 'Active Subscriptions', 'value' => $activeSubscriptionsCount],
                ['label' => 'Paid Amount', 'value' => number_format($paidAmount, 2)],
            ] as $card)
                <div class="col-md-3 layout-spacing">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">{{ $card['label'] }}</p>
                            <h3 class="mb-0">{{ $card['value'] }}</h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-5 layout-spacing">
                <div class="card">
                    <div class="card-header"><h4 class="mb-0">Attendance Summary</h4></div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody>
                            @foreach(['present', 'absent', 'late', 'excused'] as $status)
                                <tr>
                                    <td>{{ ucfirst($status) }}</td>
                                    <td class="text-end">{{ $attendanceByStatus[$status] ?? 0 }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 layout-spacing">
                <div class="card">
                    <div class="card-header"><h4 class="mb-0">Subscriptions Ending Soon</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Group</th>
                                    <th>Ends On</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($expiringSubscriptions as $subscription)
                                    <tr>
                                        <td>{{ $subscription->student?->name }}</td>
                                        <td>{{ $subscription->group?->name ?? '-' }}</td>
                                        <td>{{ $subscription->ends_on?->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center">No subscriptions ending soon.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 layout-spacing">
                <div class="card">
                    <div class="card-header"><h4 class="mb-0">Latest Payments</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($latestPayments as $payment)
                                    <tr>
                                        <td>{{ $payment->subscription?->student?->name }}</td>
                                        <td>{{ $payment->paid_at?->format('Y-m-d') }}</td>
                                        <td>{{ str_replace('_', ' ', ucfirst($payment->method)) }}</td>
                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">No payments yet.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
