<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_group_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_group_id')->constrained('academy_groups')->cascadeOnDelete();
            $table->foreignId('academy_student_id')->constrained('academy_students')->cascadeOnDelete();
            $table->date('joined_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['academy_group_id', 'academy_student_id'], 'academy_group_student_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_group_students');
    }
};
