<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coaches', function (Blueprint $table) {
            if (!Schema::hasColumn('coaches', 'compensation_type')) {
                $table->string('compensation_type')->default('session')->after('phone');
            }
            if (!Schema::hasColumn('coaches', 'compensation_value')) {
                $table->decimal('compensation_value', 10, 2)->default(0.00)->after('compensation_type');
            }
        });
    }

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
