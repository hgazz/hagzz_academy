@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.groups'))

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ trans('admin.student_management.groups') }}</h3>
                        <a href="{{ route('academy.groups.create') }}" class="btn btn-primary">{{ trans('admin.student_management.add_group') }}</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('admin.student_management.name') }}</th>
                                    <th>{{ trans('admin.student_management.training') }}</th>
                                    <th>{{ trans('admin.student_management.coach') }}</th>
                                    <th>{{ trans('admin.student_management.time') }}</th>
                                    <th>{{ trans('admin.student_management.students') }}</th>
                                    <th>{{ trans('admin.student_management.status') }}</th>
                                    <th>{{ trans('admin.student_management.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($groups as $group)
                                    <tr>
                                        <td>{{ $group->id }}</td>
                                        <td>{{ $group->name }}</td>
                                        <td>{{ $group->training?->name ?? '-' }}</td>
                                        <td>{{ $group->coach?->name ?? '-' }}</td>
                                        <td>{{ $group->start_time ?? '-' }} - {{ $group->end_time ?? '-' }}</td>
                                        <td>{{ $group->students_count }}</td>
                                        <td><span class="badge bg-{{ $group->status === 'active' ? 'success' : 'secondary' }}">{{ trans('admin.student_management.' . $group->status) }}</span></td>
                                        <td>
                                            <a href="{{ route('academy.groups.edit', $group) }}" class="btn btn-sm btn-warning">{{ trans('admin.student_management.edit') }}</a>
                                            <form action="{{ route('academy.groups.destroy', $group) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('{{ trans('admin.student_management.delete_group_confirm') }}')">{{ trans('admin.student_management.delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center">{{ trans('admin.student_management.no_groups_yet') }}</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $groups->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
