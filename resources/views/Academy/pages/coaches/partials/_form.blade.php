@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@csrf
<div class="row">
    @foreach (\App\Services\TranslatableService::getTranslatableInputs(App\Models\Coach::class) as $name => $data)
        @if(!$data['is_textarea'])
            <div class="col-md-6 mb-3">
                <label for="{{$name}}" class="form-label">{{trans('admin.training.'.$name)}} <span class="text-danger">*</span></label>
                <input type="text" id="{{$name}}" name="{{$name}}" maxlength="50" class="form-control"
                       @php
                           $language = $name == 'name_en' ? 'en' : 'ar';
                           $defaultValue = isset($coach) ? $coach->getTranslation('name', $language) : '';
                       @endphp
                       value="{{ old($name, $defaultValue) }}"
                       placeholder="{{trans('admin.training.'.$name)}}" data-parsley-required-message="Please enter {{$name}}">
                @error($name)
                <span class="text-danger">*{{$message}}</span>
                @enderror
            </div>
        @else
            <div class="col-md-6 mb-3">
                <label for="{{$name}}">
                    <span class="text-danger">*</span>
                    {{ $name === 'description_en' ? trans('admin.training.description_en') : trans('admin.training.description_ar') }}
                </label>

                <textarea class="form-control" name="{{$name}}" id="{{$name}}" placeholder="Enter">@if($name == 'description_en'){{old($name , isset($coach) ? $coach->getTranslation('description','en') : '')}}@else{{old($name , isset($coach) ? $coach->getTranslation('description','ar') : '')}}@endif</textarea>
                @error($name)
                <span class="text-danger">*{{$message}}</span>
                @enderror
            </div>
        @endif
    @endforeach


    <div class="col-md-6 mb-3">
        <label for="phone">{{ trans('admin.coaches.phone') }}    <span class="text-danger">*</span></label>
        <input class="form-control" type="text" value="{{ old('phone',(isset($coach) ? $coach->phone : '')) }}" id="phone" name="phone">
        @error('phone')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="birth_date">{{ trans('admin.coaches.birth_date') }}    <span class="text-danger">*</span></label>
        <input class="form-control" type="date" value="{{old('birth_date', (isset($coach) ?  $coach->birth_date : ''))}}" id="birth_date" name="birth_date">
        @error('birth_date')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="gender">{{trans('admin.coaches.select_gender')}}   <span class="text-danger">*</span></label>
        <select class="form-select" name="gender" id="gender">
            <option value="">{{ trans('admin.coaches.select_gender') }}</option>
            <option value="male" @selected(old('gender', isset($coach) ? $coach->getRawOriginal('gender') : '') == 'male')>{{ trans('admin.coaches.male') }}</option>
            <option value="female" @selected(old('gender', isset($coach) ? $coach->getRawOriginal('gender')  : '') == 'female')>{{ trans('admin.coaches.female') }}</option>
        </select>
        @error('gender')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="image">{{ trans('admin.coaches.image') }}</label>
        <input class="form-control" type="file"  id="image" name="image" onchange="previewImage(event)">
        <img id="imagePreview" src="{{ isset($coach) ? $coach->image : '#' }}" alt="Image Preview" width="400px" height="400px" class="mt-3 {{ isset($coach) ? 'd-block' : 'd-none' }}">
        @error('image')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="sports">{{trans('admin.coaches.select_sport')}}   <span class="text-danger">*</span></label>
        <select class="js-example-basic-multiple form-select" name="sport_id[]" multiple id="sports">
            @foreach($sports as $sport)
                <option value="{{$sport->id}}" @selected(in_array($sport->id, old('sport_id', isset($coach) ? $coach->sports()->pluck('sport_id')->toArray() : [])))>{{$sport->name}}</option>
            @endforeach
        </select>
        @error('sport_id')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="compensation_type" class="form-label fw-bold">{{ trans('admin.coaches.compensation_type') }} <span class="text-danger">*</span></label>
        <select class="form-select" name="compensation_type" id="compensation_type" onchange="updateCompensationLabel()">
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
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="compensation_value" class="form-label fw-bold" id="compensation_value_label">
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
        <input class="form-control" type="number" step="0.01" min="0" 
               value="{{ old('compensation_value', isset($coach) ? $coach->compensation_value : '0.00') }}" 
               id="compensation_value" name="compensation_value" 
               placeholder="0.00">
        <small class="text-muted d-block mt-1" id="compensation_help_text">
            @if($currentComp === 'session')
                {{ $isArLocale ? 'المبلغ الذي يتقاضاه المدرب عن كل حصة أو تمرينة يدربها.' : 'Amount paid to coach per conducted training session.' }}
            @elseif($currentComp === 'percentage')
                {{ $isArLocale ? 'النسبة المئوية من إجمالي رسوم واشتراكات التدريب.' : 'Percentage of training revenue.' }}
            @else
                {{ $isArLocale ? 'المرتب الشهري الثابت الذي يُصرف للمدرب شهرياً.' : 'Fixed monthly salary for the coach.' }}
            @endif
        </small>
        @error('compensation_value')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="active">{{ trans('admin.address.active') }}</label>
        <input class="form-check" id="active" name="active" @checked(old('active', (isset($coach) ? $coach->getRawOriginal('active') : ''))) type="checkbox">
        @error('active')
        <span class="text-danger">{{ $message }}</span>
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
                    if (help) help.textContent = isAr ? 'النسبة المئوية من إجمالي رسوم واشتراكات التدريب.' : 'Percentage of training revenue.';
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
