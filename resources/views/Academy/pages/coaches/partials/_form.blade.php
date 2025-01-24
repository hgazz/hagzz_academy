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
        @if(isset($coach))
        <img id="imagePreview" src="{{(isset($coach) ? $coach->image : '#')}}" alt="Image Preview" width="400px" height="400px" class="mt-3 ">
        @endif
        @error('image')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="sports">{{trans('admin.coaches.select_sport')}}   <span class="text-danger">*</span></label>
        <select class="js-example-basic-multiple form-select" name="sport_id[]" multiple id="sports" required>
            @foreach($sports as $sport)
                <option value="{{$sport->id}}" @selected(in_array($sport->id, old('sport_id', isset($coach) ? $coach->sports()->pluck('sport_id')->toArray() : [])))>{{$sport->name}}</option>
            @endforeach
        </select>
        @error('sport_id')
        <span class="text-danger">{{$message}}</span>
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
                    imagePreview.src = e.target.result;
                    imagePreview.classList.replace('d-none','d-block');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
