@csrf
@php
    $selectedStudents = old('student_ids', isset($group) ? $group->students()->pluck('academy_students.id')->toArray() : []);
    $selectedDays = old('days', $group->days ?? []);
@endphp
<div class="card">
    <div class="card-header">
        <h3 class="mb-0">{{ isset($group) ? 'Edit Group' : 'Add Group' }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Group Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $group->name ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Linked Training</label>
                <select name="training_id" class="form-select">
                    <option value="">No training</option>
                    @foreach($trainings as $training)
                        <option value="{{ $training->id }}" @selected(old('training_id', $group->training_id ?? '') == $training->id)>{{ $training->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Coach</label>
                <select name="coach_id" class="form-select">
                    <option value="">No coach</option>
                    @foreach($coaches as $coach)
                        <option value="{{ $coach->id }}" @selected(old('coach_id', $group->coach_id ?? '') == $coach->id)>{{ $coach->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Sport</label>
                <select name="sport_id" class="form-select">
                    <option value="">No sport</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}" @selected(old('sport_id', $group->sport_id ?? '') == $sport->id)>{{ $sport->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Start Time</label>
                <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $group->start_time ?? '') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">End Time</label>
                <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $group->end_time ?? '') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Capacity</label>
                <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $group->capacity ?? '') }}" min="1">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Status *</label>
                <select name="status" class="form-select" required>
                    <option value="active" @selected(old('status', $group->status ?? 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $group->status ?? 'active') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label d-block">Days</label>
                @foreach($days as $day)
                    <label class="me-3">
                        <input type="checkbox" name="days[]" value="{{ $day }}" @checked(in_array($day, $selectedDays ?? []))>
                        {{ ucfirst($day) }}
                    </label>
                @endforeach
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Students</label>
                <select name="student_ids[]" class="form-select" multiple size="8">
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" @selected(in_array($student->id, $selectedStudents))>{{ $student->name }} - {{ $student->phone }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Hold Ctrl/Cmd to select multiple students.</small>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $group->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-success">Save</button>
        <a href="{{ route('academy.groups.index') }}" class="btn btn-light">Cancel</a>
    </div>
</div>
