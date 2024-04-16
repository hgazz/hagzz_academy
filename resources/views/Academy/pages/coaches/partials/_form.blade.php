@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@csrf
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name">{{ trans('admin.coaches.name') }}</label>
        <input class="form-control" type="text" value="{{(isset($coach) ? $coach->name : old('name'))}}" id="name" name="name">
        @error('name')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="description">{{ trans('admin.coaches.description') }}</label>
        <input class="form-control" type="text" value="{{(isset($coach) ? $coach->description : old('description'))}}" id="description" name="description">
        @error('description')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="license">{{ trans('admin.coaches.license') }}</label>
        <input class="form-control" type="text" value="{{(isset($coach) ? $coach->license : old('license'))}}" id="license" name="license">
        @error('license')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="license_type">{{ trans('admin.coaches.license_type') }}</label>
        <input class="form-control" type="text" value="{{(isset($coach) ? $coach->license_type : old('license_type'))}}" id="license_type" name="license_type">
        @error('license_type')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="phone">{{ trans('admin.coaches.phone') }}</label>
        <input class="form-control" type="text" value="{{(isset($coach) ? $coach->phone : old('phone'))}}" id="phone" name="phone">
        @error('phone')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="image">{{ trans('admin.coaches.image') }}</label>
        <input class="form-control" type="file"  id="image" name="image">
        @error('image')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="sports">{{trans('admin.coaches.select_sport')}}</label>
        <select class="js-example-basic-multiple form-select" name="sport_id[]" multiple id="sports">
            @foreach($sports as $sport)
                <option
                    value="{{$sport->id}}" @selected(old('sport_id', isset($coach) ? in_array($sport->id, $coach->sports()->pluck('sport_id')->toArray()) : ''))>{{$sport->name}}</option>
            @endforeach
        </select>
        @error('sport_id')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="active">{{ trans('admin.address.active') }}</label>
        <input class="form-check" id="active" name="active" @checked(old('active', (isset($coach) ? $coach->getRawOriginal('active') : ''))) type="checkbox">
        @error('active')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                placeholder: "{{ trans('admin.coaches.select_sport') }}"
            });
        });
    </script>
@endpush
