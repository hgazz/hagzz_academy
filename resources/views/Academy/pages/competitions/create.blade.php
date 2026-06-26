@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.add_competition'))

@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/academy-competitions-modern.css') }}" rel="stylesheet">
@endpush

@section('content')
    @php $isArabic = app()->getLocale() === 'ar'; @endphp
    <div class="middle-content container-xxl p-0 competition-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <header class="competition-hero competition-hero-compact">
            <button type="button" class="sidebarCollapse competition-menu-btn" aria-label="Toggle menu">
                <i data-feather="menu"></i>
            </button>
            <div>
                <span>{{ $isArabic ? 'منافسة جديدة' : 'New competition' }}</span>
                <h1>{{ trans('admin.student_management.add_competition') }}</h1>
                <p>{{ $isArabic ? 'سجل بيانات المباراة واختر الطلاب المرشحين للمشاركة.' : 'Add match details and select nominated students.' }}</p>
            </div>
            <a href="{{ route('academy.competitions.index') }}" class="competition-secondary-btn">
                <i data-feather="{{ $isArabic ? 'arrow-right' : 'arrow-left' }}"></i>
                <span>{{ trans('admin.student_management.back') }}</span>
            </a>
        </header>

        @if($errors->any())
            <div class="competition-alert"><i data-feather="alert-circle"></i><strong>{{ $errors->first() }}</strong></div>
        @endif

        <form action="{{ route('academy.competitions.store') }}" method="POST" id="competitionForm">
            @csrf
            @include('Academy.pages.competitions.partials._form')
        </form>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assetsAdmin/src/plugins/src/font-icons/feather/feather.min.js') }}"></script>
    @include('Academy.pages.competitions.partials._scripts')
@endpush
