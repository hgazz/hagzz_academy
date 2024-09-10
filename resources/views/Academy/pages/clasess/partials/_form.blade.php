@csrf

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="training_id"><span class="text-danger">*</span> {{trans('admin.training.training')}} </label>
        <select id="training_id" class="form-select pt-2" name="training_id" >
            <option value=""> {{trans('admin.clasess.select_training')}} </option>
            @foreach($academyTrainings as $training)
                <option value="{{$training->id}}" @selected(old('training_id',  (isset($class) ? $class->training_id : '')) == $training->id) >{{$training->name}}</option>
            @endforeach
        </select>
        @error('training_id')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="date">
            {{ trans('admin.clasess.date') }}
            <span id="date-hint" class="text-muted"></span>
        </label>
        <input class="form-control" type="date" value="{{   old('date',( isset($class) ? $class->date : '')) }}" id="date" name="date">
        @error('date')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
    @foreach (\App\Services\TranslatableService::getTranslatableInputs(App\Models\TClass::class) as $name => $data)
        <div class="col-md-12 mb-3">
            <label for="{{$name}}" class="form-label">{{trans('admin.clasess.'.$name)}}<span class="text-danger">*</span></label>
            <input type="text" id="{{$name}}" name="{{$name}}" maxlength="50" class="form-control"
                   @php
                       $language = $name == 'title_en' ? 'en' : 'ar';
                       $defaultValue = isset($class) ? $class->getTranslation('title', $language) : '';
                   @endphp
                   value="{{ old($name, $defaultValue) }}"
                   placeholder="Enter {{$name}}">
            @error($name)
            <span class="text-danger">*{{$message}}</span>
            @enderror
        </div>
    @endforeach

    <div class="col-md-6 mb-3">
        <label for="start_time">{{ trans('admin.training.start_time') }}</label>
        <input class="form-control" type="time" value="{{ old('start_time', (isset($class) ? $class->start_time : ''))}}" id="start_time" name="start_time">
        @error('start_time')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="end_time">{{ trans('admin.training.end_time') }}</label>
        <input class="form-control" type="time" value="{{ old('end_time', (isset($class) ? $class->end_time : ''))}}" id="end_time" name="end_time">
        @error('end_time')
        <span class="text-danger">*{{$message}}</span>
        @enderror
    </div>
</div>
@if(request()->url() === route('academy.class.create'))

    <div class="row">
        <div class="row">
            <div class="col-6">
                <button id="add-outcome" type="button" class="btn btn-primary">{{ trans('admin.clasess.add_more') }}</button>
                <div class="col-md-12 mb-3" id="outcomes-container">
                    <label>{{ trans('admin.clasess.out_comes') }}</label>
                    <!-- Initial input fields for English and Arabic -->
                    <div class="input-group mb-2">
                        <textarea class="form-control outcomes-ar" name="outcomes[ar][]" placeholder="Outcome in Arabic"></textarea>
                        <textarea class="form-control outcomes-en" name="outcomes[en][]" placeholder="Outcome in English"></textarea>
                    </div>
                    @error('outcomes.*')
                    <span class="text-danger">*{{$message}}</span>
                    @enderror
                </div>
            </div>

            <div class="col-6">
                <button id="add-bring-with-me" type="button" class="btn btn-primary">{{ trans('admin.clasess.add_more') }}</button>
                <div class="col-md-12 mb-3" id="bring-with-me-container">
                    <label>{{ trans('admin.clasess.bring_with_me') }}</label>
                    <div class="input-group mb-2">
                        <textarea class="form-control bring-with-me-input-ar" name="bring_with_me[ar][]" placeholder="Bring with me in Arabic"></textarea>
                        <textarea class="form-control bring-with-me-input-en" name="bring_with_me[en][]" placeholder="Bring with me in English"></textarea>
                    </div>
                    @error('bring_with_me.*')
                    <span class="text-danger">*{{$message}}</span>
                    @enderror
                </div>
            </div>

        </div>

    </div>

@else
    @isset($class)
        <div class="row">
            <div class="col-md-6 mb-3" id="outcomes-container">
                <label>{{ trans('admin.clasess.out_comes') }}</label>
                @php
                    // Assuming $class->out_comes is an associative array with 'en' and 'ar' keys
                    $outcomesEn = $class->out_comes['en'] ?? [];
                    $outcomesAr = $class->out_comes['ar'] ?? [];
                    $maxOutcomes = max(count($outcomesEn), count($outcomesAr));
                @endphp
                @for ($i = 0; $i < $maxOutcomes; $i++)
                    <div class="input-group mb-2">
                        <textarea class="form-control outcome-input-ar" name="outcomes[ar][]" placeholder="Outcome in Arabic">{{ $outcomesAr[$i] ?? '' }}</textarea>
                        <textarea class="form-control outcome-input-en" name="outcomes[en][]" placeholder="Outcome in English">{{ $outcomesEn[$i] ?? '' }}</textarea>
                        <div class="input-group-append">
                            <button class="btn btn-danger btn-sm m-2 remove-outcome" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endfor
                <button id="add-outcome" type="button" class="btn btn-primary">{{ trans('admin.clasess.add_more') }}</button>
                @error('outcomes.*')
                <span class="text-danger">*{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6 mb-3" id="bring-with-me-container">
                <label>{{ trans('admin.clasess.bring_with_me') }}</label>
                @php
                    // Assuming $class->bring_with_me is an associative array with 'en' and 'ar' keys
                    $bringWithMeEn = $class->bring_with_me['en'] ?? [];
                    $bringWithMeAr = $class->bring_with_me['ar'] ?? [];
                    $maxBringWithMe = max(count($bringWithMeEn), count($bringWithMeAr));
                @endphp
                @for ($i = 0; $i < $maxBringWithMe; $i++)
                    <div class="input-group mb-2">
                        <textarea class="form-control bring-with-me-input-ar" name="bring_with_me[ar][]" placeholder="Bring with me in Arabic">{{ $bringWithMeAr[$i] ?? '' }}</textarea>
                        <textarea class="form-control bring-with-me-input-en" name="bring_with_me[en][]" placeholder="Bring with me in English">{{ $bringWithMeEn[$i] ?? '' }}</textarea>
                        <div class="input-group-append">
                            <button class="btn btn-danger btn-sm m-2 remove-bring-with-me" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endfor
                <button id="add-bring-with-me" type="button" class="btn btn-primary">{{ trans('admin.clasess.add_more') }}</button>
                @error('bring_with_me.*')
                <span class="text-danger">*{{ $message }}</span>
                @enderror
            </div>
        </div>

    @endisset

@endif


@push('js')
    <script !src="">
        document.addEventListener('DOMContentLoaded', function () {
            function addOutcomeInput(valueEn = '', valueAr = '') {
                var container = document.querySelector('#outcomes-container');
                var newInput = document.createElement('div');
                newInput.classList.add('input-group', 'mb-2');
                newInput.innerHTML = `
        <textarea class="form-control outcome-input-ar" name="outcomes[ar][]" placeholder="Outcome in Arabic">${valueAr}</textarea>
        <textarea class="form-control outcome-input-en" name="outcomes[en][]" placeholder="Outcome in English">${valueEn}</textarea>
        <div class="input-group-append">
            <button class="btn btn-danger btn-sm m-2 remove-outcome" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
            </button>
        </div>
    `;
                newInput.querySelector('.remove-outcome').addEventListener('click', function() {
                    this.closest('.input-group').remove();
                });
                container.appendChild(newInput);
            }

            function addBringWithMeInput(valueEn = '', valueAr = '') {
                var container = document.querySelector('#bring-with-me-container');
                var newInput = document.createElement('div');
                newInput.classList.add('input-group', 'mb-2');
                newInput.innerHTML = `

        <textarea class="form-control bring-with-me-input-ar" name="bring_with_me[ar][]" placeholder="Bring with me in Arabic">${valueAr}</textarea>
 <textarea class="form-control bring-with-me-input-en" name="bring_with_me[en][]" placeholder="Bring with me in English">${valueEn}</textarea>
        <div class="input-group-append">
            <button class="btn btn-danger btn-sm m-2 remove-bring-with-me" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
            </button>
        </div>
    `;
                newInput.querySelector('.remove-bring-with-me').addEventListener('click', function() {
                    this.closest('.input-group').remove();
                });
                container.appendChild(newInput);
            }

            document.querySelector('#add-outcome').addEventListener('click', function() {
                addOutcomeInput();
            });

            document.querySelector('#add-bring-with-me').addEventListener('click', function() {
                addBringWithMeInput();
            });

            // Adding event listeners for existing data
            document.querySelectorAll('.remove-outcome').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.input-group').remove();
                });
            });

            document.querySelectorAll('.remove-bring-with-me').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.input-group').remove();
                });
            });
        });

        // document.addEventListener('DOMContentLoaded', function () {
        //     function addOutcomeInput(valueEn = '', valueAr = '') {
        //         var container = document.querySelector('#outcomes-container');
        //         var newInput = document.createElement('div');
        //         newInput.classList.add('input-group', 'mb-2');
        //         newInput.innerHTML = `
        //     <input class="form-control outcome-input-en" type="text" name="outcomes[en][]" value="${valueEn}" placeholder="Outcome in English">
        //     <input class="form-control outcome-input-ar" type="text" name="outcomes[ar][]" value="${valueAr}" placeholder="Outcome in Arabic">
        //     <div class="input-group-append">
        //         <button class="btn btn-danger btn-sm m-2 remove-outcome" type="button">
        //             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
        //                 <polyline points="3 6 5 6 21 6"></polyline>
        //                 <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        //                 <line x1="10" y1="11" x2="10" y2="17"></line>
        //                 <line x1="14" y1="11" x2="14" y2="17"></line>
        //             </svg>
        //         </button>
        //     </div>
        // `;
        //         newInput.querySelector('.remove-outcome').addEventListener('click', function() {
        //             this.closest('.input-group').remove();
        //         });
        //         container.appendChild(newInput);
        //     }
        //
        //     function addBringWithMeInput(valueEn = '', valueAr = '') {
        //         var container = document.querySelector('#bring-with-me-container');
        //         var newInput = document.createElement('div');
        //         newInput.classList.add('input-group', 'mb-2');
        //         newInput.innerHTML = `
        //     <input class="form-control bring-with-me-input-en" type="text" name="bring_with_me[en][]" value="${valueEn}" placeholder="Bring with me in English">
        //     <input class="form-control bring-with-me-input-ar" type="text" name="bring_with_me[ar][]" value="${valueAr}" placeholder="Bring with me in Arabic">
        //     <div class="input-group-append">
        //         <button class="btn btn-danger btn-sm m-2 remove-bring-with-me" type="button">
        //             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
        //                 <polyline points="3 6 5 6 21 6"></polyline>
        //                 <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        //                 <line x1="10" y1="11" x2="10" y2="17"></line>
        //                 <line x1="14" y1="11" x2="14" y2="17"></line>
        //             </svg>
        //         </button>
        //     </div>
        // `;
        //         newInput.querySelector('.remove-bring-with-me').addEventListener('click', function() {
        //             this.closest('.input-group').remove();
        //         });
        //         container.appendChild(newInput);
        //     }
        //
        //     document.querySelector('#add-outcome').addEventListener('click', function() {
        //         addOutcomeInput();
        //     });
        //
        //     document.querySelector('#add-bring-with-me').addEventListener('click', function() {
        //         addBringWithMeInput();
        //     });
        //
        //     // Initial input rows do not have delete buttons, so no need for event listeners there
        // });
    </script>
{{--    <script>--}}
{{--            // Assuming you have old inputs for 'outcomes' and 'bring_with_me'--}}
{{--            var oldOutcomes = @json(old('outcomes', []));--}}
{{--            var oldBringWithMe = @json(old('bring_with_me', []));--}}

{{--            // Function to repopulate outcomes--}}
{{--            function repopulateOutcomes() {--}}
{{--                if (oldOutcomes.length > 0) {--}}
{{--                    // Clear initial input if old data exists--}}
{{--                    document.querySelector('#outcomes-container .input-group').remove();--}}
{{--                    oldOutcomes.forEach(function(value) {--}}
{{--                        addOutcomeInput(value); // Use the existing function to add inputs, passing the old value--}}
{{--                    });--}}
{{--                }--}}
{{--            }--}}

{{--            // Function to repopulate bring with me inputs, similar to repopulateOutcomes--}}

{{--            document.addEventListener('DOMContentLoaded', function () {--}}
{{--                // Call repopulate functions on page load--}}
{{--                repopulateOutcomes();--}}
{{--                // repopulateBringWithMe(); // Implement a similar function for bring_with_me--}}

{{--                document.querySelector('#add-outcome').addEventListener('click', function() {--}}
{{--                    var container = document.querySelector('#outcomes-container');--}}
{{--                    var newInput = document.createElement('div');--}}
{{--                    newInput.classList.add('input-group', 'mb-2');--}}
{{--                    newInput.innerHTML = `--}}
{{--            <input class="form-control outcome-input" type="text" name="outcomes[]" value="">--}}

{{--            <div class="input-group-append">--}}
{{--                <button class="btn btn-danger btn-sm m-2 remove-outcome" type="button">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>--}}
{{--            </div>--}}
{{--        `;--}}
{{--                    // Add event listener for the remove button in the new input group--}}
{{--                    newInput.querySelector('.remove-outcome').addEventListener('click', function() {--}}
{{--                        this.closest('.input-group').remove();--}}
{{--                    });--}}
{{--                    container.insertBefore(newInput, this);--}}
{{--                });--}}

{{--                // Initial removal button event listener--}}
{{--                document.querySelectorAll('.remove-outcome').forEach(button => {--}}
{{--                    button.addEventListener('click', function() {--}}
{{--                        this.closest('.input-group').remove();--}}
{{--                    });--}}
{{--                });--}}

{{--            });--}}

{{--            // Adjust the 'addOutcomeInput' function to accept a value parameter and set it to the input--}}
{{--            function addOutcomeInput(value = '') {--}}
{{--                var container = document.querySelector('#outcomes-container');--}}
{{--                var newInput = document.createElement('div');--}}
{{--                newInput.classList.add('input-group', 'mb-2');--}}
{{--                newInput.innerHTML = `--}}
{{--            <input class="form-control outcome-input" type="text" name="outcomes[]" value="${value}">--}}

{{--            <div class="input-group-append">--}}
{{--                <button class="btn btn-danger btn-sm m-2 remove-outcome" type="button">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>--}}
{{--            </div>--}}
{{--        `;--}}
{{--                newInput.querySelector('.remove-outcome').addEventListener('click', function() {--}}
{{--                    this.closest('.input-group').remove();--}}
{{--                });--}}
{{--                container.appendChild(newInput);--}}
{{--            }--}}

{{--            // You'll need to adjust your existing '#add-outcome' click event listener to use 'addOutcomeInput'--}}
{{--        </script>--}}
{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', function () {--}}
{{--            function addBringWithMeInput(value = '') {--}}
{{--                var container = document.querySelector('#bring-with-me-container');--}}
{{--                var newInput = document.createElement('div');--}}
{{--                newInput.classList.add('input-group', 'mb-2');--}}
{{--                newInput.innerHTML = `--}}
{{--            <input class="form-control bring-with-me-input" type="text" name="bring_with_me[]" value="${value}">--}}
{{--            <div class="input-group-append">--}}
{{--                <button class="btn btn-danger btn-sm m-2 remove-bring-with-me" type="button">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">--}}
{{--                        <polyline points="3 6 5 6 21 6"></polyline>--}}
{{--                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>--}}
{{--                        <line x1="10" y1="11" x2="10" y2="17"></line>--}}
{{--                        <line x1="14" y1="11" x2="14" y2="17"></line>--}}
{{--                    </svg>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--        `;--}}
{{--                container.insertBefore(newInput, document.querySelector('#add-bring-with-me'));--}}
{{--            }--}}

{{--            // Event delegation for dynamically added remove buttons--}}
{{--            document.querySelector('#bring-with-me-container').addEventListener('click', function(event) {--}}
{{--                if (event.target.closest('.remove-bring-with-me')) {--}}
{{--                    event.target.closest('.input-group').remove();--}}
{{--                }--}}
{{--            });--}}

{{--            document.querySelector('#add-bring-with-me').addEventListener('click', function() {--}}
{{--                addBringWithMeInput();--}}
{{--            });--}}

{{--            // Repopulate bring_with_me inputs from old input on validation error--}}
{{--            @if(old('bring_with_me'))--}}
{{--            const oldBringWithMe = @json(old('bring_with_me'));--}}
{{--            // Clear initial input before repopulating--}}
{{--            document.querySelectorAll('#bring-with-me-container .input-group').forEach(function(inputGroup, index) {--}}
{{--                if (index > 0) { // Skip the first input group, it's already in the DOM--}}
{{--                    inputGroup.remove();--}}
{{--                }--}}
{{--            });--}}
{{--            // Add inputs for each old value--}}
{{--            oldBringWithMe.forEach(function(value, index) {--}}
{{--                if (index === 0) {--}}
{{--                    document.querySelector('#bring-with-me-container .bring-with-me-input').value = value;--}}
{{--                } else {--}}
{{--                    addBringWithMeInput(value);--}}
{{--                }--}}
{{--            });--}}
{{--            @endif--}}
{{--        });--}}
{{--    </script>--}}
    <script>
        $(document).ready(function() {
            $('#training_id').change(function() {
                var trainingId = $(this).val();
                if (trainingId) {
                    $.ajax({
                        url: '{{ route('academy.class.trainingDate') }}',
                        type: 'GET',
                        data: { training_id: trainingId },
                        success: function(response) {
                            if (response.status === 'success') {
                                var startDate = response.data.start_date;
                                console.log(startDate);
                                var endDate = response.data.end_date;
                                $('#date-hint').text('({{trans("admin.class_must_be_between")}} ' + startDate + ' & ' + endDate + ')');
                            }
                        }
                    });
                } else {
                    $('#date-hint').text('');
                }
            });
        });
    </script>
@endpush


