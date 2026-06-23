@extends('Academy.Layouts.master')

@section('title', 'Attendance')

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Attendance</h3>
                        <a href="{{ route('academy.attendance.create') }}" class="btn btn-primary">New Session</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Group</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($sessions as $session)
                                    <tr>
                                        <td>{{ $session->id }}</td>
                                        <td>{{ $session->group?->name }}</td>
                                        <td>{{ $session->session_date?->format('Y-m-d') }}</td>
                                        <td>{{ $session->starts_at ?? '-' }} - {{ $session->ends_at ?? '-' }}</td>
                                        <td><a href="{{ route('academy.attendance.show', $session) }}" class="btn btn-sm btn-primary">Open</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">No attendance sessions yet.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $sessions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
