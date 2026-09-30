<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 2026_10_01_000001_expand_business_types_in_academies_table
 *
 * يوسع حقل business_type ليدعم:
 *   academy | gym | health_center | venue | hybrid
 *
 * آمن تماماً: لا يحذف أي بيانات، القيم الحالية تبقى كما هي.
 */
return new class extends Migration
{
    public function up(): void
    {
        // MySQL لا يدعم تعديل ENUM مباشرة — نستخدم تغيير النوع لـ VARCHAR ثم نعيده
        // هذا يضمن التوافق مع MariaDB وMySQL بدون أخطاء
        if (Schema::hasColumn('academies', 'business_type')) {
            DB::statement(
                "ALTER TABLE academies 
                 MODIFY COLUMN business_type VARCHAR(20) NOT NULL DEFAULT 'academy'"
            );
        }

        // تحديث أي قيمة null أو فارغة إلى academy (للأمان)
        DB::table('academies')
            ->whereNull('business_type')
            ->orWhere('business_type', '')
            ->update(['business_type' => 'academy']);
    }

    public function down(): void
    {
        // إعادة القيم غير المدعومة إلى academy قبل التضييق
        DB::table('academies')
            ->whereIn('business_type', ['gym', 'health_center'])
            ->update(['business_type' => 'academy']);

        if (Schema::hasColumn('academies', 'business_type')) {
            DB::statement(
                "ALTER TABLE academies 
                 MODIFY COLUMN business_type VARCHAR(20) NOT NULL DEFAULT 'academy'"
            );
        }
    }
};
