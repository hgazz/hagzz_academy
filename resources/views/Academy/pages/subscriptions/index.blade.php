@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.subscriptions'))

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ trans('admin.student_management.subscriptions') }}</h3>
                        <a href="{{ route('academy.subscriptions.create') }}" class="btn btn-primary">{{ trans('admin.student_management.add_subscription') }}</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('admin.student_management.student') }}</th>
                                    <th>{{ trans('admin.student_management.group') }}</th>
                                    <th>{{ trans('admin.student_management.period') }}</th>
                                    <th>{{ trans('admin.student_management.amount') }}</th>
                                    <th>{{ trans('admin.student_management.paid') }}</th>
                                    <th>{{ trans('admin.student_management.status') }}</th>
                                    <th>{{ trans('admin.student_management.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($subscriptions as $subscription)
                                    <tr>
                                        <td>{{ $subscription->id }}</td>
                                        <td>{{ $subscription->student?->name }}</td>
                                        <td>{{ $subscription->group?->name ?? '-' }}</td>
                                        <td>{{ $subscription->starts_on?->format('Y-m-d') }} / {{ $subscription->ends_on?->format('Y-m-d') }}</td>
                                        <td>{{ number_format($subscription->amount, 2) }}</td>
                                        <td>{{ number_format($subscription->paid_amount, 2) }}</td>
                                        <td>{{ trans('admin.student_management.' . $subscription->status) }} / {{ trans('admin.student_management.' . $subscription->payment_status) }}</td>
                                        <td>
                                            <a href="{{ route('academy.subscriptions.edit', $subscription) }}" class="btn btn-sm btn-warning">{{ trans('admin.student_management.edit') }}</a>
                                            <form action="{{ route('academy.subscriptions.destroy', $subscription) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('{{ trans('admin.student_management.delete_subscription_confirm') }}')">{{ trans('admin.student_management.delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center">{{ trans('admin.student_management.no_subscriptions_yet') }}</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $subscriptions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
