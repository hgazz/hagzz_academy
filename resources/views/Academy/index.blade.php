@extends('Academy.Layouts.master')

@stack('title', trans('admin.bokit'))
@push('css')
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="{{ asset('assetsAdmin/src/plugins/src/apex/apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assetsAdmin/src/assets/css/light/dashboard/dash_1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assetsAdmin/src/assets/css/dark/dashboard/dash_1.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="{{ asset('assetsAdmin/src/plugins/src/flatpickr/flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assetsAdmin/src/plugins/src/apex/apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assetsAdmin/src/plugins/css/light/apex/custom-apexcharts.css') }}" rel="stylesheet"
        type="text/css">

    <link href="{{ asset('assetsAdmin/src/plugins/css/dark/apex/custom-apexcharts.css') }}" rel="stylesheet"
        type="text/css" />
@endpush
<style>
    .widget.widget-card-five .account-box .info-box .icon:before {
        background-color: white !important;
    }
</style>
@section('content')
    <div class="middle-content container-xxl p-0">

        <!--  BEGIN BREADCRUMBS  -->
        <div class="secondary-nav">
            <div class="breadcrumbs-container" data-page-heading="Analytics">
                <header class="header navbar navbar-expand-sm">
                    <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-menu">
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
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.home') }}</li>
                                </ol>
                            </nav>

                        </div>
                    </div>
                </header>
            </div>
        </div>
        <!--  END BREADCRUMBS  -->

        <div class="row layout-top-spacing">

            <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="row widget-statistic">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                        <div class="widget widget-one_hybrid widget-followers">
                            <div class="widget-heading p-2">
                                <div class="w-title d-flex align-items-start justify-content-start m-0">
                                    <div
                                        class="w-icon bg-transparent p-0 d-flex align-items-start justify-content-start vertical-">
                                        <img width="48" height="48"
                                            src="https://img.icons8.com/color/48/money-bag.png" alt="money-bag" />
                                    </div>
                                    <div
                                        class="d-flex flex-column align-items-center justify-content-between gap-1 flex-shrink-1 flex-grow-1">
                                        <h5 class="fs-6">{{ trans('admin.latest_settlement_amount') }}</h5>
                                        <p class="w-value">{{ $settlements ? $settlements->total_amount : 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                        <div class="widget widget-one_hybrid widget-followers">
                            <div class="widget-heading p-2">
                                <div class="w-title d-flex align-items-start justify-content-start m-0">
                                    <div class="w-icon bg-transparent p-0">
                                        <img width="48" height="48" src="https://img.icons8.com/color/48/today.png"
                                            alt="today" />
                                    </div>
                                    <div
                                        class="d-flex flex-column align-items-center justify-content-between gap-1 flex-shrink-1 flex-grow-1">
                                        <h5 class="fs-6">{{ trans('admin.next_settlement_date') }}</h5>
                                        <p class="w-value">
                                            {{ $settlements ? \Illuminate\Support\Carbon::now($settlements->settlement_date)->addDays(auth()->user()->settlement_days_count) : '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-one_hybrid widget-followers">
                    <div class="widget-heading p-2">
                        <div class="w-title d-flex align-items-start justify-content-start m-0">
                            <div class="w-icon bg-transparent p-0">
                                <img width="48" height="48" src="https://img.icons8.com/color/48/cash-in-hand.png"
                                    alt="cash-in-hand" />
                            </div>
                            <div
                                class="d-flex flex-column align-items-center justify-content-between gap-1 flex-shrink-1 flex-grow-1">
                                <h5 class="fs-6">{{ trans('admin.training.Total Balance') }}</h5>
                                <p class="w-value">{{ auth()->user()->settlements->sum('total_amount') ?: 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="widget widget-card-five">
                    <div class="widget-content">
                        <div class="account-box">

                            <div class="info-box">
                                <div class="icon">
                                    <span>
                                        <img src="{{ asset('assetsAdmin/src/assets/img/money-bag.png') }}"
                                            alt="money-bag">
                                    </span>
                                </div>

                                <div class="balance-info d-flex flex-column align-items-center justify-content-center">
                                    <h6>{{ trans('admin.training.Total Balance') }}</h6>
                                    <p>{{ auth()->user()->settlements->sum('total_amount') ?: 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
            <div class="container">
                <div class="card">
                    <div class="row container mb-1">
                        <div class="form-group mb-0 mt-2 container">
                            <form id="filterForm" action="javascript:void(0)">
                                <div class="row">
                                    <div class="col-5">
                                        <label for="start_date"
                                            class="form-label me-2">{{ trans('admin.training.start_date') }}</label>
                                        <input type="date" class="form-control flatpickr flatpickr-input"
                                            name="start_date" id="start_date"
                                            placeholder="{{ trans('admin.select_start_date') }}">
                                    </div>
                                    <div class="col-5">
                                        <label for="end_date"
                                            class="form-label me-2">{{ trans('admin.training.end_date') }}</label>
                                        <input type="date" class="form-control flatpickr flatpickr-input"
                                            name="end_date" id="end_date"
                                            placeholder="{{ trans('admin.select_end_date') }}">
                                    </div>
                                    <div class="col-2">
                                        <label for="filter" class="form-label me-2"></label>
                                        <button type="button" class="btn btn-primary mt-4"
                                            id="filter">{{ trans('admin.apply_filter') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 layout-spacing">
                            <div class="row widget-statistic mt-2 container">
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12 layout-spacing">
                                    <div class="widget widget-one_hybrid widget-followers">
                                        <div class="widget-heading">
                                            <div class="w-title m-0">
                                                <div class="w-icon p-0 bg-transparent">
                                                    <img width="48" height="48" src="https://img.icons8.com/color/48/payment-history.png" alt="payment-history"/>
                                                </div>
                                                <div class="">
                                                    <p class="w-value" id="total_booking_balance"></p>
                                                    <h5 class="">{{ trans('admin.balance') }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12 layout-spacing">
                                    <div class="widget widget-one_hybrid widget-followers">
                                        <div class="widget-heading">
                                            <div class="w-title m-0">
                                                <div class="w-icon p-0 bg-transparent">
                                                    <img width="48" height="48" src="https://img.icons8.com/color/48/refund.png" alt="refund"/>
                                                </div>
                                                <div class="">
                                                    <p class="w-value" id="total_booking_refund_count"></p>
                                                    <h5 class="">{{ trans('admin.refunds') }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12 layout-spacing">
                                    <div class="widget widget-one_hybrid widget-followers">
                                        <div class="widget-heading">
                                            <div class="w-title m-0">
                                                <div class="w-icon p-0 bg-transparent">
                                                    <img width="48" height="48" src="https://img.icons8.com/color/48/refund-2--v1.png" alt="refund-2--v1"/>
                                                </div>
                                                <div class="">
                                                    <p class="w-value" id="total_booking_refund_amount"></p>
                                                    <h5 class="">{{ trans('admin.refund_amount') }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12 layout-spacing">
                                    <div class="widget widget-one_hybrid widget-followers">
                                        <div class="widget-heading">
                                            <div class="w-title m-0">
                                                <div class="w-icon p-0 bg-transparent">
                                                    <img width="48" height="48" src="https://img.icons8.com/color/48/event-accepted.png" alt="event-accepted"/>
                                                </div>
                                                <div class="">
                                                    <p class="w-value" id="total_booking_count"></p>
                                                    <h5 class="">{{ trans('admin.booking_count') }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="chartLine" class="col-xl-12 layout-spacing mt-2">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4>{{ trans('admin.revenue') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area">
                        <div id="s-line" class=""></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing mt-2">
                <div class="widget widget-card-five">
                    <div class="widget-content">
                        <div class="account-box">
                            <div class="info-box mb-4">
                                <div class="icon">
                                    <span>
                                        <img src="{{ asset('assetsAdmin/icons8-users-96.png') }}" alt="Total-Users">
                                    </span>
                                </div>
                                <div class="balance-info d-flex justify-content-center align-items-center">
                                    <h6 class="text-muted mb-0 mx-2">{{ trans('admin.total_users') }}</h6>
                                    <p>{{ $totalUsers }}</p>
                                </div>
                                <form method="get">
                                    <div class="dropdown d-inline-block">
                                        <a class="dropdown-toggle" href="#" role="button" id="elementDrodpown3"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-more-horizontal">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="19" cy="12" r="1"></circle>
                                                <circle cx="5" cy="12" r="1"></circle>
                                            </svg>
                                        </a>

                                        <div class="dropdown-menu left" aria-labelledby="elementDrodpown3"
                                            style="will-change: transform;">

                                            <a class="dropdown-item" id="userMonthFilter" href="javascript:void(0);"
                                                data-url="{{ route('academy.getUserDataByMonth') }}">
                                                {{ trans('admin.this_month') }}
                                            </a>
                                            <a class="dropdown-item" id="userYearFilter" href="javascript:void(0);"
                                                data-url="{{ route('academy.getUserDataByYear') }}">
                                                This Year
                                            </a>

                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div id="userChartDiv"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing mt-2">
                <div class="widget widget-card-five">
                    <div class="widget-content">
                        <div class="account-box">
                            <div class="info-box mb-4">
                                <div class="icon">
                                    <span>
                                        <img src="{{ asset('assetsAdmin/icons8-deadlift.gif') }}" alt="sports-image">
                                    </span>
                                </div>
                                <div class="balance-info d-flex justify-content-center align-items-center">
                                    <h6 class="text-muted mb-0 mx-2">{{ trans('admin.sports_level') }}</h6>
                                    {{--                                    <p>{{$totalUsers}}</p> --}}
                                </div>
                            </div>
                            <div id="sportChartDiv"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="row widget-statistic">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 layout-spacing">
                        <div class="widget widget-one_hybrid widget-followers">
                            <div class="widget-heading">
                                <div class="w-title m-0">
                                    <div class="w-icon p-0 bg-transparent">
                                        <img width="48" height="48" src="https://img.icons8.com/color/48/conference-background-selected.png" alt="conference-background-selected"/>
                                    </div>
                                    <div class="">
                                        <p class="w-value">{{ count($usersBooking) }}</p>
                                        <h5 class="">{{ trans('admin.customers_count') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 layout-spacing">
                        <div class="widget widget-one_hybrid widget-followers">
                            <div class="widget-heading">
                                <div class="w-title m-0">
                                    <div class="w-icon p-0 bg-transparent">
                                        <img width="48" height="48" src="https://img.icons8.com/color/48/add-user-male--v1.png" alt="add-user-male--v1"/>
                                    </div>
                                    <div class="">
                                        <p class="w-value">{{ count($newCustomers) }}</p>
                                        <h5 class="">{{ trans('admin.new_customers_count') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 layout-spacing">
                        <div class="widget widget-one_hybrid widget-followers">
                            <div class="widget-heading">
                                <div class="w-title m-0">
                                    <div class="w-icon p-0 bg-transparent">
                                        <img width="48" height="48" src="https://img.icons8.com/color/48/collaborating-in-circle.png" alt="collaborating-in-circle"/>
                                    </div>
                                    <div class="">
                                        <p class="w-value">{{ auth('academy')->user()->follows->count() }}</p>
                                        <h5 class="">{{ trans('admin.training.Followers') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="row widget-statistic">
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12 layout-spacing">
                        <div class="widget widget-one_hybrid widget-followers">
                            <div class="widget-heading">
                                <div class="w-title m-0">
                                    <div class="w-icon p-0 bg-transparent">
                                        <img width="48" height="48" src="https://img.icons8.com/?size=100&id=8TTVbW2U2ofn&format=png&color=000000" alt="conference-background-selected"/>
                                    </div>
                                    <div class="">
                                        <p class="w-value">{{ $fullTrainings }}</p>
                                        <h5 class="">{{ trans('admin.training_completed') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12 layout-spacing">
                        <div class="widget widget-one_hybrid widget-followers">
                            <div class="widget-heading">
                                <div class="w-title m-0">
                                    <div class="w-icon p-0 bg-transparent">
                                        <img width="48" height="48" src="https://img.icons8.com/?size=100&id=8TTVbW2U2ofn&format=png&color=000000" alt="add-user-male--v1"/>
                                    </div>
                                    <div class="">
                                        <p class="w-value">{{ $inProgressTrainings }}</p>
                                        <h5 class="">{{ trans('admin.inprogress_training') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12 layout-spacing">
                        <div class="widget widget-one_hybrid widget-followers">
                            <div class="widget-heading">
                                <div class="w-title m-0">
                                    <div class="w-icon p-0 bg-transparent">
                                        <img width="48" height="48" src="https://img.icons8.com/?size=100&id=8TTVbW2U2ofn&format=png&color=000000" alt="collaborating-in-circle"/>
                                    </div>
                                    <div class="">
                                        <p class="w-value">{{ $upcomingTrainings }}</p>
                                        <h5 class="">{{ trans('admin.upcoming_training') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12 layout-spacing">
                        <div class="widget widget-one_hybrid widget-followers">
                            <div class="widget-heading">
                                <div class="w-title m-0">
                                    <div class="w-icon p-0 bg-transparent">
                                        <img width="48" height="48" src="https://img.icons8.com/?size=100&id=8TTVbW2U2ofn&format=png&color=000000" alt="collaborating-in-circle"/>
                                    </div>
                                    <div class="">
                                        <p class="w-value">{{ $cancelledTrainings }}</p>
                                        <h5 class="">{{ trans('admin.training_cancelled') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('js')
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script src="{{ asset('assetsAdmin/src/plugins/src/apex/apexcharts.min.js') }}"></script>
    {{--    <script src="{{ asset('assetsAdmin/src/plugins/src/apex/custom-apexcharts.js') }}"></script> --}}
    <script src="{{ asset('assetsAdmin/src/assets/js/dashboard/dash_1.js') }}"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script src="{{ asset('assetsAdmin/src/plugins/src/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assetsAdmin/src/plugins/src/flatpickr/custom-flatpickr.js') }}"></script>

    <script>
        var f1 = flatpickr(document.getElementById('start_date'));
        var f2 = flatpickr(document.getElementById('end_date'));
    </script>
    <script>
        $(document).ready(function() {
            $('#filter').click(function() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                $.ajax({
                    url: '{{ route('academy.filter-bookings') }}', // Your route to handle the AJAX request
                    type: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate,
                    },
                    success: function(response) {
                        $('#total_booking_balance').text(response.total_booking_balance) +
                            '{{ trans('admin.egp') }}';
                        $('#total_booking_refund_count').text(response
                            .total_booking_refund_count);
                        $('#total_booking_refund_amount').text(response
                            .total_booking_refund_amount);
                        $('#total_booking_count').text(response.total_booking_count);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            // Load default data
            $('#filter').click();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function fetchChartData(callback) {
                // Replace with the actual URL to fetch data
                const url = '{{ route('academy.revenue-data') }}';

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        callback(data);
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            function renderJoinsChart(data, isDarkMode) {
                const rowColors = isDarkMode ? ['#3b3f5c', 'transparent'] : ['#f1f2f3', 'transparent'];

                var sline = {
                    chart: {
                        height: 350,
                        type: 'area',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    series: [{
                        name: "Revenue",
                        data: data.ordersData
                    }],
                    title: {
                        text: 'Revenue by Month',
                        align: 'left'
                    },
                    grid: {
                        row: {
                            colors: rowColors, // takes an array which will be repeated on columns
                            opacity: 0.5
                        },
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct',
                            'Nov', 'Dec'
                        ],
                    }
                };

                var chart = new ApexCharts(document.querySelector("#s-line"), sline);
                chart.render();
            }

            function initializeJoinsChart() {
                const getCorkThemeObject = localStorage.getItem("theme");
                const getParsedObject = JSON.parse(getCorkThemeObject);
                const isDarkMode = getParsedObject?.settings?.layout?.darkMode ?? false;

                fetchChartData(data => {
                    renderJoinsChart(data, isDarkMode);
                });
            }

            initializeJoinsChart();

            // Theme toggle handler
            document.querySelector('.theme-toggle').addEventListener('click', function() {
                const getCorkThemeObject = localStorage.getItem("theme");
                const getParsedObject = JSON.parse(getCorkThemeObject);
                const isDarkMode = getParsedObject?.settings?.layout?.darkMode ?? false;

                // Re-fetch data and re-render charts on theme change
                fetchChartData(data => {
                    renderJoinsChart(data, isDarkMode);
                });
            });
        });
    </script>
    <script>
        var userChartOptions = {
            chart: {
                height: 350,
                type: 'donut',
                toolbar: {
                    show: false,
                }
            },
            series: [{{ $maleUsers }}, {{ $femaleUsers }}],
            labels: ['male Users', 'Female Users'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    },
                }
            }]
        }
        var userChart = new ApexCharts(document.querySelector("#userChartDiv"), userChartOptions);
        userChart.render();

        var sportChartOptions = {
            chart: {
                height: 350,
                type: 'donut',
                toolbar: {
                    show: false,
                }
            },
            series: [{{ $beginnerLevels }}, {{ $intermediateLevels }}, {{ $advancedLevels }}],
            labels: ['Beginner', 'Intermediate', 'Advanced'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    },
                }
            }]
        }
        var sportChart = new ApexCharts(document.querySelector("#sportChartDiv"), sportChartOptions);
        sportChart.render();
    </script>
    <script>
        $('#userMonthFilter').on('click', function() {
            $.ajax({
                type: 'get',
                url: $(this).data('url'),
                success: function(response) {
                    userChart.updateSeries([response.maleUsersByMonth, response.femaleUsersByMonth]);
                }
            });
        });

        $('#userYearFilter').on('click', function() {
            $.ajax({
                type: 'get',
                url: $(this).data('url'),
                success: function(response) {
                    userChart.updateSeries([response.maleUsersByYear, response.femaleUsersByYear]);
                }
            });
        });
    </script>
@endpush
