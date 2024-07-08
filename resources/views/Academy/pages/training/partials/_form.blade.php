@csrf
<div class="row">
    @foreach (\App\Services\TranslatableService::getTranslatableInputs(App\Models\Training::class) as $name => $data)
            @if(!$data['is_textarea'])
                <div class="col-md-6 mb-3">
                    <label for="{{$name}}" class="form-label">{{trans('admin.training.'.$name)}}</label>
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
        <label for="start_date">{{ trans('admin.training.start_date') }}</label>
        <input class="form-control" type="date" value="{{ old('start_date', (isset($training) ? $training->start_date : ''))}}" id="start_date" name="start_date">
        @error('start_date')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="end_date">{{ trans('admin.training.end_date') }}</label>
        <input class="form-control" type="date" value="{{ old('end_date', (isset($training) ? $training->end_date : ''))}}" id="end_date" name="end_date">
        @error('end_date')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="sport_id">{{ trans('admin.clasess.sport') }}</label>
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
        <label for="max_players">{{ trans('admin.training.max_players') }}</label>
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
        <label for="price">{{ trans('admin.training.price') }}</label>
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
            <option  @selected(old('level', isset($training) ?  $training->level : '') == 'Beginner') value="Beginner">{{trans('admin.training.beginner')}}</option>
            <option  @selected(old('level', isset($training) ?  $training->level : '') == 'Intermediate') value="Intermediate">{{trans('admin.training.intermediate')}}</option>
            <option  @selected(old('level', isset($training) ?  $training->level : '') == 'Advanced') value="Advanced">{{trans('admin.training.advanced')}}</option>

        </select>
        @error('level')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="gender"><span class="text-danger">*</span> {{trans('admin.training.gender')}} </label>
        <select id="gender" class="form-select" name="gender">
            <option> {{trans('admin.training.select_gender')}} </option>
            <option  @selected(old('gender', isset($training) ?  $training->gender : '') == 'All') value="All">{{trans('admin.training.all')}}</option>
            <option  @selected(old('gender', isset($training) ?  $training->gender : '') == 'Men') value="Men">{{trans('admin.training.men')}}</option>
            <option  @selected(old('gender', isset($training) ?  $training->gender : '') == 'Women') value="Women">{{trans('admin.training.women')}}</option>

        </select>
        @error('gender')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="age_group"><span class="text-danger">*</span> {{trans('admin.training.age_group')}} </label>
        <select id="age_group" class="form-select" name="age_group">
            <option> {{trans('admin.training.select_gender')}} </option>
            <option  @selected(old('age_group', isset($training) ?  $training->age_group : '') == 'All') value="All">{{trans('admin.training.all')}}</option>
            <option  @selected(old('age_group', isset($training) ?  $training->age_group : '') == 'Kids') value="Kids">{{trans('admin.training.kids')}}</option>
            <option  @selected(old('age_group', isset($training) ?  $training->age_group : '') == 'Juniors') value="Juniors">{{trans('admin.training.juniors')}}</option>
            <option  @selected(old('age_group', isset($training) ?  $training->age_group : '') == 'Adults') value="Adults">{{trans('admin.training.adults')}}</option>

        </select>
        @error('age_group')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

</div>

<script>
        let sports  = document.getElementById('sport_id');
        let coachesSelect = document.getElementById('coaches');

        document.addEventListener('DOMContentLoaded', function(){
            let selectedValue = sports.value;
            if (selectedValue !== ''){
                fetch(`/partner/training/getCoachesBySports/${selectedValue}`)
                    .then(response => response.json())
                    .then(data =>{
                        coachesSelect.innerHTML = '';
                        data.coaches.forEach(coach=>{
                            coachesSelect.innerHTML += `<option value="${coach.id}">${coach.name}</option>`;
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
                            coachesSelect.innerHTML += `<option value="${coach.id}">${coach.name}</option>`;
                        })
                    })

            }
        });
    </script>

