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
        <label for="date">{{ trans('admin.clasess.date') }}</label>
        <input class="form-control" type="date" value="{{(isset($class) ? $class->date : old('date'))}}" id="data" name="date">
        @error('date')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
</div>
<div class="col-md-6 mb-3">
    <select class="form-select" name="sport_id">
        <option value="">{{ trans('admin.clasess.select_sport') }}</option>
        @foreach($sports as $sport)
            <option value="{{$sport->id}}" @selected(old('sport_id',  (isset($class) ? $class->sport_id : '')))>{{$sport->name}}</option>
        @endforeach
    </select>
    @error('academy_id')
    <span class="text-danger" >{{$message}}</span>
    @enderror
</div>

