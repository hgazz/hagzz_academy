<td>
    <div class="btn-group">
        <button type="button" class="btn btn-dark btn-sm">{{ trans('admin.open') }}</button>
        <button type="button" class="btn btn-dark btn-sm dropdown-toggle dropdown-toggle-split" id="dropdownMenuReference1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-reference="parent">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuReference1">
            <a class="dropdown-item" href="{{ route('academy.address.edit', $address) }}">{{ trans('admin.edit') }}</a>
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <a class="dropdown-item" href="javascript:void(0);" data-href="{{ route('academy.address.delete', $address) }}"  data-id="{{ $address->id }}" data-name="Address">{{ trans('admin.delete') }}</a>
        </div>
    </div>
</td>

