@extends('Academy.Layouts.master')
@section('title', trans('admin.venues.locations'))
@section('content')
<div class="middle-content container-xxl p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div><h3 class="mb-1">{{ trans('admin.venues.locations') }}</h3><p class="text-muted mb-0">{{ trans('admin.venues.locations_hint') }}</p></div>
        <a href="{{ route('academy.venues.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>{{ trans('admin.venues.add_location') }}</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-hover align-middle mb-0"><thead><tr><th>{{ trans('admin.venues.name') }}</th><th>{{ trans('admin.venues.address') }}</th><th>{{ trans('admin.venues.currency') }}</th><th>{{ trans('admin.venues.spaces') }}</th><th>{{ trans('admin.status') }}</th><th>{{ trans('admin.actions') }}</th></tr></thead>
        <tbody>@forelse($venues as $venue)<tr><td class="fw-semibold">{{ $venue->name }}</td><td>{{ $venue->address }}</td><td>{{ $venue->currency }}</td><td>{{ $venue->spaces_count }}</td><td><span class="badge {{ $venue->active ? 'bg-success' : 'bg-secondary' }}">{{ $venue->active ? trans('admin.venues.active') : trans('admin.venues.inactive') }}</span></td><td><div class="d-flex gap-2"><a class="btn btn-sm btn-outline-primary" href="{{ route('academy.venues.edit',$venue) }}"><i class="fa-solid fa-pen"></i></a><form method="POST" action="{{ route('academy.venues.destroy',$venue) }}" onsubmit="return confirm('{{ trans('admin.venues.delete_confirm') }}')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button></form></div></td></tr>@empty<tr><td colspan="6" class="text-center py-5 text-muted">{{ trans('admin.venues.empty_locations') }}</td></tr>@endforelse</tbody></table>
    </div><div class="mt-3">{{ $venues->links() }}</div>
</div>
@endsection
