<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('partner_roles')) {
            Schema::create('partner_roles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academy_id')->nullable();
                $table->string('name');
                $table->string('display_name_ar');
                $table->string('display_name_en');
                $table->boolean('is_system')->default(false);
                $table->timestamps();

                $table->foreign('academy_id')->references('id')->on('academies')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('partner_permissions')) {
            Schema::create('partner_permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name_ar');
                $table->string('display_name_en');
                $table->string('group');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('partner_role_permission')) {
            Schema::create('partner_role_permission', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('permission_id');

                $table->foreign('role_id')->references('id')->on('partner_roles')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('partner_permissions')->onDelete('cascade');
                $table->primary(['role_id', 'permission_id']);
            });
        }

        if (!Schema::hasTable('partner_user_roles')) {
            Schema::create('partner_user_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('role_id');

                $table->foreign('user_id')->references('id')->on('partner_users')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('partner_roles')->onDelete('cascade');
                $table->primary(['user_id', 'role_id']);
            });
        }

        $this->seedDefaultRolesAndPermissions();
    }

    private function seedDefaultRolesAndPermissions(): void
    {
        $permissions = [
            ['name' => 'dashboard.view', 'display_name_ar' => 'عرض لوحة التحكم', 'display_name_en' => 'View Dashboard', 'group' => 'general'],
            ['name' => 'branches.view', 'display_name_ar' => 'عرض الفروع', 'display_name_en' => 'View Branches', 'group' => 'branches'],
            ['name' => 'branches.manage', 'display_name_ar' => 'إدارة الفروع', 'display_name_en' => 'Manage Branches', 'group' => 'branches'],
            ['name' => 'trainings.view', 'display_name_ar' => 'عرض الأنشطة والتدريبات', 'display_name_en' => 'View Trainings & Classes', 'group' => 'trainings'],
            ['name' => 'trainings.manage', 'display_name_ar' => 'إدارة الأنشطة والتدريبات', 'display_name_en' => 'Manage Trainings & Classes', 'group' => 'trainings'],
            ['name' => 'coaches.view', 'display_name_ar' => 'عرض المدربين', 'display_name_en' => 'View Coaches', 'group' => 'coaches'],
            ['name' => 'coaches.manage', 'display_name_ar' => 'إدارة المدربين', 'display_name_en' => 'Manage Coaches', 'group' => 'coaches'],
            ['name' => 'bookings.view', 'display_name_ar' => 'عرض الحجوزات', 'display_name_en' => 'View Bookings', 'group' => 'bookings'],
            ['name' => 'bookings.manage', 'display_name_ar' => 'إدارة الحجوزات والتحقق', 'display_name_en' => 'Manage Bookings & Check-in', 'group' => 'bookings'],
            ['name' => 'settlements.view', 'display_name_ar' => 'عرض الماليّة والتسويات', 'display_name_en' => 'View Settlements & Finance', 'group' => 'finance'],
            ['name' => 'users.view', 'display_name_ar' => 'عرض طاقم العمل', 'display_name_en' => 'View Team Members', 'group' => 'users'],
            ['name' => 'users.manage', 'display_name_ar' => 'إدارة طاقم العمل والصلاحيات', 'display_name_en' => 'Manage Team Members & Roles', 'group' => 'users'],
            ['name' => 'settings.manage', 'display_name_ar' => 'إدارة إعدادات الشريك', 'display_name_en' => 'Manage Partner Settings', 'group' => 'settings'],
        ];

        foreach ($permissions as $p) {
            DB::table('partner_permissions')->updateOrInsert(
                ['name' => $p['name']],
                array_merge($p, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $roles = [
            [
                'name' => 'owner',
                'display_name_ar' => 'مالك الشريك',
                'display_name_en' => 'Owner',
                'is_system' => true,
                'permissions' => array_column($permissions, 'name'),
            ],
            [
                'name' => 'manager',
                'display_name_ar' => 'مدير عام',
                'display_name_en' => 'General Manager',
                'is_system' => true,
                'permissions' => ['dashboard.view', 'branches.view', 'trainings.view', 'trainings.manage', 'coaches.view', 'coaches.manage', 'bookings.view', 'bookings.manage', 'settlements.view', 'users.view', 'users.manage'],
            ],
            [
                'name' => 'branch_manager',
                'display_name_ar' => 'مدير فرع',
                'display_name_en' => 'Branch Manager',
                'is_system' => true,
                'permissions' => ['dashboard.view', 'trainings.view', 'trainings.manage', 'coaches.view', 'coaches.manage', 'bookings.view', 'bookings.manage'],
            ],
            [
                'name' => 'accountant',
                'display_name_ar' => 'محاسب',
                'display_name_en' => 'Accountant',
                'is_system' => true,
                'permissions' => ['dashboard.view', 'settlements.view', 'bookings.view'],
            ],
            [
                'name' => 'receptionist',
                'display_name_ar' => 'موظف استقبال',
                'display_name_en' => 'Receptionist / Staff',
                'is_system' => true,
                'permissions' => ['dashboard.view', 'bookings.view', 'bookings.manage'],
            ],
        ];

        $allPermIds = DB::table('partner_permissions')->pluck('id', 'name');

        foreach ($roles as $r) {
            $roleId = DB::table('partner_roles')->where('name', $r['name'])->whereNull('academy_id')->value('id');
            if (!$roleId) {
                $roleId = DB::table('partner_roles')->insertGetId([
                    'academy_id' => null,
                    'name' => $r['name'],
                    'display_name_ar' => $r['display_name_ar'],
                    'display_name_en' => $r['display_name_en'],
                    'is_system' => $r['is_system'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($r['permissions'] as $pName) {
                if (isset($allPermIds[$pName])) {
                    DB::table('partner_role_permission')->updateOrInsert([
                        'role_id' => $roleId,
                        'permission_id' => $allPermIds[$pName],
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_user_roles');
        Schema::dropIfExists('partner_role_permission');
        Schema::dropIfExists('partner_permissions');
        Schema::dropIfExists('partner_roles');
    }
};
