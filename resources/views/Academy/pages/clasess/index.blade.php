@extends('Academy.Layouts.master')

@section('title', trans('admin.clasess.clasess'))

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/src/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assetsAdmin/src/plugins/css/dark/table/datatable/custom_dt_miscellaneous.css') }}">
    <style>
        .metric-card {
            border-radius: 12px;
            padding: 16px 20px;
            background: #fff;
            border: 1px solid #e0e6ed;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.07);
        }
        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .filter-container {
            background: #ffffff;
            border: 1px solid #e0e6ed;
            border-radius: 12px;
            padding: 18px 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .dark-mode .metric-card,
        .dark-mode .filter-container {
            background: #0e1726;
            border-color: #1b2e4b;
        }
        .timeframe-pill-btn {
            padding: 6px 14px;
            border-radius: 8px !important;
            font-size: 13px;
            font-weight: 600;
        }
    </style>
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
                            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.clasess.clasess') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </header>
            </div>
        </div>
        <!--  END BREADCRUMBS  -->

        <div class="row layout-top-spacing">
            <!-- Metric Cards -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
                <div class="metric-card">
                    <div>
                        <span class="text-muted d-block mb-1" style="font-size: 13px;">{{ app()->getLocale() === 'ar' ? 'إجمالي الحصص' : 'Total Sessions' }}</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($metrics['total'] ?? 0) }}</h3>
                    </div>
                    <div class="metric-icon bg-light-primary text-primary">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
                <div class="metric-card">
                    <div>
                        <span class="text-muted d-block mb-1" style="font-size: 13px;">{{ app()->getLocale() === 'ar' ? 'حصص اليوم' : "Today's Sessions" }}</span>
                        <h3 class="fw-bold mb-0 text-success">{{ number_format($metrics['today'] ?? 0) }}</h3>
                    </div>
                    <div class="metric-icon bg-light-success text-success">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
                <div class="metric-card">
                    <div>
                        <span class="text-muted d-block mb-1" style="font-size: 13px;">{{ app()->getLocale() === 'ar' ? 'الحصص القادمة' : 'Upcoming Sessions' }}</span>
                        <h3 class="fw-bold mb-0 text-info">{{ number_format($metrics['upcoming'] ?? 0) }}</h3>
                    </div>
                    <div class="metric-icon bg-light-info text-info">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
                <div class="metric-card">
                    <div>
                        <span class="text-muted d-block mb-1" style="font-size: 13px;">{{ app()->getLocale() === 'ar' ? 'الحصص المنتهية' : 'Completed Sessions' }}</span>
                        <h3 class="fw-bold mb-0 text-secondary">{{ number_format($metrics['past'] ?? 0) }}</h3>
                    </div>
                    <div class="metric-icon bg-light-dark text-secondary">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <!-- Smart Filter Bar -->
                <div class="filter-container">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-5 col-md-6 col-12">
                            <label for="filter_training_id" class="form-label fw-bold mb-1" style="font-size: 13px;">
                                <i class="fas fa-filter text-primary me-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية حسب البرنامج التدريبي:' : 'Filter by Training Program:' }}
                            </label>
                            <select id="filter_training_id" class="form-select">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- جميع البرامج التدريبية --' : '-- All Training Programs --' }}</option>
                                @foreach($trainings as $training)
                                    @php
                                        $tName = is_array($training->name) ? ($training->name[app()->getLocale()] ?? reset($training->name)) : $training->name;
                                    @endphp
                                    <option value="{{ $training->id }}">{{ $tName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-5 col-md-6 col-12">
                            <label class="form-label fw-bold mb-1 d-block" style="font-size: 13px;">
                                <i class="fas fa-calendar-alt text-primary me-1"></i> {{ app()->getLocale() === 'ar' ? 'الفترة الزمنية:' : 'Timeframe Filter:' }}
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="timeframe_filter" id="tf_all" value="all" checked autocomplete="off">
                                <label class="btn btn-outline-primary timeframe-pill-btn" for="tf_all">
                                    {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
                                </label>

                                <input type="radio" class="btn-check" name="timeframe_filter" id="tf_today" value="today" autocomplete="off">
                                <label class="btn btn-outline-success timeframe-pill-btn" for="tf_today">
                                    {{ app()->getLocale() === 'ar' ? 'اليوم' : 'Today' }} ({{ $metrics['today'] ?? 0 }})
                                </label>

                                <input type="radio" class="btn-check" name="timeframe_filter" id="tf_upcoming" value="upcoming" autocomplete="off">
                                <label class="btn btn-outline-info timeframe-pill-btn" for="tf_upcoming">
                                    {{ app()->getLocale() === 'ar' ? 'القادمة' : 'Upcoming' }}
                                </label>

                                <input type="radio" class="btn-check" name="timeframe_filter" id="tf_past" value="past" autocomplete="off">
                                <label class="btn btn-outline-secondary timeframe-pill-btn" for="tf_past">
                                    {{ app()->getLocale() === 'ar' ? 'المنتهية' : 'Completed' }}
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-2 col-12 d-flex align-items-end justify-content-lg-end gap-2">
                            <button type="button" id="btn_reset_filters" class="btn btn-outline-danger btn-sm w-100 py-2">
                                <i class="fas fa-redo-alt me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- DataTable Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0 fw-bold">{{ trans('admin.clasess.clasess') }}</h4>
                                <span class="text-muted font-sm">{{ app()->getLocale() === 'ar' ? 'إدارة ومتابعة الحصص التدريبية المنظمة' : 'Manage scheduled training sessions' }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('academy.training.create') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus-circle me-1"></i> {{ app()->getLocale() === 'ar' ? 'برنامج تدريبي جديد' : 'New Training' }}
                                </a>
                                <a href="{{ route('academy.class.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> {{ trans('admin.clasess.create') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Delete -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">{{ app()->getLocale() === 'ar' ? 'تأكيد الحذف' : 'Confirm Delete' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من حذف هذه الحصص المحددة؟' : 'Are you sure you want to delete the selected items?' }}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                                    <form action="{{ route('academy.class.bulkDelete') }}" method="post">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="ids" id="ids">
                                        <button class="btn btn-danger">{{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        {!! $dataTable->table(['class' => 'table table-striped dt-table-hover dataTable w-100', 'id' => 'tclass-table']) !!}
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function reloadTable() {
                if (window.LaravelDataTables && window.LaravelDataTables['tclass-table']) {
                    window.LaravelDataTables['tclass-table'].ajax.reload();
                }
            }

            $('#filter_training_id').on('change', function() {
                reloadTable();
            });

            $('input[name="timeframe_filter"]').on('change', function() {
                reloadTable();
            });

            $('#btn_reset_filters').on('click', function() {
                $('#filter_training_id').val('');
                $('#tf_all').prop('checked', true);
                reloadTable();
            });
        });
    </script>
@endpush
