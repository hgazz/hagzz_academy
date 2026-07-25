<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('partner_expense_categories')) {
            Schema::create('partner_expense_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academy_id')->nullable(); // null = system default category
                $table->string('name_ar');
                $table->string('name_en');
                $table->string('icon')->default('fa-receipt');
                $table->boolean('is_system')->default(false);
                $table->timestamps();

                $table->foreign('academy_id')->references('id')->on('academies')->onDelete('cascade');
            });

            // Seed default system categories
            DB::table('partner_expense_categories')->insert([
                ['name_ar' => 'إيجار الملاعب والمقر', 'name_en' => 'Rent & Lease', 'icon' => 'fa-building', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name_ar' => 'رواتب المدربين والموظفين', 'name_en' => 'Salaries & Payroll', 'icon' => 'fa-user-nurse', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name_ar' => 'صيانة ومعدات أدوات', 'name_en' => 'Maintenance & Equipment', 'icon' => 'fa-wrench', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name_ar' => 'كهرباء ومرافق وخدمات', 'name_en' => 'Utilities & Services', 'icon' => 'fa-bolt', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name_ar' => 'تسويق وإعلانات', 'name_en' => 'Marketing & Ads', 'icon' => 'fa-bullhorn', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name_ar' => 'مستلزمات رياضية ومؤن', 'name_en' => 'Sports Supplies', 'icon' => 'fa-futbol', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name_ar' => 'ضيافة ونثريات', 'name_en' => 'Hospitality & Miscellaneous', 'icon' => 'fa-coffee', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name_ar' => 'مصروفات أخرى', 'name_en' => 'Other Expenses', 'icon' => 'fa-folder-open', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_expense_categories');
    }
};
