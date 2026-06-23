@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.reports'))

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            @foreach([
                ['label' => trans('admin.student_management.active_students'), 'value' => $activeStudentsCount],
                ['label' => trans('admin.student_management.active_groups'), 'value' => $activeGroupsCount],
                ['label' => trans('admin.student_management.active_subscriptions'), 'value' => $activeSubscriptionsCount],
                ['label' => trans('admin.student_management.paid_amount'), 'value' => number_format($paidAmount, 2)],
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
                    <div class="card-header"><h4 class="mb-0">{{ trans('admin.student_management.attendance_summary') }}</h4></div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody>
                            @foreach(['present', 'absent', 'late', 'excused'] as $status)
                                <tr>
                                    <td>{{ trans('admin.student_management.' . $status) }}</td>
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
                    <div class="card-header"><h4 class="mb-0">{{ trans('admin.student_management.subscriptions_ending_soon') }}</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>{{ trans('admin.student_management.student') }}</th>
                                    <th>{{ trans('admin.student_management.group') }}</th>
                                    <th>{{ trans('admin.student_management.ends_on') }}</th>
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
                                    <tr><td colspan="3" class="text-center">{{ trans('admin.student_management.no_subscriptions_ending_soon') }}</td></tr>
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
                    <div class="card-header"><h4 class="mb-0">{{ trans('admin.student_management.latest_payments') }}</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>{{ trans('admin.student_management.student') }}</th>
                                    <th>{{ trans('admin.student_management.date') }}</th>
                                    <th>{{ trans('admin.student_management.method') }}</th>
                                    <th>{{ trans('admin.student_management.amount') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($latestPayments as $payment)
                                    <tr>
                                        <td>{{ $payment->subscription?->student?->name }}</td>
                                        <td>{{ $payment->paid_at?->format('Y-m-d') }}</td>
                                        <td>{{ trans('admin.student_management.' . $payment->method) }}</td>
                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">{{ trans('admin.student_management.no_payments_yet') }}</td></tr>
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
