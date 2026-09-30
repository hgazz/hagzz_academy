<?php
/**
 * facility_helper.php
 * دوال مساعدة عامة لمحرك المصطلحات الديناميكي
 *
 * استخدام في Blade:
 *   {{ facility_term('student') }}          → طالب / عضو / عميل (مفرد)
 *   {{ facility_term('coach', null, true) }} → المدربون / الكباتن / الأخصائيون (جمع)
 *   @if(is_gym())  ... @endif
 *   @if(is_health_center()) ... @endif
 *   @if(has_camps()) ... @endif
 */

use App\Support\FacilityTerminology;

if (!function_exists('facility_term')) {
    /**
     * إرجاع المصطلح المناسب لنوع المنشأة واللغة الحالية
     *
     * @param string      $key     مفتاح المصطلح: 'student','coach','subscription',...
     * @param string|null $type    نوع المنشأة (null = يحمّل من المنشأة المسجلة)
     * @param bool        $plural  true للجمع
     */
    function facility_term(string $key, ?string $type = null, bool $plural = false): string
    {
        return FacilityTerminology::get($key, $type, $plural);
    }
}

if (!function_exists('is_gym')) {
    /**
     * هل المنشأة المسجلة صالة جيم أو هجينة؟
     */
    function is_gym(?string $type = null): bool
    {
        return FacilityTerminology::isGym($type);
    }
}

if (!function_exists('is_health_center')) {
    /**
     * هل المنشأة المسجلة مركز صحي أو هجينة؟
     */
    function is_health_center(?string $type = null): bool
    {
        return FacilityTerminology::isHealthCenter($type);
    }
}

if (!function_exists('is_academy')) {
    /**
     * هل المنشأة المسجلة أكاديمية رياضية أو هجينة؟
     */
    function is_academy(?string $type = null): bool
    {
        return FacilityTerminology::isAcademy($type);
    }
}

if (!function_exists('has_camps')) {
    /**
     * هل تدعم المنشأة وحدة المعسكرات والبطولات؟ (للأكاديميات فقط)
     */
    function has_camps(?string $type = null): bool
    {
        return FacilityTerminology::hasCampsAndCompetitions($type);
    }
}

if (!function_exists('show_guardian_fields')) {
    /**
     * هل يجب عرض حقول ولي الأمر؟ (للأكاديميات وناشئي الأطفال فقط)
     */
    function show_guardian_fields(?string $type = null): bool
    {
        return FacilityTerminology::showGuardianFields($type);
    }
}

if (!function_exists('show_school_fields')) {
    /**
     * هل يجب عرض حقول المدرسة؟ (للأكاديميات وناشئي الأطفال فقط)
     */
    function show_school_fields(?string $type = null): bool
    {
        return FacilityTerminology::showSchoolFields($type);
    }
}
