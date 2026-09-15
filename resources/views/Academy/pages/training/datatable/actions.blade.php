<div class="d-inline-flex align-items-center gap-1 flex-wrap">
    <a href="{{ route('academy.class.index') }}?training_id={{ $training->id }}" class="heroui-btn-action" title="{{ trans('admin.clasess.clasess') }}">
        <i class="fas fa-layer-group"></i> <span>{{ trans('admin.clasess.clasess') }}</span>
    </a>
    <a href="{{ route('academy.createBooking') }}?training_id={{ $training->id }}" class="heroui-btn-action" style="background: var(--heroui-success-light); border-color: var(--heroui-success-border); color: #059669;" title="{{ trans('admin.training.booking') }}">
        <i class="fas fa-user-plus"></i> <span>{{ trans('admin.training.booking') }}</span>
    </a>
    <a href="{{ route('academy.training.edit', $training) }}" class="heroui-btn-action" title="{{ trans('admin.edit') }}">
        <i class="fas fa-edit"></i> <span>{{ trans('admin.edit') }}</span>
    </a>
</div>
