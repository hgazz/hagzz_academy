@csrf
<div class="row">
    <div class="row">
        @foreach (\App\Services\TranslatableService::getTranslatableInputs(App\Models\TClass::class) as $name => $data)
            <div class="col-md-6 mb-3">
                <label for="{{$name}}" class="form-label">{{$name}}</label>
                <input type="text" id="{{$name}}" name="{{$name}}" maxlength="50" class="form-control"
                       value="@if ($name == 'title_en') {{old($name, isset($class) ? $class->getTranslation('title','en')  : '')}} @elseif($name == 'subtitle_en') {{old($name, isset($class) ? $class->getTranslation('subtitle','en')  : '')}} @elseif($name == 'subtitle_ar') {{old($name, isset($class) ? $class->getTranslation('subtitle','ar')  : '')}}  @else {{old($name, isset($class) ? $class->getTranslation('title','ar')  : '')}} @endif"
                       placeholder="Enter {{$name}}" data-parsley-required-message="Please enter {{$name}}">
                @error($name)
                <span class="text-danger">*{{$message}}</span>
                @enderror
            </div>
        @endforeach

    </div>
    <div class="col-md-6 mb-3">
        <label for="start_time">{{ trans('admin.training.start_time') }}</label>
        <input class="form-control" type="time" value="{{(isset($class) ? $class->start_time : old('start_time'))}}" id="start_time" name="start_time">
        @error('start_time')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="end_time">{{ trans('admin.training.end_time') }}</label>
        <input class="form-control" type="time" value="{{(isset($class) ? $class->end_time : old('end_time'))}}" id="end_time" name="end_time">
        @error('end_time')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="date">{{ trans('admin.clasess.date') }}</label>
            <input class="form-control" type="date" value="{{(isset($class) ? $class->date : old('date'))}}" id="date" name="date">
            @error('date')
            <span class="text-danger">*{{$message}}</span>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="sport_id">{{ trans('admin.clasess.sport') }}</label>
            <select class="form-select" name="sport_id" id="sport_id">
                <option value="">{{ trans('admin.clasess.select_sport') }}</option>
                @foreach($sports as $sport)
                    <option value="{{$sport->id}}" @selected(old('sport_id',  (isset($class) ? $class->sport_id : '')))>{{$sport->name}}</option>
                @endforeach
            </select>
            @error('academy_id')
            <span class="text-danger" >{{$message}}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="classes"><span class="text-danger">*</span> {{trans('admin.training.training')}} </label>
        <select id="classes" class="form-select pt-2" name="training_id" >
            <option value=""> {{trans('admin.training.training')}} </option>
            @foreach($trainings as $training)
                <option value="{{$training->id}}" @selected(old('training_id',  (isset($class) ? $class->training_id : ''))) >{{$training->name}}</option>
            @endforeach
        </select>
        @error('training_id')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="out_comes">{{ trans('admin.clasess.out_comes') }}</label>
        <textarea class="form-control" type="out_comes"  id="out_comes" name="out_comes">
            {!! (isset($class) ? $class->out_comes : old('out_comes'))!!}
        </textarea>
        @error('out_comes')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="bring_with_me">{{ trans('admin.clasess.bring_with_me') }}</label>
        <textarea class="form-control" type="bring_with_me"  id="bring_with_me" name="bring_with_me">
            {!! (isset($class) ? $class->bring_with_me : old('bring_with_me'))!!}
        </textarea>
        @error('bring_with_me')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
</div>


