<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
            $table->foreignId('training_id')->nullable()->constrained('trainings')->nullOnDelete();
            $table->foreignId('coach_id')->nullable()->constrained('coaches')->nullOnDelete();
            $table->foreignId('sport_id')->nullable()->constrained('sports')->nullOnDelete();
            $table->string('name');
            $table->json('days')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['academy_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_groups');
    }
};
