@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.new_attendance_session'))

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <form action="{{ route('academy.attendance.store') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header"><h3 class="mb-0">{{ trans('admin.student_management.new_attendance_session') }}</h3></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ trans('admin.student_management.group') }} *</label>
                                    <select name="academy_group_id" class="form-select" required>
                                        <option value="">{{ trans('admin.student_management.select_group') }}</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ trans('admin.student_management.date') }} *</label>
                                    <input type="date" name="session_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ trans('admin.student_management.starts_at') }}</label>
                                    <input type="time" name="starts_at" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ trans('admin.student_management.ends_at') }}</label>
                                    <input type="time" name="ends_at" class="form-control">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">{{ trans('admin.student_management.notes') }}</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-success">{{ trans('admin.student_management.create_session') }}</button>
                            <a href="{{ route('academy.attendance.index') }}" class="btn btn-light">{{ trans('admin.student_management.cancel') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
