@extends('Academy.Layouts.master')

@section('title', trans('admin.area.create'))


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
                                    <li class="breadcrumb-item"><a href="{{ route('academy.address.index') }}">{{ trans('admin.area.areas') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.area.create') }}</li>
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
                <form method="POST" action="{{ route('academy.address.store') }}" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-header">
                            <h3>{{ trans('admin.area.create') }}</h3>
                        </div>
                        <div class="card-body">
                            @include('Academy.pages.address.inc._form')
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success mt-3">{{ trans('admin.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
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
@endpush
