@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.new_attendance_session'))

@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/academy-attendance-modern.css') }}" rel="stylesheet">
@endpush

@section('content')
    @php $isArabic = app()->getLocale() === 'ar'; @endphp

    <div class="middle-content container-xxl p-0">
        <section class="attendance-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
            <header class="attendance-hero attendance-hero-compact">
                <button type="button" class="sidebarCollapse attendance-menu-btn" aria-label="Toggle menu">
                    <i data-feather="menu"></i>
                </button>
                <div>
                    <span class="attendance-kicker">{{ $isArabic ? 'تحضير الجلسة' : 'Session setup' }}</span>
                    <h1>{{ trans('admin.student_management.new_attendance_session') }}</h1>
                    <p>{{ $isArabic ? 'اختر المجموعة ووقت الجلسة، وبعد الإنشاء ستظهر قائمة الطلاب لتسجيل الحضور.' : 'Choose the group and session time, then mark each student from the attendance sheet.' }}</p>
                </div>
                <a href="{{ route('academy.attendance.index') }}" class="attendance-secondary-action">
                    <i data-feather="{{ $isArabic ? 'arrow-right' : 'arrow-left' }}"></i>
                    <span>{{ trans('admin.student_management.back') }}</span>
                </a>
            </header>

            @if($errors->any())
                <div class="attendance-alert">
                    <i data-feather="alert-circle"></i>
                    <div>
                        <strong>{{ $errors->first() }}</strong>
                    </div>
                </div>
            @endif

            <form action="{{ route('academy.attendance.store') }}" method="POST" class="attendance-create-shell">
                @csrf
                <div class="attendance-form-panel">
                    <div class="attendance-section-title">
                        <i data-feather="calendar"></i>
                        <div>
                            <h2>{{ $isArabic ? 'بيانات الجلسة' : 'Session details' }}</h2>
                            <p>{{ $isArabic ? 'هذه البيانات تساعد المدرب على الوصول للجلسة بسرعة من الهاتف.' : 'These details make the session easy to find from mobile.' }}</p>
                        </div>
                    </div>

                    <div class="attendance-form-grid">
                        <label class="attendance-field attendance-field-wide">
                            <span>{{ trans('admin.student_management.group') }} <b>*</b></span>
                            <div class="attendance-input">
                                <i data-feather="users"></i>
                                <select name="academy_group_id" required>
                                    <option value="">{{ trans('admin.student_management.select_group') }}</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" @selected(old('academy_group_id') == $group->id)>
                                            {{ $group->name }} - {{ $group->students_count }} {{ $isArabic ? 'طالب' : 'students' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </label>

                        <label class="attendance-field">
                            <span>{{ trans('admin.student_management.date') }} <b>*</b></span>
                            <div class="attendance-input">
                                <i data-feather="calendar"></i>
                                <input type="date" name="session_date" value="{{ old('session_date', now()->format('Y-m-d')) }}" required>
                            </div>
                        </label>

                        <label class="attendance-field">
                            <span>{{ trans('admin.student_management.starts_at') }}</span>
                            <div class="attendance-input">
                                <i data-feather="clock"></i>
                                <input type="time" name="starts_at" value="{{ old('starts_at') }}">
                            </div>
                        </label>

                        <label class="attendance-field">
                            <span>{{ trans('admin.student_management.ends_at') }}</span>
                            <div class="attendance-input">
                                <i data-feather="clock"></i>
                                <input type="time" name="ends_at" value="{{ old('ends_at') }}">
                            </div>
                        </label>

                        <label class="attendance-field attendance-field-wide">
                            <span>{{ trans('admin.student_management.notes') }}</span>
                            <div class="attendance-input attendance-textarea">
                                <i data-feather="file-text"></i>
                                <textarea name="notes" rows="4" placeholder="{{ $isArabic ? 'مثال: جلسة تعويض أو ملاحظة للمدرب' : 'Example: makeup session or coach note' }}">{{ old('notes') }}</textarea>
                            </div>
                        </label>
                    </div>

                    <div class="attendance-actions">
                        <button class="attendance-save-btn">
                            <i data-feather="check"></i>
                            <span>{{ trans('admin.student_management.create_session') }}</span>
                        </button>
                        <a href="{{ route('academy.attendance.index') }}" class="attendance-cancel-btn">{{ trans('admin.student_management.cancel') }}</a>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection

@push('js')
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.feather) {
                window.feather.replace();
            }
        });
    </script>
@endpush
