@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.competition_details'))

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
            <header class="competition-hero competition-hero-compact">
                <button type="button" class="sidebarCollapse competition-menu-btn" aria-label="Toggle menu">
                    <i data-feather="menu"></i>
                </button>
                <div>
                    <span>{{ $competition->competition_date?->format('Y-m-d') }}</span>
                    <h1>{{ $competition->home_team_name }} <small>vs</small> {{ $competition->opponent_name }}</h1>
                    <p>{{ $competition->sport?->name ?: trans('admin.student_management.no_sport') }} - {{ $competition->starts_at ?: '-' }} - {{ $competition->venue ?: '-' }}</p>
                </div>
                <div class="competition-hero-actions">
                    <a href="{{ route('academy.competitions.print', $competition) }}" target="_blank" class="competition-primary-btn"><i data-feather="printer"></i>{{ trans('admin.student_management.print') }}</a>
                    <a href="{{ route('academy.competitions.edit', $competition) }}" class="competition-secondary-btn"><i data-feather="edit-3"></i>{{ trans('admin.student_management.edit') }}</a>
                </div>
            </header>

            <div class="competition-detail-grid">
                <main class="competition-detail-main">
                    <section class="competition-panel match-score-panel">
                        <span class="competition-status {{ $statusClass[$competition->status] ?? '' }}">{{ trans('admin.student_management.' . $competition->status) }}</span>
                        <div class="score-board">
                            <div><strong>{{ $competition->home_team_name }}</strong><span>{{ $competition->home_score ?? '-' }}</span></div>
                            <small>VS</small>
                            <div><strong>{{ $competition->opponent_name }}</strong><span>{{ $competition->opponent_score ?? '-' }}</span></div>
                        </div>
                        @if($competition->result_notes)
                            <p class="result-note">{{ $competition->result_notes }}</p>
                        @endif
                    </section>

                    <section class="competition-panel">
                        <header class="competition-section-title">
                            <i data-feather="users"></i>
                            <div>
                                <h2>{{ trans('admin.student_management.nominated_players') }}</h2>
                                <p>{{ $competition->students->count() }} {{ trans('admin.student_management.students') }}</p>
                            </div>
                        </header>
                        <div class="selected-player-grid">
                            @forelse($competition->students as $student)
                                <div class="selected-player-card">
                                    <span>{{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}</span>
                                    <div>
                                        <strong>{{ $student->name }}</strong>
                                        <small>{{ $student->phone ?: ($student->guardian_phone ?: '-') }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="competition-empty compact">
                                    <i data-feather="users"></i>
                                    <h2>{{ $isArabic ? 'لم يتم اختيار لاعبين بعد' : 'No players selected yet' }}</h2>
                                </div>
                            @endforelse
                        </div>
                    </section>
                </main>

                <aside class="competition-detail-side">
                    <section class="competition-panel result-update-panel">
                        <header class="competition-section-title">
                            <i data-feather="award"></i>
                            <div>
                                <h2>{{ trans('admin.student_management.record_result') }}</h2>
                                <p>{{ $isArabic ? 'سجل النتيجة بعد انتهاء المنافسة.' : 'Record the final result after the match.' }}</p>
                            </div>
                        </header>
                        <form action="{{ route('academy.competitions.result', $competition) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="competition-score-grid single">
                                <label class="competition-field">
                                    <span>{{ trans('admin.student_management.home_score') }}</span>
                                    <div><i data-feather="hash"></i><input type="number" name="home_score" min="0" max="999" value="{{ old('home_score', $competition->home_score) }}" required></div>
                                </label>
                                <label class="competition-field">
                                    <span>{{ trans('admin.student_management.opponent_score') }}</span>
                                    <div><i data-feather="hash"></i><input type="number" name="opponent_score" min="0" max="999" value="{{ old('opponent_score', $competition->opponent_score) }}" required></div>
                                </label>
                                <label class="competition-field">
                                    <span>{{ trans('admin.student_management.result_notes') }}</span>
                                    <div class="textarea-shell"><i data-feather="file-text"></i><textarea name="result_notes" rows="4">{{ old('result_notes', $competition->result_notes) }}</textarea></div>
                                </label>
                            </div>
                            <button class="competition-save-btn w-100"><i data-feather="save"></i>{{ trans('admin.student_management.save_result') }}</button>
                        </form>
                    </section>

                    <section class="competition-panel">
                        <header class="competition-section-title">
                            <i data-feather="info"></i>
                            <div>
                                <h2>{{ trans('admin.student_management.notes') }}</h2>
                            </div>
                        </header>
                        <p class="competition-notes">{{ $competition->notes ?: '-' }}</p>
                    </section>
                </aside>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assetsAdmin/src/plugins/src/font-icons/feather/feather.min.js') }}"></script>
    <script>document.addEventListener('DOMContentLoaded', function () { if (window.feather) feather.replace(); });</script>
@endpush
