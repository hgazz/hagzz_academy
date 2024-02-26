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
        <label for="image">{{ trans('admin.coaches.image') }}</label>
        <input class="form-control" type="file"  id="image" name="image">
        @error('image')
        <span class="text-danger">*{{$message}}</span>
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

