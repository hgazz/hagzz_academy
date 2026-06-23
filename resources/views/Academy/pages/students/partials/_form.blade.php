@csrf
<div class="card">
    <div class="card-header">
        <h3 class="mb-0">{{ isset($student) ? trans('admin.student_management.edit_student') : trans('admin.student_management.add_student') }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.name') }} *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $student->name ?? '') }}" required>
                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.phone') }}</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone ?? '') }}">
                @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.email') }}</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $student->email ?? '') }}">
                @error('email')<span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">{{ trans('admin.student_management.gender') }}</label>
                <select name="gender" class="form-select">
                    <option value="">{{ trans('admin.student_management.select') }}</option>
                    <option value="male" @selected(old('gender', $student->gender ?? '') === 'male')>{{ trans('admin.student_management.male') }}</option>
                    <option value="female" @selected(old('gender', $student->gender ?? '') === 'female')>{{ trans('admin.student_management.female') }}</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">{{ trans('admin.student_management.birth_date') }}</label>
                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', isset($student) && $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.guardian_name') }}</label>
                <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name', $student->guardian_name ?? '') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.guardian_phone') }}</label>
                <input type="text" name="guardian_phone" class="form-control" value="{{ old('guardian_phone', $student->guardian_phone ?? '') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ trans('admin.student_management.status') }} *</label>
                <select name="status" class="form-select" required>
                    @foreach(['active', 'inactive', 'suspended'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $student->status ?? 'active') === $status)>{{ trans('admin.student_management.' . $status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ trans('admin.student_management.medical_notes') }}</label>
                <textarea name="medical_notes" class="form-control" rows="3">{{ old('medical_notes', $student->medical_notes ?? '') }}</textarea>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ trans('admin.student_management.notes') }}</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $student->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-success">{{ trans('admin.student_management.save') }}</button>
        <a href="{{ route('academy.students.index') }}" class="btn btn-light">{{ trans('admin.student_management.cancel') }}</a>
    </div>
</div>
