@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.add_group'))

@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/academy-group-form-modern.css') }}" rel="stylesheet">
@endpush

@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

@section('content')
    <div class="middle-content container-xxl p-0 group-form-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <header class="group-page-header">
            <div class="group-title-group">
                <button type="button" class="group-menu-toggle btn-toggle sidebarCollapse" aria-label="Toggle menu">
                    <i data-feather="menu"></i>
                </button>
                <div>
                    <span>{{ $isArabic ? 'إدارة الطلاب والمجموعات' : 'Students and groups' }}</span>
                    <h1>{{ trans('admin.student_management.add_group') }}</h1>
                    <p>{{ $isArabic ? 'أنشئ مجموعة تدريبية وحدد جدولها ومدربها والطلاب المشاركين فيها.' : 'Create a training group, assign its schedule, coach and participating students.' }}</p>
                </div>
            </div>
            <a href="{{ route('academy.groups.index') }}" class="group-back-link">
                <i data-feather="{{ $isArabic ? 'arrow-right' : 'arrow-left' }}"></i>
                <span>{{ $isArabic ? 'العودة إلى المجموعات' : 'Back to groups' }}</span>
            </a>
        </header>

        @if ($errors->any())
            <div class="group-error-summary" role="alert">
                <i data-feather="alert-triangle"></i>
                <div>
                    <strong>{{ $isArabic ? 'يرجى مراجعة بيانات المجموعة' : 'Please review the group details' }}</strong>
                    <p>{{ $errors->first() }}</p>
                </div>
            </div>
        @endif

        <form action="{{ route('academy.groups.store') }}" method="POST" id="groupForm">
            @include('Academy.pages.groups.partials._form')
        </form>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assetsAdmin/src/plugins/src/font-icons/feather/feather.min.js') }}"></script>
    @include('Academy.pages.groups.partials._scripts')
@endpush
