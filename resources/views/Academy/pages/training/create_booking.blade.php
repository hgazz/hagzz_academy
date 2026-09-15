@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'إضافة حجز أوفلاين' : 'Create offline booking')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 46px;
        border: 1px solid #bfc9d4;
        border-radius: 8px;
        display: flex;
        align-items: center;
        padding: 0 10px;
        background-color: #fff;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 44px;
        padding: 0;
        color: #3b3f5c;
        font-size: 14px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
        right: 10px;
    }
    [dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__arrow {
        left: 10px;
        right: auto;
    }
    .select2-dropdown {
        border: 1px solid #bfc9d4;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,.1);
        z-index: 1060;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #bfc9d4;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 13px;
    }
    .select2-results__option {
        padding: 8px 12px;
        font-size: 13px;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
@endpush

@section('content')
@php($ar = app()->getLocale() === 'ar')
<div class="middle-content container-xxl p-0">
    <div class="secondary-nav"><div class="breadcrumbs-container"><header class="header navbar navbar-expand-sm">
        <a href="javascript:void(0);" class="btn-toggle sidebarCollapse"><i data-feather="menu"></i></a>
        <div class="d-flex breadcrumb-content"><div class="page-header"><nav class="breadcrumb-style-one"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academy.students.index') }}">{{ $ar ? 'الطلاب والأعضاء' : 'Students & members' }}</a></li>
            <li class="breadcrumb-item active">{{ $ar ? 'حجز أوفلاين' : 'Offline booking' }}</li>
        </ol></nav></div></div>
    </header></div></div>

    <div class="row layout-top-spacing"><div class="col-12 layout-spacing"><div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div><h3 class="mb-1">{{ $ar ? 'تسجيل حجز لطالب موجود' : 'Book for an existing student' }}</h3>
                <p class="text-muted mb-0">{{ $ar ? 'هذه الصفحة للحجز والدفع فقط. بيانات الطالب تُدار من ملف الطالب.' : 'This page is for booking and payment only. Student data is managed in the student profile.' }}</p></div>
            <a href="{{ route('academy.students.create') }}" class="btn btn-outline-primary"><i data-feather="user-plus" class="me-1"></i>{{ $ar ? 'إضافة طالب جديد' : 'Add new student' }}</a>
        </div>
        <div class="card-body p-4">
            @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('academy.storeBooking') }}">@csrf
                <div class="row g-4">
                    <div class="col-lg-{{ isset($sports) && $sports->count() > 1 ? '4' : '6' }}">
                        <label class="form-label fw-bold" for="academy_student_id">{{ $ar ? 'الطالب أو العضو' : 'Student or member' }} <span class="text-danger">*</span></label>
                        <select class="form-select" id="academy_student_id" name="academy_student_id" required>
                            <option value="">{{ $ar ? 'اختر أو ابحث عن الطالب...' : 'Select or search student...' }}</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('academy_student_id') == $student->id)>{{ $student->name }}{{ $student->phone ? ' — '.$student->phone : '' }}{{ $student->guardian_name ? ' — '.$student->guardian_name : '' }}</option>
                            @endforeach
                        </select>
                        @if($students->isEmpty())<small class="text-danger">{{ $ar ? 'لا يوجد طلاب نشطون. أضف الطالب أولًا.' : 'No active students. Add a student first.' }}</small>@endif
                    </div>

                    @if(isset($sports) && $sports->count() > 1)
                    <div class="col-lg-4" id="sport_wrap">
                        <label class="form-label fw-bold" for="sport_id"><i class="fa-solid fa-trophy text-warning me-1"></i> {{ $ar ? 'نوع الرياضة' : 'Sport Type' }}</label>
                        <select class="form-select" id="sport_id">
                            <option value="">{{ $ar ? 'جميع الرياضات' : 'All sports' }}</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-lg-{{ isset($sports) && $sports->count() > 1 ? '4' : '6' }}">
                        <label class="form-label fw-bold" for="training_id">{{ trans('admin.training.training_name') }} <span class="text-danger">*</span></label>
                        <select class="form-select" id="training_id" name="training_id" required>
                            <option value="">{{ trans('admin.academies.select_training') }}</option>
                            @foreach($data as $training)
                                <option value="{{ $training->id }}" data-sport="{{ $training->sport_id }}" data-price="{{ number_format((float)$training->price, 2, '.', '') }}" @selected(old('training_id') == $training->id)>{{ $training->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4"><label class="form-label fw-bold">{{ $ar ? 'قيمة الحجز' : 'Booking total' }}</label><input id="price" class="form-control" type="number" step="0.01" readonly></div>
                    <div class="col-md-4"><label class="form-label fw-bold" for="paid_amount">{{ $ar ? 'المبلغ المدفوع' : 'Paid Amount' }} <span class="text-danger">*</span></label><input id="paid_amount" name="paid_amount" class="form-control" type="number" min="0" step="0.01" value="{{ old('paid_amount', 0) }}" required></div>
                    <div class="col-md-4"><label class="form-label fw-bold">{{ $ar ? 'المبلغ المتبقي' : 'Remaining Amount' }}</label><input id="remaining_amount" class="form-control" type="number" step="0.01" readonly></div>
                    <div class="col-12"><label class="form-label fw-bold"><i class="fa-solid fa-credit-card text-info me-1"></i> {{ trans('admin.payment_method') }} <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            @foreach(App\Helpers\PaymentMethodHelper::getMethodsForCountry(auth('academy')->user()?->academy?->country?->iso2 ?: 'SA') as $pm)
                                <label class="btn btn-outline-light border shadow-sm p-2 d-flex align-items-center gap-2">
                                    <input type="radio" name="payment_method" value="{{ $pm['id'] }}" @checked(old('payment_method', 'cash') === $pm['id']) required>
                                    <img src="{{ $pm['logo'] }}" alt="{{ $pm['name_ar'] }}" style="height: 26px; width: 50px; object-fit: contain;">
                                    <span class="fw-bold text-dark small">{{ $ar ? $pm['name_ar'] : $pm['name_en'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6 d-none" id="other_wrap"><label class="form-label fw-bold" for="payment_method_other">{{ trans('admin.payment_method_other') }}</label><input id="payment_method_other" name="payment_method_other" class="form-control" value="{{ old('payment_method_other') }}"></div>
                    <div class="col-12 d-flex justify-content-end gap-2"><a href="{{ route('academy.report.offline-joins') }}" class="btn btn-light">{{ $ar ? 'إلغاء' : 'Cancel' }}</a><button class="btn btn-primary" type="submit" @disabled($students->isEmpty())><i data-feather="check-circle" class="me-1"></i>{{ $ar ? 'حفظ الحجز' : 'Save booking' }}</button></div>
                </div>
            </form>
        </div>
    </div></div></div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Select2 on Student Select for quick search
    if (window.jQuery && $('#academy_student_id').length) {
        $('#academy_student_id').select2({
            placeholder: "{{ $ar ? 'اختر أو ابحث عن الطالب بالاسم أو الهاتف...' : 'Select or search student by name or phone...' }}",
            allowClear: true,
            width: '100%',
            dir: "{{ $ar ? 'rtl' : 'ltr' }}"
        });
    }

    const sport = document.getElementById('sport_id');
    const training = document.getElementById('training_id');
    const paid = document.getElementById('paid_amount');
    const price = document.getElementById('price');
    const remaining = document.getElementById('remaining_amount');
    const otherWrap = document.getElementById('other_wrap');
    const otherInput = document.getElementById('payment_method_other');
    const methodRadios = document.querySelectorAll('input[name="payment_method"]');

    // Filter trainings by sport if multiple sports exist
    if (sport && training) {
        const trainingOptions = Array.from(training.querySelectorAll('option[data-sport]'));

        sport.addEventListener('change', () => {
            const selectedSport = sport.value;
            const currentSelected = training.value;
            let currentValid = false;

            trainingOptions.forEach(opt => {
                const optSport = opt.dataset.sport;
                const match = !selectedSport || optSport === selectedSport;
                opt.hidden = !match;
                if (!match && opt.value === currentSelected) {
                    currentValid = false;
                } else if (match && opt.value === currentSelected) {
                    currentValid = true;
                }
            });

            if (!currentValid && currentSelected) {
                training.value = '';
                totals();
            }
        });
    }

    function totals() {
        const total = Number(training?.selectedOptions?.[0]?.dataset?.price || 0);
        if (price) price.value = total.toFixed(2);
        if (remaining) remaining.value = Math.max(0, total - Number(paid?.value || 0)).toFixed(2);
    }

    function methodState() {
        const selected = document.querySelector('input[name="payment_method"]:checked')?.value;
        if (otherWrap) {
            const isOther = selected === 'other';
            otherWrap.classList.toggle('d-none', !isOther);
            if (otherInput) otherInput.required = isOther;
        }
    }

    if (training) training.addEventListener('change', totals);
    if (paid) paid.addEventListener('input', totals);
    methodRadios.forEach(r => r.addEventListener('change', methodState));

    totals();
    methodState();
});
</script>
@endpush
