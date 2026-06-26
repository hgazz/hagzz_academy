@extends('Academy.Layouts.master')

@section('title', trans('admin.training.edit'))

@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/training-form-modern.css') }}" rel="stylesheet">
@endpush

@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

@section('content')
    <div class="middle-content container-xxl p-0 training-create-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <header class="training-page-header">
            <div class="training-title-group">
                <button type="button" class="training-menu-toggle btn-toggle sidebarCollapse" aria-label="Toggle menu">
                    <i data-feather="menu"></i>
                </button>
                <div>
                    <span>{{ $isArabic ? 'إدارة التدريبات' : 'Training management' }}</span>
                    <h1>{{ trans('admin.training.edit') }}</h1>
                    <p>{{ $isArabic ? 'عدّل بيانات التدريب والجداول والأسعار، ثم احفظ التغييرات لتظهر للأكاديمية والعملاء.' : 'Update training details, schedule and pricing, then save your changes.' }}</p>
                </div>
            </div>
            <a href="{{ route('academy.training.index') }}" class="training-back-link">
                <i data-feather="{{ $isArabic ? 'arrow-right' : 'arrow-left' }}"></i>
                <span>{{ $isArabic ? 'العودة إلى التدريبات' : 'Back to trainings' }}</span>
            </a>
        </header>

        @if ($errors->any())
            <div class="training-error-summary" role="alert">
                <i data-feather="alert-triangle"></i>
                <div>
                    <strong>{{ $isArabic ? 'يرجى مراجعة البيانات التالية' : 'Please review the following fields' }}</strong>
                    <p>{{ $isArabic ? 'توجد بعض الحقول التي تحتاج إلى تصحيح قبل حفظ التعديلات.' : 'Some fields need correction before the changes can be saved.' }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('academy.training.update', $training) }}" id="trainingForm" novalidate>
            @method('PUT')

            <div class="training-form-layout">
                <main class="training-form-main">
                    @include('Academy.pages.training.partials._form')
                </main>

                <aside class="training-form-aside">
                    <div class="training-preview">
                        <div class="preview-accent" id="previewAccent"></div>
                        <div class="preview-heading">
                            <span>{{ $isArabic ? 'معاينة التدريب' : 'Training preview' }}</span>
                            <span class="preview-status">{{ $isArabic ? 'تعديل' : 'Edit' }}</span>
                        </div>
                        <h2 id="previewName">{{ $isArabic ? 'اسم التدريب' : 'Training name' }}</h2>
                        <p id="previewDescription">{{ $isArabic ? 'سيظهر وصف التدريب هنا أثناء التعديل.' : 'The training description will appear here as you edit.' }}</p>
                        <dl>
                            <div><dt><i data-feather="calendar"></i>{{ $isArabic ? 'الأيام' : 'Days' }}</dt><dd id="previewDays">-</dd></div>
                            <div><dt><i data-feather="clock"></i>{{ $isArabic ? 'الوقت' : 'Time' }}</dt><dd id="previewTime">-</dd></div>
                            <div><dt><i data-feather="map-pin"></i>{{ trans('admin.training.address') }}</dt><dd id="previewAddress">-</dd></div>
                            <div><dt><i data-feather="users"></i>{{ trans('admin.training.max_players') }}</dt><dd id="previewCapacity">-</dd></div>
                        </dl>
                        <div class="preview-price">
                            <span>{{ trans('admin.training.price') }}</span>
                            <strong id="previewPrice">0</strong>
                            <small>{{ $isArabic ? 'ج.م' : 'EGP' }}</small>
                        </div>
                    </div>

                    <div class="form-progress-card">
                        <div class="progress-heading">
                            <span>{{ $isArabic ? 'اكتمال البيانات' : 'Form completion' }}</span>
                            <strong id="formProgressValue">0%</strong>
                        </div>
                        <div class="progress-track"><span id="formProgressBar"></span></div>
                        <p>{{ $isArabic ? 'يمكنك تعديل الحقول ثم الضغط على حفظ التغييرات.' : 'Update the fields, then save your changes.' }}</p>
                    </div>
                </aside>
            </div>

            <footer class="training-form-footer">
                <a href="{{ route('academy.training.index') }}" class="training-cancel-button">{{ $isArabic ? 'إلغاء' : 'Cancel' }}</a>
                <button type="submit" class="training-submit-button" id="trainingSubmit">
                    <i data-feather="save"></i>
                    <span>{{ trans('admin.submit') }}</span>
                </button>
            </footer>
        </form>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('trainingForm');
            if (!form) return;

            const locale = @json(app()->getLocale());
            const requiredFields = Array.from(form.querySelectorAll('[data-required="true"]'));
            const daysSelect = document.getElementById('classes_days');

            function selectedText(id) {
                const element = document.getElementById(id);
                return element && element.selectedIndex >= 0 ? element.options[element.selectedIndex].text.trim() : '-';
            }

            function updatePreview() {
                const localizedName = document.getElementById(locale === 'ar' ? 'name_ar' : 'name_en');
                const localizedDescription = document.getElementById(locale === 'ar' ? 'description_ar' : 'description_en');
                document.getElementById('previewName').textContent = localizedName?.value.trim() || @json($isArabic ? 'اسم التدريب' : 'Training name');
                document.getElementById('previewDescription').textContent = localizedDescription?.value.trim() || @json($isArabic ? 'سيظهر وصف التدريب هنا أثناء التعديل.' : 'The training description will appear here as you edit.');
                document.getElementById('previewTime').textContent = [document.getElementById('start_time').value, document.getElementById('end_time').value].filter(Boolean).join(' - ') || '-';
                document.getElementById('previewAddress').textContent = selectedText('address_id');
                document.getElementById('previewCapacity').textContent = document.getElementById('max_players').value || '-';
                document.getElementById('previewPrice').textContent = Number(document.getElementById('price').value || 0).toLocaleString();
                document.getElementById('previewAccent').style.backgroundColor = document.getElementById('color').value || '#2563eb';

                const selectedDays = daysSelect ? Array.from(daysSelect.selectedOptions).map(option => option.text.trim()) : [];
                document.getElementById('previewDays').textContent = selectedDays.join('، ') || '-';
            }

            function updateProgress() {
                const complete = requiredFields.filter(field => {
                    if (field.tagName === 'SELECT' && field.multiple) return field.selectedOptions.length > 0;
                    return String(field.value || '').trim() !== '';
                }).length;
                const value = requiredFields.length ? Math.round((complete / requiredFields.length) * 100) : 0;
                document.getElementById('formProgressValue').textContent = `${value}%`;
                document.getElementById('formProgressBar').style.width = `${value}%`;
            }

            form.addEventListener('input', function () {
                updatePreview();
                updateProgress();
            });
            form.addEventListener('change', function () {
                updatePreview();
                updateProgress();
            });
            updatePreview();
            updateProgress();
            if (window.feather) feather.replace();
        });
    </script>
@endpush
