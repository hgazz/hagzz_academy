<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('academy_students') && !Schema::hasColumn('academy_students', 'image')) {
            Schema::table('academy_students', function (Blueprint $table) {
                $table->string('image')->nullable()->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('academy_students') && Schema::hasColumn('academy_students', 'image')) {
            Schema::table('academy_students', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
