@extends('Academy.Layouts.master')

@section('title', 'Subscriptions')

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Subscriptions</h3>
                        <a href="{{ route('academy.subscriptions.create') }}" class="btn btn-primary">Add Subscription</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Group</th>
                                    <th>Period</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Status</th>
                                    <th>Actions</th>
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
                                        <td>{{ $subscription->status }} / {{ $subscription->payment_status }}</td>
                                        <td>
                                            <a href="{{ route('academy.subscriptions.edit', $subscription) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('academy.subscriptions.destroy', $subscription) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this subscription?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center">No subscriptions yet.</td></tr>
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
