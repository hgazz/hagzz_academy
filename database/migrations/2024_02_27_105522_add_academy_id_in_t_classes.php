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
            $table->after('date', function (Blueprint $table) {
                $table->unsignedBigInteger('academy_id')->nullable();
                $table->foreign('academy_id')->references('id')->on('academies')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('sport_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_classes', function (Blueprint $table) {
            //
        });
    }
};
