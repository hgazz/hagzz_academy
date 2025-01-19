<div class="btn-sm btn-group" role="group" aria-label="Button group with nested dropdown">
    <div class="btn-group dropstart" role="group">
        <button id="btnGroupDrop1" type="button" class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            {{ trans('admin.actions') }}
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>
        <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
            <li class="mb-1">
                <a href="{{ route('academy.report.view-booking-details', $join) }}">{{ trans('admin.training.Show Details') }}</a>
            </li>
        </ul>
    </div>
</div>

