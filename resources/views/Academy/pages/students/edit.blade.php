@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.edit_student'))

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
                    <span>{{ $isArabic ? 'إدارة الطلاب' : 'Student management' }}</span>
                    <h1>{{ trans('admin.student_management.edit_student') }}</h1>
                    <p>{{ $isArabic ? 'حدّث بيانات الطالب وولي الأمر والحالة والملاحظات المسجلة.' : 'Update the student, guardian, status and recorded notes.' }}</p>
                </div>
            </div>
            <a href="{{ route('academy.students.index') }}" class="student-back-link">
                <i data-feather="{{ $isArabic ? 'arrow-right' : 'arrow-left' }}"></i>
                <span>{{ $isArabic ? 'العودة إلى الطلاب' : 'Back to students' }}</span>
            </a>
        </header>

        @if ($errors->any())
            <div class="student-error-summary" role="alert">
                <i data-feather="alert-triangle"></i>
                <div><strong>{{ $isArabic ? 'يرجى مراجعة بيانات الطالب' : 'Please review the student details' }}</strong><p>{{ $errors->first() }}</p></div>
            </div>
        @endif

        <form action="{{ route('academy.students.update', $student) }}" method="POST" id="studentForm">
            @method('PUT')
            @include('Academy.pages.students.partials._form')
        </form>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assetsAdmin/src/plugins/src/font-icons/feather/feather.min.js') }}"></script>
    @include('Academy.pages.students.partials._scripts')
@endpush
