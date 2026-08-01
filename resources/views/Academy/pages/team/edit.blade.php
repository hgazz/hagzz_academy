@extends('Academy.Layouts.master')

@php
    $ar = session('locale', app()->getLocale()) === 'ar';
    $t = $ar ? [
        'page_title' => 'تعديل بيانات وصلاحيات العضو',
        'breadcrumb_team' => 'طاقم العمل والصلاحيات',
        'breadcrumb_edit' => 'تعديل عضو',
        'card_title' => 'تعديل بيانات وتخصيص صلاحيات',
        'full_name' => 'الاسم بالكامل',
        'email' => 'البريد الإلكتروني',
        'phone' => 'رقم الهاتف',
        'password' => 'كلمة المرور (اتركها فارغة إذا لم ترد التغيير)',
        'role_section' => 'الدور والصلاحيات',
        'select_role' => 'اختر الدور الوظيفي',
        'owner_auto_rights' => 'المالك الرئيسي يملك كافة الصلاحيات تلقائياً.',
        'branch_section' => 'نطاق الفروع المتاحة للعمل (Branch Access Scope)',
        'all_branches_label' => 'منح الصلاحية للوصول وإدارة كافة الفروع',
        'select_branches_label' => 'حدد الفروع المسموح للمستخدم بإدارتها:',
        'no_branches' => 'لا توجد فروع إضافية مسجلة حالياً.',
        'sports_section' => 'تحديد نطاق الرياضات المتاحة للعمل (Sports Access Scope)',
        'all_sports_label' => 'منح الوصول لجميع الألعاب الرياضية',
        'select_sports_label' => 'حدد الرياضات المسموح للمستخدم برؤيتها وإدارتها:',
        'no_sports' => 'لا توجد رياضات مسجلة في هذه الأكاديمية.',
        'cancel' => 'إلغاء',
        'save' => 'حفظ التعديلات',
    ] : [
        'page_title' => 'Edit Member & Permissions',
        'breadcrumb_team' => 'Team & Permissions',
        'breadcrumb_edit' => 'Edit Member',
        'card_title' => 'Edit Member & Permissions',
        'full_name' => 'Full Name',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'password' => 'Password (leave blank to keep unchanged)',
        'role_section' => 'Role & Permissions',
        'select_role' => 'Select Job Role',
        'owner_auto_rights' => 'Primary owner automatically possesses all permissions.',
        'branch_section' => 'Branch Access Scope',
        'all_branches_label' => 'Grant access to manage all branches',
        'select_branches_label' => 'Select branches allowed for this user:',
        'no_branches' => 'No additional branches registered.',
        'sports_section' => 'Sports Access Scope',
        'all_sports_label' => 'Grant access to all sports',
        'select_sports_label' => 'Select sports allowed for this user:',
        'no_sports' => 'No sports registered for this academy.',
        'cancel' => 'Cancel',
        'save' => 'Save Changes',
    ];
@endphp

@section('title', $t['page_title'])

@section('content')
<div class="middle-content container-xxl p-0">
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
                            <li class="breadcrumb-item"><a href="{{ route('academy.team.index') }}">{{ $t['breadcrumb_team'] }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $t['breadcrumb_edit'] }}</li>
                        </ol>
                    </nav>
                </div>
            </header>
        </div>
    </div>

    <div class="row layout-top-spacing">
        <div class="col-lg-10 offset-lg-1 layout-spacing">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title m-0 fw-bold"><i class="fa-solid fa-user-pen text-primary me-2"></i> {{ $t['card_title'] }}: {{ $team->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('academy.team.update', $team->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $t['full_name'] }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $team->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $t['email'] }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $team->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $t['phone'] }}</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $team->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $t['password'] }}</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="******">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <hr class="my-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user-shield me-2"></i> {{ $t['role_section'] }}</h6>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">{{ $t['select_role'] }} <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required {{ $team->is_owner ? 'disabled' : '' }}>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $selectedRole) == $role->id ? 'selected' : '' }}>
                                            {{ $ar ? $role->display_name_ar : $role->display_name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($team->is_owner)
                                    <input type="hidden" name="role_id" value="{{ $selectedRole }}">
                                    <small class="text-muted">{{ $t['owner_auto_rights'] }}</small>
                                @endif
                                @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <hr class="my-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-code-branch me-2"></i> {{ $t['branch_section'] }}</h6>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="access_all_branches" value="1" id="accessAllCheck" {{ old('access_all_branches', $team->access_all_branches) ? 'checked' : '' }} {{ $team->is_owner ? 'disabled' : '' }}>
                                    <label class="form-check-label fw-bold" for="accessAllCheck">
                                        {{ $t['all_branches_label'] }}
                                    </label>
                                </div>
                                @if($team->is_owner)
                                    <input type="hidden" name="access_all_branches" value="1">
                                @endif
                            </div>

                            <div class="col-md-12" id="branchesSelectContainer" style="{{ old('access_all_branches', $team->access_all_branches) ? 'display: none;' : '' }}">
                                <label class="form-label fw-bold">{{ $t['select_branches_label'] }}</label>
                                <div class="row g-2">
                                    @forelse($branches as $branch)
                                        @php
                                            $isAssigned = in_array($branch->id, old('branch_ids', $assignedBranchIds));
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="border rounded p-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branch_{{ $branch->id }}" {{ $isAssigned ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="branch_{{ $branch->id }}">
                                                        {{ $branch->commercial_name }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-muted">{{ $t['no_branches'] }}</div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-md-12">
                                <hr class="my-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-futbol me-2"></i> {{ $t['sports_section'] }}</h6>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="access_all_sports" value="1" id="accessAllSportsCheck" {{ old('access_all_sports', $team->access_all_sports) ? 'checked' : '' }} {{ $team->is_owner ? 'disabled' : '' }}>
                                    <label class="form-check-label fw-bold" for="accessAllSportsCheck">
                                        {{ $t['all_sports_label'] }}
                                    </label>
                                </div>
                                @if($team->is_owner)
                                    <input type="hidden" name="access_all_sports" value="1">
                                @endif
                            </div>

                            <div class="col-md-12" id="sportsSelectContainer" style="{{ old('access_all_sports', $team->access_all_sports) ? 'display: none;' : '' }}">
                                <label class="form-label fw-bold">{{ $t['select_sports_label'] }}</label>
                                <div class="row g-2">
                                    @forelse($sports as $sport)
                                        @php
                                            $isSportAssigned = in_array($sport->id, old('sport_ids', $assignedSportIds));
                                        @endphp
                                        <div class="col-md-3">
                                            <div class="border rounded p-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="sport_ids[]" value="{{ $sport->id }}" id="sport_{{ $sport->id }}" {{ $isSportAssigned ? 'checked' : '' }}>
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="sport_{{ $sport->id }}">
                                                        @if($sport->icon)
                                                            <img src="{{ $sport->icon }}" alt="{{ $sport->name }}" width="22" height="22" style="object-fit:contain; border-radius:4px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                                            <i class="fa-solid fa-trophy text-primary" style="display:none; font-size:16px;"></i>
                                                        @else
                                                            <i class="fa-solid fa-trophy text-primary" style="font-size:16px;"></i>
                                                        @endif
                                                        <span>{{ $sport->name }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-muted">{{ $t['no_sports'] }}</div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <a href="{{ route('academy.team.index') }}" class="btn btn-light me-2">{{ $t['cancel'] }}</a>
                                <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-check me-1"></i> {{ $t['save'] }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const branchCheck = document.getElementById('accessAllCheck');
        const branchContainer = document.getElementById('branchesSelectContainer');
        if (branchCheck && branchContainer) {
            branchCheck.addEventListener('change', function() {
                branchContainer.style.display = this.checked ? 'none' : 'block';
            });
        }

        const sportCheck = document.getElementById('accessAllSportsCheck');
        const sportContainer = document.getElementById('sportsSelectContainer');
        if (sportCheck && sportContainer) {
            sportCheck.addEventListener('change', function() {
                sportContainer.style.display = this.checked ? 'none' : 'block';
            });
        }
    });
</script>
@endpush
@endsection
