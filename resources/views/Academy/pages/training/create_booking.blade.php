@extends('Academy.Layouts.master')

@section('title', trans('admin.training.training'))


@section('content')
    <div class="middle-content container-xxl p-0">

        <!--  BEGIN BREADCRUMBS  -->
        <div class="secondary-nav">
            <div class="breadcrumbs-container" data-page-heading="Analytics">
                <header class="header navbar navbar-expand-sm">
                    <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </a>
                    <div class="d-flex breadcrumb-content">
                        <div class="page-header">

                            <div class="page-title">
                            </div>

                            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('academy.training.index') }}">{{ trans('admin.training.training') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.training.training') }}</li>
                                </ol>
                            </nav>

                        </div>
                    </div>
                </header>
            </div>
        </div>
        <!--  END BREADCRUMBS  -->

        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="margin-bottom:24px;">
                <form method="POST" action="{{route('academy.storeBooking')}}">
                    @csrf
{{--                    <input type="hidden" name="training_id" value="{{ $training->id }}">--}}
                    <div class="card">
                        <div class="card-header">
                            <h3>{{ trans('admin.training.create_booking') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="training_id">{{trans('admin.training.training_name')}} <code>*</code></label>
                                    <select id="training_id" name="training_id" class="form-control">
                                        <option value="">{{ trans('admin.academies.select_training') }}</option>
                                        @foreach($data as $training)
                                            <option value="{{ $training->id }}" @selected(old('training_id') == $training->id)>{{ $training->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('training_id')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="price">{{trans('admin.training.price')}} <code>*</code></label>
                                    <input type="text" name="price" class="form-control"
                                           value="{{ old('price') }}" id="price"
                                           placeholder="{{trans('admin.training.price')}}" min="1">
                                    @error('price')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{trans('admin.user.name')}} <code>*</code></label>
                                    <input type="text" name="name" class="form-control"
                                           value="{{ old('name') }}" id="phone"
                                           placeholder="{{trans('admin.user.name')}}">
                                    @error('name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender">{{trans('admin.user.gender')}} <code>*</code></label>
                                    <select id="gender" name="gender" class="form-control">
                                        <option value="">{{ trans('admin.user.select_gender') }}</option>
                                        <option value="male" @selected(old('gender') == 'male' )>{{ trans('admin.user.male') }}</option>
                                        <option value="female" @selected(old('gender') == 'female' )>{{ trans('admin.user.female') }}</option>
                                    </select>
                                    @error('gender')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="country_code">{{trans('admin.training.country_code')}} <code>*</code></label>
                                    <input type="text" name="country_code" class="form-control"
                                           value="{{ old('country_code') }}" id="country_code"
                                           placeholder="{{trans('admin.training.country_code')}}">
                                    @error('country_code')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone">{{trans('admin.user.phone')}} <code>*</code></label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ old('phone') }}" id="phone"
                                           placeholder="{{trans('admin.academies.phone')}}">
                                    @error('phone')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="country">{{ trans('admin.training.country') }} <code>*</code></label>
                                    <select class="form-select" id="country" name="country_id">
                                        <option value="">{{ trans('admin.training.Select County') }}</option>
                                        @foreach($countries as $country)
                                            <option
                                                value="{{ $country->id }}" @selected(old('country_id'))>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="city">{{ trans('admin.city.city') }} <code>*</code></label>
                                    <select class="form-select citySelected" id="city" name="city_id">
                                        @if(old('city_id'))
                                            <option value="{{ old('city_id') }}" selected>
                                                {{ old('city_name') }} <!-- Provide city name dynamically from controller if needed -->
                                            </option>
                                        @endif
                                    </select>
                                    @error('city_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="area">{{ trans('admin.area.area') }} <code>*</code></label>
                                    <select class="form-select" id="area" name="area_id">
                                        @if(old('area_id'))
                                            <option value="{{ old('area_id') }}" selected>
                                                {{ old('area_name') }} <!-- Provide city name dynamically from controller if needed -->
                                            </option>
                                        @endif
                                    </select>
                                    @error('area_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone">{{trans('admin.user.birth_date')}} <code>*</code></label>
                                    <input type="date" name="birth_date" class="form-control"
                                           value="{{ old('birth_date') }}"
                                           id="phone"
                                           max="{{ date('Y-m-d', strtotime('-2 years')) }}"
                                           placeholder="{{trans('admin.academies.birth_date')}}">
                                    @error('birth_date')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email">{{trans('admin.academies.email')}} <code>*</code></label>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email') }}"
                                           id="email"
                                           placeholder="{{trans('admin.academies.email')}}">
                                    @error('email')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="user_type">{{trans('admin.academies.user_type')}} <code>*</code></label>
                                    <select class="form-select" id="user_type" name="child_type" required>
                                        <option value="">{{ trans('admin.academies.select_user_type') }}</option>
                                        <option value="parent" @selected(old('child_type') == 'parent')>{{ trans('admin.academies.parent') }}</option>
                                        <option value="child" @selected(old('child_type') == 'child')>{{ trans('admin.academies.child') }}</option>
                                        <option value="athlete" @selected(old('child_type') == 'athlete')>{{ trans('admin.academies.athlete') }}</option>
                                    </select>
                                    @error('child_type')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="school_name">{{trans('admin.academies.school_name')}} <code>*</code></label>
                                    <input type="text" name="school_name" class="form-control"
                                           value="{{ old('school_name') }}"
                                           id="school_name"
                                           placeholder="{{trans('admin.academies.school_name')}}">
                                    @error('school_name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="coach_preference">{{trans('admin.academies.coach_preference')}} ({{trans('admin.academies.coach_gender')}})<code>*</code></label>
                                    <select class="form-select" id="coach_preference" name="coach_preference" required>
                                        <option value="">{{ trans('admin.academies.coach_preference') }}</option>
                                        <option value="male" @selected(old('coach_preference') == 'male')>{{ trans('admin.user.male') }}</option>
                                        <option value="female" @selected(old('coach_preference') == 'female')>{{ trans('admin.user.female') }}</option>
                                        <option value="not_important" @selected(old('coach_preference') == 'not_important')>{{ trans('admin.academies.not_important') }}</option>
                                    </select>
                                    @error('coach_preference')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label for="club_member" class="form-label">
                                        {{ trans('admin.academies.club_member') }} <code>*</code>
                                    </label>
                                    <select class="form-select" id="club_member" name="club_member" required>
                                        <option value="">{{ trans('admin.academies.select_club_member') }}</option>
                                        <option value="yes" @selected(old('club_member') == 'yes')>{{ trans('admin.academies.yes') }}</option>
                                        <option value="no" @selected(old('club_member') == 'no')>{{ trans('admin.academies.no') }}</option>
                                    </select>
                                    @error('club_member')
                                    <span class="text-danger d-block mt-2">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="frequent_attendance">{{trans('admin.academies.frequent_attendance')}}<code>*</code></label>
                                    <select class="form-select" id="frequent_attendance" name="frequent_attendance" required>
                                        <option value="">{{ trans('admin.academies.frequent_attendance') }}</option>
                                        <option value="daily" @selected(old('frequent_attendance') == 'daily')>{{ trans('admin.academies.daily') }}</option>
                                        <option value="weekly" @selected(old('frequent_attendance') == 'weekly')>{{ trans('admin.academies.weekly') }}</option>
                                        <option value="monthly" @selected(old('frequent_attendance') == 'monthly')>{{ trans('admin.academies.monthly') }}</option>
                                    </select>
                                    @error('frequent_attendance')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="parent_name">{{trans('admin.academies.parent_name')}} <code>*</code></label>
                                    <input type="text" name="parent_name" class="form-control"
                                           value="{{ old('parent_name') }}"
                                           id="parent_name"
                                           placeholder="{{trans('admin.academies.parent_name')}}">
                                    @error('parent_name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="parent_phone">{{trans('admin.academies.parent_phone')}} <code>*</code></label>
                                    <input type="tel" name="parent_phone" class="form-control"
                                           value="{{ old('parent_phone') }}"
                                           id="parent_phone"
                                           placeholder="{{trans('admin.academies.parent_phone')}}">
                                    @error('parent_phone')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="child_with_relation">{{trans('admin.academies.child_with_relation')}}<code>*</code></label>
                                    <select class="form-select" id="child_with_relation" name="relation_with_child" required>
                                        <option value="">{{ trans('admin.academies.child_with_relation') }}</option>
                                        <option value="father" @selected(old('relation_with_child') == 'father')>{{ trans('admin.academies.father') }}</option>
                                        <option value="mother" @selected(old('relation_with_child') == 'mother')>{{ trans('admin.academies.mother') }}</option>
                                        <option value="brother" @selected(old('relation_with_child') == 'brother')>{{ trans('admin.academies.brother') }}</option>
                                        <option value="sister" @selected(old('relation_with_child') == 'sister')>{{ trans('admin.academies.sister') }}</option>
                                        <option value="guardian" @selected(old('relation_with_child') == 'guardian')>{{ trans('admin.academies.guardian') }}</option>
                                    </select>
                                    @error('relation_with_child')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="delivery_service">{{trans('admin.academies.delivery_service')}}<code>*</code></label>
                                    <select class="form-select" id="delivery_service" name="delivery_service" required>
                                        <option value="">{{ trans('admin.academies.delivery_service') }}</option>
                                        <option value="yes" @selected(old('delivery_service') == 'yes')>{{ trans('admin.academies.yes') }}</option>
                                        <option value="no" @selected(old('delivery_service') == 'no')>{{ trans('admin.academies.no') }}</option>
                                    </select>
                                    @error('delivery_service')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="how_did_you_hear_about_us">{{trans('admin.academies.how_did_you_hear_about_us')}}<code>*</code></label>
                                    <select class="form-select" id="how_did_you_hear_about_us" name="referral_source" required>
                                        <option value="">{{ trans('admin.academies.how_did_you_hear_about_us') }}</option>
                                        <option value="friends" @selected(old('referral_source') == 'friends')>{{ trans('admin.academies.friends') }}</option>
                                        <option value="facebook" @selected(old('referral_source') == 'facebook')>{{ trans('admin.academies.facebook') }}</option>
                                        <option value="hagzz_app" @selected(old('referral_source') == 'hagzz_app')>{{ trans('admin.academies.hagzz_app') }}</option>
                                    </select>
                                    @error('referral_source')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label for="medical_condition" class="form-label">
                                        {{ trans('admin.academies.did_you_have_medical_conditions') }} <code>*</code>
                                    </label>
                                    <select class="form-select" id="medical_condition" name="medical_condition" required>
                                        <option value="">{{ trans('admin.academies.select_medical_condition') }}</option>
                                        <option value="yes" @selected(old('medical_condition') == 'yes')>{{ trans('admin.academies.yes') }}</option>
                                        <option value="no" @selected(old('medical_condition') == 'no')>{{ trans('admin.academies.no') }}</option>
                                    </select>
                                    @error('medical_condition')
                                    <span class="text-danger d-block mt-2">{{ $message }}</span>
                                    @enderror
                                    <div class="col-md-6 mb-2">
                                        <input type="text" id="medical_condition_txt" class="form-control ms-2 d-none"
                                               value="{{ old('medical_condition_details') }}"
                                               name="medical_condition_details"
                                               placeholder="{{ trans('admin.academies.medical_condition') }}">
                                        @error('medical_condition_details')
                                        <span class="text-danger d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="additional_information">{{trans('admin.academies.additional_information')}} <code>*</code></label>
                                    <textarea name="additional_information" class="form-control" id="additional_information" cols="20" rows="5">
            {{ old('additional_information') }}
        </textarea>
                                    @error('additional_information')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                            </div>


{{--                            <div class="row">--}}
{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label for="training_id">{{trans('admin.training.training_name')}}</label>--}}
{{--                                    <input type="text" name="training" class="form-control"--}}
{{--                                           value="{{ $training->name }}" id="training_id"--}}
{{--                                           placeholder="{{$training->name}}" readonly>--}}
{{--                                    @error('training_id')--}}
{{--                                    <span class="text-danger">{{$message}}</span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label for="price">{{trans('admin.training.price')}}</label>--}}
{{--                                    <input type="text" name="price" class="form-control"--}}
{{--                                           value="{{ $training->price }}" id="price"--}}
{{--                                           placeholder="{{$training->price}}" readonly>--}}
{{--                                    @error('price')--}}
{{--                                    <span class="text-danger">{{$message}}</span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label for="name">{{trans('admin.training.name')}}</label>--}}
{{--                                    <input type="text" name="name" class="form-control"--}}
{{--                                           value="{{ old('name') }}" id="phone"--}}
{{--                                           placeholder="{{trans('admin.training.name')}}">--}}
{{--                                    @error('name')--}}
{{--                                    <span class="text-danger">{{$message}}</span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label for="phone">{{trans('admin.training.phone')}}</label>--}}
{{--                                    <input type="text" name="phone" class="form-control"--}}
{{--                                           value="{{ old('phone') }}" id="phone"--}}
{{--                                           placeholder="{{trans('admin.training.phone')}}">--}}
{{--                                    @error('phone')--}}
{{--                                    <span class="text-danger">{{$message}}</span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}

{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label for="gender">{{trans('admin.training.gender')}}</label>--}}
{{--                                    <select id="gender" name="gender" class="form-control">--}}
{{--                                        <option value="">{{ trans('admin.training.select_gender') }}</option>--}}
{{--                                        <option value="male" @selected(old('gender') == 'male' )>{{ trans('admin.training.male') }}</option>--}}
{{--                                        <option value="female" @selected(old('gender') == 'female' )>{{ trans('admin.training.female') }}</option>--}}
{{--                                    </select>--}}
{{--                                    @error('gender')--}}
{{--                                    <span class="text-danger">{{$message}}</span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label for="name">{{trans('admin.training.country_code')}}</label>--}}
{{--                                    <input type="text" name="country_code" class="form-control"--}}
{{--                                           value="{{ old('country_code') }}" id="phone"--}}
{{--                                           placeholder="{{trans('admin.training.country_code')}}">--}}
{{--                                    @error('country_code')--}}
{{--                                    <span class="text-danger">{{$message}}</span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}

{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label for="country">{{ trans('admin.training.country') }}</label>--}}
{{--                                    <select class="form-select" id="country" name="country_id">--}}
{{--                                        <option value="">{{ trans('admin.training.Select County') }}</option>--}}
{{--                                        @foreach($countries as $country)--}}
{{--                                            <option--}}
{{--                                                value="{{ $country->id }}" @selected(old('country_id') == $country->id)>{{ $country->name }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                    @error('country_id')--}}
{{--                                    <span class="text-danger">{{ $message }}</span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}

{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label for="city">{{ trans('admin.city.city') }}</label>--}}
{{--                                    <select class="form-select citySelected" id="city" name="city_id">--}}
{{--                                        @if(old('city_id'))--}}
{{--                                            <option value="{{ old('city_id') }}" selected>--}}
{{--                                                {{ old('city_name') }} <!-- Provide city name dynamically from controller if needed -->--}}
{{--                                            </option>--}}
{{--                                        @endif--}}
{{--                                    </select>--}}
{{--                                    @error('city_id')--}}
{{--                                    <span class="text-danger">{{ $message }}</span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}

{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label for="area">{{ trans('admin.area.area') }}</label>--}}
{{--                                    <select class="form-select" id="area" name="area_id">--}}
{{--                                        @if(old('area_id'))--}}
{{--                                            <option value="{{ old('area_id') }}" selected>--}}
{{--                                                {{ old('area_name') }} <!-- Provide area name dynamically from controller if needed -->--}}
{{--                                            </option>--}}
{{--                                        @endif--}}
{{--                                    </select>--}}
{{--                                    @error('area_id')--}}
{{--                                    <span class="text-danger">{{ $message }}</span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                                <div class="col-md-12 mb-3">--}}
{{--                                    <label for="phone">{{trans('admin.training.birth_date')}}</label>--}}
{{--                                    <input type="date" name="birth_date" class="form-control"--}}
{{--                                           value="{{ old('birth_date') }}"--}}
{{--                                           id="phone"--}}
{{--                                           max="{{ date('Y-m-d', strtotime('-2 years')) }}"--}}
{{--                                           placeholder="{{trans('admin.training.birth_date')}}">--}}
{{--                                    @error('birth_date')--}}
{{--                                    <span class="text-danger">{{$message}}</span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                            </div>--}}
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success mt-3">{{ trans('admin.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <input type="text" hidden="hidden" value="{{ app()->getLocale() }}" id="lang">
@endsection

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const medicalConditionSelect = document.getElementById('medical_condition');
            const medicalConditionTxt = document.getElementById('medical_condition_txt');

            function toggleMedicalConditionInput() {
                if (medicalConditionSelect.value === 'yes') {
                    medicalConditionTxt.classList.remove('d-none');
                } else {
                    medicalConditionTxt.classList.add('d-none');
                    medicalConditionTxt.value = '';
                }
            }

            // Add event listener for select changes
            medicalConditionSelect.addEventListener('change', toggleMedicalConditionInput);

            // Check the initial state of the select (e.g., after validation errors)
            toggleMedicalConditionInput();
        });
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const lang = $('#lang').val();

            function loadCities(countryID, callback) {
                if (countryID) {
                    $.ajax({
                        url: '{{ route("academy.training.getCities") }}',
                        type: "POST",
                        data: { country_id: countryID },
                        dataType: "json",
                        success: function(data) {
                            $('#city').empty();
                            $('#city').append('<option value="">{{ trans('admin.training.select_city') }}</option>');
                            $.each(data, function(key, value) {
                                $('#city').append('<option value="' + value.id + '">' + value.name[lang] + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#city').empty();
                    $('#area').empty();
                }
            }

            function loadAreas(cityID, callback) {
                if (cityID) {
                    $.ajax({
                        url: '{{ route("academy.training.getAreaByCity") }}',
                        type: "POST",
                        data: { city_id: cityID },
                        dataType: "json",
                        success: function(data) {
                            $('#area').empty();
                            $('#area').append('<option value="">{{ trans('admin.training.select_area') }}</option>');
                            $.each(data, function(key, value) {
                                $('#area').append('<option value="' + value.id + '">' + value.name[lang] + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#area').empty();
                }
            }

            $('#country').change(function() {
                var countryID = $(this).val();
                loadCities(countryID);
            });

            $('#city').change(function() {
                const cityID = $(this).val();
                loadAreas(cityID);
            });

            // Load initial data if old values exist
            const oldCountry = '{{ old('country_id') }}';
            const oldCity = '{{ old('city_id') }}';
            const oldArea = '{{ old('area_id') }}';

            if (oldCountry) {
                $('#country').val(oldCountry);
                loadCities(oldCountry, function() {
                    if (oldCity) {
                        $('#city').val(oldCity);
                        loadAreas(oldCity, function() {
                            if (oldArea) {
                                $('#area').val(oldArea);
                            }
                        });
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const yesRadio = document.getElementById('medical_condition_yes');
            const noRadio = document.getElementById('medical_condition_no');
            const medicalConditionTxt = document.getElementById('medical_condition_txt');

            // Function to toggle visibility of the text input based on the selected radio button
            function toggleMedicalConditionInput() {
                if (yesRadio.checked) {
                    medicalConditionTxt.classList.remove('d-none'); // Show the input field
                } else {
                    medicalConditionTxt.classList.add('d-none'); // Hide the input field
                    medicalConditionTxt.value = ''; // Clear the value if hidden
                }
            }

            // Add event listeners for radio button changes
            yesRadio.addEventListener('click', toggleMedicalConditionInput);
            noRadio.addEventListener('click', toggleMedicalConditionInput);

            // Check the initial state of the radio buttons (e.g., after validation errors)
            toggleMedicalConditionInput();
        });
    </script>
@endpush



