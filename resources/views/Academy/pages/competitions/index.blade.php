@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.competitions'))

@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/academy-competitions-modern.css') }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $statusClass = ['scheduled' => 'is-scheduled', 'completed' => 'is-completed', 'cancelled' => 'is-cancelled'];
    @endphp

    <div class="middle-content container-xxl p-0">
        <section class="competition-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
            <header class="competition-hero">
                <button type="button" class="sidebarCollapse competition-menu-btn" aria-label="Toggle menu">
                    <i data-feather="menu"></i>
                </button>
                <div>
                    <span>{{ $isArabic ? 'المنافسات والمباريات' : 'Competitions and matches' }}</span>
                    <h1>{{ trans('admin.student_management.competitions') }}</h1>
                    <p>{{ $isArabic ? 'أنشئ المنافسة، اختر اللاعبين المرشحين، سجل النتيجة، واطبع كشفا رسميا.' : 'Create matches, select nominated players, record results, and print official sheets.' }}</p>
                </div>
                <a href="{{ route('academy.competitions.create') }}" class="competition-primary-btn">
                    <i data-feather="plus"></i>
                    <span>{{ trans('admin.student_management.add_competition') }}</span>
                </a>
            </header>

            <div class="competition-list">
                @forelse($competitions as $competition)
                    <article class="competition-card">
                        <div class="competition-date">
                            <strong>{{ $competition->competition_date?->format('d') }}</strong>
                            <span>{{ $competition->competition_date?->format('M') }}</span>
                        </div>
                        <div class="competition-body">
                            <div class="competition-title-row">
                                <div>
                                    <h2>{{ $competition->home_team_name }} <small>vs</small> {{ $competition->opponent_name }}</h2>
                                    <p>
                                        <i data-feather="clock"></i>
                                        <span>{{ $competition->starts_at ?: '-' }}</span>
                                        <i data-feather="map-pin"></i>
                                        <span>{{ $competition->venue ?: '-' }}</span>
                                    </p>
                                </div>
                                <span class="competition-status {{ $statusClass[$competition->status] ?? '' }}">
                                    {{ trans('admin.student_management.' . $competition->status) }}
                                </span>
                            </div>

                            <div class="competition-meta-grid">
                                <div><i data-feather="target"></i><span>{{ $competition->sport?->name ?: trans('admin.student_management.no_sport') }}</span></div>
                                <div><i data-feather="users"></i><span>{{ $competition->students_count }} {{ trans('admin.student_management.students') }}</span></div>
                                <div><i data-feather="award"></i><span>{{ $competition->status === 'completed' ? (($competition->home_score ?? 0) . ' - ' . ($competition->opponent_score ?? 0)) : trans('admin.student_management.result_pending') }}</span></div>
                            </div>

                            <div class="competition-actions">
                                <a href="{{ route('academy.competitions.show', $competition) }}"><i data-feather="eye"></i>{{ trans('admin.student_management.open') }}</a>
                                <a href="{{ route('academy.competitions.edit', $competition) }}"><i data-feather="edit-3"></i>{{ trans('admin.student_management.edit') }}</a>
                                <a href="{{ route('academy.competitions.print', $competition) }}" target="_blank"><i data-feather="printer"></i>{{ trans('admin.student_management.print') }}</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="competition-empty">
                        <i data-feather="flag"></i>
                        <h2>{{ trans('admin.student_management.no_competitions_yet') }}</h2>
                        <p>{{ $isArabic ? 'ابدأ بإضافة مباراة أو منافسة، ثم اختر الطلاب المشاركين.' : 'Start by adding a match, then choose participating students.' }}</p>
                        <a href="{{ route('academy.competitions.create') }}">{{ trans('admin.student_management.add_competition') }}</a>
                    </div>
                @endforelse
            </div>

            <div class="competition-pagination">{{ $competitions->links() }}</div>
        </section>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assetsAdmin/src/plugins/src/font-icons/feather/feather.min.js') }}"></script>
    <script>document.addEventListener('DOMContentLoaded', function () { if (window.feather) feather.replace(); });</script>
@endpush
