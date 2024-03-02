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
//        Schema::table('trainings', function (Blueprint $table) {
//            $table->after('coach_id', function (Blueprint $table) {
//                $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
//            });
//        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            //
        });
    }
};
