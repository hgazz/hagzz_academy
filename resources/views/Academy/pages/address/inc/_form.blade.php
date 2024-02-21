@csrf
<div class="row">
    <div class="row">
        @foreach (\App\Services\TranslatableService::getTranslatableInputs(App\Models\Address::class) as $name => $data)
            <div class="col-md-6 mb-3">
                <label for="{{$name}}" class="form-label">{{$name}}</label>
                <input type="text" id="{{$name}}" name="{{$name}}" maxlength="50" class="form-control"
                       value="@if ($name == 'address_en') {{old($name, $address?->getTranslation('address','en')  ?? '')}} @else {{old($name, $address?->getTranslation('address','ar')  ?? '')}} @endif"
                       placeholder="Enter {{$name}}" data-parsley-required-message="Please enter {{$name}}">
                @error($name)
                <span class="text-danger">*{{$message}}</span>
                @enderror
            </div>
        @endforeach

    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="longitude">{{ trans('admin.address.longitude') }}</label>
            <input class="form-control" type="text" value="{{(isset($address) ? $address->longitude : old('longitude'))}}" id="longitude" name="longitude">
            @error('longitude')
            <span class="text-danger">*{{$message}}</span>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="latitude">{{ trans('admin.address.latitude') }}</label>
            <input class="form-control" type="text" value="{{(isset($address) ? $address->latitude : old('latitude'))}}" id="latitude" name="latitude">
            @error('latitude')
            <span class="text-danger">*{{$message}}</span>
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="form-group mb-4">
            <label for="exampleFormControlSelect1">{{ trans('admin.address.city') }}</label>
            <select class="form-select" id="city"  name="city_id" >
                <option value="">{{ trans('admin.area.select_city') }}</option>
                @foreach($cities as $city)
{{--                    <input type="hidden" value="{{$city->id}}" id="city_id">--}}
                    <option  value="{{ $city->id }}"  @selected(old('city_id', isset($address) ? $address->city_id : '') == $city->id)>{{ $city->name }}</option>

                @endforeach
            </select>
            @error('city_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="form-group mb-4">
            <label for="areaSelect">{{ trans('admin.address.area') }}</label>
            <select class="form-select" id="areaSelect" name="area_id">
                <option value="">{{ trans('admin.area.select_city') }}</option>
{{--                @foreach($areas as $area)--}}
{{--                    <option value="{{ $area->id }}" @selected(old('city_id', isset($address) ? $address->area_id : '') == $area->id)>{{ $area->name }}</option>--}}
{{--                @endforeach--}}
            </select>
            @error('area_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

    </div>

</div>
<label for="active">{{ trans('admin.address.active') }}</label>
<input class="form-check" id="active" name="active" @if(isset($address) ?? $address->active) checked @endif  type="checkbox">
@error('active')
<span class="text-danger">{{ $message }}</span>
@enderror

<input type="hidden" value="{{app()->getLocale()}}" id="local">
<script>
   var city = document.getElementById('city');
   var areaSelect = document.getElementById('areaSelect');
   var local = document.getElementById('local');
  city.addEventListener('change',function (){
        var cityId = city.value;
        fetch(`area/${cityId}`)
        .then(response => response.json())
            .then(data =>{
                areaSelect.innerHTML = '<option value="" disabled selected>Select Area</option>';

                // Populate the area select with fetched areas
                data.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area.id;
                    option.textContent = (local.value == 'en')  ? `${area.name.en}` : `${area.name.ar}`;
                    areaSelect.appendChild(option);
                });
                // Enable the area select
                areaSelect.disabled = false;
            })
  })
</script>
