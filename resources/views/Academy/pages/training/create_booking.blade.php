@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'إضافة حجز أوفلاين' : 'Create offline booking')

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
                    <div class="col-lg-6">
                        <label class="form-label fw-bold" for="academy_student_id">{{ $ar ? 'الطالب أو العضو' : 'Student or member' }} <span class="text-danger">*</span></label>
                        <select class="form-select" id="academy_student_id" name="academy_student_id" required>
                            <option value="">{{ $ar ? 'اختر من الطلاب المسجلين' : 'Select a registered student' }}</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('academy_student_id') == $student->id)>{{ $student->name }}{{ $student->phone ? ' — '.$student->phone : '' }}{{ $student->guardian_name ? ' — '.$student->guardian_name : '' }}</option>
                            @endforeach
                        </select>
                        @if($students->isEmpty())<small class="text-danger">{{ $ar ? 'لا يوجد طلاب نشطون. أضف الطالب أولًا.' : 'No active students. Add a student first.' }}</small>@endif
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label fw-bold" for="training_id">{{ trans('admin.training.training_name') }} <span class="text-danger">*</span></label>
                        <select class="form-select" id="training_id" name="training_id" required>
                            <option value="">{{ trans('admin.academies.select_training') }}</option>
                            @foreach($data as $training)<option value="{{ $training->id }}" data-price="{{ number_format((float)$training->price, 2, '.', '') }}" @selected(old('training_id') == $training->id)>{{ $training->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-4"><label class="form-label fw-bold">{{ $ar ? 'قيمة الحجز' : 'Booking total' }}</label><input id="price" class="form-control" type="number" step="0.01" readonly></div>
                    <div class="col-md-4"><label class="form-label fw-bold" for="paid_amount">{{ trans('admin.bookings.paid_amount') }} <span class="text-danger">*</span></label><input id="paid_amount" name="paid_amount" class="form-control" type="number" min="0" step="0.01" value="{{ old('paid_amount', 0) }}" required></div>
                    <div class="col-md-4"><label class="form-label fw-bold">{{ trans('admin.bookings.remaining_amount') }}</label><input id="remaining_amount" class="form-control" type="number" step="0.01" readonly></div>
                    <div class="col-md-6"><label class="form-label fw-bold" for="payment_method">{{ trans('admin.payment_method') }} <span class="text-danger">*</span></label>
                        <select id="payment_method" name="payment_method" class="form-select" required>@foreach(['cash','instapay','fawry','app_online','other'] as $method)<option value="{{ $method }}" @selected(old('payment_method','cash') === $method)>{{ trans('admin.payment_methods.'.$method) }}</option>@endforeach</select>
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
<script>
document.addEventListener('DOMContentLoaded', () => {
    const training = document.getElementById('training_id'), paid = document.getElementById('paid_amount');
    const price = document.getElementById('price'), remaining = document.getElementById('remaining_amount');
    const method = document.getElementById('payment_method'), other = document.getElementById('other_wrap');
    function totals(){ const total = Number(training.selectedOptions[0]?.dataset.price || 0); price.value=total.toFixed(2); remaining.value=Math.max(0,total-Number(paid.value||0)).toFixed(2); }
    function methodState(){ other.classList.toggle('d-none', method.value !== 'other'); document.getElementById('payment_method_other').required = method.value === 'other'; }
    training.addEventListener('change', totals); paid.addEventListener('input', totals); method.addEventListener('change', methodState); totals(); methodState();
});
</script>
@endpush
