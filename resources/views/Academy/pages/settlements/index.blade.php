@extends('Academy.Layouts.master')

@section('title', trans('admin.settlement.Settlements'))

@push('css')
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-1.13.8/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assetsAdmin/src/plugins/src/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assetsAdmin/src/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assetsAdmin/src/plugins/css/light/table/datatable/custom_dt_miscellaneous.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/custom_dt_miscellaneous.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush




@section('content')
    <div class="middle-content container-xxl p-0">

        <!--  BEGIN BREADCRUMBS  -->
        <div class="secondary-nav">
            <div class="breadcrumbs-container" data-page-heading="Analytics">
                <header class="header navbar navbar-expand-sm">
                    <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-menu">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </a>
                    <div class="d-flex breadcrumb-content">
                        <div class="page-header">

                            <div class="page-title">
                            </div>

                            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                                    <li class="breadcrumb-item active"
                                        aria-current="page">{{ trans('admin.settlement.Settlements') }}</li>
                                </ol>
                            </nav>

                        </div>
                    </div>
                </header>
            </div>
        </div>
        <!--  END BREADCRUMBS  -->

        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="card">

                    <div class="card-body">
                        {!! $dataTable->table(['class' => 'table table-striped dt-table-hover dataTable']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('js')
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-1.13.8/datatables.min.js"></script>
    {!! $dataTable->scripts() !!}
@endpush
