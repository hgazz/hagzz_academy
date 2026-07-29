<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add access_all_sports to partner_users
        if (!Schema::hasColumn('partner_users', 'access_all_sports')) {
            Schema::table('partner_users', function (Blueprint $table) {
                $table->boolean('access_all_sports')->default(true)->after('access_all_branches');
            });
        }

        // 2. Create partner_user_sports pivot table
        if (!Schema::hasTable('partner_user_sports')) {
            Schema::create('partner_user_sports', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('sport_id');
                $table->primary(['user_id', 'sport_id']);
                $table->foreign('user_id')->references('id')->on('partner_users')->onDelete('cascade');
                $table->foreign('sport_id')->references('id')->on('sports')->onDelete('cascade');
            });
        }

        // 3. Seed sport_supervisor role if not exists
        $this->seedSportSupervisorRole();
    }

    private function seedSportSupervisorRole(): void
    {
        $exists = DB::table('partner_roles')
            ->where('name', 'sport_supervisor')
            ->whereNull('academy_id')
            ->exists();

        if ($exists) {
            return;
        }

        // Ensure permissions exist first
        $permissions = [
            ['name' => 'sports.view', 'display_name_ar' => 'عرض الرياضات المخصصة', 'display_name_en' => 'View Assigned Sports', 'group' => 'sports'],
        ];

        foreach ($permissions as $p) {
            DB::table('partner_permissions')->updateOrInsert(
                ['name' => $p['name']],
                array_merge($p, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $sportSupervisorPermissions = [
            'dashboard.view',
            'trainings.view',
            'coaches.view',
            'bookings.view',
            'sports.view',
        ];

        $roleId = DB::table('partner_roles')->insertGetId([
            'academy_id'      => null,
            'name'            => 'sport_supervisor',
            'display_name_ar' => 'مشرف رياضة',
            'display_name_en' => 'Sport Supervisor',
            'is_system'       => true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $allPermIds = DB::table('partner_permissions')->pluck('id', 'name');

        foreach ($sportSupervisorPermissions as $pName) {
            if (isset($allPermIds[$pName])) {
                DB::table('partner_role_permission')->updateOrInsert([
                    'role_id'       => $roleId,
                    'permission_id' => $allPermIds[$pName],
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_user_sports');

        if (Schema::hasColumn('partner_users', 'access_all_sports')) {
            Schema::table('partner_users', function (Blueprint $table) {
                $table->dropColumn('access_all_sports');
            });
        }

        DB::table('partner_roles')
            ->where('name', 'sport_supervisor')
            ->whereNull('academy_id')
            ->delete();
    }
};
