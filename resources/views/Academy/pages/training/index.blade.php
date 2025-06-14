@extends('Academy.Layouts.master')

@section('title', trans('admin.training.training'))

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/src/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/custom_dt_miscellaneous.css') }}">
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
                                        aria-current="page">{{ trans('admin.training.training') }}</li>
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
{{--                <a class="btn btn-success w-25 mt-3 mb-2" href="{{route('academy.training.export')}}">--}}
{{--                    Export--}}
{{--                </a>--}}
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{route('academy.class.create')}}">
                                <h3>{{ trans('admin.training.training') }}</h3>
                            </a>
                            <a href="{{route('academy.training.create')}}" class="btn btn-primary">
                                {{ trans('admin.training.create') }}
                            </a>
                        </div>

                    </div>

                    <!-- Button trigger modal -->


                    <button type="button" class="btn btn-danger w-25 mt-2 d-none dt-button" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        {{ trans('admin.delete') }}
                    </button>

                    <form action="{{route('academy.training.publish')}}" method="post">
                        @csrf
                        <input type="hidden" name="pub_ids" id="pub_ids">
                        <button class="btn btn-primary w-25 mt-2 d-none pub-button">
                            {{ trans('admin.publish') }}
                        </button>
                    </form>


                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Are you sure</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete this items from Training
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <form class=" " action="{{route('academy.training.bulkDelete')}}" method="post">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="ids" id="ids">
                                        <button  class="btn btn-danger">{{ trans('admin.delete') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">

                        {!! $dataTable->table(['class' => 'table table-striped dt-table-hover dataTable']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('js')
    <script src="{{ asset('assetsAdmin/src/plugins/src/table/datatable/datatables.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="/vendor/datatables/buttons.server-side.js"></script>
    {!! $dataTable->scripts() !!}

@endpush
