<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_group_id')->constrained('academy_groups')->cascadeOnDelete();
            $table->foreignId('t_class_id')->nullable()->constrained('t_classes')->nullOnDelete();
            $table->date('session_date');
            $table->time('starts_at')->nullable();
            $table->time('ends_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['academy_group_id', 'session_date'], 'academy_attendance_session_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_attendance_sessions');
    }
};
