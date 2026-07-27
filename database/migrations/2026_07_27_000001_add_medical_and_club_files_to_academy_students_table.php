<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('academy_students')) {
            Schema::table('academy_students', function (Blueprint $table) {
                if (!Schema::hasColumn('academy_students', 'medical_certificate')) {
                    $table->string('medical_certificate')->nullable()->after('medical_notes');
                }
                if (!Schema::hasColumn('academy_students', 'club_card_number')) {
                    $table->string('club_card_number', 100)->nullable()->after('club_member');
                }
                if (!Schema::hasColumn('academy_students', 'club_card_file')) {
                    $table->string('club_card_file')->nullable()->after('club_card_number');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'medical_certificate')) {
                    $table->string('medical_certificate')->nullable();
                }
                if (!Schema::hasColumn('users', 'club_card_number')) {
                    $table->string('club_card_number', 100)->nullable();
                }
                if (!Schema::hasColumn('users', 'club_card_file')) {
                    $table->string('club_card_file')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('academy_students')) {
            Schema::table('academy_students', function (Blueprint $table) {
                $table->dropColumn(['medical_certificate', 'club_card_number', 'club_card_file']);
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['medical_certificate', 'club_card_number', 'club_card_file']);
            });
        }
    }
};
