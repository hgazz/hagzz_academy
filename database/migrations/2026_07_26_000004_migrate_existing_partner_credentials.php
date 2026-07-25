<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $academies = DB::table('academies')->get();
        $ownerRoleId = DB::table('partner_roles')->where('name', 'owner')->whereNull('academy_id')->value('id');

        foreach ($academies as $academy) {
            if (!empty($academy->email)) {
                // Check if user already exists
                $existingUserId = DB::table('partner_users')->where('email', $academy->email)->value('id');

                // If academy has a branch_to, find the root academy ID
                $rootAcademyId = $academy->branch_to ? $academy->branch_to : $academy->id;

                if (!$existingUserId) {
                    $name = trim(($academy->first_name ?? '') . ' ' . ($academy->last_name ?? ''));
                    if (empty($name)) {
                        $name = $academy->name ?? 'Partner Owner';
                    }

                    $userId = DB::table('partner_users')->insertGetId([
                        'academy_id' => $rootAcademyId,
                        'name' => $name,
                        'email' => $academy->email,
                        'phone' => $academy->phone,
                        'password' => $academy->password ?? bcrypt('12345678'),
                        'is_owner' => ($academy->branch_to === null),
                        'access_all_branches' => true,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if ($ownerRoleId) {
                        DB::table('partner_user_roles')->updateOrInsert([
                            'user_id' => $userId,
                            'role_id' => $ownerRoleId,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructing action needed to avoid data loss
    }
};
