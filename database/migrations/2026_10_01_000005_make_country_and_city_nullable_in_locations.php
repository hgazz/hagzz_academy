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
        // Set safe defaults and allow NULL in cities and areas so MySQL strict mode never breaks
        try {
            DB::statement("ALTER TABLE `cities` MODIFY `country_id` BIGINT UNSIGNED NULL DEFAULT 4");
        } catch (\Throwable $e) {
            // ignore if already modified
        }

        try {
            DB::statement("ALTER TABLE `areas` MODIFY `city_id` BIGINT UNSIGNED NULL DEFAULT 1");
        } catch (\Throwable $e) {
            // ignore if already modified
        }

        try {
            DB::statement("ALTER TABLE `academy_students` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'active'");
        } catch (\Throwable $e) {
            // ignore if already modified
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
