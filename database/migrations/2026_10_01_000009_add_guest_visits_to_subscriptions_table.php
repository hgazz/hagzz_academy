<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('academy_student_subscriptions')) {
            Schema::table('academy_student_subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('academy_student_subscriptions', 'guest_visits_total')) {
                    $table->unsignedSmallInteger('guest_visits_total')->nullable()
                          ->comment('رصيد زيارات الضيوف/المرافقين المسموح بها في الباقة');
                }
                if (!Schema::hasColumn('academy_student_subscriptions', 'guest_visits_used')) {
                    $table->unsignedSmallInteger('guest_visits_used')->default(0)
                          ->comment('عدد زيارات الضيوف المستهلكة');
                }
            });
        }

        if (Schema::hasTable('gym_gate_entries')) {
            Schema::table('gym_gate_entries', function (Blueprint $table) {
                if (!Schema::hasColumn('gym_gate_entries', 'is_guest')) {
                    $table->boolean('is_guest')->default(false)
                          ->comment('هل هذه الحركة لدخول ضيف/مرافق بصحبة العضو');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('academy_student_subscriptions')) {
            Schema::table('academy_student_subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('academy_student_subscriptions', 'guest_visits_total')) {
                    $table->dropColumn('guest_visits_total');
                }
                if (Schema::hasColumn('academy_student_subscriptions', 'guest_visits_used')) {
                    $table->dropColumn('guest_visits_used');
                }
            });
        }

        if (Schema::hasTable('gym_gate_entries')) {
            Schema::table('gym_gate_entries', function (Blueprint $table) {
                if (Schema::hasColumn('gym_gate_entries', 'is_guest')) {
                    $table->dropColumn('is_guest');
                }
            });
        }
    }
};
