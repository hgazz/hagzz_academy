<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('academy_students')) {
            Schema::table('academy_students', function (Blueprint $table) {
                if (!Schema::hasColumn('academy_students', 'previous_club_name')) {
                    $table->string('previous_club_name')->nullable()->after('club_member');
                }
                if (!Schema::hasColumn('academy_students', 'injury_type')) {
                    $table->string('injury_type')->nullable()->after('medical_condition');
                }
                if (!Schema::hasColumn('academy_students', 'has_allergy')) {
                    $table->string('has_allergy', 20)->nullable()->default('no')->after('injury_type');
                }
                if (!Schema::hasColumn('academy_students', 'allergy_type')) {
                    $table->string('allergy_type')->nullable()->after('has_allergy');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('academy_students')) {
            Schema::table('academy_students', function (Blueprint $table) {
                $columns = ['previous_club_name', 'injury_type', 'has_allergy', 'allergy_type'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('academy_students', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
