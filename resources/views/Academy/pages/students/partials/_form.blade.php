@csrf
@php
    $isArabic = app()->getLocale() === 'ar';
    $birthDate = old('birth_date', isset($student) && $student->birth_date ? $student->birth_date->format('Y-m-d') : '');
@endphp

<div class="student-form-layout">
    <main class="student-form-main">
        <section class="student-form-section">
            <header class="student-section-header">
                <span class="student-section-icon"><i data-feather="user"></i></span>
                <div>
                    <h2>{{ $isArabic ? 'البيانات الشخصية' : 'Personal information' }}</h2>
                    <p>{{ $isArabic ? 'المعلومات الأساسية ووسائل التواصل الخاصة بالطالب.' : 'The student basic information and contact details.' }}</p>
                </div>
            </header>
            <div class="student-section-body">
                <div class="student-fields-grid">
                    <div class="student-field field-full">
                        <label for="studentName">{{ trans('admin.student_management.name') }} <b>*</b></label>
                        <div class="student-input-shell"><i data-feather="user"></i><input type="text" id="studentName" name="name" maxlength="255" value="{{ old('name', $student->name ?? '') }}" required placeholder="{{ $isArabic ? 'اسم الطالب بالكامل' : 'Student full name' }}"></div>
                        @error('name')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                    <div class="student-field">
                        <label for="studentPhone">{{ trans('admin.student_management.phone') }}</label>
                        <div class="student-input-shell"><i data-feather="phone"></i><input type="tel" id="studentPhone" name="phone" maxlength="30" value="{{ old('phone', $student->phone ?? '') }}" dir="ltr" placeholder="+20 / +974"></div>
                        @error('phone')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                    <div class="student-field">
                        <label for="studentEmail">{{ trans('admin.student_management.email') }}</label>
                        <div class="student-input-shell"><i data-feather="mail"></i><input type="email" id="studentEmail" name="email" maxlength="255" value="{{ old('email', $student->email ?? '') }}" dir="ltr" placeholder="student@example.com"></div>
                        @error('email')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                    <div class="student-field">
                        <label for="studentGender">{{ trans('admin.student_management.gender') }}</label>
                        <div class="student-select-shell"><i data-feather="users"></i><select id="studentGender" name="gender">
                            <option value="">{{ trans('admin.student_management.select') }}</option>
                            <option value="male" @selected(old('gender', $student->gender ?? '') === 'male')>{{ trans('admin.student_management.male') }}</option>
                            <option value="female" @selected(old('gender', $student->gender ?? '') === 'female')>{{ trans('admin.student_management.female') }}</option>
                        </select><i data-feather="chevron-down"></i></div>
                        @error('gender')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                    <div class="student-field">
                        <label for="studentBirthDate">{{ trans('admin.student_management.birth_date') }}</label>
                        <div class="student-input-shell"><i data-feather="calendar"></i><input type="date" id="studentBirthDate" name="birth_date" value="{{ $birthDate }}"></div>
                        @error('birth_date')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </section>

        <section class="student-form-section">
            <header class="student-section-header">
                <span class="student-section-icon is-orange"><i data-feather="shield"></i></span>
                <div>
                    <h2>{{ $isArabic ? 'بيانات ولي الأمر' : 'Guardian information' }}</h2>
                    <p>{{ $isArabic ? 'بيانات التواصل الضرورية، خصوصًا للمتدربين صغار السن.' : 'Essential contact details, especially for younger trainees.' }}</p>
                </div>
            </header>
            <div class="student-section-body">
                <div class="student-fields-grid">
                    <div class="student-field">
                        <label for="guardianName">{{ trans('admin.student_management.guardian_name') }}</label>
                        <div class="student-input-shell"><i data-feather="user-check"></i><input type="text" id="guardianName" name="guardian_name" maxlength="255" value="{{ old('guardian_name', $student->guardian_name ?? '') }}" placeholder="{{ $isArabic ? 'اسم ولي الأمر' : 'Guardian name' }}"></div>
                        @error('guardian_name')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                    <div class="student-field">
                        <label for="guardianPhone">{{ trans('admin.student_management.guardian_phone') }}</label>
                        <div class="student-input-shell"><i data-feather="phone-call"></i><input type="tel" id="guardianPhone" name="guardian_phone" maxlength="30" value="{{ old('guardian_phone', $student->guardian_phone ?? '') }}" dir="ltr" placeholder="+20 / +974"></div>
                        @error('guardian_phone')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </section>

        <section class="student-form-section">
            <header class="student-section-header">
                <span class="student-section-icon is-teal"><i data-feather="clipboard"></i></span>
                <div>
                    <h2>{{ $isArabic ? 'الحالة والملاحظات' : 'Status and notes' }}</h2>
                    <p>{{ $isArabic ? 'حالة الطالب وأي معلومات طبية أو إدارية مهمة.' : 'Student status and important medical or administrative information.' }}</p>
                </div>
            </header>
            <div class="student-section-body">
                <div class="student-fields-grid">
                    <div class="student-field"><label>{{ $isArabic ? 'نوع الحساب' : 'Account type' }}</label><div class="student-select-shell"><i data-feather="user"></i><select name="child_type"><option value="">-</option>@foreach(['parent','child','athlete'] as $v)<option value="{{ $v }}" @selected(old('child_type',$student->child_type ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ trans('admin.academies.school_name') }}</label><div class="student-input-shell"><i data-feather="book-open"></i><input name="school_name" value="{{ old('school_name',$student->school_name ?? '') }}"></div></div>
                    <div class="student-field"><label>{{ trans('admin.academies.club_member') }}</label><div class="student-select-shell"><i data-feather="award"></i><select name="club_member"><option value="">-</option>@foreach(['yes','no'] as $v)<option value="{{ $v }}" @selected(old('club_member',$student->club_member ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ trans('admin.academies.coach_preference') }}</label><div class="student-select-shell"><i data-feather="user-check"></i><select name="coach_preference"><option value="">-</option>@foreach(['male','female','not_important'] as $v)<option value="{{ $v }}" @selected(old('coach_preference',$student->coach_preference ?? '')===$v)>{{ $v === 'not_important' ? trans('admin.academies.not_important') : trans('admin.user.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ trans('admin.academies.frequent_attendance') }}</label><div class="student-select-shell"><i data-feather="repeat"></i><select name="frequent_attendance"><option value="">-</option>@foreach(['daily','weekly','monthly'] as $v)<option value="{{ $v }}" @selected(old('frequent_attendance',$student->frequent_attendance ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ $isArabic ? 'تاريخ بدء التدريب' : 'Training start date' }}</label><div class="student-input-shell"><i data-feather="calendar"></i><input type="date" name="start_date" value="{{ old('start_date',isset($student) && $student->start_date ? $student->start_date->format('Y-m-d') : '') }}"></div></div>
                    <div class="student-field"><label>{{ trans('admin.training.country_code') }}</label><div class="student-input-shell"><i data-feather="phone"></i><input name="country_code" dir="ltr" value="{{ old('country_code',$student->country_code ?? '') }}" placeholder="+20"></div></div>
                    <div class="student-field"><label>{{ trans('admin.training.country') }}</label><div class="student-select-shell"><i data-feather="globe"></i><select id="country" name="country_id"><option value="">-</option>@foreach($countries as $country)<option value="{{ $country->id }}" @selected(old('country_id',$student->country_id ?? '')==$country->id)>{{ $country->name }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ trans('admin.city.city') }}</label><div class="student-select-shell"><i data-feather="map"></i><select id="city" name="city_id" data-selected="{{ old('city_id',$student->city_id ?? '') }}"></select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ trans('admin.area.area') }}</label><div class="student-select-shell"><i data-feather="navigation"></i><select id="area" name="area_id" data-selected="{{ old('area_id',$student->area_id ?? '') }}"></select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ $isArabic ? 'صلة ولي الأمر' : 'Guardian relation' }}</label><div class="student-select-shell"><i data-feather="users"></i><select name="relation_with_child"><option value="">-</option>@foreach(['father','mother','brother','sister','guardian'] as $v)<option value="{{ $v }}" @selected(old('relation_with_child',$student->relation_with_child ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ $isArabic ? 'مصدر التعرف علينا' : 'Referral source' }}</label><div class="student-select-shell"><i data-feather="share-2"></i><select name="referral_source"><option value="">-</option>@foreach(['friends','facebook','hagzz_app'] as $v)<option value="{{ $v }}" @selected(old('referral_source',$student->referral_source ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ trans('admin.academies.delivery_service') }}</label><div class="student-select-shell"><i data-feather="truck"></i><select name="delivery_service"><option value="">-</option>@foreach(['yes','no'] as $v)<option value="{{ $v }}" @selected(old('delivery_service',$student->delivery_service ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ $isArabic ? 'هل توجد حالة طبية؟' : 'Medical condition?' }}</label><div class="student-select-shell"><i data-feather="heart"></i><select name="medical_condition"><option value="">-</option>@foreach(['yes','no'] as $v)<option value="{{ $v }}" @selected(old('medical_condition',$student->medical_condition ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field field-full">
                        <label for="studentStatus">{{ trans('admin.student_management.status') }} <b>*</b></label>
                        <div class="student-select-shell"><i data-feather="toggle-right"></i><select id="studentStatus" name="status" required>
                            @foreach(['active', 'inactive', 'suspended'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $student->status ?? 'active') === $status)>{{ trans('admin.student_management.' . $status) }}</option>
                            @endforeach
                        </select><i data-feather="chevron-down"></i></div>
                        @error('status')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                    <div class="student-field">
                        <label for="medicalNotes">{{ trans('admin.student_management.medical_notes') }}</label>
                        <div class="student-textarea-shell is-medical"><i data-feather="heart"></i><textarea id="medicalNotes" name="medical_notes" rows="5" placeholder="{{ $isArabic ? 'الحساسية، الإصابات، الأدوية أو أي تنبيه طبي...' : 'Allergies, injuries, medication or medical alerts...' }}">{{ old('medical_notes', $student->medical_notes ?? '') }}</textarea></div>
                        @error('medical_notes')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                    <div class="student-field">
                        <label for="studentNotes">{{ trans('admin.student_management.notes') }}</label>
                        <div class="student-textarea-shell"><i data-feather="file-text"></i><textarea id="studentNotes" name="notes" rows="5" placeholder="{{ $isArabic ? 'ملاحظات إدارية أو تعليمات خاصة بالطالب...' : 'Administrative notes or special student instructions...' }}">{{ old('notes', $student->notes ?? '') }}</textarea></div>
                        @error('notes')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </section>
    </main>

    <aside class="student-form-aside">
        <div class="student-preview-card">
            <div class="preview-avatar" id="studentAvatar">{{ mb_strtoupper(mb_substr(old('name', $student->name ?? ($isArabic ? 'ط' : 'S')), 0, 1)) }}</div>
            <span>{{ $isArabic ? 'بطاقة الطالب' : 'Student card' }}</span>
            <h2 id="previewStudentName">{{ old('name', $student->name ?? ($isArabic ? 'طالب جديد' : 'New student')) }}</h2>
            <p id="previewStudentContact">-</p>
            <dl>
                <div><dt><i data-feather="calendar"></i>{{ $isArabic ? 'العمر' : 'Age' }}</dt><dd id="previewAge">-</dd></div>
                <div><dt><i data-feather="users"></i>{{ trans('admin.student_management.gender') }}</dt><dd id="previewGender">-</dd></div>
                <div><dt><i data-feather="shield"></i>{{ trans('admin.student_management.guardian') }}</dt><dd id="previewGuardian">-</dd></div>
                <div><dt><i data-feather="phone"></i>{{ trans('admin.student_management.guardian_phone') }}</dt><dd id="previewGuardianPhone">-</dd></div>
            </dl>
            <div class="student-preview-status" id="previewStatus"><i data-feather="check-circle"></i><span>{{ trans('admin.student_management.active') }}</span></div>
        </div>

        <div class="student-safety-card">
            <i data-feather="heart"></i>
            <div><strong>{{ $isArabic ? 'سلامة الطالب أولًا' : 'Student safety first' }}</strong><p>{{ $isArabic ? 'دوّن أي حساسية أو إصابة أو دواء يحتاج المدرب إلى معرفته.' : 'Record any allergy, injury or medication the coach should know about.' }}</p></div>
        </div>
    </aside>
</div>

<footer class="student-form-footer">
    <a href="{{ route('academy.students.index') }}" class="student-cancel-button">{{ trans('admin.student_management.cancel') }}</a>
    <button type="submit" class="student-submit-button"><i data-feather="save"></i><span>{{ trans('admin.student_management.save') }}</span></button>
</footer>
