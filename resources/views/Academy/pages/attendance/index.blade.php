@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.attendance'))

@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/academy-attendance-modern.css') }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $statusMeta = [
            'present' => ['icon' => 'check-circle', 'class' => 'is-present'],
            'late' => ['icon' => 'clock', 'class' => 'is-late'],
            'absent' => ['icon' => 'x-circle', 'class' => 'is-absent'],
            'excused' => ['icon' => 'shield', 'class' => 'is-excused'],
        ];
    @endphp

    <div class="middle-content container-xxl p-0">
        <section class="attendance-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
            <header class="attendance-hero">
                <button type="button" class="sidebarCollapse attendance-menu-btn" aria-label="Toggle menu">
                    <i data-feather="menu"></i>
                </button>
                <div>
                    <span class="attendance-kicker">{{ $isArabic ? 'إدارة يومية سهلة' : 'Daily tracking' }}</span>
                    <h1>{{ trans('admin.student_management.attendance') }}</h1>
                    <p>{{ $isArabic ? 'تابع حضور الطلاب لكل مجموعة واحسب الغياب والتأخير من مكان واحد.' : 'Track student attendance by group and keep absence, lateness, and excuses clear.' }}</p>
                </div>
                <a href="{{ route('academy.attendance.create') }}" class="attendance-primary-action">
                    <i data-feather="plus"></i>
                    <span>{{ trans('admin.student_management.new_attendance_session') }}</span>
                </a>
            </header>

            <div class="attendance-session-list">
                @forelse($sessions as $session)
                    @php
                        $attendedCount = (int) $session->present_count + (int) $session->late_count;
                        $totalCount = max(1, (int) $session->records_count);
                        $rate = round(($attendedCount / $totalCount) * 100);
                    @endphp
                    <article class="attendance-session-card">
                        <div class="session-date-block">
                            <span>{{ $session->session_date?->format('d') }}</span>
                            <small>{{ $session->session_date?->format('M') }}</small>
                        </div>
                        <div class="session-main">
                            <div class="session-title-row">
                                <div>
                                    <h2>{{ $session->group?->name }}</h2>
                                    <p>
                                        <i data-feather="clock"></i>
                                        <span>{{ $session->starts_at ?? '-' }} - {{ $session->ends_at ?? '-' }}</span>
                                    </p>
                                </div>
                                <a href="{{ route('academy.attendance.show', $session) }}" class="session-open-btn">
                                    <span>{{ trans('admin.student_management.open') }}</span>
                                    <i data-feather="{{ $isArabic ? 'arrow-left' : 'arrow-right' }}"></i>
                                </a>
                            </div>

                            <div class="session-progress" aria-label="Attendance completion">
                                <span style="width: {{ $rate }}%"></span>
                            </div>

                            <div class="session-status-grid">
                                @foreach($statusMeta as $status => $meta)
                                    @php $countName = $status . '_count'; @endphp
                                    <div class="status-pill {{ $meta['class'] }}">
                                        <i data-feather="{{ $meta['icon'] }}"></i>
                                        <span>{{ trans('admin.student_management.' . $status) }}</span>
                                        <strong>{{ $session->{$countName} }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="attendance-empty">
                        <i data-feather="clipboard"></i>
                        <h2>{{ trans('admin.student_management.no_attendance_sessions_yet') }}</h2>
                        <p>{{ $isArabic ? 'ابدأ بإنشاء جلسة حضور للمجموعة التي ستتدرب اليوم.' : 'Create the first attendance session for today’s group.' }}</p>
                        <a href="{{ route('academy.attendance.create') }}">{{ trans('admin.student_management.new_attendance_session') }}</a>
                    </div>
                @endforelse
            </div>

            <div class="attendance-pagination">
                {{ $sessions->links() }}
            </div>
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
