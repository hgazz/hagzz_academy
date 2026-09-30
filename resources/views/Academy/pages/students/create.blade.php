@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.add_student'))

@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/academy-student-form-modern.css') }}" rel="stylesheet">
@endpush

@php($isArabic = app()->getLocale() === 'ar')

@section('content')
    <div class="middle-content container-xxl p-0 student-form-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <header class="student-page-header">
            <div class="student-title-group">
                <button type="button" class="student-menu-toggle btn-toggle sidebarCollapse" aria-label="Toggle menu">
                    <i data-feather="menu"></i>
                </button>
                <div>
                    <span>{{ $isArabic ? ('إدارة ' . ($termStudentsPlural ?? 'الأعضاء')) : 'Management' }}</span>
                    <h1>{{ trans('admin.student_management.add_student') }}</h1>
                    <p>{{ $isArabic ? (!empty($isGymFacility) ? 'سجّل بيانات العضو الشخصية ومعلومات الاتصال للاشتراك في الصالة.' : 'سجّل بيانات الطالب وولي الأمر والمعلومات المهمة لمتابعته داخل الأكاديمية.') : 'Record member profile and contact details.' }}</p>
                </div>
            </div>
            <a href="{{ route('academy.students.index') }}" class="student-back-link">
                <i data-feather="{{ $isArabic ? 'arrow-right' : 'arrow-left' }}"></i>
                <span>{{ $isArabic ? ('العودة إلى ' . ($termStudentsPlural ?? 'الأعضاء')) : 'Back' }}</span>
            </a>
        </header>

        @if ($errors->any())
            <div class="student-error-summary" role="alert">
                <i data-feather="alert-triangle"></i>
                <div><strong>{{ $isArabic ? ('يرجى مراجعة بيانات ' . ($termStudent ?? 'العضو')) : 'Please review details' }}</strong><p>{{ $errors->first() }}</p></div>
            </div>
        @endif

        <form action="{{ route('academy.students.store') }}" method="POST" id="studentForm" enctype="multipart/form-data">
            @include('Academy.pages.students.partials._form')
        </form>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assetsAdmin/src/plugins/src/font-icons/feather/feather.min.js') }}"></script>
    @include('Academy.pages.students.partials._scripts')
@endpush
