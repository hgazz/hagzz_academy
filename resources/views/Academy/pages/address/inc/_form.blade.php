@csrf
<div class="row">
    <div class="row">
        @foreach (\App\Services\TranslatableService::getTranslatableInputs(App\Models\Address::class) as $name => $data)
            <div class="col-md-6 mb-3">
                <label for="{{$name}}" class="form-label">{{trans('admin.address.'.$name)}}    <span class="text-danger">*</span></label>
                <input type="text" id="{{$name}}" name="{{$name}}" maxlength="50" class="form-control"
                       @php
                           $language = $name == 'address_en' ? 'en' : 'ar';
                           $defaultValue = isset($address) ? $address->getTranslation('address', $language) : '';
                       @endphp
                       value="{{ old($name, $defaultValue) }}"
                       placeholder="Enter {{$name}}" data-parsley-required-message="Please enter {{$name}}">
                @error($name)
                <span class="text-danger">*{{$message}}</span>
                @enderror
            </div>
        @endforeach

    </div>

    <div class="row">
        <div class="form-group mb-4">
            <label for="exampleFormControlSelect1">{{ trans('admin.address.country') }}   <span class="text-danger">*</span></label>
            <select class="form-select" id="country"  name="country_id" >
                <option value="0">{{ trans('admin.address.country') }}</option>
                @foreach($countries as $country)
                    <option  value="{{ $country->id }}"  @selected(old('country_id', isset($address) ? $address->country_id : '') == $country->id)>{{ $country->name }}</option>
                @endforeach
            </select>
            @error('country_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="form-group mb-4">
            <label for="exampleFormControlSelect1">{{ trans('admin.address.city') }}   <span class="text-danger">*</span></label>
            <select class="form-select citySelected" id="city"  name="city_id" >
                <input type="hidden" value="{{old('city_id', isset($address) ? $address->city_id : '')}}" id="select_city_id">
{{--                <option disabled>{{ trans('admin.area.select_city') }}</option>--}}
{{--                @foreach($cities as $city)--}}
{{--                    <input type="hidden" value="{{$city->id}}" id="city_id">--}}
{{--                    <option  value="{{ $city->id }}"  @selected(old('city_id', isset($address) ? $address->city_id : '') == $city->id)>{{ $city->name }}</option>--}}

{{--                @endforeach--}}
            </select>
            @error('city_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="form-group mb-4">
            <label for="areaSelect">{{ trans('admin.address.area') }}   <span class="text-danger">*</span></label>
            <select class="form-select" id="areaSelect" name="area_id">
                <input type="hidden" value="{{old('city_id', isset($address) ? $address->area_id : '')}}" id="select_area_id">
{{--                @foreach($areas as $area)--}}
{{--                    <option value="{{ $area->id }}" @selected(old('city_id', isset($address) ? $address->area_id : '') == $area->id)>{{ $area->name }}</option>--}}
{{--                @endforeach--}}
            </select>
            @error('area_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

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
</div>
<div class="row">
    <div class="col-md-6">
        <label for="active">{{ trans('admin.address.active') }}</label>
        <input class="form-check" id="active" name="active" @if(isset($address) && $address->active) checked @endif  type="checkbox">
        @error('active')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="location_owned">{{ trans('admin.location_owned') }}   <span class="text-danger">*</span></label>
        <input class="form-check" id="location_owned" name="location_owned" @checked(old('location_owned', isset($address) ? $address->location_owned : ''))  value="1" type="checkbox">
        @error('location_owned')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>




<input type="hidden" value="{{app()->getLocale()}}" id="local">

