<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academy_attendance_session_id');
            $table->unsignedBigInteger('academy_student_id');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('absent');
            $table->time('check_in_at')->nullable();
            $table->time('check_out_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('academy_attendance_session_id', 'attendance_records_session_fk')
                ->references('id')
                ->on('academy_attendance_sessions')
                ->cascadeOnDelete();
            $table->foreign('academy_student_id', 'attendance_records_student_fk')
                ->references('id')
                ->on('academy_students')
                ->cascadeOnDelete();
            $table->unique(['academy_attendance_session_id', 'academy_student_id'], 'academy_attendance_record_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_attendance_records');
    }
};
