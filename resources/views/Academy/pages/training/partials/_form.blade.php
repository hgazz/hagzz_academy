@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/training-form-modern.css') }}" rel="stylesheet">
@endpush

@csrf
@php
    $isArabic = app()->getLocale() === 'ar';
    $savedDays = old('classes_days', isset($training) ? ($training->classes_days ?: []) : []);
@endphp

<section class="training-form-section">
    <header class="form-section-header">
        <span class="section-number">01</span>
        <div>
            <h2>{{ $isArabic ? 'المعلومات الأساسية' : 'Basic information' }}</h2>
            <p>{{ $isArabic ? 'الاسم والوصف اللذان سيظهران للعملاء في التطبيق.' : 'The name and description customers will see in the app.' }}</p>
        </div>
    </header>
    <div class="form-section-body">
        <div class="form-grid">
            @foreach (\App\Services\TranslatableService::getTranslatableInputs(App\Models\Training::class) as $name => $data)
                @php
                    $language = str_ends_with($name, '_en') ? 'en' : 'ar';
                    $fieldDirection = $language === 'ar' ? 'rtl' : 'ltr';
                    $baseField = str_starts_with($name, 'name_') ? 'name' : 'description';
                    $value = old($name, isset($training) ? $training->getTranslation($baseField, $language) : '');
                @endphp
                <div class="modern-field {{ $data['is_textarea'] ? 'field-half' : 'field-half' }}">
                    <label for="{{ $name }}">
                        <span>{{ $data['is_textarea'] ? ($name === 'description_en' ? trans('admin.training.description_en') : trans('admin.training.description_ar')) : trans('admin.training.' . $name) }}</span>
                        <b>*</b>
                    </label>
                    @if(!$data['is_textarea'])
                        <div class="input-shell">
                            <i data-feather="type"></i>
                            <input type="text" id="{{ $name }}" name="{{ $name }}" maxlength="50"
                                   value="{{ $value }}" dir="{{ $fieldDirection }}" data-required="true"
                                   placeholder="{{ $language === 'ar' ? 'اكتب الاسم بالعربية' : 'Enter the English name' }}">
                            <small class="character-count" data-for="{{ $name }}">0/50</small>
                        </div>
                    @else
                        <div class="textarea-shell">
                            <textarea name="{{ $name }}" id="{{ $name }}" maxlength="255" dir="{{ $fieldDirection }}"
                                      data-required="true"
                                      placeholder="{{ $language === 'ar' ? 'اكتب وصفًا واضحًا للتدريب بالعربية' : 'Describe the training clearly in English' }}">{{ $value }}</textarea>
                            <small class="character-count" data-for="{{ $name }}">0/255</small>
                        </div>
                    @endif
                    @error($name)<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="training-form-section">
    <header class="form-section-header">
        <span class="section-number">02</span>
        <div>
            <h2>{{ $isArabic ? 'الجدول والتنظيم' : 'Schedule and organization' }}</h2>
            <p>{{ $isArabic ? 'حدد الرياضة والمدرب والموقع ومواعيد الحصص الأسبوعية.' : 'Choose the sport, coach, location and weekly session schedule.' }}</p>
        </div>
    </header>
    <div class="form-section-body">
        <div class="form-grid">
            <div class="modern-field field-half">
                <label for="sport_id"><span>{{ trans('admin.clasess.sport') }}</span><b>*</b></label>
                <div class="select-shell"><i data-feather="activity"></i><select name="sport_id" id="sport_id" data-required="true">
                    <option value="">{{ trans('admin.clasess.select_sport') }}</option>
                    @foreach($sports as $sport)<option value="{{ $sport->id }}" @selected(old('sport_id', isset($training) ? $training->sport_id : '') == $sport->id)>{{ $sport->name }}</option>@endforeach
                </select><i data-feather="chevron-down"></i></div>
                @error('sport_id')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
            <div class="modern-field field-half">
                <label for="coaches"><span>{{ trans('admin.training.coach') }}</span><b>*</b></label>
                <div class="select-shell"><i data-feather="award"></i><select id="coaches" name="coach_id" data-required="true" data-selected="{{ old('coach_id', isset($training) ? $training->coach_id : '') }}">
                    <option value="">{{ trans('admin.training.Choose Coach') }}</option>
                    @foreach($academyCoaches as $coach)<option value="{{ $coach->id }}" @selected(old('coach_id', isset($training) ? $training->coach_id : '') == $coach->id)>{{ $coach->name }}</option>@endforeach
                </select><i data-feather="chevron-down"></i></div>
                <small class="field-help" id="coachHelp">{{ $isArabic ? 'اختر الرياضة أولاً لتصفية المدربين.' : 'Choose a sport first to filter coaches.' }}</small>
                @error('coach_id')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
            <div class="modern-field field-half">
                <label for="address_id"><span>{{ trans('admin.training.address') }}</span><b>*</b></label>
                <div class="select-shell"><i data-feather="map-pin"></i><select id="address_id" name="address_id" data-required="true">
                    <option value="">{{ trans('admin.training.select_address') }}</option>
                    @foreach($addresses as $address)<option value="{{ $address->id }}" @selected(old('address_id', isset($training) ? $training->address_id : '') == $address->id)>{{ $address->address }}</option>@endforeach
                </select><i data-feather="chevron-down"></i></div>
                @error('address_id')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
            <div class="modern-field field-half">
                <label for="classes_number"><span>{{ trans('admin.training.classes_number') }}</span></label>
                <div class="input-shell"><i data-feather="hash"></i><input type="number" value="{{ old('classes_number', isset($training) ? $training->classes_number : '') }}" id="classes_number" name="classes_number" min="1" placeholder="0"></div>
                @error('classes_number')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
            <div class="modern-field field-half">
                <label for="start_time"><span>{{ trans('admin.training.classes_start_time') }}</span><b>*</b></label>
                <div class="input-shell"><i data-feather="clock"></i><input type="time" value="{{ old('start_time', isset($training) && $training->start_time ? $training->start_time->format('H:i') : '') }}" id="start_time" name="start_time" data-required="true"></div>
                @error('start_time')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
            <div class="modern-field field-half">
                <label for="end_time"><span>{{ trans('admin.training.classes_end_time') }}</span><b>*</b></label>
                <div class="input-shell"><i data-feather="clock"></i><input type="time" value="{{ old('end_time', isset($training) && $training->end_time ? $training->end_time->format('H:i') : '') }}" id="end_time" name="end_time" data-required="true"></div>
                <small class="field-help">{{ $isArabic ? 'يمكن أن يكون وقت النهاية بعد منتصف الليل.' : 'The ending time may be after midnight.' }}</small>
                @error('end_time')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
            <div class="modern-field field-full">
                <label><span>{{ trans('admin.training.classes_days') }}</span><b>*</b></label>
                <div class="days-picker" id="daysPicker">
                    @foreach([
                        'saturday' => trans('admin.training.saturday'), 'sunday' => trans('admin.training.sunday'),
                        'monday' => trans('admin.training.monday'), 'tuesday' => trans('admin.training.tuesday'),
                        'wednesday' => trans('admin.training.wednesday'), 'thursday' => trans('admin.training.thursday'),
                        'friday' => trans('admin.training.friday')
                    ] as $dayValue => $dayLabel)
                        <button type="button" class="day-option {{ in_array($dayValue, $savedDays) ? 'is-selected' : '' }}" data-day="{{ $dayValue }}">{{ $dayLabel }}</button>
                    @endforeach
                </div>
                <select id="classes_days" name="classes_days[]" multiple data-required="true" class="accessible-days-select" aria-label="{{ trans('admin.training.classes_days') }}">
                    @foreach(['saturday','sunday','monday','tuesday','wednesday','thursday','friday'] as $dayValue)
                        <option value="{{ $dayValue }}" @selected(in_array($dayValue, $savedDays))>{{ trans('admin.training.' . $dayValue) }}</option>
                    @endforeach
                </select>
                @error('classes_days')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
</section>

<section class="training-form-section">
    <header class="form-section-header">
        <span class="section-number">03</span>
        <div>
            <h2>{{ $isArabic ? 'الجمهور والسعة' : 'Audience and capacity' }}</h2>
            <p>{{ $isArabic ? 'حدد مستوى المتدربين والفئة المناسبة والعدد الأقصى.' : 'Define the suitable level, audience and maximum capacity.' }}</p>
        </div>
    </header>
    <div class="form-section-body">
        <div class="form-grid">
            <div class="modern-field field-half">
                <label for="max_players"><span>{{ trans('admin.training.max_players') }}</span><b>*</b></label>
                <div class="input-shell"><i data-feather="users"></i><input type="number" value="{{ old('max_players', isset($training) ? $training->max_players : '') }}" id="max_players" name="max_players" min="1" data-required="true" placeholder="0"></div>
                @error('max_players')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
            <div class="modern-field field-half">
                <label for="level"><span>{{ trans('admin.training.levels') }}</span><b>*</b></label>
                <div class="select-shell"><i data-feather="bar-chart-2"></i><select id="level" name="level" data-required="true">
                    <option value="">{{ trans('admin.training.select_level') }}</option>
                    <option value="Beginner" @selected(old('level', isset($training) ? $training->getRawOriginal('level') : '') === 'Beginner')>{{ trans('admin.training.beginner') }}</option>
                    <option value="Intermediate" @selected(old('level', isset($training) ? $training->getRawOriginal('level') : '') === 'Intermediate')>{{ trans('admin.training.intermediate') }}</option>
                    <option value="Advanced" @selected(old('level', isset($training) ? $training->getRawOriginal('level') : '') === 'Advanced')>{{ trans('admin.training.advanced') }}</option>
                    <option value="Any_Level" @selected(old('level', isset($training) ? $training->getRawOriginal('level') : '') === 'Any_Level')>{{ trans('admin.training.Any_Level') }}</option>
                </select><i data-feather="chevron-down"></i></div>
                @error('level')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
            <div class="modern-field field-half">
                <label for="gender"><span>{{ trans('admin.training.gender') }}</span><b>*</b></label>
                <div class="select-shell"><i data-feather="user"></i><select id="gender" name="gender" data-required="true">
                    <option value="">{{ trans('admin.training.select_gender') }}</option>
                    <option value="All" @selected(old('gender', isset($training) ? $training->getRawOriginal('gender') : '') === 'All')>{{ trans('admin.training.all') }}</option>
                    <option value="Men" @selected(old('gender', isset($training) ? $training->getRawOriginal('gender') : '') === 'Men')>{{ trans('admin.coaches.male') }}</option>
                    <option value="Women" @selected(old('gender', isset($training) ? $training->getRawOriginal('gender') : '') === 'Women')>{{ trans('admin.coaches.female') }}</option>
                </select><i data-feather="chevron-down"></i></div>
                @error('gender')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
            <div class="modern-field field-half">
                <label for="age_group"><span>{{ trans('admin.training.age_group') }}</span><b>*</b></label>
                <div class="select-shell"><i data-feather="users"></i><select id="age_group" name="age_group" data-required="true">
                    <option value="">{{ trans('admin.training.select_gender') }}</option>
                    <option value="All" @selected(old('age_group', isset($training) ? $training->getRawOriginal('age_group') : '') === 'All')>{{ trans('admin.training.all') }}</option>
                    <option value="Kids" @selected(old('age_group', isset($training) ? $training->getRawOriginal('age_group') : '') === 'Kids')>{{ trans('admin.training.kids') }}</option>
                    <option value="Juniors" @selected(old('age_group', isset($training) ? $training->getRawOriginal('age_group') : '') === 'Juniors')>{{ trans('admin.training.juniors') }}</option>
                    <option value="Adults" @selected(old('age_group', isset($training) ? $training->getRawOriginal('age_group') : '') === 'Adults')>{{ trans('admin.training.adults') }}</option>
                </select><i data-feather="chevron-down"></i></div>
                @error('age_group')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
</section>

<section class="training-form-section">
    <header class="form-section-header">
        <span class="section-number">04</span>
        <div>
            <h2>{{ $isArabic ? 'السعر والمظهر' : 'Pricing and appearance' }}</h2>
            <p>{{ $isArabic ? 'حدد السعر والخصم واللون المستخدم لتمييز التدريب في التقويم.' : 'Set pricing, discount and the color used in the calendar.' }}</p>
        </div>
    </header>
    <div class="form-section-body">
        <div class="form-grid">
            <div class="modern-field field-half">
                <label for="price"><span>{{ trans('admin.training.price') }}</span><b>*</b></label>
                <div class="input-shell has-suffix"><i data-feather="credit-card"></i><input type="number" value="{{ old('price', isset($training) ? $training->price : '') }}" id="price" name="price" min="1" data-required="true" placeholder="0"><span>{{ $isArabic ? 'ج.م' : 'EGP' }}</span></div>
                @error('price')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
            <div class="modern-field field-half">
                <label for="discount_price"><span>{{ trans('admin.training.discount') }}</span><b>*</b></label>
                <div class="input-shell has-suffix"><i data-feather="tag"></i><input type="number" value="{{ old('discount_price', isset($training) ? $training->discount_price : 0) }}" id="discount_price" name="discount_price" min="0" data-required="true" placeholder="0"><span>{{ $isArabic ? 'ج.م' : 'EGP' }}</span></div>
                @error('discount_price')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
            <div class="modern-field field-full">
                <label for="color"><span>{{ trans('admin.training.color') }}</span></label>
                <div class="color-field">
                    <input type="color" value="{{ old('color', isset($training) ? $training->color : '#2563eb') }}" id="color" name="color">
                    <div><strong>{{ $isArabic ? 'لون التدريب في التقويم' : 'Calendar training color' }}</strong><small>{{ $isArabic ? 'اختر لونًا واضحًا ليسهل تمييز التدريب.' : 'Choose a clear color to identify this training.' }}</small></div>
                    <code id="colorValue">{{ old('color', isset($training) ? $training->color : '#2563eb') }}</code>
                </div>
                @error('color')<span class="field-error"><i data-feather="alert-circle"></i>{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sportSelect = document.getElementById('sport_id');
        const coachSelect = document.getElementById('coaches');
        let selectedCoach = String(coachSelect.dataset.selected || '');
        const locale = @json(app()->getLocale());
        const coachEndpoint = @json(url(app()->getLocale() . '/partner/training/getCoachesBySports'));

        async function loadCoaches() {
            if (!sportSelect.value) return;
            coachSelect.disabled = true;
            try {
                const response = await fetch(`${coachEndpoint}/${sportSelect.value}`, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const data = await response.json();
                coachSelect.innerHTML = `<option value="">${@json(trans('admin.training.Choose Coach'))}</option>`;
                data.coaches.forEach(coach => {
                    const option = document.createElement('option');
                    option.value = coach.id;
                    option.textContent = coach.name?.[locale] || coach.name?.en || coach.name?.ar || '';
                    option.selected = String(coach.id) === selectedCoach;
                    coachSelect.appendChild(option);
                });
                selectedCoach = '';
            } catch (error) {
                console.error('[Hagzz] Failed to load coaches', error);
            } finally {
                coachSelect.disabled = false;
                coachSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        sportSelect.addEventListener('change', loadCoaches);
        if (sportSelect.value) loadCoaches();

        const daysSelect = document.getElementById('classes_days');
        document.querySelectorAll('.day-option').forEach(button => {
            button.addEventListener('click', function () {
                const option = Array.from(daysSelect.options).find(item => item.value === button.dataset.day);
                option.selected = !option.selected;
                button.classList.toggle('is-selected', option.selected);
                daysSelect.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        document.querySelectorAll('.character-count').forEach(counter => {
            const field = document.getElementById(counter.dataset.for);
            const refresh = () => counter.textContent = `${field.value.length}/${field.maxLength}`;
            field.addEventListener('input', refresh);
            refresh();
        });

        const color = document.getElementById('color');
        color.addEventListener('input', () => document.getElementById('colorValue').textContent = color.value);
        if (window.feather) feather.replace();
    });
</script>
