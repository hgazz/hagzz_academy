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
        Schema::table('t_classes', function (Blueprint $table) {
            $table->dropColumn(['out_comes','bring_with_me']);
        });
        Schema::table('t_classes', function (Blueprint $table) {
            $table->json('out_comes')->nullable();
            $table->json('bring_with_me')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_classess', function (Blueprint $table) {
            //
        });
    }
};
