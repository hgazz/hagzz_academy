@csrf
<div class="row">
    <div class="row">
        @foreach (\App\Services\TranslatableService::getTranslatableInputs(App\Models\TClass::class) as $name => $data)
            <div class="col-md-6 mb-3">
                <label for="{{$name}}" class="form-label">{{$name}}</label>
                <input type="text" id="{{$name}}" name="{{$name}}" maxlength="50" class="form-control"
                       value="@if ($name == 'title_en') {{old($name, isset($class) ? $class->getTranslation('title','en')  : '')}} @elseif($name == 'subtitle_en') {{old($name, isset($class) ? $class->getTranslation('subtitle','en')  : '')}} @elseif($name == 'subtitle_ar') {{old($name, isset($class) ? $class->getTranslation('subtitle','ar')  : '')}}  @else {{old($name, isset($class) ? $class->getTranslation('title','ar')  : '')}} @endif"
                       placeholder="Enter {{$name}}" data-parsley-required-message="Please enter {{$name}}">
                @error($name)
                <span class="text-danger">*{{$message}}</span>
                @enderror
            </div>
        @endforeach

    </div>
    <div class="col-md-6 mb-3">
        <label for="start_time">{{ trans('admin.training.start_time') }}</label>
        <input class="form-control" type="time" value="{{(isset($class) ? $class->start_time : old('start_time'))}}" id="start_time" name="start_time">
        @error('start_time')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="end_time">{{ trans('admin.training.end_time') }}</label>
        <input class="form-control" type="time" value="{{(isset($class) ? $class->end_time : old('end_time'))}}" id="end_time" name="end_time">
        @error('end_time')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="date">{{ trans('admin.clasess.date') }}</label>
            <input class="form-control" type="date" value="{{ isset($class) ? $class->date : old('date') }}" id="date" name="date">
            @error('date')
            <span class="text-danger">*{{$message}}</span>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="classes"><span class="text-danger">*</span> {{trans('admin.training.training')}} </label>
            <select id="classes" class="form-select pt-2" name="training_id" >
                <option value=""> {{trans('admin.clasess.select_training')}} </option>
                @foreach($trainings as $training)
                    <option value="{{$training->id}}" @selected(old('training_id',  (isset($class) ? $class->training_id : '')) == $training->id) >{{$training->name}}</option>
                @endforeach
            </select>
            @error('training_id')
            <span class="text-danger">*{{$message}}</span>
            @enderror
        </div>
    </div>

</div>
@if(request()->url() === route('academy.class.create'))
<div class="row">
    <div class="col-md-6 mb-3" id="outcomes-container">
        <label for="outcomes">{{ trans('admin.clasess.out_comes') }}</label>
        <!-- Initial input field -->
        <div class="input-group mb-2">
            <input class="form-control outcome-input" type="text" name="outcomes[]" value="" id="outcomes">
            <div class="input-group-append">
                <button class="btn btn-danger remove-outcome" type="button">-</button>
            </div>
        </div>
        <button id="add-outcome" type="button" class="btn btn-primary">{{ trans('admin.clasess.add_more') }}</button>
        @error('outcomes.*')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3" id="bring-with-me-container">
        <label for="bring_with_me">{{ trans('admin.clasess.bring_with_me') }}</label>
        <!-- Initial input field -->
        <div class="input-group mb-2">
            <input class="form-control bring-with-me-input" type="text" name="bring_with_me[]" value="" id="outcomes">
            <div class="input-group-append">
                <button class="btn btn-danger remove-bring-with-me" type="button">-</button>
            </div>
        </div>
        <button id="add-bring-with-me" type="button" class="btn btn-primary">{{ trans('admin.clasess.add_more') }}</button>
        @error('bring_with_me.*')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

</div>
@else
    @isset($class)
        <div class="row">

            <div class="col-md-6 mb-3" id="outcomes-container">
                @php
                    $outcomes = json_decode($class->out_comes, true);
                    $numberOfOutcomes = is_array($outcomes) ? count($outcomes) : 0;
                @endphp
                @for($i = 0; $i < $numberOfOutcomes; $i++)
                    <label for="outcomes">{{ trans('admin.clasess.out_comes') }}</label>
                    <!-- Initial input field -->
                    <div class="input-group mb-2">
                        <input class="form-control outcome-input" type="text" name="outcomes[]" value="{{ $numberOfOutcomes[$i] }}" id="outcomes">
                        <div class="input-group-append">
                            <button class="btn btn-danger remove-outcome" type="button">-</button>
                        </div>
                    </div>
                    <button id="add-outcome" type="button" class="btn btn-primary">{{ trans('admin.clasess.add_more') }}</button>
                    @error('outcomes.*')
                    <span class="text-danger">*{{$message}}</span>
                    @enderror
                @endfor
            </div>
            <div class="col-md-6 mb-3" id="bring-with-me-container">
                @php
                    $bring_with_me = json_decode($class->bring_with_me, true);
                    $numberBringsWithMe = is_array($bring_with_me) ? count($bring_with_me) : 0;
                @endphp
                @for($i = 0; $i < $numberBringsWithMe; $i++)
                    <label for="bring_with_me">{{ trans('admin.clasess.bring_with_me') }}</label>
                    <!-- Initial input field -->
                    <div class="input-group mb-2">
                        <input class="form-control bring-with-me-input" type="text" name="bring_with_me[]" value="{{ $bring_with_me[$i] }}" id="bring_with_me">
                        <div class="input-group-append">
                            <button class="btn btn-danger remove-bring-with-me" type="button">-</button>
                        </div>
                    </div>
                    <button id="add-bring-with-me" type="button" class="btn btn-primary">{{ trans('admin.clasess.add_more') }}</button>
                    @error('bring_with_me.*')
                    <span class="text-danger">*{{$message}}</span>
                    @enderror
                @endfor
            </div>
        </div>
    @endisset

@endif


@push('js')
    <script>
            // Assuming you have old inputs for 'outcomes' and 'bring_with_me'
            var oldOutcomes = @json(old('outcomes', []));
            var oldBringWithMe = @json(old('bring_with_me', []));

            // Function to repopulate outcomes
            function repopulateOutcomes() {
                if (oldOutcomes.length > 0) {
                    // Clear initial input if old data exists
                    document.querySelector('#outcomes-container .input-group').remove();
                    oldOutcomes.forEach(function(value) {
                        addOutcomeInput(value); // Use the existing function to add inputs, passing the old value
                    });
                }
            }

            // Function to repopulate bring with me inputs, similar to repopulateOutcomes

            document.addEventListener('DOMContentLoaded', function () {
                // Call repopulate functions on page load
                repopulateOutcomes();
                // repopulateBringWithMe(); // Implement a similar function for bring_with_me

                document.querySelector('#add-outcome').addEventListener('click', function() {
                    var container = document.querySelector('#outcomes-container');
                    var newInput = document.createElement('div');
                    newInput.classList.add('input-group', 'mb-2');
                    newInput.innerHTML = `
            <input class="form-control outcome-input" type="text" name="outcomes[]" value="">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary remove-outcome" type="button">-</button>
            </div>
        `;
                    // Add event listener for the remove button in the new input group
                    newInput.querySelector('.remove-outcome').addEventListener('click', function() {
                        this.closest('.input-group').remove();
                    });
                    container.insertBefore(newInput, this);
                });

                // Initial removal button event listener
                document.querySelectorAll('.remove-outcome').forEach(button => {
                    button.addEventListener('click', function() {
                        this.closest('.input-group').remove();
                    });
                });

            });

            // Adjust the 'addOutcomeInput' function to accept a value parameter and set it to the input
            function addOutcomeInput(value = '') {
                var container = document.querySelector('#outcomes-container');
                var newInput = document.createElement('div');
                newInput.classList.add('input-group', 'mb-2');
                newInput.innerHTML = `
            <input class="form-control outcome-input" type="text" name="outcomes[]" value="${value}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary remove-outcome" type="button">-</button>
            </div>
        `;
                newInput.querySelector('.remove-outcome').addEventListener('click', function() {
                    this.closest('.input-group').remove();
                });
                container.appendChild(newInput);
            }

            // You'll need to adjust your existing '#add-outcome' click event listener to use 'addOutcomeInput'
        </script>
    <script>
            document.addEventListener('DOMContentLoaded', function () {
                function addBringWithMeInput(value = '') {
                    var container = document.querySelector('#bring-with-me-container');
                    var newInput = document.createElement('div');
                    newInput.classList.add('input-group', 'mb-2');
                    newInput.innerHTML = `
            <input class="form-control bring-with-me-input" type="text" name="bring_with_me[]" value="${value}">
            <div class="input-group-append">
                <button class="btn btn-danger remove-bring-with-me" type="button">-</button>
            </div>
        `;
                    newInput.querySelector('.remove-bring-with-me').addEventListener('click', function() {
                        this.closest('.input-group').remove();
                    });
                    container.insertBefore(newInput, document.querySelector('#add-bring-with-me'));
                }

                document.querySelector('#add-bring-with-me').addEventListener('click', function() {
                    addBringWithMeInput();
                });

                // Repopulate bring_with_me inputs from old input on validation error
                @if(old('bring_with_me'))
                // Clear initial input before repopulating
                document.querySelectorAll('#bring-with-me-container .input-group').forEach(function(inputGroup, index) {
                    if (index > 0) { // Skip the first input group, it's already in the DOM
                        inputGroup.remove();
                    }
                });

                // Add inputs for each old value
                const oldBringWithMe = @json(old('bring_with_me'));
                oldBringWithMe.forEach(function(value, index) {
                    // For the first item, set value to the first input, for others, add new inputs
                    if(index === 0) {
                        document.querySelector('#bring-with-me-container .bring-with-me-input').value = value;
                    } else {
                        addBringWithMeInput(value);
                    }
                });
                @endif
            });
        </script>
@endpush


