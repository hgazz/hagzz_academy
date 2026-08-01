@extends('Academy.Layouts.master')

@php
    $ar = session('locale', app()->getLocale()) === 'ar';
    $t = $ar ? [
        'page_title' => 'طاقم العمل والصلاحيات',
        'breadcrumb_team' => 'طاقم العمل والصلاحيات',
        'card_title' => 'إدارة أعضاء طاقم العمل',
        'add_member' => 'إضافة عضو جديد',
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'role' => 'الدور (Role)',
        'branches_scope' => 'نطاق الفروع',
        'sports_scope' => 'نطاق الرياضات',
        'status' => 'الحالة',
        'actions' => 'الإجراءات',
        'owner' => 'المالك',
        'partner_owner' => 'مالك الشريك',
        'all_branches' => 'كافة الفروع',
        'assigned_branches' => 'فرع مخصص',
        'all_sports' => 'كافة الرياضات',
        'assigned_sports' => 'رياضة مخصصة',
        'active' => 'نشط',
        'inactive' => 'معطل',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'delete_confirm' => 'هل أنت تأكد من رغبتك في حذف هذا العضو؟',
        'no_members' => 'لا يوجد أعضاء آخرين في طاقم العمل حالياً.',
    ] : [
        'page_title' => 'Team & Permissions',
        'breadcrumb_team' => 'Team & Permissions',
        'card_title' => 'Team Members & Permissions Management',
        'add_member' => 'Add New Member',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'role' => 'Role',
        'branches_scope' => 'Branches Scope',
        'sports_scope' => 'Sports Scope',
        'status' => 'Status',
        'actions' => 'Actions',
        'owner' => 'Owner',
        'partner_owner' => 'Partner Owner',
        'all_branches' => 'All Branches',
        'assigned_branches' => 'Assigned Branches',
        'all_sports' => 'All Sports',
        'assigned_sports' => 'Assigned Sports',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'delete_confirm' => 'Are you sure you want to delete this team member?',
        'no_members' => 'No team members registered yet.',
    ];
@endphp

@section('title', $t['page_title'])

@section('content')
<div class="middle-content container-xxl p-0">
    <!-- BREADCRUMBS -->
    <div class="secondary-nav mb-4">
        <div class="breadcrumbs-container">
            <header class="header navbar navbar-expand-sm">
                <a href="javascript:void(0);" class="btn-toggle sidebarCollapse">
                    <i class="fa-solid fa-bars"></i>
                </a>
                <div class="d-flex breadcrumb-content">
                    <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $t['breadcrumb_team'] }}</li>
                        </ol>
                    </nav>
                </div>
            </header>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row layout-top-spacing">
        <div class="col-12 layout-spacing">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 fw-bold"><i class="fa-solid fa-users-gear text-primary me-2"></i> {{ $t['card_title'] }}</h5>
                    <a href="{{ route('academy.team.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-user-plus me-1"></i> {{ $t['add_member'] }}
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ $t['name'] }}</th>
                                    <th>{{ $t['email'] }}</th>
                                    <th>{{ $t['phone'] }}</th>
                                    <th>{{ $t['role'] }}</th>
                                    <th>{{ $t['branches_scope'] }}</th>
                                    <th>{{ $t['sports_scope'] }}</th>
                                    <th>{{ $t['status'] }}</th>
                                    <th class="text-center">{{ $t['actions'] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-bold">
                                            {{ $user->name }}
                                            @if($user->is_owner)
                                                <span class="badge bg-warning text-dark ms-1"><i class="fa-solid fa-crown me-1"></i>{{ $t['owner'] }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone ?? '-' }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-info text-dark me-1">{{ $ar ? $role->display_name_ar : $role->display_name_en }}</span>
                                            @endforeach
                                            @if($user->is_owner && $user->roles->isEmpty())
                                                <span class="badge bg-warning text-dark">{{ $t['partner_owner'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_owner || $user->access_all_branches)
                                                <span class="badge bg-success">{{ $t['all_branches'] }}</span>
                                            @else
                                                <span class="badge bg-secondary" title="{{ $user->assignedBranches->pluck('commercial_name')->join(', ') }}">
                                                    {{ $user->assignedBranches->count() }} {{ $t['assigned_branches'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_owner || $user->access_all_sports)
                                                <span class="badge bg-success">{{ $t['all_sports'] }}</span>
                                            @else
                                                <span class="badge bg-info text-dark" title="{{ $user->assignedSports->pluck('name')->join(', ') }}">
                                                    {{ $user->assignedSports->count() }} {{ $t['assigned_sports'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                                {{ $user->is_active ? $t['active'] : $t['inactive'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('academy.team.edit', $user->id) }}" class="btn btn-sm btn-outline-warning" title="{{ $t['edit'] }}">
                                                    <i class="fa-solid fa-user-pen"></i>
                                                </a>

                                                @if(!$user->is_owner && $user->id !== auth('academy')->id())
                                                    <form action="{{ route('academy.team.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $t['delete_confirm'] }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ $t['delete'] }}">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">{{ $t['no_members'] }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
