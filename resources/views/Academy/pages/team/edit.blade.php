@extends('Academy.Layouts.master')

@section('title', 'تعديل بيانات وصلاحيات العضو')

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
                            <li class="breadcrumb-item"><a href="{{ route('academy.team.index') }}">طاقم العمل والصلاحيات</a></li>
                            <li class="breadcrumb-item active" aria-current="page">تعديل عضو</li>
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
                    <h5 class="card-title m-0 fw-bold"><i class="fa-solid fa-user-pen text-primary me-2"></i> تعديل بيانات وتخصيص صلاحيات: {{ $team->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('academy.team.update', $team->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">الاسم بالكامل <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $team->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $team->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">رقم الهاتف</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $team->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">كلمة المرور (اتركها فارغة إذا لم ترد التغيير)</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="******">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <hr class="my-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user-shield me-2"></i> الدور والصلاحيات (Role & Permissions)</h6>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">اختر الدور الوظيفي <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required {{ $team->is_owner ? 'disabled' : '' }}>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $selectedRole) == $role->id ? 'selected' : '' }}>
                                            {{ app()->getLocale() == 'ar' ? $role->display_name_ar : $role->display_name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($team->is_owner)
                                    <input type="hidden" name="role_id" value="{{ $selectedRole }}">
                                    <small class="text-muted">المالك الرئيسي يملك كافة الصلاحيات تلقائياً.</small>
                                @endif
                                @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <hr class="my-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-code-branch me-2"></i> نطاق الفروع المتاحة للعمل (Branch Access Scope)</h6>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="access_all_branches" value="1" id="accessAllCheck" {{ old('access_all_branches', $team->access_all_branches) ? 'checked' : '' }} {{ $team->is_owner ? 'disabled' : '' }}>
                                    <label class="form-check-label fw-bold" for="accessAllCheck">
                                        منح الصلاحية للوصول وإدارة كافة الفروع
                                    </label>
                                </div>
                                @if($team->is_owner)
                                    <input type="hidden" name="access_all_branches" value="1">
                                @endif
                            </div>

                            <div class="col-md-12" id="branchesSelectContainer" style="{{ old('access_all_branches', $team->access_all_branches) ? 'display: none;' : '' }}">
                                <label class="form-label fw-bold">حدد الفروع المسموح للمستخدم بإدارتها:</label>
                                <div class="row g-2">
                                    @forelse($branches as $branch)
                                        <div class="col-md-4">
                                            <div class="border rounded p-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branch_{{ $branch->id }}" {{ in_array($branch->id, old('branch_ids', $selectedBranches)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="branch_{{ $branch->id }}">
                                                        {{ $branch->commercial_name }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-muted">لا يوجد فروع إضافية مسجلة حالياً.</div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- ─── Sports Scope ─── --}}
                            <div class="col-md-12">
                                <hr class="my-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-futbol me-2"></i> نطاق الرياضات المتاحة للعمل (Sport Access Scope)</h6>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="access_all_sports" value="1" id="accessAllSportsCheck" {{ old('access_all_sports', $team->access_all_sports) ? 'checked' : '' }} {{ $team->is_owner ? 'disabled' : '' }}>
                                    <label class="form-check-label fw-bold" for="accessAllSportsCheck">
                                        منح الصلاحية للوصول لكافة الرياضات
                                    </label>
                                </div>
                                @if($team->is_owner)
                                    <input type="hidden" name="access_all_sports" value="1">
                                @endif
                            </div>

                            <div class="col-md-12" id="sportsSelectContainer" style="{{ old('access_all_sports', $team->access_all_sports) ? 'display: none;' : '' }}">
                                <label class="form-label fw-bold">حدد الرياضات المسموح للمستخدم برؤيتها وإدارتها:</label>
                                <div class="row g-2">
                                    @forelse($sports as $sport)
                                        <div class="col-md-3">
                                            <div class="border rounded p-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="sport_ids[]" value="{{ $sport->id }}" id="sport_{{ $sport->id }}" {{ in_array($sport->id, old('sport_ids', $selectedSports)) ? 'checked' : '' }}>
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="sport_{{ $sport->id }}">
                                                        @if($sport->getRawOriginal('icon'))
                                                            <img src="{{ $sport->icon }}" alt="" width="20" height="20" style="object-fit:contain">
                                                        @endif
                                                        {{ $sport->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-muted">لا توجد رياضات مسجلة في هذه الأكاديمية.</div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <a href="{{ route('academy.team.index') }}" class="btn btn-light me-2">إلغاء</a>
                                <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-check me-1"></i> تحديث البيانات</button>
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
        // Branch toggle
        const branchCheck = document.getElementById('accessAllCheck');
        const branchContainer = document.getElementById('branchesSelectContainer');
        if (branchCheck && branchContainer) {
            branchCheck.addEventListener('change', function() {
                branchContainer.style.display = this.checked ? 'none' : 'block';
            });
        }

        // Sport toggle
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

