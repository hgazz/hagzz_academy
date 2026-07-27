@csrf
@php
    $isArabic = app()->getLocale() === 'ar';
    $birthDate = old('birth_date', isset($student) && $student->birth_date ? $student->birth_date->format('Y-m-d') : '');
    $currentStatus = old('status', $student->status ?? 'active');
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
                    <div class="student-field"><label>{{ $isArabic ? 'صلة ولي الأمر' : 'Guardian relation' }}</label><div class="student-select-shell"><i data-feather="users"></i><select name="relation_with_child"><option value="">-</option>@foreach(['father','mother','brother','sister','guardian'] as $v)<option value="{{ $v }}" @selected(old('relation_with_child',$student->relation_with_child ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                </div>
            </div>
        </section>

        <section class="student-form-section">
            <header class="student-section-header">
                <span class="student-section-icon is-teal"><i data-feather="clipboard"></i></span>
                <div>
                    <h2>{{ $isArabic ? 'الحالة والملاحظات' : 'Status and notes' }}</h2>
                    <p>{{ $isArabic ? 'حالة الطالب وتفاصيل الإقامة، المستندات، والنادي.' : 'Student status, location, documents and club details.' }}</p>
                </div>
            </header>
            <div class="student-section-body">
                <div class="student-fields-grid">
                    {{-- Status Toggle Component --}}
                    <div class="student-field field-full">
                        <label for="studentStatus">{{ trans('admin.student_management.status') }} <b>*</b></label>
                        <input type="hidden" name="status" id="studentStatus" value="{{ $currentStatus }}" required>
                        <div class="status-toggle-selector" id="statusToggleSelector">
                            <button type="button" class="status-toggle-pill is-active {{ $currentStatus === 'active' ? 'selected' : '' }}" data-value="active">
                                <i data-feather="check-circle"></i>
                                <span>{{ trans('admin.student_management.active') }}</span>
                            </button>
                            <button type="button" class="status-toggle-pill is-inactive {{ $currentStatus === 'inactive' ? 'selected' : '' }}" data-value="inactive">
                                <i data-feather="minus-circle"></i>
                                <span>{{ trans('admin.student_management.inactive') }}</span>
                            </button>
                            <button type="button" class="status-toggle-pill is-suspended {{ $currentStatus === 'suspended' ? 'selected' : '' }}" data-value="suspended">
                                <i data-feather="alert-octagon"></i>
                                <span>{{ trans('admin.student_management.suspended') }}</span>
                            </button>
                        </div>
                        @error('status')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    {{-- Location Fields --}}
                    <input type="hidden" name="country_code" id="countryCodeInput" value="{{ old('country_code', $student->country_code ?? '') }}">
                    <div class="student-field">
                        <label for="country">{{ trans('admin.training.country') }}</label>
                        <div class="student-select-shell"><i data-feather="globe"></i><select id="country" name="country_id"><option value="">-</option>@foreach($countries as $country)<option value="{{ $country->id }}" @selected(old('country_id',$student->country_id ?? '')==$country->id)>{{ $country->name }}</option>@endforeach</select><i data-feather="chevron-down"></i></div>
                    </div>
                    <div class="student-field">
                        <label for="city">{{ trans('admin.city.city') }}</label>
                        <div class="student-select-shell"><i data-feather="map"></i><select id="city" name="city_id" data-selected="{{ old('city_id',$student->city_id ?? '') }}"></select><i data-feather="chevron-down"></i></div>
                    </div>
                    <div class="student-field">
                        <label for="area">{{ trans('admin.area.area') }}</label>
                        <div class="student-select-shell"><i data-feather="navigation"></i><select id="area" name="area_id" data-selected="{{ old('area_id',$student->area_id ?? '') }}"></select><i data-feather="chevron-down"></i></div>
                    </div>

                    {{-- Account details --}}
                    <div class="student-field"><label>{{ $isArabic ? 'نوع الحساب' : 'Account type' }}</label><div class="student-select-shell"><i data-feather="user"></i><select name="child_type"><option value="">-</option>@foreach(['parent','child','athlete'] as $v)<option value="{{ $v }}" @selected(old('child_type',$student->child_type ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ trans('admin.academies.school_name') }}</label><div class="student-input-shell"><i data-feather="book-open"></i><input name="school_name" value="{{ old('school_name',$student->school_name ?? '') }}"></div></div>

                    {{-- Club Member & Membership Card Details --}}
                    <div class="student-field">
                        <label for="clubMemberSelect">{{ trans('admin.academies.club_member') }}</label>
                        <div class="student-select-shell"><i data-feather="award"></i><select name="club_member" id="clubMemberSelect"><option value="">-</option>@foreach(['yes','no'] as $v)<option value="{{ $v }}" @selected(old('club_member',$student->club_member ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div>
                    </div>

                    <div class="student-field field-full club-card-box" id="clubDetailsBox" style="{{ old('club_member', $student->club_member ?? '') === 'yes' ? 'display:block;' : 'display:none;' }}">
                        <div class="club-card-inner">
                            <div class="club-card-title"><i data-feather="credit-card"></i><span>{{ $isArabic ? 'تفاصيل عضوية النادي' : 'Club membership details' }}</span></div>
                            <div class="student-fields-grid">
                                <div class="student-field">
                                    <label for="clubCardNumber">{{ $isArabic ? 'رقم عضوية النادي' : 'Club membership number' }}</label>
                                    <div class="student-input-shell"><i data-feather="hash"></i><input type="text" id="clubCardNumber" name="club_card_number" value="{{ old('club_card_number', $student->club_card_number ?? '') }}" placeholder="{{ $isArabic ? 'مثال: 458920' : 'e.g. 458920' }}"></div>
                                </div>
                                <div class="student-field">
                                    <label for="clubCardFileInput">{{ $isArabic ? 'صورة / مستند كارنيه النادي' : 'Club card photo / document' }}</label>
                                    <div class="student-file-shell">
                                        <i data-feather="upload-cloud"></i>
                                        <input type="file" id="clubCardFileInput" name="club_card_file" accept="image/*,.pdf">
                                        <span class="file-label-text" id="clubCardFileText">{{ $isArabic ? 'اختر ملف الكارنيه (صورة أو PDF)' : 'Upload card (Image or PDF)' }}</span>
                                    </div>
                                    @if(isset($student) && $student->club_card_file)
                                        <a href="{{ asset($student->club_card_file) }}" target="_blank" class="existing-file-badge"><i data-feather="external-link"></i>{{ $isArabic ? 'عرض كارنيه النادي الحالي' : 'View current club card' }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Coach preference & Attendance --}}
                    <div class="student-field"><label>{{ trans('admin.academies.coach_preference') }}</label><div class="student-select-shell"><i data-feather="user-check"></i><select name="coach_preference"><option value="">-</option>@foreach(['male','female','not_important'] as $v)<option value="{{ $v }}" @selected(old('coach_preference',$student->coach_preference ?? '')===$v)>{{ $v === 'not_important' ? trans('admin.academies.not_important') : trans('admin.user.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ trans('admin.academies.frequent_attendance') }}</label><div class="student-select-shell"><i data-feather="repeat"></i><select name="frequent_attendance"><option value="">-</option>@foreach(['daily','weekly','monthly'] as $v)<option value="{{ $v }}" @selected(old('frequent_attendance',$student->frequent_attendance ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ $isArabic ? 'تاريخ بدء التدريب' : 'Training start date' }}</label><div class="student-input-shell"><i data-feather="calendar"></i><input type="date" name="start_date" value="{{ old('start_date',isset($student) && $student->start_date ? $student->start_date->format('Y-m-d') : '') }}"></div></div>
                    <div class="student-field"><label>{{ $isArabic ? 'مصدر التعرف علينا' : 'Referral source' }}</label><div class="student-select-shell"><i data-feather="share-2"></i><select name="referral_source"><option value="">-</option>@foreach(['friends','facebook','hagzz_app'] as $v)<option value="{{ $v }}" @selected(old('referral_source',$student->referral_source ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ trans('admin.academies.delivery_service') }}</label><div class="student-select-shell"><i data-feather="truck"></i><select name="delivery_service"><option value="">-</option>@foreach(['yes','no'] as $v)<option value="{{ $v }}" @selected(old('delivery_service',$student->delivery_service ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ $isArabic ? 'هل توجد حالة طبية؟' : 'Medical condition?' }}</label><div class="student-select-shell"><i data-feather="heart"></i><select name="medical_condition"><option value="">-</option>@foreach(['yes','no'] as $v)<option value="{{ $v }}" @selected(old('medical_condition',$student->medical_condition ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>

                    {{-- Medical Notes & Certificate Document Upload --}}
                    <div class="student-field field-full">
                        <label for="medicalCertificateInput">{{ $isArabic ? 'رفع مستند / شهادة الفحص الطبي' : 'Medical certificate document' }}</label>
                        <div class="student-file-shell">
                            <i data-feather="file-text"></i>
                            <input type="file" id="medicalCertificateInput" name="medical_certificate" accept="image/*,.pdf">
                            <span class="file-label-text" id="medicalCertFileText">{{ $isArabic ? 'اختر ملف الشهادة الطبية (PDF أو صورة)' : 'Select medical certificate (PDF or Image)' }}</span>
                        </div>
                        @if(isset($student) && $student->medical_certificate)
                            <a href="{{ asset($student->medical_certificate) }}" target="_blank" class="existing-file-badge"><i data-feather="external-link"></i>{{ $isArabic ? 'عرض الشهادة الطبية الحالية' : 'View current medical certificate' }}</a>
                        @endif
                        @error('medical_certificate')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="student-field field-full">
                        <label for="medicalNotes">{{ trans('admin.student_management.medical_notes') }}</label>
                        <div class="student-textarea-shell is-medical"><i data-feather="heart"></i><textarea id="medicalNotes" name="medical_notes" rows="4" placeholder="{{ $isArabic ? 'الحساسية، الإصابات، الأدوية أو أي تنبيه طبي...' : 'Allergies, injuries, medication or medical alerts...' }}">{{ old('medical_notes', $student->medical_notes ?? '') }}</textarea></div>
                        @error('medical_notes')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="student-field field-full">
                        <label for="studentNotes">{{ trans('admin.student_management.notes') }}</label>
                        <div class="student-textarea-shell"><i data-feather="file-text"></i><textarea id="studentNotes" name="notes" rows="4" placeholder="{{ $isArabic ? 'ملاحظات إدارية أو تعليمات خاصة بالطالب...' : 'Administrative notes or special student instructions...' }}">{{ old('notes', $student->notes ?? '') }}</textarea></div>
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
            <div class="student-preview-status" id="previewStatus" data-status="{{ $currentStatus }}"><i data-feather="check-circle"></i><span>{{ trans('admin.student_management.' . $currentStatus) }}</span></div>
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
