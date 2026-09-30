@csrf
@php
    $isArabic = app()->getLocale() === 'ar';
    $birthDate = old('birth_date', isset($student) && $student->birth_date ? $student->birth_date->format('Y-m-d') : '');
    $currentStatus = old('status', $student->status ?? 'active');

    $selectedCountryId = old('country_id', $student->country_id ?? null);
    if (!$selectedCountryId) {
        $egyptCountry = $countries->first(fn ($c) => strtoupper($c->iso2 ?? '') === 'EG' || str_contains($c->name, 'مصر') || str_contains(strtolower($c->name), 'egypt'));
        $selectedCountryId = $egyptCountry?->id;
    }

    $defaultAvatar = !empty($isGymFacility)
        ? asset('assetsAdmin/img/gym-member-avatar.svg')
        : (!empty($isHealthFacility)
            ? asset('assetsAdmin/img/gym-member-avatar.svg')
            : asset('assetsAdmin/img/default-user-male.webp'));

    $memberTerm = !empty($isGymFacility) ? 'العضو' : (!empty($isHealthFacility) ? 'العميل' : 'الطالب');
    $memberGenitiveTerm = !empty($isGymFacility) ? 'للعضو' : (!empty($isHealthFacility) ? 'للعميل' : 'للطالب');
    $memberNewTerm = !empty($isGymFacility) ? 'عضو جديد' : (!empty($isHealthFacility) ? 'عميل جديد' : 'طالب جديد');
    $memberCardTerm = !empty($isGymFacility) ? 'بطاقة العضو' : (!empty($isHealthFacility) ? 'بطاقة العميل' : 'بطاقة الطالب');
    $avatarLetter = (!empty($isGymFacility) || !empty($isHealthFacility)) ? 'ع' : 'ط';
@endphp

<div class="student-form-layout">
    <main class="student-form-main">
        <section class="student-form-section">
            <header class="student-section-header">
                <span class="student-section-icon"><i data-feather="user"></i></span>
                <div>
                    <h2>{{ $isArabic ? 'البيانات الشخصية' : 'Personal information' }}</h2>
                    <p>{{ $isArabic ? (!empty($isGymFacility) ? 'المعلومات الأساسية ووسائل التواصل الخاصة بالعضو.' : (!empty($isHealthFacility) ? 'المعلومات الأساسية ووسائل التواصل الخاصة بالعميل.' : 'المعلومات الأساسية ووسائل التواصل الخاصة بالطالب.')) : 'Basic profile information and contact details.' }}</p>
                </div>
            </header>
            <div class="student-section-body">
                <div class="student-avatar-picker-box mb-4 p-3 rounded-3 bg-white border d-flex align-items-center gap-3 shadow-sm">
                    <div class="position-relative" style="width: 80px; height: 80px; min-width: 80px;">
                        <img id="avatarPreviewImage"
                             src="{{ isset($student) ? $student->avatarUrl() : $defaultAvatar }}"
                             alt="{{ $memberTerm }}"
                             class="rounded-circle border border-2 border-primary shadow-sm"
                             style="width: 80px; height: 80px; object-fit: cover;"
                             onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';">
                    </div>
                    <div class="flex-grow-1">
                        <label for="studentImageInput" class="form-label fw-bold mb-1 d-block text-dark" style="cursor: pointer;">
                            <i data-feather="camera" style="width: 16px; height: 16px;" class="me-1 text-primary"></i>
                            {{ $isArabic ? "الصورة الشخصية {$memberGenitiveTerm}" : 'Profile Photo' }}
                        </label>
                        <span class="text-muted d-block small mb-2">
                            {{ $isArabic ? (!empty($isGymFacility) ? 'ارفع صورة شخصية حديثة للعضو ليتم اعتمادها في ملفه وبطاقة عضويته وسجل الدخول.' : (!empty($isHealthFacility) ? 'ارفع صورة شخصية حديثة للعميل ليتم اعتمادها في ملفه الصحي.' : 'ارفع صورة شخصية حديثة للاعب ليتم استبدال الصورة الحالية بها في ملفه وكارنيه النادي.')) : 'Upload profile photo to replace the current image across profile and cards.' }}
                        </span>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <input type="file" id="studentImageInput" name="image" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg,image/webp" style="max-width: 300px;" onchange="previewStudentAvatar(this)">
                            @if(isset($student) && $student->image)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                    <i data-feather="check" style="width: 12px; height: 12px;"></i> {{ $isArabic ? 'توجد صورة مخصصة' : 'Custom photo set' }}
                                </span>
                            @endif
                        </div>
                        @error('image')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="student-fields-grid">
                    <div class="student-field field-full">
                        <label for="studentName">{{ trans('admin.student_management.name') }} <b>*</b></label>
                        <div class="student-input-shell"><i data-feather="user"></i><input type="text" id="studentName" name="name" maxlength="255" value="{{ old('name', $student->name ?? '') }}" required placeholder="{{ $isArabic ? (!empty($isGymFacility) ? 'اسم العضو بالكامل' : (!empty($isHealthFacility) ? 'اسم العميل بالكامل' : 'اسم الطالب بالكامل')) : 'Full name' }}"></div>
                        @error('name')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                    <div class="student-field">
                        <label for="studentPhone">{{ trans('admin.student_management.phone') }}</label>
                        <div class="student-input-shell"><i data-feather="phone"></i><input type="tel" id="studentPhone" name="phone" maxlength="30" value="{{ old('phone', $student->phone ?? '') }}" dir="ltr" placeholder="+20 / +974"></div>
                        @error('phone')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                    <div class="student-field">
                        <label for="studentEmail">{{ trans('admin.student_management.email') }}</label>
                        <div class="student-input-shell"><i data-feather="mail"></i><input type="email" id="studentEmail" name="email" maxlength="255" value="{{ old('email', $student->email ?? '') }}" dir="ltr" placeholder="member@example.com"></div>
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
                        <label for="studentBirthDate">{{ $isArabic ? 'تاريخ الميلاد' : trans('admin.student_management.birth_date') }}</label>
                        <div class="student-input-shell"><i data-feather="calendar"></i><input type="date" id="studentBirthDate" name="birth_date" value="{{ $birthDate }}"></div>
                        @error('birth_date')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </section>

        @if(empty($isGymFacility) && empty($isHealthFacility))
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
        @else
        <section class="student-form-section">
            <header class="student-section-header">
                <span class="student-section-icon is-orange"><i data-feather="phone-call"></i></span>
                <div>
                    <h2>{{ $isArabic ? 'بيانات الطوارئ (اختياري)' : 'Emergency Contact (Optional)' }}</h2>
                    <p>{{ $isArabic ? 'بيانات شخص قريب للتواصل معه في الحالات الطارئة أو الاستفسارات الطبية العاجلة.' : 'Contact person details for emergency or urgent health inquiries.' }}</p>
                </div>
            </header>
            <div class="student-section-body">
                <div class="student-fields-grid">
                    <div class="student-field">
                        <label for="guardianName">{{ $isArabic ? 'اسم جهة الاتصال عند الطوارئ' : 'Emergency Contact Name' }}</label>
                        <div class="student-input-shell"><i data-feather="user-check"></i><input type="text" id="guardianName" name="guardian_name" maxlength="255" value="{{ old('guardian_name', $student->guardian_name ?? '') }}" placeholder="{{ $isArabic ? 'مثال: الأخ، الزوج/ـة، الصديق...' : 'e.g. Spouse, Brother, Friend...' }}"></div>
                        @error('guardian_name')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                    <div class="student-field">
                        <label for="guardianPhone">{{ $isArabic ? 'هاتف الطوارئ' : 'Emergency Contact Phone' }}</label>
                        <div class="student-input-shell"><i data-feather="phone-call"></i><input type="tel" id="guardianPhone" name="guardian_phone" maxlength="30" value="{{ old('guardian_phone', $student->guardian_phone ?? '') }}" dir="ltr" placeholder="+20 / +974"></div>
                        @error('guardian_phone')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                    <div class="student-field">
                        <label>{{ $isArabic ? 'صلة القرابة / العلاقة' : 'Relationship' }}</label>
                        <div class="student-select-shell">
                            <i data-feather="users"></i>
                            <select name="relation_with_child">
                                <option value="">- {{ $isArabic ? 'حدد الصلة' : 'Select' }} -</option>
                                <option value="brother" @selected(old('relation_with_child', $student->relation_with_child ?? '') === 'brother')>{{ $isArabic ? 'أخ / أخت' : 'Brother / Sister' }}</option>
                                <option value="guardian" @selected(old('relation_with_child', $student->relation_with_child ?? '') === 'guardian')>{{ $isArabic ? 'زوج / زوجة / قريب' : 'Spouse / Relative' }}</option>
                                <option value="father" @selected(old('relation_with_child', $student->relation_with_child ?? '') === 'father')>{{ $isArabic ? 'أب' : 'Father' }}</option>
                                <option value="mother" @selected(old('relation_with_child', $student->relation_with_child ?? '') === 'mother')>{{ $isArabic ? 'أم' : 'Mother' }}</option>
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <section class="student-form-section">
            <header class="student-section-header">
                <span class="student-section-icon is-teal"><i data-feather="{{ !empty($isGymFacility) ? 'activity' : 'clipboard' }}"></i></span>
                <div>
                    <h2>{{ $isArabic ? (!empty($isGymFacility) ? 'بيانات اللياقة والعضوية' : 'الحالة والملاحظات') : 'Membership & Fitness details' }}</h2>
                    <p>{{ $isArabic ? (!empty($isGymFacility) ? 'مستوى اللياقة، تفضيل التدريب، الأهداف الصحية، والموقع.' : 'حالة الطالب وتفاصيل الإقامة، المستندات، والنادي.') : 'Status, fitness details, location, and records.' }}</p>
                </div>
            </header>
            <div class="student-section-body">
                <div class="student-fields-grid">
                    {{-- Status Toggle Component --}}
                    <div class="student-field field-full">
                        <label for="studentStatus">{{ $isArabic ? (!empty($isGymFacility) ? 'حالة العضوية' : trans('admin.student_management.status')) : 'Membership Status' }} <b>*</b></label>
                        <input type="hidden" name="status" id="studentStatus" value="{{ $currentStatus }}" required>
                        <div class="status-toggle-selector" id="statusToggleSelector">
                            <button type="button" class="status-toggle-pill is-active {{ $currentStatus === 'active' ? 'selected' : '' }}" data-value="active">
                                <i data-feather="check-circle"></i>
                                <span>{{ $isArabic ? (!empty($isGymFacility) ? 'عضو نشط' : trans('admin.student_management.active')) : 'Active' }}</span>
                            </button>
                            <button type="button" class="status-toggle-pill is-inactive {{ $currentStatus === 'inactive' ? 'selected' : '' }}" data-value="inactive">
                                <i data-feather="minus-circle"></i>
                                <span>{{ $isArabic ? (!empty($isGymFacility) ? 'غير نشط' : trans('admin.student_management.inactive')) : 'Inactive' }}</span>
                            </button>
                            <button type="button" class="status-toggle-pill is-suspended {{ $currentStatus === 'suspended' ? 'selected' : '' }}" data-value="suspended">
                                <i data-feather="alert-octagon"></i>
                                <span>{{ $isArabic ? (!empty($isGymFacility) ? 'مجمّد / موقوف' : trans('admin.student_management.suspended')) : 'Suspended' }}</span>
                            </button>
                        </div>
                        @error('status')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    {{-- Location Fields --}}
                    <input type="hidden" name="country_code" id="countryCodeInput" value="{{ old('country_code', $student->country_code ?? '+20') }}">
                    <div class="student-field">
                        <label for="country">{{ trans('admin.training.country') }}</label>
                        <div class="student-select-shell"><i data-feather="globe"></i><select id="country" name="country_id"><option value="">-</option>@foreach($countries as $country)<option value="{{ $country->id }}" data-iso2="{{ strtoupper($country->iso2 ?? '') }}" @selected($selectedCountryId == $country->id)>{{ $country->name }}</option>@endforeach</select><i data-feather="chevron-down"></i></div>
                    </div>
                    <div class="student-field">
                        <label for="city">{{ trans('admin.city.city') }}</label>
                        <div class="student-select-shell"><i data-feather="map"></i><select id="city" name="city_id" data-selected="{{ old('city_id',$student->city_id ?? '') }}"></select><i data-feather="chevron-down"></i></div>
                        <div class="student-input-shell" id="customCityShell" style="display: none; margin-top: 8px;"><i data-feather="edit-3"></i><input type="text" id="customCityInput" name="custom_city_name" value="{{ old('custom_city_name') }}" placeholder="{{ $isArabic ? 'اكتب اسم المدينة يدوياً...' : 'Type custom city name...' }}"></div>
                    </div>
                    <div class="student-field">
                        <label for="area">{{ trans('admin.area.area') }}</label>
                        <div class="student-select-shell"><i data-feather="navigation"></i><select id="area" name="area_id" data-selected="{{ old('area_id',$student->area_id ?? '') }}"></select><i data-feather="chevron-down"></i></div>
                        <div class="student-input-shell" id="customAreaShell" style="display: none; margin-top: 8px;"><i data-feather="edit-3"></i><input type="text" id="customAreaInput" name="custom_area_name" value="{{ old('custom_area_name') }}" placeholder="{{ $isArabic ? 'اكتب اسم المنطقة يدوياً...' : 'Type custom area name...' }}"></div>
                    </div>

                    {{-- Fitness / Account Details --}}
                    @if(!empty($isGymFacility))
                    <div class="student-field">
                        <label>{{ $isArabic ? 'مستوى اللياقة والخبرة بالجيم' : 'Fitness Level' }}</label>
                        <div class="student-select-shell">
                            <i data-feather="trending-up"></i>
                            <select name="child_type">
                                <option value="">- {{ $isArabic ? 'اختر مستوى اللياقة' : 'Select Level' }} -</option>
                                <option value="child" @selected(old('child_type', $student->child_type ?? '') === 'child')>{{ $isArabic ? 'مبتدئ (بداية التمارين)' : 'Beginner' }}</option>
                                <option value="parent" @selected(old('child_type', $student->child_type ?? '') === 'parent')>{{ $isArabic ? 'متوسط (ممارسة متقطعة / سابقة)' : 'Intermediate' }}</option>
                                <option value="athlete" @selected(old('child_type', $student->child_type ?? '') === 'athlete')>{{ $isArabic ? 'متقدم (رياضي متمرس / بناء أجسام)' : 'Advanced / Athlete' }}</option>
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                    </div>
                    <div class="student-field">
                        <label>{{ $isArabic ? 'المهنة أو جهة العمل (اختياري)' : 'Occupation / Workplace' }}</label>
                        <div class="student-input-shell">
                            <i data-feather="briefcase"></i>
                            <input name="school_name" value="{{ old('school_name', $student->school_name ?? '') }}" placeholder="{{ $isArabic ? 'مثال: مهندس، موظف، طالب جامعي...' : 'e.g. Engineer, Student...' }}">
                        </div>
                    </div>
                    @else
                    <div class="student-field"><label>{{ $isArabic ? 'نوع الحساب' : 'Account type' }}</label><div class="student-select-shell"><i data-feather="user"></i><select name="child_type"><option value="">-</option>@foreach(['parent','child','athlete'] as $v)<option value="{{ $v }}" @selected(old('child_type',$student->child_type ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    <div class="student-field"><label>{{ trans('admin.academies.school_name') }}</label><div class="student-input-shell"><i data-feather="book-open"></i><input name="school_name" value="{{ old('school_name',$student->school_name ?? '') }}"></div></div>
                    @endif

                    {{-- Club Member & Membership Card Details --}}
                    <div class="student-field">
                        <label for="clubMemberSelect">{{ $isArabic ? (!empty($isGymFacility) ? 'هل سبقت لك العضوية في جيم أو نادٍ آخر؟' : trans('admin.academies.club_member')) : 'Previous Member?' }}</label>
                        <div class="student-select-shell">
                            <i data-feather="award"></i>
                            <select name="club_member" id="clubMemberSelect">
                                <option value="">-</option>
                                <option value="no" @selected(old('club_member', $student->club_member ?? 'no') === 'no')>{{ $isArabic ? 'لا' : 'No' }}</option>
                                <option value="yes" @selected(old('club_member', $student->club_member ?? '') === 'yes')>{{ $isArabic ? 'نعم' : 'Yes' }}</option>
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                    </div>

                    <div class="student-field field-full club-card-box" id="clubDetailsBox" style="{{ old('club_member', $student->club_member ?? '') === 'yes' ? 'display:block;' : 'display:none;' }}">
                        <div class="club-card-inner">
                            <div class="club-card-title">
                                <i data-feather="credit-card"></i>
                                <span>{{ $isArabic ? (!empty($isGymFacility) ? 'تفاصيل العضوية السابقة / الكارنيه' : 'تفاصيل عضوية النادي') : 'Membership details' }}</span>
                            </div>
                            <div class="student-fields-grid">
                                <div class="student-field">
                                    <label for="previousClubName">{{ $isArabic ? (!empty($isGymFacility) ? 'اسم الجيم أو النادي السابق / الآخر' : 'اسم النادي الآخر') : 'Previous Gym / Club Name' }}</label>
                                    <div class="student-input-shell">
                                        <i data-feather="shield"></i>
                                        <input type="text" id="previousClubName" name="previous_club_name" value="{{ old('previous_club_name', $student->previous_club_name ?? '') }}" placeholder="{{ $isArabic ? 'مثال: جولدز جيم، فتنس تايم، نادي الصيد...' : 'e.g. Gold\'s Gym, Fitness Time...' }}">
                                    </div>
                                    @error('previous_club_name')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                                </div>
                                <div class="student-field">
                                    <label for="clubCardNumber">{{ $isArabic ? (!empty($isGymFacility) ? 'رقم العضوية أو الكارنيه السابق' : 'رقم عضوية النادي') : 'Card Number' }}</label>
                                    <div class="student-input-shell"><i data-feather="hash"></i><input type="text" id="clubCardNumber" name="club_card_number" value="{{ old('club_card_number', $student->club_card_number ?? '') }}" placeholder="{{ $isArabic ? 'مثال: 458920' : 'e.g. 458920' }}"></div>
                                    @error('club_card_number')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                                </div>
                                <div class="student-field field-full">
                                    <label for="clubCardFileInput">{{ $isArabic ? (!empty($isGymFacility) ? 'صورة كارنيه العضوية السابق (اختياري)' : 'صورة / مستند كارنيه النادي') : 'Card file' }}</label>
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
                    <div class="student-field">
                        <label>{{ $isArabic ? (!empty($isGymFacility) ? 'تفضيل المدرب الخاص (PT)' : trans('admin.academies.coach_preference')) : 'Coach preference' }}</label>
                        <div class="student-select-shell">
                            <i data-feather="user-check"></i>
                            <select name="coach_preference">
                                <option value="">-</option>
                                <option value="male" @selected(old('coach_preference', $student->coach_preference ?? '') === 'male')>{{ $isArabic ? 'كابتن / مدرب (ذكر)' : 'Male Coach' }}</option>
                                <option value="female" @selected(old('coach_preference', $student->coach_preference ?? '') === 'female')>{{ $isArabic ? 'كابتن / مدربة (أنثى)' : 'Female Coach' }}</option>
                                <option value="not_important" @selected(old('coach_preference', $student->coach_preference ?? '') === 'not_important')>{{ $isArabic ? 'تدريب عام / غير محدد' : 'No preference' }}</option>
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                    </div>
                    <div class="student-field">
                        <label>{{ $isArabic ? (!empty($isGymFacility) ? 'معدل الحضور والتدريب المستهدف' : trans('admin.academies.frequent_attendance')) : 'Attendance Frequency' }}</label>
                        <div class="student-select-shell">
                            <i data-feather="repeat"></i>
                            <select name="frequent_attendance">
                                <option value="">-</option>
                                <option value="daily" @selected(old('frequent_attendance', $student->frequent_attendance ?? '') === 'daily')>{{ $isArabic ? (!empty($isGymFacility) ? 'يومي (تدريب مكثف 5-6 أيام)' : trans('admin.academies.daily')) : 'Daily' }}</option>
                                <option value="weekly" @selected(old('frequent_attendance', $student->frequent_attendance ?? '') === 'weekly')>{{ $isArabic ? (!empty($isGymFacility) ? '3-4 أيام أسبوعياً' : trans('admin.academies.weekly')) : 'Weekly' }}</option>
                                <option value="monthly" @selected(old('frequent_attendance', $student->frequent_attendance ?? '') === 'monthly')>{{ $isArabic ? (!empty($isGymFacility) ? 'دوري / مرن شهرياً' : trans('admin.academies.monthly')) : 'Monthly' }}</option>
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                    </div>
                    <div class="student-field">
                        <label>{{ $isArabic ? (!empty($isGymFacility) ? 'تاريخ بدء الاشتراك والتدريب' : 'تاريخ بدء التدريب') : 'Start date' }}</label>
                        <div class="student-input-shell"><i data-feather="calendar"></i><input type="date" name="start_date" value="{{ old('start_date', isset($student) && $student->start_date ? $student->start_date->format('Y-m-d') : date('Y-m-d')) }}"></div>
                    </div>
                    <div class="student-field">
                        <label>{{ $isArabic ? (!empty($isGymFacility) ? 'كيف تعرفت على الجيم؟' : 'مصدر التعرف علينا') : 'Referral source' }}</label>
                        <div class="student-select-shell">
                            <i data-feather="share-2"></i>
                            <select name="referral_source">
                                <option value="">-</option>
                                <option value="friends" @selected(old('referral_source', $student->referral_source ?? '') === 'friends')>{{ $isArabic ? 'ترشيح من صديق / عضو' : 'Friends / Referral' }}</option>
                                <option value="facebook" @selected(old('referral_source', $student->referral_source ?? '') === 'facebook')>{{ $isArabic ? 'وسائل التواصل الاجتماعي' : 'Social media' }}</option>
                                <option value="hagzz_app" @selected(old('referral_source', $student->referral_source ?? '') === 'hagzz_app')>{{ $isArabic ? 'تطبيق Hagzz الرياضي' : 'Hagzz App' }}</option>
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                    </div>

                    @if(empty($isGymFacility))
                    <div class="student-field"><label>{{ trans('admin.academies.delivery_service') }}</label><div class="student-select-shell"><i data-feather="truck"></i><select name="delivery_service"><option value="">-</option>@foreach(['yes','no'] as $v)<option value="{{ $v }}" @selected(old('delivery_service',$student->delivery_service ?? '')===$v)>{{ trans('admin.academies.'.$v) }}</option>@endforeach</select><i data-feather="chevron-down"></i></div></div>
                    @endif

                    <div class="student-field">
                        <label for="medicalConditionSelect">{{ $isArabic ? (!empty($isGymFacility) ? 'هل توجد إصابات أو حالات صحية سابقة؟' : 'هل توجد حالة طبية أو إصابات؟') : 'Medical condition / Injuries?' }}</label>
                        <div class="student-select-shell">
                            <i data-feather="heart"></i>
                            <select name="medical_condition" id="medicalConditionSelect">
                                <option value="no" @selected(old('medical_condition', $student->medical_condition ?? 'no') === 'no')>{{ $isArabic ? 'لا (سليم والحمد لله)' : 'No' }}</option>
                                <option value="yes" @selected(old('medical_condition', $student->medical_condition ?? '') === 'yes')>{{ $isArabic ? 'نعم (توجد إصابات أو موانع)' : 'Yes' }}</option>
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                    </div>

                    <div class="student-field" id="injuryTypeBox" style="{{ old('medical_condition', $student->medical_condition ?? '') === 'yes' ? 'display:block;' : 'display:none;' }}">
                        <label for="injuryTypeInput">{{ $isArabic ? 'نوع الإصابة أو الحالة الصحية' : 'Injury / Condition Type' }}</label>
                        <div class="student-input-shell">
                            <i data-feather="alert-circle"></i>
                            <input type="text" id="injuryTypeInput" name="injury_type" list="injurySuggestions" value="{{ old('injury_type', $student->injury_type ?? '') }}" placeholder="{{ $isArabic ? 'مثال: انزلاق غضروفي، رباط صليبي، خلع كتف...' : 'e.g. Lumbar disc, ACL tear, Shoulder dislocation...' }}">
                        </div>
                        <datalist id="injurySuggestions">
                            <option value="{{ $isArabic ? 'انزلاق غضروفي / آلام أسفل الظهر' : 'Herniated Disc / Lower Back Pain' }}"></option>
                            <option value="{{ $isArabic ? 'إصابة بالركبة / رباط صليبي أو غضروف' : 'Knee Injury / ACL / Meniscus' }}"></option>
                            <option value="{{ $isArabic ? 'إصابة بالكتف / أوتار الكتف' : 'Shoulder / Rotator Cuff' }}"></option>
                            <option value="{{ $isArabic ? 'تمزق عضلي سابق' : 'Muscle Tear' }}"></option>
                            <option value="{{ $isArabic ? 'كسر عظمي سابق أو شرائح ومسامير' : 'Bone Fracture' }}"></option>
                            <option value="{{ $isArabic ? 'عملية جراحية سابقة' : 'Previous Surgery' }}"></option>
                            <option value="{{ $isArabic ? 'ضغط دم مرتفع / مشاكل قلبية' : 'Hypertension' }}"></option>
                            <option value="{{ $isArabic ? 'مرض السكري' : 'Diabetes' }}"></option>
                        </datalist>
                        @error('injury_type')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="student-field">
                        <label for="hasAllergySelect">{{ $isArabic ? 'هل تعاني من أي نوع حساسية؟' : 'Any Allergies?' }}</label>
                        <div class="student-select-shell">
                            <i data-feather="shield"></i>
                            <select name="has_allergy" id="hasAllergySelect">
                                <option value="no" @selected(old('has_allergy', $student->has_allergy ?? 'no') === 'no')>{{ $isArabic ? 'لا توجد حساسية' : 'No Allergies' }}</option>
                                <option value="yes" @selected(old('has_allergy', $student->has_allergy ?? '') === 'yes')>{{ $isArabic ? 'نعم (توجد حساسية)' : 'Yes' }}</option>
                            </select>
                            <i data-feather="chevron-down"></i>
                        </div>
                    </div>

                    <div class="student-field" id="allergyTypeBox" style="{{ old('has_allergy', $student->has_allergy ?? '') === 'yes' ? 'display:block;' : 'display:none;' }}">
                        <label for="allergyTypeInput">{{ $isArabic ? 'نوع وتفاصيل الحساسية' : 'Allergy Type & Details' }}</label>
                        <div class="student-input-shell">
                            <i data-feather="alert-triangle"></i>
                            <input type="text" id="allergyTypeInput" name="allergy_type" list="allergySuggestions" value="{{ old('allergy_type', $student->allergy_type ?? '') }}" placeholder="{{ $isArabic ? 'مثال: حساسية صدر/ربو، حساسية أدوية (بنسلين)، حساسية لاكتوز...' : 'e.g. Asthma, Penicillin, Lactose, Nuts...' }}">
                        </div>
                        <datalist id="allergySuggestions">
                            <option value="{{ $isArabic ? 'حساسية صدرية / ربو (Asthma)' : 'Asthma / Respiratory Allergy' }}"></option>
                            <option value="{{ $isArabic ? 'حساسية أدوية (مثل البنسلين أو مسكنات)' : 'Drug Allergy (Penicillin)' }}"></option>
                            <option value="{{ $isArabic ? 'حساسية أطعمة / لاكتوز / جلوتين' : 'Food / Lactose Allergy' }}"></option>
                            <option value="{{ $isArabic ? 'حساسية مكملات غذائية أو واي بروتين' : 'Supplements / Whey Allergy' }}"></option>
                            <option value="{{ $isArabic ? 'حساسية جلدية / تلامسية' : 'Skin / Contact Allergy' }}"></option>
                        </datalist>
                        @error('allergy_type')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    {{-- Medical Notes & Document Upload --}}
                    <div class="student-field field-full">
                        <label for="medicalCertificateInput">{{ $isArabic ? (!empty($isGymFacility) ? 'رفع تقرير طبي أو نتائج الفحص البدني (InBody / تقرير)' : 'رفع مستند / شهادة الفحص الطبي') : 'Medical / InBody report' }}</label>
                        <div class="student-file-shell">
                            <i data-feather="file-text"></i>
                            <input type="file" id="medicalCertificateInput" name="medical_certificate" accept="image/*,.pdf">
                            <span class="file-label-text" id="medicalCertFileText">{{ $isArabic ? 'اختر ملف التقرير الطبي أو InBody (PDF أو صورة)' : 'Select medical or InBody report (PDF or Image)' }}</span>
                        </div>
                        @if(isset($student) && $student->medical_certificate)
                            <a href="{{ asset($student->medical_certificate) }}" target="_blank" class="existing-file-badge"><i data-feather="external-link"></i>{{ $isArabic ? 'عرض الملف المرفق الحالي' : 'View current attached file' }}</a>
                        @endif
                        @error('medical_certificate')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="student-field field-full">
                        <label for="medicalNotes">{{ $isArabic ? (!empty($isGymFacility) ? 'الملاحظات الصحية والإصابات البدنية' : trans('admin.student_management.medical_notes')) : 'Medical & Health notes' }}</label>
                        <div class="student-textarea-shell is-medical">
                            <i data-feather="heart"></i>
                            <textarea id="medicalNotes" name="medical_notes" rows="3" placeholder="{{ $isArabic ? (!empty($isGymFacility) ? 'إصابات المفاصل، الغضروف، ضغط الدم، السكري، آلام أسفل الظهر، أو أي تنبيه يجب على المدرب مراعاته...' : 'الحساسية، الإصابات، الأدوية أو أي تنبيه طبي...') : 'Injuries, back pain, joint issues, allergies, or coach alerts...' }}">{{ old('medical_notes', $student->medical_notes ?? '') }}</textarea>
                        </div>
                        @error('medical_notes')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>

                    <div class="student-field field-full">
                        <label for="studentNotes">{{ $isArabic ? (!empty($isGymFacility) ? 'الهدف التدريبي والملاحظات الإضافية' : trans('admin.student_management.notes')) : 'Fitness Goals & Notes' }}</label>
                        <div class="student-textarea-shell">
                            <i data-feather="target"></i>
                            <textarea id="studentNotes" name="notes" rows="3" placeholder="{{ $isArabic ? (!empty($isGymFacility) ? 'الهدف البدني (تخسيس، زيادة كتلة عضلية، لياقة عامة، مرونة، تأهيل بدني) أو أي متطلبات خاصة...' : 'ملاحظات إدارية أو تعليمات خاصة بالطالب...') : 'Fitness goal (Weight loss, Muscle gain, General fitness) or general notes...' }}">{{ old('notes', $student->notes ?? '') }}</textarea>
                        </div>
                        @error('notes')<span class="student-field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </section>
    </main>

    <aside class="student-form-aside">
        <div class="student-preview-card">
            <div class="preview-avatar" id="studentAvatar">
                @if(isset($student) && $student->image)
                    <img src="{{ $student->avatarUrl() }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                @else
                    {{ mb_strtoupper(mb_substr(old('name', $student->name ?? $avatarLetter), 0, 1)) }}
                @endif
            </div>
            <span>{{ $memberCardTerm }}</span>
            <h2 id="previewStudentName">{{ old('name', $student->name ?? $memberNewTerm) }}</h2>
            <p id="previewStudentContact">{{ old('phone', $student->phone ?? (old('email', $student->email ?? '-'))) }}</p>
            <dl>
                <div><dt><i data-feather="calendar"></i>{{ $isArabic ? 'العمر' : 'Age' }}</dt><dd id="previewAge">-</dd></div>
                <div><dt><i data-feather="users"></i>{{ trans('admin.student_management.gender') }}</dt><dd id="previewGender">-</dd></div>
                @if(!empty($isGymFacility))
                    <div><dt><i data-feather="phone"></i>{{ $isArabic ? 'رقم الهاتف' : 'Phone' }}</dt><dd id="previewPhone">-</dd></div>
                    <div><dt><i data-feather="phone-call"></i>{{ $isArabic ? 'جهة الطوارئ' : 'Emergency' }}</dt><dd id="previewGuardian">-</dd></div>
                @else
                    <div><dt><i data-feather="shield"></i>{{ trans('admin.student_management.guardian') }}</dt><dd id="previewGuardian">-</dd></div>
                    <div><dt><i data-feather="phone"></i>{{ trans('admin.student_management.guardian_phone') }}</dt><dd id="previewGuardianPhone">-</dd></div>
                @endif
            </dl>
            <div class="student-preview-status" id="previewStatus" data-status="{{ $currentStatus }}"><i data-feather="check-circle"></i><span>{{ trans('admin.student_management.' . $currentStatus) }}</span></div>
        </div>

        <div class="student-safety-card">
            <i data-feather="{{ !empty($isGymFacility) ? 'activity' : 'heart' }}"></i>
            <div>
                <strong>{{ $isArabic ? (!empty($isGymFacility) ? 'الجاهزية والسلامة البدنية' : 'سلامة الطالب أولًا') : 'Safety First' }}</strong>
                <p>{{ $isArabic ? (!empty($isGymFacility) ? 'تسجيل الإصابات والبيانات الصحية بدقة يساعد المدرب في تصميم البرنامج الرياضي الآمن للعضو.' : 'دوّن أي حساسية أو إصابة أو دواء يحتاج المدرب إلى معرفته.') : 'Recording health background ensures optimal coaching and physical safety.' }}</p>
            </div>
        </div>
    </aside>
</div>

<footer class="student-form-footer">
    <a href="{{ route('academy.students.index') }}" class="student-cancel-button">{{ trans('admin.student_management.cancel') }}</a>
    <button type="submit" class="student-submit-button"><i data-feather="save"></i><span>{{ trans('admin.student_management.save') }}</span></button>
</footer>
