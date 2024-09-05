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
                <form method="POST" action="{{route('academy.training.storeBooking')}}">
                    @csrf
                    <input type="hidden" name="training_id" value="{{ $training->id }}">
                    <div class="card">
                        <div class="card-header">
                            <h3>{{ trans('admin.training.create_booking') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="training_id">{{trans('admin.training.training_name')}}</label>
                                    <input type="text" name="training" class="form-control"
                                           value="{{ $training->name }}" id="training_id"
                                           placeholder="{{$training->name}}" readonly>
                                    @error('training_id')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="price">{{trans('admin.training.price')}}</label>
                                    <input type="text" name="price" class="form-control"
                                           value="{{ $training->price }}" id="price"
                                           placeholder="{{$training->price}}" readonly>
                                    @error('price')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{trans('admin.training.name')}}</label>
                                    <input type="text" name="name" class="form-control"
                                           value="{{ old('name') }}" id="phone"
                                           placeholder="{{trans('admin.training.name')}}">
                                    @error('name')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone">{{trans('admin.training.phone')}}</label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ old('phone') }}" id="phone"
                                           placeholder="{{trans('admin.training.phone')}}">
                                    @error('phone')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gender">{{trans('admin.training.gender')}}</label>
                                    <select id="gender" name="gender" class="form-control">
                                        <option value="">{{ trans('admin.training.select_gender') }}</option>
                                        <option value="male" @selected(old('gender') == 'male' )>{{ trans('admin.training.male') }}</option>
                                        <option value="female" @selected(old('gender') == 'female' )>{{ trans('admin.training.female') }}</option>
                                    </select>
                                    @error('gender')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">{{trans('admin.training.country_code')}}</label>
                                    <input type="text" name="country_code" class="form-control"
                                           value="{{ old('country_code') }}" id="phone"
                                           placeholder="{{trans('admin.training.country_code')}}">
                                    @error('country_code')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="country">{{ trans('admin.training.country') }}</label>
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
                                    <label for="city">{{ trans('admin.city.city') }}</label>
                                    <select class="form-select citySelected" id="city" name="city_id">

                                    </select>
                                    @error('city_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="area">{{ trans('admin.area.area') }}</label>
                                    <select class="form-select" id="area" name="area_id">

                                    </select>
                                    @error('area_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="phone">{{trans('admin.training.birth_date')}}</label>
                                    <input type="date" name="birth_date" class="form-control"
                                           value="{{ old('birth_date') }}"
                                           id="phone"
                                           max="{{ date('Y-m-d', strtotime('-2 years')) }}"
                                           placeholder="{{trans('admin.training.birth_date')}}">
                                    @error('birth_date')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
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
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const lang = $('#lang').val();
            $('#country').change(function() {
                var countryID = $(this).val();
                if (countryID) {
                    $.ajax({
                        url: '{{ route("academy.training.getCities") }}',
                        type: "POST",
                        data: {
                            country_id: countryID
                        },
                        dataType: "json",
                        success:function(data) {
                            $('#city').empty();
                            $('#city').append('<option value="">{{ trans('admin.training.select_city')}}</option>');
                            $.each(data, function(key, value) {
                                $('#city').append('<option value="'+ value.id +'">'+ value.name[lang] +'</option>');
                            });
                        }
                    });
                } else {
                    $('#city').empty();
                }
            });

            $('#city').change(function() {
                const cityID = $(this).val();
                if (cityID) {
                    $.ajax({
                        url: '{{ route("academy.training.getAreaByCity") }}',
                        type: "POST",
                        data: {
                            city_id: cityID
                        },
                        dataType: "json",
                        success:function(data) {
                            $('#area').empty();
                            $('#area').append('<option value="">{{ trans('admin.training.select_area') }}</option>');
                            $.each(data, function(key, value) {
                                $('#area').append('<option value="'+ value.id +'">'+ value.name[lang] +'</option>');
                            });
                        }
                    });
                } else {
                    $('#area').empty();
                }
            });
        });
    </script>

@endpush


