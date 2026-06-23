@extends('Academy.Layouts.master')

@section('title', 'Groups')

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Groups</h3>
                        <a href="{{ route('academy.groups.create') }}" class="btn btn-primary">Add Group</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Training</th>
                                    <th>Coach</th>
                                    <th>Time</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Actions</th>
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
                                        <td><span class="badge bg-{{ $group->status === 'active' ? 'success' : 'secondary' }}">{{ $group->status }}</span></td>
                                        <td>
                                            <a href="{{ route('academy.groups.edit', $group) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('academy.groups.destroy', $group) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this group?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center">No groups yet.</td></tr>
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
