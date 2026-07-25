@extends('Academy.Layouts.master')

@section('title', 'طاقم العمل والصلاحيات')

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
                            <li class="breadcrumb-item active" aria-current="page">طاقم العمل والصلاحيات</li>
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
                    <h5 class="card-title m-0 fw-bold"><i class="fa-solid fa-users-gear text-primary me-2"></i> إدارة أعضاء طاقم العمل</h5>
                    <a href="{{ route('academy.team.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-user-plus me-1"></i> إضافة عضو جديد
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th>الدور (Role)</th>
                                    <th>نطاق الفروع</th>
                                    <th>الحالة</th>
                                    <th class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-bold">
                                            {{ $user->name }}
                                            @if($user->is_owner)
                                                <span class="badge bg-warning text-dark ms-1"><i class="fa-solid fa-crown me-1"></i>المالك</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone ?? '-' }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-info text-dark me-1">{{ app()->getLocale() == 'ar' ? $role->display_name_ar : $role->display_name_en }}</span>
                                            @endforeach
                                            @if($user->is_owner && $user->roles->isEmpty())
                                                <span class="badge bg-warning text-dark">مالك الشريك</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_owner || $user->access_all_branches)
                                                <span class="badge bg-success">كافة الفروع</span>
                                            @else
                                                <span class="badge bg-secondary" title="{{ $user->assignedBranches->pluck('commercial_name')->join(', ') }}">
                                                    {{ $user->assignedBranches->count() }} فرع مخصص
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->status === 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-danger">مجمد</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('academy.team.edit', $user->id) }}" class="btn btn-outline-primary" title="تعديل">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>

                                                @if(!$user->is_owner)
                                                    <form action="{{ route('academy.team.updateStatus', $user->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-outline-warning" title="{{ $user->status === 'active' ? 'تجميد' : 'تفعيل' }}">
                                                            <i class="fa-solid {{ $user->status === 'active' ? 'fa-ban' : 'fa-check' }}"></i>
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('academy.team.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت تأكد من رغبتك في حذف هذا المستخدم؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="fa-solid fa-folder-open fa-2x mb-2 d-block"></i>
                                            لا يوجد أعضاء مضافون حتى الآن.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($users->hasPages())
                    <div class="card-footer bg-white py-3">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
