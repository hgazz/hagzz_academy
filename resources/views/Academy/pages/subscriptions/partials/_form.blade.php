@csrf
<div class="card">
    <div class="card-header"><h3 class="mb-0">{{ isset($subscription) ? trans('admin.student_management.edit_subscription') : trans('admin.student_management.add_subscription') }}</h3></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.student') }} *</label>
                <select name="academy_student_id" class="form-select" required>
                    <option value="">{{ trans('admin.student_management.select_student') }}</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" @selected(old('academy_student_id', $subscription->academy_student_id ?? '') == $student->id)>{{ $student->name }} - {{ $student->phone }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ trans('admin.student_management.group') }}</label>
                <select name="academy_group_id" class="form-select">
                    <option value="">{{ trans('admin.student_management.no_group') }}</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" @selected(old('academy_group_id', $subscription->academy_group_id ?? '') == $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">{{ trans('admin.student_management.starts_on') }} *</label>
                <input type="date" name="starts_on" class="form-control" value="{{ old('starts_on', isset($subscription) ? $subscription->starts_on->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">{{ trans('admin.student_management.ends_on') }} *</label>
                <input type="date" name="ends_on" class="form-control" value="{{ old('ends_on', isset($subscription) ? $subscription->ends_on->format('Y-m-d') : now()->addMonth()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">{{ trans('admin.student_management.amount') }} *</label>
                <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $subscription->amount ?? 0) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">{{ trans('admin.student_management.status') }} *</label>
                <select name="status" class="form-select" required>
                    @foreach(['pending', 'active', 'expired', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $subscription->status ?? 'active') === $status)>{{ trans('admin.student_management.' . $status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ trans('admin.student_management.payment_status') }} *</label>
                <select name="payment_status" class="form-select" required>
                    @foreach(['unpaid', 'partial', 'paid'] as $status)
                        <option value="{{ $status }}" @selected(old('payment_status', $subscription->payment_status ?? 'unpaid') === $status)>{{ trans('admin.student_management.' . $status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8 mb-3">
                <label class="form-label">{{ trans('admin.student_management.notes') }}</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $subscription->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-success">{{ trans('admin.student_management.save') }}</button>
        <a href="{{ route('academy.subscriptions.index') }}" class="btn btn-light">{{ trans('admin.student_management.cancel') }}</a>
    </div>
</div>
