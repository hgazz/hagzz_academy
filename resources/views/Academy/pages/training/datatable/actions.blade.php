<td>
    <div class="btn-group  mb-2 me-4" role="group">
        <button id="btndefault" type="button" class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ trans('admin.actions') }} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
        <div class="dropdown-menu" aria-labelledby="btndefault">
            <a class="dropdown-item" href="{{ route('academy.training.edit', $training) }}">{{ trans('admin.edit') }}</a>
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <a class="dropdown-item" href="javascript:void(0);" data-href="{{ route('academy.training.delete', $training) }}"  data-id="{{ $training->id }}" data-name="Training">{{ trans('admin.delete') }}</a>
            <div class="dropdown-divider"></div>
            <form action="{{route('academy.training.updateActive',$training)}}" method="post" class="mx-3">
                @csrf @method('PUT')
                @if($training->active)
                    <button class="btn btn-sm btn-danger">{{trans('admin.training.deactivated')}}</button>
                @else
                    <button class="btn btn-sm btn-success">{{trans('admin.training.Active')}}</button>
                @endif
            </form>
        </div>
    </div>
</td>

