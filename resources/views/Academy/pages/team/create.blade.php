@extends('Academy.Layouts.master')

@php
    $ar = session('locale', app()->getLocale()) === 'ar';
    $t = $ar ? [
        'page_title' => 'إضافة عضو جديد لطاقم العمل',
        'breadcrumb_team' => 'طاقم العمل والصلاحيات',
        'breadcrumb_create' => 'إضافة عضو جديد',
        'card_title' => 'إضافة عضو جديد لطاقم العمل',
        'full_name' => 'الاسم بالكامل',
        'name_placeholder' => 'أدخل اسم العضو',
        'email' => 'البريد الإلكتروني',
        'phone' => 'رقم الهاتف',
        'phone_placeholder' => '050XXXXXXX',
        'password' => 'كلمة المرور',
        'role_section' => 'الدور والصلاحيات',
        'select_role' => 'اختر الدور الوظيفي',
        'choose_role' => '-- اختر الدور --',
        'branch_section' => 'نطاق الفروع المتاحة للعمل (Branch Access Scope)',
        'all_branches_label' => 'منح الصلاحية للوصول وإدارة كافة الفروع',
        'select_branches_label' => 'حدد الفروع المسموح للمستخدم بإدارتها:',
        'no_branches' => 'لا توجد فروع إضافية مسجلة حالياً.',
        'sports_section' => 'تحديد نطاق الرياضات المتاحة للعمل (Sports Access Scope)',
        'all_sports_label' => 'منح الوصول لجميع الألعاب الرياضية',
        'select_sports_label' => 'حدد الرياضات المسموح للمستخدم برؤيتها وإدارتها:',
        'no_sports' => 'لا توجد رياضات مسجلة في هذه الأكاديمية.',
        'cancel' => 'إلغاء',
        'save' => 'حفظ العضو',
    ] : [
        'page_title' => 'Add New Team Member',
        'breadcrumb_team' => 'Team & Permissions',
        'breadcrumb_create' => 'Add Member',
        'card_title' => 'Add New Team Member',
        'full_name' => 'Full Name',
        'name_placeholder' => 'Enter member name',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'phone_placeholder' => '050XXXXXXX',
        'password' => 'Password',
        'role_section' => 'Role & Permissions',
        'select_role' => 'Select Job Role',
        'choose_role' => '-- Select Role --',
        'branch_section' => 'Branch Access Scope',
        'all_branches_label' => 'Grant access to manage all branches',
        'select_branches_label' => 'Select branches allowed for this user:',
        'no_branches' => 'No additional branches registered.',
        'sports_section' => 'Sports Access Scope',
        'all_sports_label' => 'Grant access to all sports',
        'select_sports_label' => 'Select sports allowed for this user:',
        'no_sports' => 'No sports registered for this academy.',
        'cancel' => 'Cancel',
        'save' => 'Save Member',
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
                            <li class="breadcrumb-item active" aria-current="page">{{ $t['breadcrumb_create'] }}</li>
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
                    <h5 class="card-title m-0 fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i> {{ $t['card_title'] }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('academy.team.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $t['full_name'] }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="{{ $t['name_placeholder'] }}">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $t['email'] }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="example@domain.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $t['phone'] }}</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="{{ $t['phone_placeholder'] }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ $t['password'] }} <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="******">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <hr class="my-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user-shield me-2"></i> {{ $t['role_section'] }}</h6>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">{{ $t['select_role'] }} <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    <option value="">{{ $t['choose_role'] }}</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $ar ? $role->display_name_ar : $role->display_name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <hr class="my-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-code-branch me-2"></i> {{ $t['branch_section'] }}</h6>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="access_all_branches" value="1" id="accessAllCheck" {{ old('access_all_branches', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="accessAllCheck">
                                        {{ $t['all_branches_label'] }}
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12" id="branchesSelectContainer" style="{{ old('access_all_branches', '1') ? 'display: none;' : '' }}">
                                <label class="form-label fw-bold">{{ $t['select_branches_label'] }}</label>
                                <div class="row g-2">
                                    @forelse($branches as $branch)
                                        <div class="col-md-4">
                                            <div class="border rounded p-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branch_{{ $branch->id }}" {{ is_array(old('branch_ids')) && in_array($branch->id, old('branch_ids')) ? 'checked' : '' }}>
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
                                    <input class="form-check-input" type="checkbox" name="access_all_sports" value="1" id="accessAllSportsCheck" {{ old('access_all_sports', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="accessAllSportsCheck">
                                        {{ $t['all_sports_label'] }}
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12" id="sportsSelectContainer" style="{{ old('access_all_sports', '1') ? 'display: none;' : '' }}">
                                <label class="form-label fw-bold">{{ $t['select_sports_label'] }}</label>
                                <div class="row g-2">
                                    @forelse($sports as $sport)
                                        <div class="col-md-3">
                                            <div class="border rounded p-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="sport_ids[]" value="{{ $sport->id }}" id="sport_{{ $sport->id }}" {{ is_array(old('sport_ids')) && in_array($sport->id, old('sport_ids')) ? 'checked' : '' }}>
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
