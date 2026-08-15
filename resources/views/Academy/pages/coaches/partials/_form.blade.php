@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@csrf
<div class="row">
    <!-- Coach Name AR -->
    <div class="col-md-6 mb-3">
        <label for="name_ar" class="form-label fw-bold">{{ trans('admin.area.name_ar') ?: 'اسم المدرب (بالعربية)' }} <span class="text-danger">*</span></label>
        <input type="text" id="name_ar" name="name_ar" maxlength="100" class="form-control"
               value="{{ old('name_ar', isset($coach) ? $coach->getTranslation('name', 'ar') : '') }}"
               placeholder="{{ trans('admin.area.name_ar') ?: 'اسم المدرب بالعربية' }}" required>
        @error('name_ar')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>

    <!-- Coach Name EN -->
    <div class="col-md-6 mb-3">
        <label for="name_en" class="form-label fw-bold">{{ trans('admin.area.name_en') ?: 'اسم المدرب (بالإنجليزية)' }} <span class="text-danger">*</span></label>
        <input type="text" id="name_en" name="name_en" maxlength="100" class="form-control"
               value="{{ old('name_en', isset($coach) ? $coach->getTranslation('name', 'en') : '') }}"
               placeholder="{{ trans('admin.area.name_en') ?: 'Coach name in English' }}" required>
        @error('name_en')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>

    <!-- Phone -->
    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label fw-bold">{{ trans('admin.coaches.phone') }} <span class="text-danger">*</span></label>
        <input class="form-control" type="text" value="{{ old('phone', isset($coach) ? $coach->phone : '') }}" id="phone" name="phone" required placeholder="01xxxxxxxxx">
        @error('phone')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>

    <!-- Sports -->
    <div class="col-md-6 mb-3">
        <label for="sports" class="form-label fw-bold">{{ trans('admin.coaches.select_sport') }} <span class="text-danger">*</span></label>
        <select class="js-example-basic-multiple form-select" name="sport_id[]" multiple id="sports" required>
            @foreach($sports as $sport)
                <option value="{{ $sport->id }}" @selected(in_array($sport->id, old('sport_id', isset($coach) ? $coach->sports()->pluck('sport_id')->toArray() : [])))>
                    {{ $sport->name }}
                </option>
            @endforeach
        </select>
        @error('sport_id')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>

    <!-- Compensation Type -->
    <div class="col-md-6 mb-3">
        <label for="compensation_type" class="form-label fw-bold text-primary">{{ trans('admin.coaches.compensation_type') }} <span class="text-danger">*</span></label>
        <select class="form-select border-primary" name="compensation_type" id="compensation_type" onchange="updateCompensationLabel()" required>
            <option value="session" @selected(old('compensation_type', isset($coach) ? $coach->compensation_type : 'session') == 'session')>
                {{ app()->getLocale() === 'ar' ? '⚽ نظام الحصة التدريبية (لكل تمرين)' : '⚽ Per Session / Class' }}
            </option>
            <option value="percentage" @selected(old('compensation_type', isset($coach) ? $coach->compensation_type : '') == 'percentage')>
                {{ app()->getLocale() === 'ar' ? '📊 نظام النسبة المئوية من التدريبات (%)' : '📊 Percentage of Training Revenue (%)' }}
            </option>
            <option value="salary" @selected(old('compensation_type', isset($coach) ? $coach->compensation_type : '') == 'salary')>
                {{ app()->getLocale() === 'ar' ? '💵 نظام المرتب الشهري الثابت' : '💵 Fixed Monthly Salary' }}
            </option>
        </select>
        @error('compensation_type')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>

    <!-- Compensation Value -->
    <div class="col-md-6 mb-3">
        <label for="compensation_value" class="form-label fw-bold text-primary" id="compensation_value_label">
            @php
                $currentComp = old('compensation_type', isset($coach) ? $coach->compensation_type : 'session');
                $isArLocale = app()->getLocale() === 'ar';
            @endphp
            @if($currentComp === 'session')
                {{ $isArLocale ? 'سعر الحصة للمدرب (ج.م)' : 'Rate per session (EGP)' }}
            @elseif($currentComp === 'percentage')
                {{ $isArLocale ? 'النسبة المئوية للمدرب (%)' : 'Percentage Value (%)' }}
            @else
                {{ $isArLocale ? 'قيمة المرتب الشهري (ج.م)' : 'Monthly Salary (EGP)' }}
            @endif
            <span class="text-danger">*</span>
        </label>
        <input class="form-control border-primary" type="number" step="0.01" min="0" 
               value="{{ old('compensation_value', isset($coach) ? (float)$coach->compensation_value : '0.00') }}" 
               id="compensation_value" name="compensation_value" 
               placeholder="0.00" required>
        <small class="text-muted d-block mt-1" id="compensation_help_text">
            @if($currentComp === 'session')
                {{ $isArLocale ? 'المبلغ الذي يتقاضاه المدرب عن كل حصة أو تمرينة يدربها.' : 'Amount paid to coach per conducted training session.' }}
            @elseif($currentComp === 'percentage')
                {{ $isArLocale ? 'النسبة المئوية من إجمالي رسوم واشتراكات التدريب (مثال: 30).' : 'Percentage of training revenue (e.g. 30).' }}
            @else
                {{ $isArLocale ? 'المرتب الشهري الثابت الذي يُصرف للمدرب شهرياً.' : 'Fixed monthly salary for the coach.' }}
            @endif
        </small>
        @error('compensation_value')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>

    <!-- Gender -->
    <div class="col-md-6 mb-3">
        <label for="gender" class="form-label fw-bold">{{ trans('admin.coaches.select_gender') }} <span class="text-danger">*</span></label>
        <select class="form-select" name="gender" id="gender" required>
            <option value="male" @selected(old('gender', isset($coach) ? $coach->getRawOriginal('gender') : 'male') == 'male')>{{ trans('admin.coaches.male') }}</option>
            <option value="female" @selected(old('gender', isset($coach) ? $coach->getRawOriginal('gender') : '') == 'female')>{{ trans('admin.coaches.female') }}</option>
        </select>
        @error('gender')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>

    <!-- Birth Date -->
    <div class="col-md-6 mb-3">
        <label for="birth_date" class="form-label fw-bold">{{ trans('admin.coaches.birth_date') }}</label>
        <input class="form-control" type="date" value="{{ old('birth_date', isset($coach) ? $coach->birth_date : '') }}" id="birth_date" name="birth_date">
        @error('birth_date')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>

    <!-- Image -->
    <div class="col-md-6 mb-3">
        <label for="image" class="form-label fw-bold">{{ trans('admin.coaches.image') }}</label>
        <input class="form-control" type="file" id="image" name="image" onchange="previewImage(event)" accept="image/*">
        @if(isset($coach) && $coach->image)
            <img id="imagePreview" src="{{ $coach->image }}" alt="Image Preview" style="width:120px; height:120px; object-fit:cover; border-radius:8px;" class="mt-2 d-block">
        @else
            <img id="imagePreview" src="#" alt="Image Preview" style="width:120px; height:120px; object-fit:cover; border-radius:8px;" class="mt-2 d-none">
        @endif
        @error('image')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>

    <!-- Active Status -->
    <div class="col-md-6 mb-3 d-flex flex-column justify-content-center">
        <label class="form-label fw-bold mb-2">{{ trans('admin.address.active') }}</label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="active" name="active" value="1" @checked(old('active', isset($coach) ? $coach->getRawOriginal('active') : 1) == 1) style="width: 2.5em; height: 1.3em;">
            <label class="form-check-label ms-2" for="active">{{ app()->getLocale() === 'ar' ? 'حساب المدرب مفعل ونشط' : 'Coach account is active' }}</label>
        </div>
        @error('active')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>

    <!-- Bio AR -->
    <div class="col-md-6 mb-3">
        <label for="description_ar" class="form-label fw-bold">{{ trans('admin.training.description_ar') ?: 'السيرة الذاتية (بالعربية)' }}</label>
        <textarea class="form-control" rows="3" name="description_ar" id="description_ar" placeholder="{{ app()->getLocale() === 'ar' ? 'نبذة عن خبرات وإنجازات المدرب...' : 'Coach bio...' }}">{{ old('description_ar', isset($coach) ? $coach->getTranslation('description', 'ar') : '') }}</textarea>
        @error('description_ar')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>

    <!-- Bio EN -->
    <div class="col-md-6 mb-3">
        <label for="description_en" class="form-label fw-bold">{{ trans('admin.training.description_en') ?: 'السيرة الذاتية (بالإنجليزية)' }}</label>
        <textarea class="form-control" rows="3" name="description_en" id="description_en" placeholder="Coach bio in English...">{{ old('description_en', isset($coach) ? $coach->getTranslation('description', 'en') : '') }}</textarea>
        @error('description_en')
        <span class="text-danger d-block mt-1">*{{ $message }}</span>
        @enderror
    </div>
</div>

@push('js')
    <script>
        function updateCompensationLabel() {
            const select = document.getElementById('compensation_type');
            const label = document.getElementById('compensation_value_label');
            const help = document.getElementById('compensation_help_text');
            const isAr = "{{ app()->getLocale() }}" === 'ar';
            if (select && label) {
                if (select.value === 'session') {
                    label.innerHTML = (isAr ? 'سعر الحصة للمدرب (ج.م)' : 'Rate per session (EGP)') + ' <span class="text-danger">*</span>';
                    if (help) help.textContent = isAr ? 'المبلغ الذي يتقاضاه المدرب عن كل حصة أو تمرينة يدربها.' : 'Amount paid to coach per conducted training session.';
                } else if (select.value === 'percentage') {
                    label.innerHTML = (isAr ? 'النسبة المئوية للمدرب (%)' : 'Percentage Value (%)') + ' <span class="text-danger">*</span>';
                    if (help) help.textContent = isAr ? 'النسبة المئوية من إجمالي رسوم واشتراكات التدريب (مثال: 30).' : 'Percentage of training revenue (e.g. 30).';
                } else {
                    label.innerHTML = (isAr ? 'قيمة المرتب الشهري (ج.م)' : 'Monthly Salary (EGP)') + ' <span class="text-danger">*</span>';
                    if (help) help.textContent = isAr ? 'المرتب الشهري الثابت الذي يُصرف للمدرب شهرياً.' : 'Fixed monthly salary for the coach.';
                }
            }
        }
    </script>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                placeholder: "{{ trans('admin.coaches.select_sport') }}"
            });
        });
    </script>
    <script>
        function previewImage(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imagePreview = document.getElementById('imagePreview');
                    if (!imagePreview) {
                        return;
                    }
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    imagePreview.classList.add('d-block');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
