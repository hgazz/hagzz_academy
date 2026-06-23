@extends('Academy.Layouts.master')

@section('title', trans('admin.coaches.create'))


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
                                    <li class="breadcrumb-item"><a href="{{ route('academy.coach') }}">{{ trans('admin.coaches.coaches') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.coaches.create') }}</li>
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
        <form id="coach-create-form" method="POST" action="{{ route('academy.coach.store') }}" enctype="multipart/form-data" novalidate>
            <div class="card">
                <div class="card-header">
                    <h3>{{ trans('admin.coaches.create') }}</h3>
                </div>
                <div class="card-body">
                    @include('Academy.pages.coaches.partials._form')
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success mt-3" id="coach-submit-button"
                            onclick="document.getElementById('coach-submit-status').textContent = 'تم الضغط على الحفظ'; console.info('[Coach Form] Save button clicked');">
                        {{ trans('admin.submit') }}
                    </button>
                    <span id="coach-submit-status" class="ms-3 text-muted" aria-live="polite"></span>
                    <small class="d-block mt-2 text-muted">coach-form-20260623-3</small>
                </div>
            </div>
        </form>
    </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.getElementById('coach-create-form')?.addEventListener('submit', function () {
            const button = document.getElementById('coach-submit-button');
            const status = document.getElementById('coach-submit-status');

            if (!button) {
                return;
            }

            if (status) {
                status.textContent = 'جارٍ إرسال البيانات...';
            }

            console.info('[Coach Form] Native form submission started', {
                action: this.action,
                method: this.method
            });

            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
                button.textContent.trim();
        });
    </script>
@endpush
