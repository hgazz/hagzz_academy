@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.attendance_records'))

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <form action="{{ route('academy.attendance.update', $session) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">{{ $session->group?->name }} - {{ $session->session_date?->format('Y-m-d') }}</h3>
                            <a href="{{ route('academy.attendance.index') }}" class="btn btn-light">{{ trans('admin.student_management.back') }}</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>{{ trans('admin.student_management.student') }}</th>
                                        <th>{{ trans('admin.student_management.status') }}</th>
                                        <th>{{ trans('admin.student_management.check_in') }}</th>
                                        <th>{{ trans('admin.student_management.check_out') }}</th>
                                        <th>{{ trans('admin.student_management.notes') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($session->records as $record)
                                        <tr>
                                            <td>{{ $record->student?->name }}</td>
                                            <td>
                                                <select name="records[{{ $record->id }}][status]" class="form-select">
                                                    @foreach(['present', 'absent', 'late', 'excused'] as $status)
                                                        <option value="{{ $status }}" @selected($record->status === $status)>{{ trans('admin.student_management.' . $status) }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" name="records[{{ $record->id }}][check_in_at]" class="form-control" value="{{ $record->check_in_at }}"></td>
                                            <td><input type="time" name="records[{{ $record->id }}][check_out_at]" class="form-control" value="{{ $record->check_out_at }}"></td>
                                            <td><input type="text" name="records[{{ $record->id }}][notes]" class="form-control" value="{{ $record->notes }}"></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-success">{{ trans('admin.student_management.save_attendance') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
