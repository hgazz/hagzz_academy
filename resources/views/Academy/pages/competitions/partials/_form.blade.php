@php
    $isArabic = app()->getLocale() === 'ar';
    $selectedStudents = old('student_ids', isset($competition) ? $competition->students()->pluck('academy_students.id')->toArray() : []);
    $selectedStudents = is_array($selectedStudents) ? array_map('intval', $selectedStudents) : [];
    $currentStatus = old('status', $competition->status ?? 'scheduled');
@endphp

<div class="competition-form-layout">
    <main class="competition-form-main">
        <section class="competition-panel">
            <header class="competition-section-title">
                <i data-feather="flag"></i>
                <div>
                    <h2>{{ trans('admin.student_management.competition_details') }}</h2>
                    <p>{{ $isArabic ? 'بيانات المباراة الأساسية كما ستظهر في الطباعة الرسمية.' : 'Core match details as they will appear on the official print sheet.' }}</p>
                </div>
            </header>

            <div class="competition-fields-grid">
                <label class="competition-field">
                    <span>{{ trans('admin.student_management.home_team') }} <b>*</b></span>
                    <div><i data-feather="home"></i><input name="home_team_name" required value="{{ old('home_team_name', $competition->home_team_name ?? $defaultTeamName) }}"></div>
                </label>

                <label class="competition-field">
                    <span>{{ trans('admin.student_management.opponent') }} <b>*</b></span>
                    <div><i data-feather="shield"></i><input name="opponent_name" required value="{{ old('opponent_name', $competition->opponent_name ?? '') }}" placeholder="{{ $isArabic ? 'اسم المنافس' : 'Opponent name' }}"></div>
                </label>

                <label class="competition-field">
                    <span>{{ trans('admin.student_management.sport') }}</span>
                    <div>
                        <i data-feather="target"></i>
                        <select name="sport_id">
                            <option value="">{{ trans('admin.student_management.no_sport') }}</option>
                            @foreach($competitionSports as $sport)
                                <option value="{{ $sport->id }}" @selected(old('sport_id', $competition->sport_id ?? '') == $sport->id)>{{ $sport->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>

                <label class="competition-field">
                    <span>{{ trans('admin.student_management.date') }} <b>*</b></span>
                    <div><i data-feather="calendar"></i><input type="date" name="competition_date" required value="{{ old('competition_date', isset($competition) ? $competition->competition_date?->format('Y-m-d') : now()->format('Y-m-d')) }}"></div>
                </label>

                <label class="competition-field">
                    <span>{{ trans('admin.student_management.time') }}</span>
                    <div><i data-feather="clock"></i><input type="time" name="starts_at" value="{{ old('starts_at', $competition->starts_at ?? '') }}"></div>
                </label>

                <label class="competition-field">
                    <span>{{ trans('admin.student_management.venue') }}</span>
                    <div><i data-feather="map-pin"></i><input name="venue" value="{{ old('venue', $competition->venue ?? '') }}" placeholder="{{ $isArabic ? 'الملعب أو المكان' : 'Court, field, or location' }}"></div>
                </label>

                <label class="competition-field">
                    <span>{{ trans('admin.student_management.status') }} <b>*</b></span>
                    <div>
                        <i data-feather="toggle-right"></i>
                        <select name="status" id="competitionStatus" required>
                            @foreach(['scheduled', 'completed', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected($currentStatus === $status)>{{ trans('admin.student_management.' . $status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>
            </div>
        </section>

        <section class="competition-panel result-fields-panel" id="resultFields" @if($currentStatus !== 'completed') hidden @endif>
            <header class="competition-section-title">
                <i data-feather="award"></i>
                <div>
                    <h2>{{ trans('admin.student_management.result') }}</h2>
                    <p>{{ $isArabic ? 'يمكنك تسجيل النتيجة الآن أو لاحقا من صفحة التفاصيل.' : 'Record the result now or later from the details page.' }}</p>
                </div>
            </header>
            <div class="competition-score-grid">
                <label class="competition-field">
                    <span>{{ trans('admin.student_management.home_score') }}</span>
                    <div><i data-feather="hash"></i><input type="number" name="home_score" min="0" max="999" value="{{ old('home_score', $competition->home_score ?? '') }}"></div>
                </label>
                <label class="competition-field">
                    <span>{{ trans('admin.student_management.opponent_score') }}</span>
                    <div><i data-feather="hash"></i><input type="number" name="opponent_score" min="0" max="999" value="{{ old('opponent_score', $competition->opponent_score ?? '') }}"></div>
                </label>
                <label class="competition-field field-full">
                    <span>{{ trans('admin.student_management.result_notes') }}</span>
                    <div class="textarea-shell"><i data-feather="file-text"></i><textarea name="result_notes" rows="3">{{ old('result_notes', $competition->result_notes ?? '') }}</textarea></div>
                </label>
            </div>
        </section>

        <section class="competition-panel">
            <header class="competition-section-title has-badge">
                <i data-feather="user-check"></i>
                <div>
                    <h2>{{ trans('admin.student_management.nominated_players') }}</h2>
                    <p>{{ $isArabic ? 'اختر الطلاب المرشحين للمشاركة في هذه المنافسة.' : 'Select students nominated for this competition.' }}</p>
                </div>
                <span><strong id="selectedPlayersCount">{{ count($selectedStudents) }}</strong> {{ $isArabic ? 'لاعب' : 'players' }}</span>
            </header>

            <div class="competition-search">
                <i data-feather="search"></i>
                <input type="search" id="playerSearch" placeholder="{{ $isArabic ? 'ابحث باسم الطالب أو الهاتف' : 'Search by student name or phone' }}">
            </div>

            <div class="competition-player-list" id="playerList">
                @forelse($competitionStudents as $student)
                    <label class="competition-player" data-search="{{ mb_strtolower($student->name . ' ' . $student->phone . ' ' . $student->guardian_phone) }}">
                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" @checked(in_array((int) $student->id, $selectedStudents, true))>
                        <span class="player-avatar">{{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}</span>
                        <span>
                            <strong>{{ $student->name }}</strong>
                            <small>{{ $student->phone ?: ($student->guardian_phone ?: '-') }}</small>
                        </span>
                        <i data-feather="check"></i>
                    </label>
                @empty
                    <div class="competition-empty compact">
                        <i data-feather="users"></i>
                        <h2>{{ trans('admin.student_management.no_students_yet') }}</h2>
                        <a href="{{ route('academy.students.create') }}">{{ trans('admin.student_management.add_student') }}</a>
                    </div>
                @endforelse
            </div>
            <p class="competition-no-results" id="noPlayerResults">{{ $isArabic ? 'لا توجد نتائج مطابقة.' : 'No matching players.' }}</p>
        </section>

        <section class="competition-panel">
            <header class="competition-section-title">
                <i data-feather="file-text"></i>
                <div>
                    <h2>{{ trans('admin.student_management.notes') }}</h2>
                    <p>{{ $isArabic ? 'أي تعليمات إدارية أو فنية قبل المباراة.' : 'Any management or technical notes before the match.' }}</p>
                </div>
            </header>
            <label class="competition-field">
                <div class="textarea-shell"><i data-feather="edit-3"></i><textarea name="notes" rows="4">{{ old('notes', $competition->notes ?? '') }}</textarea></div>
            </label>
        </section>
    </main>

    <aside class="competition-aside">
        <div class="competition-summary-card">
            <i data-feather="shield"></i>
            <span>{{ trans('admin.student_management.official_sheet') }}</span>
            <h2 id="summaryMatch">{{ old('home_team_name', $competition->home_team_name ?? $defaultTeamName) }} vs {{ old('opponent_name', $competition->opponent_name ?? '-') }}</h2>
            <p>{{ $isArabic ? 'سيتم استخدام هذه البيانات في صفحة الطباعة الرسمية.' : 'These details will be used in the official printable sheet.' }}</p>
        </div>
    </aside>
</div>

<footer class="competition-form-footer">
    <a href="{{ route('academy.competitions.index') }}" class="competition-cancel-btn">{{ trans('admin.student_management.cancel') }}</a>
    <button class="competition-save-btn">
        <i data-feather="save"></i>
        <span>{{ trans('admin.student_management.save') }}</span>
    </button>
</footer>
