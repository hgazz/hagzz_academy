@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js">
    </script>
    <script src=https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.js">
    </script>
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/ui-lightness/jquery-ui.css"
          rel="stylesheet" type="text/css" />
    <script src="colorpicker-master/jquery.colorpicker.js">
    </script>
    <link href="colorpicker-master/jquery.colorpicker.css"
          rel="stylesheet" type="text/css" />
@endpush

@csrf
<div class="row">
    @foreach (\App\Services\TranslatableService::getTranslatableInputs(App\Models\Training::class) as $name => $data)
            @if(!$data['is_textarea'])
                <div class="col-md-6 mb-3">
                    <label for="{{$name}}" class="form-label">{{trans('admin.training.'.$name)}} <span class="text-danger">*</span></label>
                    <input type="text" id="{{$name}}" name="{{$name}}" maxlength="50" class="form-control"
                           @php
                               $language = $name == 'name_en' ? 'en' : 'ar';
                               $defaultValue = isset($training) ? $training->getTranslation('name', $language) : '';
                           @endphp
                           value="{{ old($name, $defaultValue) }}"
                           placeholder="{{$name}}" data-parsley-required-message="Please enter {{$name}}">
                    @error($name)
                    <span class="text-danger">*{{$message}}</span>
                    @enderror
                </div>
            @else
            <div class="col-md-6 mb-3">
                <label for="{{$name}}">
                    <span class="text-danger">*</span>
                    {{ $name === 'description_en' ? trans('admin.training.description_en') : trans('admin.training.description_ar') }}
                </label>

                <textarea class="form-control" name="{{$name}}" id="{{$name}}" placeholder="Enter">@if($name == 'description_en'){{old($name , isset($training) ? $training->getTranslation('description','en') : '')}}@else{{old($name , isset($training) ? $training->getTranslation('description','ar') : '')}}@endif</textarea>
                @error($name)
                    <span class="text-danger">*{{$message}}</span>
                @enderror
            </div>
        @endif
        @endforeach
    <div class="col-md-6 mb-3">
        <label for="start_date">{{ trans('admin.training.classes_start_time') }}  <span class="text-danger">*</span></label>
        <input class="form-control" type="time" value="{{ old('start_time', (isset($training) ? $training->start_time : ''))}}" id="start_time" name="start_time">
        @error('start_time')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="end_date">{{ trans('admin.training.classes_end_time') }}    <span class="text-danger">*</span></label>
        <input class="form-control" type="time" value="{{ old('end_time', (isset($training) ? $training->end_time : ''))}}" id="end_time" name="end_time">
        @error('end_time')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="sport_id">{{ trans('admin.clasess.sport') }}    <span class="text-danger">*</span></label>
        <select class="form-select" name="sport_id" id="sport_id">
            <option value="">{{ trans('admin.clasess.select_sport') }}</option>
            @foreach($sports as $sport)
                <option value="{{$sport->id}}" @selected(old('sport_id',  (isset($training) ? $training->sport_id : '')) == $sport->id)>{{$sport->name}}</option>
            @endforeach
        </select>
        @error('sport_id')
        <span class="text-danger" >{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="coaches"><span class="text-danger">*</span> {{trans('admin.training.coach')}} </label>
        <select id="coaches" class="form-select" name="coach_id">
            <option> {{trans('admin.training.Choose Coach')}} </option>
        </select>
        @error('coach_id')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="max_players">{{ trans('admin.training.max_players') }}    <span class="text-danger">*</span></label>
        <input class="form-control" type="number" value="{{ old('max_players', (isset($training) ? $training->max_players : ''))}}" id="max_players" name="max_players">
        @error('max_players')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="address_id"><span class="text-danger">*</span> {{trans('admin.training.address')}} </label>
        <select id="address_id" class="form-select" name="address_id">
            <option> {{trans('admin.training.select_address')}} </option>
            @foreach($addresses as $address)
                <option  @selected(old('address_id', isset($training) ?  $training->address_id : '') == $address->id) value="{{$address->id}}">{{$address->address}}</option>
            @endforeach
        </select>
        @error('address_id')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="price">{{ trans('admin.training.price') }}   <span class="text-danger">*</span></label>
        <input class="form-control" type="number" value="{{ old('price', (isset($training) ? $training->price : ''))}}" id="price" name="price">
        @error('price')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="discount_price">{{ trans('admin.training.discount') }}</label>
        <input class="form-control" type="number" value="{{ old('discount_price', (isset($training) ? $training->discount_price : ''))}}" id="discount_price" name="discount_price">
        @error('discount_price')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="level"><span class="text-danger">*</span> {{trans('admin.training.levels')}} </label>
        <select id="level" class="form-select" name="level">
            <option> {{trans('admin.training.select_level')}} </option>
            <option  @selected(old('level', isset($training) ?  $training->getRawOriginal('level') : '') == 'Beginner') value="Beginner">{{trans('admin.training.beginner')}}</option>
            <option  @selected(old('level', isset($training) ?  $training->getRawOriginal('level') : '') == 'Intermediate') value="Intermediate">{{trans('admin.training.intermediate')}}</option>
            <option  @selected(old('level', isset($training) ?  $training->getRawOriginal('level') : '') == 'Advanced') value="Advanced">{{trans('admin.training.advanced')}}</option>
            <option  @selected(old('level', isset($training) ?  $training->getRawOriginal('level') : '') == 'Any_Level') value="Any_Level">{{trans('admin.training.Any_Level')}}</option>

        </select>
        @error('level')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="gender"><span class="text-danger">*</span> {{trans('admin.training.gender')}} </label>
        <select id="gender" class="form-select" name="gender">
            <option> {{trans('admin.training.select_gender')}} </option>
            <option  @selected(old('gender', isset($training) ?  $training->getRawOriginal('gender') : '') == 'All') value="All">{{trans('admin.training.all')}}</option>
            <option  @selected(old('gender', isset($training) ?  $training->getRawOriginal('gender') : '') == 'Men') value="Men">{{trans('admin.coaches.male')}}</option>
            <option  @selected(old('gender', isset($training) ?  $training->getRawOriginal('gender') : '') == 'Women') value="Women">{{trans('admin.coaches.female')}}</option>

        </select>
        @error('gender')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="age_group"><span class="text-danger">*</span> {{trans('admin.training.age_group')}} </label>
        <select id="age_group" class="form-select" name="age_group">
            <option> {{trans('admin.training.select_gender')}} </option>
            <option  @selected(old('age_group', isset($training) ?  $training->getRawOriginal('age_group') : '') == 'All') value="All">{{trans('admin.training.all')}}</option>
            <option  @selected(old('age_group', isset($training) ?  $training->getRawOriginal('age_group') : '') == 'Kids') value="Kids">{{trans('admin.training.kids')}}</option>
            <option  @selected(old('age_group', isset($training) ?  $training->getRawOriginal('age_group') : '') == 'Juniors') value="Juniors">{{trans('admin.training.juniors')}}</option>
            <option  @selected(old('age_group', isset($training) ?  $training->getRawOriginal('age_group') : '') == 'Adults') value="Adults">{{trans('admin.training.adults')}}</option>

        </select>
        @error('age_group')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
        <div class="col-md-6 mb-3">
            <label for="color">{{ trans('admin.training.color') }}</label>
            <input class="form-control" type="color" value="{{ old('color', (isset($training) ? $training->color : '#fff'))}}" id="color" name="color">
            @error('color')
            <span class="text-danger">*{{$message}}</span>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="classes_number">{{ trans('admin.training.classes_number') }}</label>
            <input class="form-control" type="number" value="{{ old('classes_number', (isset($training) ? $training->classes_number : ''))}}" id="classes_number" name="classes_number" min="1">
            @error('classes_number')
            <span class="text-danger">*{{$message}}</span>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="classes_days"><span class="text-danger">*</span> {{trans('admin.training.classes_days')}} </label>
            <select id="classes_days" class="js-example-basic-multiple js-example-responsive form-select" name="classes_days[]" multiple data-selectable="" style="width: 100%">
                <option  @selected(old('classes_days[]', isset($training) && $training->getRawOriginal('classes_days') != null ?  in_array('saturday',json_decode($training->getRawOriginal('classes_days'))) : '') == 'saturday') value="saturday">{{trans('admin.training.saturday')}}</option>
                <option  @selected(old('classes_days[]', isset($training) && $training->getRawOriginal('classes_days') != null ?  in_array('sunday',json_decode($training->getRawOriginal('classes_days'))) : '') == 'sunday') value="sunday">{{trans('admin.training.sunday')}}</option>
                <option  @selected(old('classes_days[]', isset($training) && $training->getRawOriginal('classes_days') != null ?  in_array('monday',json_decode($training->getRawOriginal('classes_days'))) : '') == 'monday') value="monday">{{trans('admin.training.monday')}}</option>
                <option  @selected(old('classes_days[]', isset($training) && $training->getRawOriginal('classes_days') != null ?  in_array('tuesday',json_decode($training->getRawOriginal('classes_days'))) : '') == 'tuesday') value="tuesday">{{trans('admin.training.tuesday')}}</option>
                <option  @selected(old('classes_days[]', isset($training) && $training->getRawOriginal('classes_days') != null ?  in_array('wednesday',json_decode($training->getRawOriginal('classes_days'))) : '') == 'wednesday') value="wednesday">{{trans('admin.training.wednesday')}}</option>
                <option  @selected(old('classes_days[]', isset($training) && $training->getRawOriginal('classes_days') != null ?  in_array('thursday',json_decode($training->getRawOriginal('classes_days'))) : '') == 'thursday') value="thursday">{{trans('admin.training.thursday')}}</option>
                <option  @selected(old('classes_days[]', isset($training) && $training->getRawOriginal('classes_days') != null ?  in_array('friday',json_decode($training->getRawOriginal('classes_days'))) : '') == 'friday') value="friday">{{trans('admin.training.friday')}}</option>
            </select>
            @error('classes_days')
            <span class="text-danger">*{{$message}}</span>
            @enderror
        </div>
</div>
<script>
    let sports  = document.getElementById('sport_id');
    let coachesSelect = document.getElementById('coaches');
    const lang = "{{app()->getLocale()}}";

    document.addEventListener('DOMContentLoaded', function(){
        let selectedValue = sports.value;
        if (selectedValue !== ''){
            fetch(`/partner/training/getCoachesBySports/${selectedValue}`)
                .then(response => response.json())
                .then(data =>{
                    coachesSelect.innerHTML = '';
                    data.coaches.forEach(coach=>{
                        coachesSelect.innerHTML += `<option value="${coach.id}">${coach.name[lang]}</option>`;
                    })
                })

        }
    })

    sports.addEventListener('change', function() {
        let selectedValue = sports.value;
        if (selectedValue !== ''){
            fetch(`/partner/training/getCoachesBySports/${selectedValue}`)
                .then(response => response.json())
                .then(data =>{
                    coachesSelect.innerHTML = '';
                    data.coaches.forEach(coach=>{
                        coachesSelect.innerHTML += `<option value="${coach.id}">${coach.name[lang]}</option>`;
                    })
                })

        }
    });
</script>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2({
            placeholder: "{{trans('admin.training.select_days')}}",
            width: 'resolve'
        });
    });
</script>
@endpush
