<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('partner_staff_attendances');

        Schema::create('partner_staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
            $table->unsignedBigInteger('branch_id')->nullable(); // Can be Address id or sub-academy id
            $table->enum('staff_type', ['employee', 'coach'])->default('employee');
            $table->foreignId('partner_user_id')->nullable()->constrained('partner_users')->nullOnDelete();
            $table->foreignId('coach_id')->nullable()->constrained('coaches')->nullOnDelete();
            $table->date('attendance_date');
            $table->dateTime('check_in_at');
            $table->dateTime('check_out_at')->nullable();
            $table->unsignedInteger('work_minutes')->default(0);
            $table->enum('status', ['present', 'late', 'left_early', 'absent', 'excused'])->default('present');
            $table->string('verification_method', 50)->default('dynamic_qr'); // dynamic_qr, manual, app
            $table->string('ip_address', 45)->nullable();
            $table->string('device_info', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('partner_users')->nullOnDelete();
            $table->timestamps();

            $table->index(['academy_id', 'attendance_date'], 'idx_staff_att_acad_date');
            $table->index(['staff_type', 'partner_user_id', 'attendance_date'], 'idx_staff_att_user');
            $table->index(['staff_type', 'coach_id', 'attendance_date'], 'idx_staff_att_coach');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_staff_attendances');
    }
};
