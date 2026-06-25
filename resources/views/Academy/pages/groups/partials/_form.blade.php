@csrf
@php
    $isArabic = app()->getLocale() === 'ar';
    $selectedStudents = old('student_ids', isset($group) ? $group->students()->pluck('academy_students.id')->toArray() : []);
    $selectedStudents = is_array($selectedStudents) ? array_map('intval', $selectedStudents) : [];
    $selectedDays = old('days', isset($group) ? ($group->days ?? []) : []);
    $selectedDays = is_array($selectedDays) ? $selectedDays : [];
@endphp

<div class="group-form-layout">
    <main class="group-form-main">
        <section class="group-form-section">
            <header class="group-section-header">
                <span class="group-section-icon"><i data-feather="info"></i></span>
                <div>
                    <h2>{{ $isArabic ? 'بيانات المجموعة' : 'Group information' }}</h2>
                    <p>{{ $isArabic ? 'الاسم والروابط الأساسية التي تساعدك على تنظيم المجموعة.' : 'The name and core links used to organize this group.' }}</p>
                </div>
            </header>
            <div class="group-section-body">
                <div class="group-fields-grid">
                    <div class="group-field field-full">
                        <label for="groupName">{{ trans('admin.student_management.group_name') }} <b>*</b></label>
                        <div class="group-input-shell">
                            <i data-feather="users"></i>
                            <input type="text" id="groupName" name="name" maxlength="255"
                                   value="{{ old('name', $group->name ?? '') }}" required
                                   placeholder="{{ $isArabic ? 'مثال: مجموعة الناشئين - السبت والثلاثاء' : 'Example: Junior group - Saturday and Tuesday' }}">
                        </div>
                        @error('name')<span class="group-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="group-field">
                        <label for="trainingId">{{ trans('admin.student_management.linked_training') }}</label>
                        <div class="group-select-shell">
                            <i data-feather="activity"></i>
                            <select name="training_id" id="trainingId">
                                <option value="">{{ trans('admin.student_management.no_training') }}</option>
                                @foreach($groupTrainings as $training)
                                    <option value="{{ $training->id }}" @selected(old('training_id', $group->training_id ?? '') == $training->id)>{{ $training->name }}</option>
                                @endforeach
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                        @error('training_id')<span class="group-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="group-field">
                        <label for="coachId">{{ trans('admin.student_management.coach') }}</label>
                        <div class="group-select-shell">
                            <i data-feather="award"></i>
                            <select name="coach_id" id="coachId">
                                <option value="">{{ trans('admin.student_management.no_coach') }}</option>
                                @foreach($groupCoaches as $coach)
                                    <option value="{{ $coach->id }}" @selected(old('coach_id', $group->coach_id ?? '') == $coach->id)>{{ $coach->name }}</option>
                                @endforeach
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                        @error('coach_id')<span class="group-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="group-field">
                        <label for="sportId">{{ trans('admin.student_management.sport') }}</label>
                        <div class="group-select-shell">
                            <i data-feather="target"></i>
                            <select name="sport_id" id="sportId">
                                <option value="">{{ trans('admin.student_management.no_sport') }}</option>
                                @foreach($groupSports as $sport)
                                    <option value="{{ $sport->id }}" @selected(old('sport_id', $group->sport_id ?? '') == $sport->id)>{{ $sport->name }}</option>
                                @endforeach
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                        @error('sport_id')<span class="group-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="group-field">
                        <label for="groupStatus">{{ trans('admin.student_management.status') }} <b>*</b></label>
                        <div class="group-select-shell">
                            <i data-feather="toggle-right"></i>
                            <select name="status" id="groupStatus" required>
                                <option value="active" @selected(old('status', $group->status ?? 'active') === 'active')>{{ trans('admin.student_management.active') }}</option>
                                <option value="inactive" @selected(old('status', $group->status ?? 'active') === 'inactive')>{{ trans('admin.student_management.inactive') }}</option>
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                        @error('status')<span class="group-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </section>

        <section class="group-form-section">
            <header class="group-section-header">
                <span class="group-section-icon is-teal"><i data-feather="calendar"></i></span>
                <div>
                    <h2>{{ $isArabic ? 'الجدول والسعة' : 'Schedule and capacity' }}</h2>
                    <p>{{ $isArabic ? 'حدد أيام التدريب ووقت البداية والنهاية والعدد الأقصى للطلاب.' : 'Set training days, start and end times, and maximum student capacity.' }}</p>
                </div>
            </header>
            <div class="group-section-body">
                <div class="group-fields-grid">
                    <div class="group-field field-full">
                        <label>{{ trans('admin.student_management.days') }}</label>
                        <div class="group-days-picker">
                            @foreach($groupDays as $day)
                                <label class="group-day-option">
                                    <input type="checkbox" name="days[]" value="{{ $day }}" @checked(in_array($day, $selectedDays, true))>
                                    <span><i data-feather="check"></i>{{ trans('admin.training.' . $day) }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('days')<span class="group-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="group-field">
                        <label for="startTime">{{ trans('admin.student_management.start_time') }}</label>
                        <div class="group-input-shell">
                            <i data-feather="clock"></i>
                            <input type="time" id="startTime" name="start_time" value="{{ old('start_time', $group->start_time ?? '') }}">
                        </div>
                        @error('start_time')<span class="group-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="group-field">
                        <label for="endTime">{{ trans('admin.student_management.end_time') }}</label>
                        <div class="group-input-shell">
                            <i data-feather="clock"></i>
                            <input type="time" id="endTime" name="end_time" value="{{ old('end_time', $group->end_time ?? '') }}">
                        </div>
                        @error('end_time')<span class="group-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="group-field field-full">
                        <label for="groupCapacity">{{ trans('admin.student_management.capacity') }}</label>
                        <div class="group-input-shell">
                            <i data-feather="user-plus"></i>
                            <input type="number" id="groupCapacity" name="capacity" value="{{ old('capacity', $group->capacity ?? '') }}" min="1"
                                   placeholder="{{ $isArabic ? 'العدد الأقصى للطلاب' : 'Maximum number of students' }}">
                        </div>
                        @error('capacity')<span class="group-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </section>

        <section class="group-form-section">
            <header class="group-section-header">
                <span class="group-section-icon is-orange"><i data-feather="user-check"></i></span>
                <div>
                    <h2>{{ $isArabic ? 'طلاب المجموعة' : 'Group students' }}</h2>
                    <p>{{ $isArabic ? 'ابحث عن الطلاب وحدد من تريد إضافته إلى المجموعة.' : 'Search students and select who should join this group.' }}</p>
                </div>
                <span class="selected-students-badge"><strong id="selectedStudentsCount">{{ count($selectedStudents) }}</strong> {{ $isArabic ? 'محدد' : 'selected' }}</span>
            </header>
            <div class="group-section-body">
                <div class="student-search-shell">
                    <i data-feather="search"></i>
                    <input type="search" id="studentSearch" placeholder="{{ $isArabic ? 'ابحث بالاسم أو رقم الهاتف' : 'Search by name or phone number' }}">
                </div>

                <div class="group-students-list" id="studentsList">
                    @forelse($groupStudents as $student)
                        <label class="group-student-option" data-search="{{ mb_strtolower($student->name . ' ' . $student->phone) }}">
                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" @checked(in_array((int) $student->id, $selectedStudents, true))>
                            <span class="student-avatar">{{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}</span>
                            <span class="student-copy">
                                <strong>{{ $student->name }}</strong>
                                <small><i data-feather="phone"></i>{{ $student->phone ?: ($isArabic ? 'بدون رقم هاتف' : 'No phone number') }}</small>
                            </span>
                            <span class="student-check"><i data-feather="check"></i></span>
                        </label>
                    @empty
                        <div class="group-empty-state">
                            <i data-feather="users"></i>
                            <strong>{{ $isArabic ? 'لا يوجد طلاب نشطون' : 'No active students' }}</strong>
                            <p>{{ $isArabic ? 'أضف الطلاب أولًا ثم عد لاختيارهم داخل المجموعة.' : 'Add students first, then return to assign them to the group.' }}</p>
                            <a href="{{ route('academy.students.create') }}">{{ trans('admin.student_management.add_student') }}</a>
                        </div>
                    @endforelse
                </div>
                <p class="no-search-results" id="noStudentResults">{{ $isArabic ? 'لا توجد نتائج مطابقة للبحث.' : 'No students match your search.' }}</p>
                @error('student_ids')<span class="group-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
        </section>

        <section class="group-form-section">
            <header class="group-section-header">
                <span class="group-section-icon is-purple"><i data-feather="file-text"></i></span>
                <div>
                    <h2>{{ trans('admin.student_management.notes') }}</h2>
                    <p>{{ $isArabic ? 'أضف تعليمات داخلية أو ملاحظات خاصة بهذه المجموعة.' : 'Add internal instructions or notes for this group.' }}</p>
                </div>
            </header>
            <div class="group-section-body">
                <div class="group-field">
                    <div class="group-textarea-shell">
                        <textarea name="notes" id="groupNotes" rows="4" placeholder="{{ $isArabic ? 'اكتب أي ملاحظات مهمة للمدرب أو الإدارة...' : 'Write any important notes for the coach or management...' }}">{{ old('notes', $group->notes ?? '') }}</textarea>
                    </div>
                    @error('notes')<span class="group-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                </div>
            </div>
        </section>
    </main>

    <aside class="group-form-aside">
        <div class="group-summary-card">
            <div class="summary-icon"><i data-feather="users"></i></div>
            <span>{{ $isArabic ? 'ملخص المجموعة' : 'Group summary' }}</span>
            <h2 id="summaryName">{{ old('name', $group->name ?? ($isArabic ? 'مجموعة جديدة' : 'New group')) }}</h2>
            <dl>
                <div><dt><i data-feather="activity"></i>{{ trans('admin.student_management.training') }}</dt><dd id="summaryTraining">-</dd></div>
                <div><dt><i data-feather="award"></i>{{ trans('admin.student_management.coach') }}</dt><dd id="summaryCoach">-</dd></div>
                <div><dt><i data-feather="calendar"></i>{{ trans('admin.student_management.days') }}</dt><dd id="summaryDays">-</dd></div>
                <div><dt><i data-feather="clock"></i>{{ trans('admin.student_management.time') }}</dt><dd id="summaryTime">-</dd></div>
                <div><dt><i data-feather="user-check"></i>{{ trans('admin.student_management.students') }}</dt><dd id="summaryStudents">{{ count($selectedStudents) }}</dd></div>
            </dl>
            <div class="summary-status" id="summaryStatus">
                <i data-feather="check-circle"></i>
                <span>{{ trans('admin.student_management.active') }}</span>
            </div>
        </div>

        <div class="group-tip-card">
            <i data-feather="zap"></i>
            <div>
                <strong>{{ $isArabic ? 'تنظيم أفضل' : 'Better organization' }}</strong>
                <p>{{ $isArabic ? 'اربط المجموعة بالتدريب والمدرب لتسهيل تسجيل الحضور ومتابعة الاشتراكات لاحقًا.' : 'Link the group to a training and coach to simplify attendance and subscription tracking.' }}</p>
            </div>
        </div>
    </aside>
</div>

<footer class="group-form-footer">
    <a href="{{ route('academy.groups.index') }}" class="group-cancel-button">{{ trans('admin.student_management.cancel') }}</a>
    <button type="submit" class="group-submit-button">
        <i data-feather="save"></i>
        <span>{{ trans('admin.student_management.save') }}</span>
    </button>
</footer>
