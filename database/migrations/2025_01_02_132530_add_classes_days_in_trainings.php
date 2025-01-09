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
        Schema::table('trainings', function (Blueprint $table) {
            $table->json('classes_days')->nullable();
            $table->string('color')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedBigInteger('classes_number')->nullable();

        });
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
