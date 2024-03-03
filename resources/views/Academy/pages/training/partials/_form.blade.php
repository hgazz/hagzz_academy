@csrf
<div class="row">
    <div class="row">
        @foreach (\App\Services\TranslatableService::getTranslatableInputs(App\Models\Training::class) as $name => $data)
            @if(!$data['is_textarea'])
                <div class="col-md-6 mb-3">
                    <label for="{{$name}}" class="form-label">{{$name}}</label>
                    <input type="text" id="{{$name}}" name="{{$name}}" maxlength="50" class="form-control"
                           value="@if ($name == 'name_en') {{old($name, isset($training) ? $training->getTranslation('name','en')  : '')}}  @else {{old($name, isset($training) ? $training->getTranslation('name','ar')  : '')}} @endif"
                           placeholder="Enter {{$name}}" data-parsley-required-message="Please enter {{$name}}">
                    @error($name)
                    <span class="text-danger">*{{$message}}</span>
                    @enderror
                </div>
            @else
            <div class="col-md-6 mb-3">
                <label for="{{$name}}">
                    <span class="text-danger">*</span>
                    {{$name}}
                </label>

                <textarea class="form-control" name="{{$name}}" id="{{$name}}" placeholder="Enter {{$name}}">
                    @if($name == 'description_en') {{old($name , isset($training) ? $training->getTranslation('description','en') : '')}} @else {{old($name , isset($training) ? $training->getTranslation('description','ar') : '')}} @endif
                </textarea>
                @error($name)
                <span class="text-danger">*{{$message}}</span>
                @enderror
            </div>
        @endif

        @endforeach

    </div>
    <div class="col-md-6 mb-3">
        <label for="start_date">{{ trans('admin.training.start_date') }}</label>
        <input class="form-control" type="date" value="{{(isset($training) ? $training->start_date : old('start_date'))}}" id="start_date" name="start_date">
        @error('start_date')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="end_date">{{ trans('admin.training.end_date') }}</label>
        <input class="form-control" type="date" value="{{(isset($training) ? $training->end_date : old('end_date'))}}" id="end_date" name="end_date">
        @error('end_date')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="start_time">{{ trans('admin.training.start_time') }}</label>
        <input class="form-control" type="time" value="{{(isset($training) ? $training->start_time : old('start_time'))}}" id="start_time" name="start_time">
        @error('start_time')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="end_time">{{ trans('admin.training.end_time') }}</label>
        <input class="form-control" type="time" value="{{(isset($training) ? $training->end_time : old('end_time'))}}" id="end_time" name="end_time">
        @error('end_time')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="coaches"><span class="text-danger">*</span> {{trans('admin.training.coach')}} </label>
        <select id="coaches" class="form-select" name="coach_id">
            <option> {{trans('admin.training.Choose Coach')}} </option>
            @foreach($coaches as $coach)
                <option  @selected(old('coach_id', isset($training) ?  $training->coach_id : '') == $coach->id) value="{{$coach->id}}">{{$coach->name}}</option>
            @endforeach
        </select>
        @error('coach_id')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="price">{{ trans('admin.training.price') }}</label>
        <input class="form-control" type="number" value="{{(isset($training) ? $training->price : old('price'))}}" id="price" name="price">
        @error('price')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="image"><span class="text-danger">*</span> {{trans('admin.training.image')}} </label>
        <input type="file" class="form-control" id="image" name="image">
        @error('image')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
</div>

