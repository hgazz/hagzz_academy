<?php

namespace App\Support;

/**
 * FacilityTerminology — محرك المصطلحات الديناميكي
 *
 * يعيد المسمى المناسب لكل عنصر في الواجهة بناءً على:
 *   1. نوع نشاط المنشأة (business_type): academy | gym | health_center | venue | hybrid
 *   2. لغة الواجهة (locale): ar | en
 *
 * الفلسفة: الجيم والمراكز الصحية يستخدمان غالباً نفس المسميات (عضو / عميل)
 * وكلاهما موجه للبالغين الكبار (Adults) — وليس للأطفال والناشئين.
 *
 * الاستخدام في Blade:
 *   {{ facility_term('student') }}           → طالب / عضو / عميل
 *   {{ facility_term('coach') }}             → مدرب / كابتن / أخصائي
 *   {{ facility_term('subscription') }}      → اشتراك / عضوية / باقة جلسات
 *   {{ facility_term('group') }}             → مجموعة / باقة / فئة خدمة
 *   {{ facility_term('attendance') }}        → حضور الحصة / دخول البوابة / تأكيد الموعد
 */
class FacilityTerminology
{
    /**
     * المصطلحات المكونة من (مفردة مذكر, مفردة مؤنث, جمع, وصف النشاط, نوع بطاقة العضو)
     * القيم: [ar_singular, ar_plural, en_singular, en_plural]
     */
    protected static array $terms = [

        // ── اسم العميل / المشترك ─────────────────────────────────────
        'student' => [
            'academy'       => ['ar' => 'الطالب / المشترك', 'ar_pl' => 'الطلاب والمشتركين',  'en' => 'Student',   'en_pl' => 'Students'],
            'gym'           => ['ar' => 'العضو',            'ar_pl' => 'الأعضاء',             'en' => 'Member',    'en_pl' => 'Members'],
            'health_center' => ['ar' => 'العميل / المراجع',  'ar_pl' => 'العملاء والمراجعين',  'en' => 'Client',    'en_pl' => 'Clients'],
            'venue'         => ['ar' => 'الحاجز',           'ar_pl' => 'الحاجزين',            'en' => 'Booker',    'en_pl' => 'Bookers'],
            'hybrid'        => ['ar' => 'العضو',            'ar_pl' => 'الأعضاء والمشتركين', 'en' => 'Member',    'en_pl' => 'Members'],
        ],

        // ── قائمة الأشخاص ────────────────────────────────────────────
        'students_list' => [
            'academy'       => ['ar' => 'قائمة الطلاب',       'en' => 'Students List'],
            'gym'           => ['ar' => 'سجل الأعضاء',         'en' => 'Members Registry'],
            'health_center' => ['ar' => 'ملفات المراجعين',     'en' => 'Client Records'],
            'venue'         => ['ar' => 'الحاجزون',            'en' => 'Bookers'],
            'hybrid'        => ['ar' => 'سجل الأعضاء والمشتركين', 'en' => 'Members & Subscribers'],
        ],

        // ── زر إضافة عضو جديد ────────────────────────────────────────
        'add_student' => [
            'academy'       => ['ar' => 'إضافة طالب جديد',      'en' => 'Add New Student'],
            'gym'           => ['ar' => 'تسجيل عضو جديد',        'en' => 'Register New Member'],
            'health_center' => ['ar' => 'إضافة عميل / مراجع',    'en' => 'Add New Client'],
            'venue'         => ['ar' => 'إضافة عضو',             'en' => 'Add Member'],
            'hybrid'        => ['ar' => 'تسجيل عضو جديد',        'en' => 'Register New Member'],
        ],

        // ── مقدم الخدمة / المدرب ─────────────────────────────────────
        'coach' => [
            'academy'       => ['ar' => 'المدرب',             'ar_pl' => 'المدربون',         'en' => 'Coach',             'en_pl' => 'Coaches'],
            'gym'           => ['ar' => 'الكابتن / مدرب PT',   'ar_pl' => 'كباتن الصالة',     'en' => 'Captain / PT',      'en_pl' => 'Personal Trainers'],
            'health_center' => ['ar' => 'الأخصائي / المعالج', 'ar_pl' => 'الأخصائيين الطبيين','en' => 'Specialist / Therapist','en_pl' => 'Specialists'],
            'venue'         => ['ar' => 'المسؤول',            'ar_pl' => 'المسؤولون',        'en' => 'Staff',             'en_pl' => 'Staff'],
            'hybrid'        => ['ar' => 'المدرب / الكابتن',   'ar_pl' => 'المدربون والأخصائيون','en' => 'Trainer',          'en_pl' => 'Trainers'],
        ],

        // ── قائمة المدربين ────────────────────────────────────────────
        'coaches_list' => [
            'academy'       => ['ar' => 'طاقم التدريب',                   'en' => 'Coaching Staff'],
            'gym'           => ['ar' => 'كباتن الصالة',                   'en' => 'Personal Trainers (PT)'],
            'health_center' => ['ar' => 'الأخصائيين الطبيين',             'en' => 'Medical Specialists & Therapists'],
            'venue'         => ['ar' => 'طاقم العمل',                     'en' => 'Staff Members'],
            'hybrid'        => ['ar' => 'المدربون والأخصائيون',           'en' => 'Trainers & Specialists'],
        ],

        // ── البرنامج / الخدمة ─────────────────────────────────────────
        'training' => [
            'academy'       => ['ar' => 'البرنامج التدريبي',  'ar_pl' => 'البرامج التدريبية', 'en' => 'Training Program', 'en_pl' => 'Training Programs'],
            'gym'           => ['ar' => 'باقة العضوية',       'ar_pl' => 'باقات العضوية',     'en' => 'Membership Plan',  'en_pl' => 'Membership Plans'],
            'health_center' => ['ar' => 'الجلسات والخدمات',   'ar_pl' => 'الخدمات والجلسات',  'en' => 'Therapy Service',  'en_pl' => 'Services & Sessions'],
            'venue'         => ['ar' => 'حجز الملعب',         'ar_pl' => 'حجوزات الملاعب',    'en' => 'Venue Booking',    'en_pl' => 'Venue Bookings'],
            'hybrid'        => ['ar' => 'النشاط والباقة',      'ar_pl' => 'الأنشطة والباقات',  'en' => 'Activity Plan',    'en_pl' => 'Activity Plans'],
        ],

        // ── الحصة / الموعد ────────────────────────────────────────────
        'class' => [
            'academy'       => ['ar' => 'الحصص والمواعيد',   'ar_pl' => 'الحصص والمواعيد',       'en' => 'Class',        'en_pl' => 'Classes & Sessions'],
            'gym'           => ['ar' => 'حصص اللياقة',       'ar_pl' => 'حصص اللياقة',           'en' => 'Fitness Class', 'en_pl' => 'Fitness Classes'],
            'health_center' => ['ar' => 'مواعيد الجلسات',    'ar_pl' => 'مواعيد الجلسات',        'en' => 'Appointment',  'en_pl' => 'Appointments'],
            'venue'         => ['ar' => 'وقت الحجز',         'ar_pl' => 'أوقات الحجز',           'en' => 'Time Slot',    'en_pl' => 'Time Slots'],
            'hybrid'        => ['ar' => 'الحصة أو الموعد',   'ar_pl' => 'الحصص والمواعيد',       'en' => 'Session',      'en_pl' => 'Sessions'],
        ],

        // ── المجموعة / الفئة ──────────────────────────────────────────
        'group' => [
            'academy'       => ['ar' => 'المجموعة',        'ar_pl' => 'المجموعات',          'en' => 'Group',        'en_pl' => 'Groups'],
            'gym'           => ['ar' => 'فئة العضوية',      'ar_pl' => 'فئات وباقات العضوية','en' => 'Category',     'en_pl' => 'Categories'],
            'health_center' => ['ar' => 'نوع الخدمة',       'ar_pl' => 'أنواع الخدمات',      'en' => 'Service Type', 'en_pl' => 'Service Types'],
            'venue'         => ['ar' => 'المساحة',          'ar_pl' => 'المساحات والملاعب',  'en' => 'Space',        'en_pl' => 'Spaces'],
            'hybrid'        => ['ar' => 'الفئة أو المجموعة','ar_pl' => 'الفئات والمجموعات', 'en' => 'Group',        'en_pl' => 'Groups'],
        ],

        // ── الاشتراك ──────────────────────────────────────────────────
        'subscription' => [
            'academy'       => ['ar' => 'الاشتراك التدريبي',    'ar_pl' => 'اشتراكات التدريب',   'en' => 'Training Subscription', 'en_pl' => 'Subscriptions'],
            'gym'           => ['ar' => 'عضوية الجيم',          'ar_pl' => 'عضويات الجيم',       'en' => 'Gym Membership',        'en_pl' => 'Memberships'],
            'health_center' => ['ar' => 'باقة الجلسات',         'ar_pl' => 'باقات الجلسات',      'en' => 'Session Package',        'en_pl' => 'Session Packages'],
            'venue'         => ['ar' => 'اشتراك ملعب دوري',     'ar_pl' => 'اشتراكات الملاعب',   'en' => 'Venue Subscription',    'en_pl' => 'Venue Subscriptions'],
            'hybrid'        => ['ar' => 'العضوية والاشتراك',      'ar_pl' => 'العضويات والاشتراكات','en' => 'Membership',            'en_pl' => 'Memberships'],
        ],

        // ── تسجيل الحضور ─────────────────────────────────────────────
        'attendance' => [
            'academy'       => ['ar' => 'حضور الحصة',          'en' => 'Class Attendance'],
            'gym'           => ['ar' => 'دخول البوابة',         'en' => 'Gate Entry / Check-in'],
            'health_center' => ['ar' => 'تأكيد موعد الجلسة',    'en' => 'Session Check-in'],
            'venue'         => ['ar' => 'حضور الملعب',          'en' => 'Venue Attendance'],
            'hybrid'        => ['ar' => 'الحضور والدخول',       'en' => 'Attendance & Check-in'],
        ],

        // ── الملف الفني / الشخصي ─────────────────────────────────────
        'technical_file' => [
            'academy'       => ['ar' => 'المهارات والبطولة',           'en' => 'Sports & Skills Record'],
            'gym'           => ['ar' => 'قياسات InBody',              'en' => 'InBody Metrics & Reports'],
            'health_center' => ['ar' => 'السجل والملف الصحي',          'en' => 'Health & Clinical Record'],
            'venue'         => ['ar' => 'سجل الحجوزات',               'en' => 'Booking History'],
            'hybrid'        => ['ar' => 'الملف الشخصي والصحي',        'en' => 'Personal & Health Profile'],
        ],

        // ── بطاقة العضو (نوع الكارت المطبوع) ────────────────────────
        'member_card_type' => [
            'academy'       => ['ar' => 'بطاقة طالب ورياضي',   'en' => 'Student & Athlete Card'],
            'gym'           => ['ar' => 'بطاقة عضوية الصالة',   'en' => 'Gym Membership Card'],
            'health_center' => ['ar' => 'بطاقة عميل المركز',    'en' => 'Health Center Client Card'],
            'venue'         => ['ar' => 'بطاقة حجوزات',         'en' => 'Venue Booking Card'],
            'hybrid'        => ['ar' => 'بطاقة عضوية متكاملة',  'en' => 'Integrated Member Card'],
        ],

        // ── رجوع لقائمة الأشخاص (زر back) ───────────────────────────
        'back_to_students' => [
            'academy'       => ['ar' => 'العودة للطلاب',    'en' => 'Back to Students'],
            'gym'           => ['ar' => 'العودة للأعضاء',   'en' => 'Back to Members'],
            'health_center' => ['ar' => 'العودة للعملاء',   'en' => 'Back to Clients'],
            'venue'         => ['ar' => 'العودة للحاجزين',  'en' => 'Back to Bookers'],
            'hybrid'        => ['ar' => 'العودة للأعضاء',   'en' => 'Back to Members'],
        ],

        // ── هل لديه ولي أمر؟ (الأكاديميات للأطفال فقط) ──────────────
        'show_guardian_fields' => [
            'academy'       => true,
            'gym'           => false,
            'health_center' => false,
            'venue'         => false,
            'hybrid'        => false,
        ],

        // ── هل لديه حقل المدرسة؟ ─────────────────────────────────────
        'show_school_fields' => [
            'academy'       => true,
            'gym'           => false,
            'health_center' => false,
            'venue'         => false,
            'hybrid'        => false,
        ],
    ];

    /**
     * إرجاع المصطلح المناسب (مفرد افتراضياً)
     */
    public static function get(string $key, ?string $type = null, bool $plural = false): string
    {
        $type = $type ?? static::currentType();
        $locale = app()->getLocale();
        $map = static::$terms[$key] ?? [];

        $entry = $map[$type] ?? $map['academy'] ?? [];

        if (is_bool($entry)) {
            return (string) $entry;
        }

        if ($locale === 'ar') {
            return $plural ? ($entry['ar_pl'] ?? $entry['ar'] ?? $key) : ($entry['ar'] ?? $key);
        }

        return $plural ? ($entry['en_pl'] ?? $entry['en'] ?? $key) : ($entry['en'] ?? $key);
    }

    /**
     * هل نعرض حقل ولي الأمر / المدرسة؟
     */
    public static function showGuardianFields(?string $type = null): bool
    {
        $type = $type ?? static::currentType();
        return (bool) (static::$terms['show_guardian_fields'][$type] ?? false);
    }

    public static function showSchoolFields(?string $type = null): bool
    {
        $type = $type ?? static::currentType();
        return (bool) (static::$terms['show_school_fields'][$type] ?? false);
    }

    /**
     * نوع النشاط الحالي للمنشأة المسجلة دخولاً
     */
    public static function currentType(): string
    {
        $user = auth('academy')->user();
        if (!$user) {
            return 'academy';
        }
        if ($user instanceof \App\Models\Academies) {
            return $user->business_type ?? 'academy';
        }
        if ($user instanceof \App\Models\PartnerUser) {
            return $user->academy?->business_type ?? 'academy';
        }
        return 'academy';
    }

    /**
     * هل المنشأة جيم؟
     */
    public static function isGym(?string $type = null): bool
    {
        return in_array($type ?? static::currentType(), ['gym', 'hybrid'], true);
    }

    /**
     * هل المنشأة مركز صحي؟
     */
    public static function isHealthCenter(?string $type = null): bool
    {
        return in_array($type ?? static::currentType(), ['health_center', 'hybrid'], true);
    }

    /**
     * هل المنشأة أكاديمية؟
     */
    public static function isAcademy(?string $type = null): bool
    {
        return in_array($type ?? static::currentType(), ['academy', 'hybrid'], true);
    }

    /**
     * هل تدعم وحدة المعسكرات والبطولات؟ (للأكاديميات فقط)
     */
    public static function hasCampsAndCompetitions(?string $type = null): bool
    {
        return in_array($type ?? static::currentType(), ['academy', 'hybrid'], true);
    }

    /**
     * تطبيق التحويل الديناميكي على مترجم Laravel (Translator)
     * يعيد كتابة كافة المفاتيح في الذاكرة لتطابق وثيقة المواصفات الفنية بدقة 100%.
     */
    public static function applyDynamicTranslations(?string $type = null): void
    {
        $type = $type ?? static::currentType();

        $translator = app('translator');
        if (!$translator) {
            return;
        }

        // تحميل ملفات اللغة الأصلية من القرص أولاً لضمان عدم حجب مفاتيح النظام الأساسية
        foreach (['ar', 'en'] as $l) {
            foreach (['admin', 'auth', 'pagination', 'passwords', 'validation'] as $grp) {
                $translator->get("{$grp}.__probe__", [], $l);
            }
        }

        // إصلح خطأ الترجمة الأصلي لكلمة calendar 'اجنده' في كافة الأنشطة
        $translator->addLines([
            'admin.training.calendar' => $type === 'gym' ? 'تقويم الحصص والمواعيد' : ($type === 'health_center' ? 'تقويم المواعيد والجلسات' : 'تقويم الحصص والتدريبات'),
        ], 'ar');
        $translator->addLines([
            'admin.training.calendar' => $type === 'gym' ? 'Fitness & Classes Calendar' : ($type === 'health_center' ? 'Appointments & Sessions Calendar' : 'Trainings Calendar'),
        ], 'en');

        if ($type === 'academy') {
            return;
        }

        if (in_array($type, ['gym', 'hybrid'])) {
            $translator->addLines([
                // ── إدارة الأعضاء ──────────────────────────────
                'admin.student_management.menu' => 'سجل الأعضاء',
                'admin.student_management.students' => 'الأعضاء',
                'admin.student_management.student' => 'العضو',
                'admin.student_management.add_student' => 'تسجيل عضو جديد',
                'admin.student_management.edit_student' => 'تعديل بيانات العضو',
                'admin.student_management.select_student' => 'اختر العضو',
                'admin.student_management.subscriptions' => 'عضويات واشتراكات الصالة',
                'admin.student_management.add_subscription' => 'تسجيل عضوية جديدة',
                'admin.student_management.edit_subscription' => 'تعديل العضوية',
                'admin.student_management.groups' => 'فئات وباقات العضوية',
                'admin.student_management.add_group' => 'إضافة فئة عضوية',
                'admin.student_management.edit_group' => 'تعديل فئة العضوية',
                'admin.student_management.group' => 'فئة العضوية',
                'admin.student_management.group_name' => 'اسم الباقة / الفئة',
                'admin.student_management.attendance' => 'دخول الصالة / البوابة',
                'admin.student_management.attendance_records' => 'سجل دخول الصالة',
                'admin.student_management.new_attendance_session' => 'تسجيل دخول جديد',
                'admin.student_management.reports' => 'تقارير الأعضاء',
                'admin.student_management.download_students_template' => 'تحميل نموذج الأعضاء',
                'admin.student_management.import_students' => 'استيراد الأعضاء من ملف',
                'admin.student_management.upload_students_file' => 'رفع ملف الأعضاء',
                'admin.student_management.total_students' => 'إجمالي الأعضاء',
                'admin.student_management.active_students' => 'الأعضاء النشطون',
                'admin.student_management.inactive_students' => 'الأعضاء غير النشطين',
                'admin.student_management.active_groups' => 'فئات العضوية النشطة',
                'admin.student_management.active_subscriptions' => 'العضويات النشطة',
                'admin.student_management.export_excel' => 'تصدير سجل الأعضاء Excel',
                'admin.student_management.students_print' => 'سجل الأعضاء للطباعة',
                'admin.student_management.students_list_title' => 'سجل الأعضاء',
                'admin.student_management.selected_count' => 'تم تحديد :count عضو',
                'admin.student_management.bulk_delete_confirm' => 'هل أنت متأكد من حذف الأعضاء المحددين؟',
                'admin.student_management.bulk_deleted_success' => 'تم حذف :count عضو بنجاح.',
                'admin.student_management.bulk_status_success' => 'تم تحديث حالة :count عضو بنجاح.',
                'admin.student_management.student_created' => 'تم تسجيل العضو بنجاح',
                'admin.student_management.student_updated' => 'تم تحديث بيانات العضو بنجاح',
                'admin.student_management.student_deleted' => 'تم حذف العضو بنجاح',
                'admin.student_management.group_created' => 'تم حفظ فئة العضوية بنجاح',
                'admin.student_management.group_updated' => 'تم تحديث فئة العضوية بنجاح',
                'admin.student_management.group_deleted' => 'تم حذف فئة العضوية بنجاح',
                'admin.student_management.subscription_created' => 'تم تسجيل العضوية بنجاح',
                'admin.student_management.subscription_updated' => 'تم تحديث العضوية بنجاح',
                'admin.student_management.subscription_deleted' => 'تم حذف العضوية بنجاح',
                'admin.student_management.no_students_yet' => 'لا يوجد أعضاء مسجلون بعد.',
                'admin.student_management.no_subscriptions_yet' => 'لا توجد عضويات مسجلة بعد.',
                'admin.student_management.delete_student_confirm' => 'هل تريد حذف هذا العضو؟',
                'admin.student_management.delete_subscription_confirm' => 'هل تريد إلغاء هذه العضوية؟',

                // ── الكباتن والمدربون ──────────────────────────
                'admin.coaches.coaches' => 'كباتن الصالة ومدربو PT',
                'admin.coaches.coach' => 'الكابتن / مدرب PT',
                'admin.coaches.create' => 'إضافة كابتن / مدرب PT',
                'admin.coaches.add_coach' => 'إضافة كابتن / مدرب PT',
                'admin.coaches.edit' => 'تعديل بيانات الكابتن',
                'admin.coaches.edit_coach' => 'تعديل بيانات الكابتن',
                'admin.coaches.select_sport' => 'اختر التخصص / النشاط التدريبي',
                'admin.coaches.compensation_type' => 'نظام الاستحقاق المالي للكابتن',
                'admin.coaches.session' => 'نظام الحصة / جلسة التدريب الخاص (PT)',
                'admin.coaches.session_rate' => 'سعر حصة PT للكابتن (ج.م)',
                'admin.coaches.total_bookings' => 'إجمالي المشتركين مع الكابتن',
                'admin.coaches.active_bookings' => 'الاشتراكات النشطة مع الكابتن',
                'admin.coaches.error_delete' => 'غير مسموح بحذف هذا الكابتن لوجود باقات مرتبطة به',
                'admin.coaches.delete_confirm' => 'هل أنت متأكد من حذف هذا الكابتن؟',

                // ── باقات العضوية والاشتراك ─────────────────────
                'admin.training.training' => 'باقات العضوية',
                'admin.training.trainings' => 'باقات العضوية',
                'admin.training.training_name' => 'باقة العضوية / مدة الاشتراك',
                'admin.training.add_training' => 'إضافة باقة عضوية',
                'admin.training.edit_training' => 'تعديل باقة العضوية',
                'admin.training.edit' => 'تعديل باقة العضوية',
                'admin.training.create' => 'إضافة باقة عضوية جديدة',
                'admin.training.created_successfully' => 'تم إنشاء باقة العضوية بنجاح',
                'admin.training.updated_successfully' => 'تم تحديث باقة العضوية بنجاح',
                'admin.training.deleted_successfully' => 'تم حذف باقة العضوية بنجاح',
                'admin.training.booking' => 'تسجيل اشتراك / حجز',
                'admin.training.coach' => 'الكابتن / مدرب PT',
                'admin.training.Choose Coach' => 'اختر الكابتن',
                'admin.training.class' => 'حصة اللياقة',
                'admin.training.Classes' => 'حصص اللياقة',
                'admin.training.Choose Classes' => 'اختر حصص اللياقة',
                'admin.training.classes_days' => 'أيام حصص اللياقة',
                'admin.training.classes_start_time' => 'وقت بدء الحصة',
                'admin.training.classes_end_time' => 'وقت انتهاء الحصة',
                'admin.training.classes_number' => 'عدد الحصص في الباقة',
                'admin.training.max_players' => 'الحد الأقصى للمشتركين بالحصة',
                'admin.training.max player' => 'الحد الأقصى للمشتركين بالحصة',
                'admin.training.levels' => 'مستوى اللياقة',
                'admin.training.select_level' => 'اختر مستوى اللياقة',
                'admin.training.age_group' => 'الفئة العمرية (بالغين / كبار)',
                'admin.training.select_age_group' => 'اختر الفئة (كبار - Adults)',
                'admin.training.adults' => 'الكبار (Adults)',
                'admin.training.All users who joined the user training' => 'جميع الأعضاء المشتركين في هذه الباقة',
                'admin.academies.select_training' => 'اختر باقة العضوية أو مدة الاشتراك',

                // ── حصص اللياقة (دعم classes و clasess) ─────────
                'admin.classes' => 'حصص اللياقة',
                'admin.class' => 'حصة اللياقة',
                'admin.clasess.clasess' => 'حصص اللياقة',
                'admin.clasess.create' => 'إضافة حصة لياقة',
                'admin.clasess.edit' => 'تعديل حصة اللياقة',
                'admin.clasess.training' => 'باقة العضوية',
                'admin.clasess.select_training' => 'اختر باقة العضوية',
                'admin.clasess.sport' => 'النشاط / نوع اللياقة',
                'admin.clasess.select_sport' => 'اختر النشاط / نوع اللياقة',

                // ── الحجوزات والدخول ────────────────────────────
                'admin.bookings.booking' => 'تسجيل اشتراك / حجز',
                'admin.bookings.bookings' => 'سجل اشتراكات الصالة',
                'admin.bookings.offline_bookings' => 'سجل الاشتراكات المباشرة',
                'admin.bookings.training' => 'باقة العضوية',
                'admin.bookings.user' => 'العضو',
            ], 'ar');

            $translator->addLines([
                'admin.student_management.menu' => 'Members Registry',
                'admin.student_management.students' => 'Members',
                'admin.student_management.student' => 'Member',
                'admin.student_management.add_student' => 'Register New Member',
                'admin.student_management.edit_student' => 'Edit Member',
                'admin.student_management.select_student' => 'Select Member',
                'admin.student_management.subscriptions' => 'Gym Memberships',
                'admin.student_management.add_subscription' => 'New Membership',
                'admin.student_management.edit_subscription' => 'Edit Membership',
                'admin.student_management.groups' => 'Membership Categories',
                'admin.student_management.add_group' => 'Add Category',
                'admin.student_management.edit_group' => 'Edit Category',
                'admin.student_management.group' => 'Category',
                'admin.student_management.group_name' => 'Category / Plan Name',
                'admin.student_management.attendance' => 'Gate Check-in',
                'admin.student_management.attendance_records' => 'Gate Entry Log',
                'admin.student_management.reports' => 'Member Reports',
                'admin.student_management.download_students_template' => 'Download Members Template',
                'admin.student_management.import_students' => 'Import Members',
                'admin.student_management.upload_students_file' => 'Upload Members File',
                'admin.student_management.total_students' => 'Total Members',
                'admin.student_management.active_students' => 'Active Members',
                'admin.student_management.inactive_students' => 'Inactive Members',
                'admin.student_management.export_excel' => 'Export Members Excel',
                'admin.student_management.students_print' => 'Print Members List',
                'admin.student_management.students_list_title' => 'Members List',
                'admin.student_management.selected_count' => ':count member(s) selected',
                'admin.student_management.bulk_delete_confirm' => 'Are you sure you want to delete selected members?',
                'admin.student_management.bulk_deleted_success' => ':count member(s) deleted successfully.',
                'admin.student_management.bulk_status_success' => 'Status updated for :count member(s).',
                'admin.student_management.student_created' => 'Member registered successfully',
                'admin.student_management.student_updated' => 'Member updated successfully',
                'admin.student_management.student_deleted' => 'Member deleted successfully',
                'admin.coaches.coaches' => 'Personal Trainers (PT)',
                'admin.coaches.coach' => 'Captain / Trainer',
                'admin.coaches.create' => 'Add Trainer / PT',
                'admin.coaches.add_coach' => 'Add Trainer / PT',
                'admin.coaches.edit' => 'Edit Trainer',
                'admin.coaches.edit_coach' => 'Edit Trainer',
                'admin.training.training' => 'Membership Plans',
                'admin.training.trainings' => 'Membership Plans',
                'admin.training.training_name' => 'Membership Plan / Duration',
                'admin.training.add_training' => 'Add Membership Plan',
                'admin.training.edit_training' => 'Edit Membership Plan',
                'admin.training.create' => 'Add New Membership Plan',
                'admin.training.coach' => 'Captain / Trainer',
                'admin.academies.select_training' => 'Select Membership Plan',
                'admin.classes' => 'Fitness Classes',
                'admin.class' => 'Fitness Class',
                'admin.clasess.clasess' => 'Fitness Classes',
                'admin.clasess.create' => 'Add Fitness Class',
                'admin.clasess.edit' => 'Edit Fitness Class',
                'admin.bookings.booking' => 'Membership Booking',
                'admin.bookings.offline_bookings' => 'Offline Subscriptions',
            ], 'en');
        } elseif ($type === 'health_center') {
            $translator->addLines([
                // ── ملفات المراجعين ───────────────────────────
                'admin.student_management.menu' => 'ملفات المراجعين والعملاء',
                'admin.student_management.students' => 'العملاء والمراجعون',
                'admin.student_management.student' => 'العميل / المراجع',
                'admin.student_management.add_student' => 'إضافة عميل جديد',
                'admin.student_management.edit_student' => 'تعديل بيانات العميل',
                'admin.student_management.select_student' => 'اختر العميل',
                'admin.student_management.subscriptions' => 'باقات الجلسات العلاجية',
                'admin.student_management.add_subscription' => 'إضافة باقة جلسات',
                'admin.student_management.edit_subscription' => 'تعديل الباقة',
                'admin.student_management.groups' => 'أنواع الخدمات والجلسات',
                'admin.student_management.add_group' => 'إضافة نوع خدمة',
                'admin.student_management.edit_group' => 'تعديل نوع الخدمة',
                'admin.student_management.group' => 'نوع الخدمة',
                'admin.student_management.group_name' => 'اسم الخدمة / الباقة',
                'admin.student_management.attendance' => 'تأكيد حضور الجلسات',
                'admin.student_management.attendance_records' => 'سجل حضور الجلسات',
                'admin.student_management.new_attendance_session' => 'تسجيل موعد جلسة',
                'admin.student_management.reports' => 'تقارير المراجعين',
                'admin.student_management.total_students' => 'إجمالي المراجعين',
                'admin.student_management.active_students' => 'المراجعون النشطون',
                'admin.student_management.inactive_students' => 'المراجعون غير النشطين',
                'admin.student_management.active_groups' => 'الخدمات النشطة',
                'admin.student_management.active_subscriptions' => 'الباقات النشطة',
                'admin.student_management.student_created' => 'تم تسجيل العميل بنجاح',
                'admin.student_management.student_updated' => 'تم تحديث بيانات العميل بنجاح',
                'admin.student_management.student_deleted' => 'تم حذف العميل بنجاح',

                // ── الأخصائيون والمعالجون ─────────────────────
                'admin.coaches.coaches' => 'الأخصائيون والمعالجون',
                'admin.coaches.coach' => 'الأخصائي / المعالج',
                'admin.coaches.create' => 'إضافة أخصائي / معالج',
                'admin.coaches.add_coach' => 'إضافة أخصائي / معالج',
                'admin.coaches.edit' => 'تعديل بيانات الأخصائي',
                'admin.coaches.edit_coach' => 'تعديل بيانات الأخصائي',
                'admin.coaches.select_sport' => 'اختر التخصص الطبي / العلاجي',
                'admin.coaches.compensation_type' => 'نظام الاستحقاق المالي للأخصائي',
                'admin.coaches.session' => 'نظام الجلسة العلاجية',
                'admin.coaches.session_rate' => 'سعر الجلسة للأخصائي (ج.م)',

                // ── الخدمات والجلسات ──────────────────────────
                'admin.training.training' => 'الخدمات والجلسات العلاجية',
                'admin.training.trainings' => 'الخدمات والجلسات العلاجية',
                'admin.training.training_name' => 'اسم الخدمة أو الباقة',
                'admin.training.add_training' => 'إضافة خدمة علاجية',
                'admin.training.edit_training' => 'تعديل الخدمة العلاجية',
                'admin.training.edit' => 'تعديل الخدمة العلاجية',
                'admin.training.create' => 'إضافة خدمة علاجية جديدة',
                'admin.training.coach' => 'الأخصائي / المعالج',
                'admin.training.Choose Coach' => 'اختر الأخصائي / المعالج',
                'admin.training.class' => 'موعد الجلسة',
                'admin.training.Classes' => 'مواعيد الجلسات',
                'admin.training.Choose Classes' => 'اختر مواعيد الجلسات',
                'admin.training.booking' => 'حجز موعد جلسة',
                'admin.academies.select_training' => 'اختر الخدمة أو باقة الجلسات',

                // ── مواعيد الجلسات (دعم classes و clasess) ─────
                'admin.classes' => 'مواعيد الجلسات',
                'admin.class' => 'موعد الجلسة',
                'admin.clasess.clasess' => 'مواعيد الجلسات',
                'admin.clasess.create' => 'إضافة موعد جلسة',
                'admin.clasess.edit' => 'تعديل موعد الجلسة',
                'admin.clasess.training' => 'الخدمة / الجلسة',
                'admin.clasess.select_training' => 'اختر الخدمة / الجلسة',
                'admin.clasess.sport' => 'القسم / التخصص',
                'admin.clasess.select_sport' => 'اختر القسم / التخصص',

                // ── الحجوزات ──────────────────────────────────
                'admin.bookings.booking' => 'حجز موعد جلسة',
                'admin.bookings.bookings' => 'سجل المواعيد والجلسات',
                'admin.bookings.offline_bookings' => 'سجل الحجوزات المباشرة',
                'admin.bookings.training' => 'الخدمة / الجلسة',
                'admin.bookings.user' => 'العميل',
            ], 'ar');

            $translator->addLines([
                'admin.student_management.menu' => 'Client Records',
                'admin.student_management.students' => 'Clients & Patients',
                'admin.student_management.student' => 'Client',
                'admin.student_management.add_student' => 'Add New Client',
                'admin.student_management.edit_student' => 'Edit Client',
                'admin.student_management.select_student' => 'Select Client',
                'admin.student_management.subscriptions' => 'Session Packages',
                'admin.student_management.add_subscription' => 'New Package',
                'admin.student_management.edit_subscription' => 'Edit Package',
                'admin.student_management.groups' => 'Service Types',
                'admin.student_management.add_group' => 'Add Service Type',
                'admin.student_management.group' => 'Service Type',
                'admin.student_management.group_name' => 'Service / Package Name',
                'admin.student_management.attendance' => 'Session Check-in',
                'admin.student_management.attendance_records' => 'Session Log',
                'admin.coaches.coaches' => 'Therapists & Specialists',
                'admin.coaches.coach' => 'Therapist / Specialist',
                'admin.coaches.create' => 'Add Specialist',
                'admin.coaches.add_coach' => 'Add Specialist',
                'admin.coaches.edit' => 'Edit Specialist',
                'admin.training.training' => 'Therapy Services',
                'admin.training.trainings' => 'Therapy Services',
                'admin.training.training_name' => 'Service Name',
                'admin.training.add_training' => 'Add Service',
                'admin.training.create' => 'Add New Service',
                'admin.classes' => 'Appointments',
                'admin.class' => 'Appointment',
                'admin.clasess.clasess' => 'Appointments',
                'admin.clasess.create' => 'Add Appointment',
                'admin.clasess.edit' => 'Edit Appointment',
            ], 'en');
        }
    }
}
