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
        Schema::table('coaches', function (Blueprint $table) {
            if (!Schema::hasColumn('coaches', 'compensation_type')) {
                $table->string('compensation_type')->default('salary')->after('phone');
            }
            if (!Schema::hasColumn('coaches', 'compensation_value')) {
                $table->decimal('compensation_value', 10, 2)->default(0.00)->after('compensation_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coaches', function (Blueprint $table) {
            if (Schema::hasColumn('coaches', 'compensation_type')) {
                $table->dropColumn('compensation_type');
            }
            if (Schema::hasColumn('coaches', 'compensation_value')) {
                $table->dropColumn('compensation_value');
            }
        });
    }
};
