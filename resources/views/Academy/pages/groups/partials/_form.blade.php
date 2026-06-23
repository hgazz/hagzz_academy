@csrf
@php
    $selectedStudents = old('student_ids', isset($group) ? $group->students()->pluck('academy_students.id')->toArray() : []);
    $selectedDays = old('days', $group->days ?? []);
@endphp
<div class="card">
    <div class="card-header">
        <h3 class="mb-0">{{ isset($group) ? trans('admin.student_management.edit_group') : trans('admin.student_management.add_group') }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.group_name') }} *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $group->name ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.linked_training') }}</label>
                <select name="training_id" class="form-select">
                    <option value="">{{ trans('admin.student_management.no_training') }}</option>
                    @foreach($trainings as $training)
                        <option value="{{ $training->id }}" @selected(old('training_id', $group->training_id ?? '') == $training->id)>{{ $training->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.coach') }}</label>
                <select name="coach_id" class="form-select">
                    <option value="">{{ trans('admin.student_management.no_coach') }}</option>
                    @foreach($coaches as $coach)
                        <option value="{{ $coach->id }}" @selected(old('coach_id', $group->coach_id ?? '') == $coach->id)>{{ $coach->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.sport') }}</label>
                <select name="sport_id" class="form-select">
                    <option value="">{{ trans('admin.student_management.no_sport') }}</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}" @selected(old('sport_id', $group->sport_id ?? '') == $sport->id)>{{ $sport->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">{{ trans('admin.student_management.start_time') }}</label>
                <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $group->start_time ?? '') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">{{ trans('admin.student_management.end_time') }}</label>
                <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $group->end_time ?? '') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">{{ trans('admin.student_management.capacity') }}</label>
                <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $group->capacity ?? '') }}" min="1">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">{{ trans('admin.student_management.status') }} *</label>
                <select name="status" class="form-select" required>
                    <option value="active" @selected(old('status', $group->status ?? 'active') === 'active')>{{ trans('admin.student_management.active') }}</option>
                    <option value="inactive" @selected(old('status', $group->status ?? 'active') === 'inactive')>{{ trans('admin.student_management.inactive') }}</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label d-block">{{ trans('admin.student_management.days') }}</label>
                @foreach($days as $day)
                    <label class="me-3">
                        <input type="checkbox" name="days[]" value="{{ $day }}" @checked(in_array($day, $selectedDays ?? []))>
                        {{ trans('admin.training.' . $day) }}
                    </label>
                @endforeach
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.students') }}</label>
                <select name="student_ids[]" class="form-select" multiple size="8">
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" @selected(in_array($student->id, $selectedStudents))>{{ $student->name }} - {{ $student->phone }}</option>
                    @endforeach
                </select>
                <small class="text-muted">{{ trans('admin.student_management.hold_to_select') }}</small>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">{{ trans('admin.student_management.notes') }}</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $group->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-success">{{ trans('admin.student_management.save') }}</button>
        <a href="{{ route('academy.groups.index') }}" class="btn btn-light">{{ trans('admin.student_management.cancel') }}</a>
    </div>
</div>
