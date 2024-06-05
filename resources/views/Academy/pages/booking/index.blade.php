@extends('Academy.Layouts.master')

@section('title', trans('admin.bookings.bookings'))

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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </a>
                    <div class="d-flex breadcrumb-content">
                        <div class="page-header">

                            <div class="page-title">
                            </div>

                            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.bookings.bookings') }}</li>
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
                <a class="btn btn-success w-25 mt-1 mb-2" href="{{route('academy.report.booking.export')}}">
                    {{trans('admin.Export')}}
                </a>
                <div class="card">
                    <div class="card-header">

                        <div class="row">
                            <form method="GET" action="{{ route('academy.report.invoice.filter') }}">
                                <div class="col-md-5">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-5">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary mt-2">Apply</button>
                            </form>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ URL::current() }}">
                                <h3>{{ trans('admin.bookings.bookings') }}</h3>
                            </a>
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
    {{--        <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-1.13.8/datatables.min.js"></script>--}}
    <script src="{{ asset('assetsAdmin/src/plugins/src/table/datatable/datatables.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="/vendor/datatables/buttons.server-side.js"></script>

    <script>
        let lang = $('meta[name="lang"]').attr('content');
        translate  ={
            "title_del" : {
                'en': 'Are you sure ?',
                'ar': "هل أنت متأكد ؟",
            },
            "text_del" :{
                'en':'You will not be able to recover this ',
                'ar':'لن تتمكن من استرداد هذا'
            },
            "cancel_btn" :{
                'en':'Cancel' ,
                'ar':'إلغاء'
            },
            'submit_btn':{
                'en':'Submit' ,
                'ar':'إرسال'
            },
            "confirm_del":{
                'en':'Yes, Cancel it!' ,
                'ar':'نعم , قم بالغائه !'
            },
            "title_del2" : {
                'en': 'Are you sure you want to Cancel this record?',
                'ar': "هل أنت متأكد أنك تريد الغاء هذا السجل؟",
            },

            "text_del2" : {
                // 'en': 'If you delete this, it will be gone forever',
                // 'ar': "إذا حذفت هذا ، فسيختفي إلى الأبد",
            },
            "removed" : {
                'en': 'Canceled!',
                'ar': 'الغاء!',
            },
            "messageSuccess" : {
                'en': 'Record Has Been Canceled !',
                'ar': '! تم الغاء السجل ',
            },

            "messageError" : {
                'en': 'Record Removed Failed',
                'ar': 'فشل حذف السجل',
            },
        }
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $('.table').on('init.dt', function () {

                $(document).on('click', '.show_confirm_two',function (event) {
                    var url = $(this).data('href');
                    var id = $(this).data('id');
                    var name = $(this).data('name');
                    var token = $('#token').val();
                    var parent = $(this).parent();


                    Swal.fire({
                        title: translate.title_del2[lang],
                        text: translate.text_del2[lang],
                        icon: 'warning',
                        padding: '3em',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: translate.confirm_del[lang],
                        cancelButtonText: translate.cancel_btn[lang],
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let temp = `#${name}-${id}`;

                            $.ajax({
                                url: url,
                                type: 'GET',
                                data: { id: id },
                                dataType: 'json'
                                , success: function (res) {
                                    if (res.data.status === 'success'){
                                        $(temp).remove();
                                        Swal.fire(
                                            res.data.model,
                                            res.data.message,
                                            'success'
                                        )
                                        location.reload();
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            `Error : ${res.message} !`,
                                            'error'
                                        )
                                    }


                                }, error: function (resp) {
                                    Swal.fire(
                                        translate.removed[lang],
                                        translate.messageError[lang],
                                        `error`,
                                    )
                                }
                            });
                        }
                    });
                });

            })
        });

    </script>

    {!! $dataTable->scripts() !!}
@endpush
