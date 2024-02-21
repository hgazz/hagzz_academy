@csrf
<div class="row">
    <div class="row">
        @foreach (\App\Services\TranslatableService::getTranslatableInputs(App\Models\TClass::class) as $name => $data)
            <div class="col-md-6 mb-3">
                <label for="{{$name}}" class="form-label">{{$name}}</label>
                <input type="text" id="{{$name}}" name="{{$name}}" maxlength="50" class="form-control"
                       value="@if ($name == 'title_en') {{old($name, $class?->getTranslation('title','en')  ?? '')}} @elseif($name == 'subtitle_en') {{old($name, $class?->getTranslation('subtitle','en')  ?? '')}} @elseif($name == 'subtitle_ar') {{old($name, $class?->getTranslation('subtitle','ar')  ?? '')}}  @else {{old($name, $class?->getTranslation('title','ar')  ?? '')}} @endif"
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

