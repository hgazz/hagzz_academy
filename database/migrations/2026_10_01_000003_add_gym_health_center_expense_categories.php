<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 2026_10_01_000003_add_gym_health_center_expense_categories
 *
 * إضافة بنود وفئات المصروفات والحسابات المتوافقة مع الصالات الرياضية (الجيمات) والمراكز الصحية والتأهيلية
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('partner_expense_categories')) {
            $categories = [
                [
                    'name_ar'    => 'صيانة وتجهيز أجهزة اللياقة والحديد',
                    'name_en'    => 'Gym & Fitness Equipment Maintenance',
                    'icon'       => 'fa-dumbbell',
                    'is_system'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name_ar'    => 'مستلزمات النظافة والساونا والجاكوزي والمناشف',
                    'name_en'    => 'Hygiene, Spa, Sauna & Towel Supplies',
                    'icon'       => 'fa-hot-tub-person',
                    'is_system'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name_ar'    => 'مشروبات ومكملات البار وبوفيه الأعضاء',
                    'name_en'    => 'Gym Bar, Supplements & Nutrition',
                    'icon'       => 'fa-bottle-water',
                    'is_system'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name_ar'    => 'عمولات التدريب الشخصي (PT) ومكافآت الكباتن',
                    'name_en'    => 'Personal Training (PT) Commissions & Bonuses',
                    'icon'       => 'fa-award',
                    'is_system'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name_ar'    => 'أدوات التأهيل والعلاج الطبيعي والمستهلكات الطبية',
                    'name_en'    => 'Therapy, Rehab & Clinic Supplies',
                    'icon'       => 'fa-kit-medical',
                    'is_system'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name_ar'    => 'تراخيص النشاط والموسيقى وتطبيقات التشغيل',
                    'name_en'    => 'Facility Licenses, Music & Software Subscriptions',
                    'icon'       => 'fa-shield-halved',
                    'is_system'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($categories as $cat) {
                $exists = DB::table('partner_expense_categories')
                    ->where('name_ar', $cat['name_ar'])
                    ->exists();

                if (!$exists) {
                    DB::table('partner_expense_categories')->insert($cat);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('partner_expense_categories')) {
            DB::table('partner_expense_categories')
                ->whereIn('name_ar', [
                    'صيانة وتجهيز أجهزة اللياقة والحديد',
                    'مستلزمات النظافة والساونا والجاكوزي والمناشف',
                    'مشروبات ومكملات البار وبوفيه الأعضاء',
                    'عمولات التدريب الشخصي (PT) ومكافآت الكباتن',
                    'أدوات التأهيل والعلاج الطبيعي والمستهلكات الطبية',
                    'تراخيص النشاط والموسيقى وتطبيقات التشغيل',
                ])
                ->delete();
        }
    }
};
