@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.attendance'))

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ trans('admin.student_management.attendance') }}</h3>
                        <a href="{{ route('academy.attendance.create') }}" class="btn btn-primary">{{ trans('admin.student_management.new_attendance_session') }}</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('admin.student_management.group') }}</th>
                                    <th>{{ trans('admin.student_management.date') }}</th>
                                    <th>{{ trans('admin.student_management.time') }}</th>
                                    <th>{{ trans('admin.student_management.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($sessions as $session)
                                    <tr>
                                        <td>{{ $session->id }}</td>
                                        <td>{{ $session->group?->name }}</td>
                                        <td>{{ $session->session_date?->format('Y-m-d') }}</td>
                                        <td>{{ $session->starts_at ?? '-' }} - {{ $session->ends_at ?? '-' }}</td>
                                        <td><a href="{{ route('academy.attendance.show', $session) }}" class="btn btn-sm btn-primary">{{ trans('admin.student_management.open') }}</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">{{ trans('admin.student_management.no_attendance_sessions_yet') }}</td></tr>
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
