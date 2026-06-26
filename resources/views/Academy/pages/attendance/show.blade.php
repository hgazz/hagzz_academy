@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.attendance_records'))

@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/academy-attendance-modern.css') }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $statuses = [
            'present' => ['icon' => 'check-circle', 'class' => 'is-present'],
            'absent' => ['icon' => 'x-circle', 'class' => 'is-absent'],
            'late' => ['icon' => 'clock', 'class' => 'is-late'],
            'excused' => ['icon' => 'shield', 'class' => 'is-excused'],
        ];
    @endphp

    <div class="middle-content container-xxl p-0">
        <section class="attendance-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
            <header class="attendance-hero attendance-hero-compact">
                <button type="button" class="sidebarCollapse attendance-menu-btn" aria-label="Toggle menu">
                    <i data-feather="menu"></i>
                </button>
                <div>
                    <span class="attendance-kicker">{{ $session->session_date?->format('Y-m-d') }}</span>
                    <h1>{{ $session->group?->name }}</h1>
                    <p>
                        {{ trans('admin.student_management.time') }}:
                        {{ $session->starts_at ?? '-' }} - {{ $session->ends_at ?? '-' }}
                    </p>
                </div>
                <a href="{{ route('academy.attendance.index') }}" class="attendance-secondary-action">
                    <i data-feather="{{ $isArabic ? 'arrow-right' : 'arrow-left' }}"></i>
                    <span>{{ trans('admin.student_management.back') }}</span>
                </a>
            </header>

            <div class="attendance-summary-grid">
                @foreach($statuses as $status => $meta)
                    <div class="attendance-summary-card {{ $meta['class'] }}">
                        <i data-feather="{{ $meta['icon'] }}"></i>
                        <span>{{ trans('admin.student_management.' . $status) }}</span>
                        <strong data-summary-status="{{ $status }}">{{ $summary[$status] ?? 0 }}</strong>
                    </div>
                @endforeach
                <div class="attendance-summary-card is-total">
                    <i data-feather="users"></i>
                    <span>{{ $isArabic ? 'إجمالي الطلاب' : 'Total students' }}</span>
                    <strong data-summary-total>{{ $summary['total'] ?? $session->records->count() }}</strong>
                </div>
            </div>

            <form action="{{ route('academy.attendance.update', $session) }}" method="POST" class="attendance-record-form">
                @csrf
                @method('PUT')

                <div class="attendance-toolbar">
                    <div>
                        <h2>{{ trans('admin.student_management.attendance_records') }}</h2>
                        <p>{{ $isArabic ? 'اضغط على حالة كل طالب. التصميم مناسب للموبايل أثناء التمرين.' : 'Tap each student status. The layout is mobile friendly during practice.' }}</p>
                    </div>
                    <button class="attendance-save-btn">
                        <i data-feather="save"></i>
                        <span>{{ trans('admin.student_management.save_attendance') }}</span>
                    </button>
                </div>

                <div class="attendance-record-list">
                    @foreach($session->records as $record)
                        @php
                            $student = $record->student;
                            $initials = collect(explode(' ', trim($student?->name ?? '')))
                                ->filter()
                                ->take(2)
                                ->map(fn($part) => mb_substr($part, 0, 1))
                                ->implode('');
                        @endphp
                        <article class="attendance-student-card" data-record-card>
                            <div class="student-identity">
                                <div class="student-avatar">{{ $initials ?: '#' }}</div>
                                <div>
                                    <h3>{{ $student?->name }}</h3>
                                    <p>
                                        <i data-feather="phone"></i>
                                        <span>{{ $student?->phone ?: ($student?->guardian_phone ?: '-') }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="status-toggle-group" role="group" aria-label="{{ trans('admin.student_management.status') }}">
                                @foreach($statuses as $status => $meta)
                                    <label class="status-toggle {{ $meta['class'] }}">
                                        <input type="radio"
                                               name="records[{{ $record->id }}][status]"
                                               value="{{ $status }}"
                                               @checked($record->status === $status)
                                               data-status-radio>
                                        <span>
                                            <i data-feather="{{ $meta['icon'] }}"></i>
                                            {{ trans('admin.student_management.' . $status) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="attendance-time-grid">
                                <label>
                                    <span>{{ trans('admin.student_management.check_in') }}</span>
                                    <input type="time" name="records[{{ $record->id }}][check_in_at]" value="{{ $record->check_in_at }}">
                                </label>
                                <label>
                                    <span>{{ trans('admin.student_management.check_out') }}</span>
                                    <input type="time" name="records[{{ $record->id }}][check_out_at]" value="{{ $record->check_out_at }}">
                                </label>
                                <label class="record-note">
                                    <span>{{ trans('admin.student_management.notes') }}</span>
                                    <input type="text" name="records[{{ $record->id }}][notes]" value="{{ $record->notes }}" placeholder="{{ $isArabic ? 'ملاحظة قصيرة' : 'Short note' }}">
                                </label>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="attendance-mobile-save">
                    <button class="attendance-save-btn">
                        <i data-feather="save"></i>
                        <span>{{ trans('admin.student_management.save_attendance') }}</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection

@push('js')
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var refreshIcons = function () {
                if (window.feather) {
                    window.feather.replace();
                }
            };
            var updateSummary = function () {
                var counts = {present: 0, absent: 0, late: 0, excused: 0};
                document.querySelectorAll('[data-status-radio]:checked').forEach(function (radio) {
                    counts[radio.value] = (counts[radio.value] || 0) + 1;
                });
                Object.keys(counts).forEach(function (status) {
                    var target = document.querySelector('[data-summary-status="' + status + '"]');
                    if (target) {
                        target.textContent = counts[status];
                    }
                });
            };

            document.querySelectorAll('[data-status-radio]').forEach(function (radio) {
                radio.addEventListener('change', updateSummary);
            });

            updateSummary();
            refreshIcons();
        });
    </script>
@endpush
