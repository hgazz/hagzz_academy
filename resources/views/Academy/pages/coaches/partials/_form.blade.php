@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@csrf
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name">{{ trans('admin.coaches.name') }}</label>
        <input class="form-control" type="text" value="{{old('name', (isset($coach) ? $coach->name : ''))}}" id="name" name="name">
        @error('name')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="description">{{ trans('admin.coaches.description') }}</label>
        <input class="form-control" type="text" value="{{ old('description',(isset($coach) ? $coach->description :''))}}" id="description" name="description">
        @error('description')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="license">{{ trans('admin.coaches.license') }}</label>
        <input class="form-control" type="text" value="{{old('license', (isset($coach) ? $coach->license: ''))}}" id="license" name="license">
        @error('license')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="license_type">{{ trans('admin.coaches.license_type') }}</label>
        <input class="form-control" type="text" value="{{ old('license_type',(isset($coach) ? $coach->license_type : ''))}}" id="license_type" name="license_type">
        @error('license_type')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="phone">{{ trans('admin.coaches.phone') }}</label>
        <input class="form-control" type="text" value="{{ old('phone',(isset($coach) ? $coach->phone : '')) }}" id="phone" name="phone">
        @error('phone')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="birth_date">{{ trans('admin.coaches.birth_date') }}</label>
        <input class="form-control" type="date" value="{{old('birth_date', (isset($coach) ?  $coach->birth_date : ''))}}" id="birth_date" name="birth_date">
        @error('birth_date')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="gender">{{trans('admin.coaches.select_gender')}}</label>
        <select class="form-select" name="gender" id="gender">
            <option value="">{{ trans('admin.coaches.select_gender') }}</option>
            <option value="male" @selected(old('gender', isset($coach) ? $coach->gender : '') == 'male')>{{ trans('admin.coaches.male') }}</option>
            <option value="female" @selected(old('gender', isset($coach) ? $coach->gender : '') == 'female')>{{ trans('admin.coaches.female') }}</option>
        </select>
        @error('gender')
        <span class="text-danger">{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="image">{{ trans('admin.coaches.image') }}</label>
        <input class="form-control" type="file"  id="image" name="image" onchange="previewImage(event)">
        <img id="imagePreview" src="{{(isset($coach) ? $coach->image : '#')}}" alt="Image Preview" width="400px" height="400px" class="mt-3 ">
        @error('image')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="sports">{{trans('admin.coaches.select_sport')}}</label>
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
