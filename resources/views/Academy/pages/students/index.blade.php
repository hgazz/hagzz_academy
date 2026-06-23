@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.students'))

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ trans('admin.student_management.students') }}</h3>
                        <a href="{{ route('academy.students.create') }}" class="btn btn-primary">{{ trans('admin.student_management.add_student') }}</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('admin.student_management.name') }}</th>
                                    <th>{{ trans('admin.student_management.phone') }}</th>
                                    <th>{{ trans('admin.student_management.guardian') }}</th>
                                    <th>{{ trans('admin.student_management.status') }}</th>
                                    <th>{{ trans('admin.student_management.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>{{ $student->id }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->phone ?? '-' }}</td>
                                        <td>{{ $student->guardian_name ?? '-' }}<br><small>{{ $student->guardian_phone }}</small></td>
                                        <td><span class="badge bg-{{ $student->status === 'active' ? 'success' : 'secondary' }}">{{ trans('admin.student_management.' . $student->status) }}</span></td>
                                        <td>
                                            <a href="{{ route('academy.students.edit', $student) }}" class="btn btn-sm btn-warning">{{ trans('admin.student_management.edit') }}</a>
                                            <form action="{{ route('academy.students.destroy', $student) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ trans('admin.student_management.delete_student_confirm') }}')">{{ trans('admin.student_management.delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ trans('admin.student_management.no_students_yet') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
