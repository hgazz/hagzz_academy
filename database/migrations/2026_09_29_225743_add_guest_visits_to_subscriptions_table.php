<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academy_student_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('academy_student_subscriptions', 'guest_visits_total')) {
                $table->unsignedSmallInteger('guest_visits_total')->nullable()->after('sessions_used')
                      ->comment('رصيد زيارات الضيوف/المرافقين المسموح بها في الباقة');
                $table->unsignedSmallInteger('guest_visits_used')->default(0)->after('guest_visits_total')
                      ->comment('عدد زيارات الضيوف المستهلكة');
            }
        });

        Schema::table('gym_gate_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('gym_gate_entries', 'is_guest')) {
                $table->boolean('is_guest')->default(false)->after('subscription_valid')
                      ->comment('هل هذه الحركة لدخول ضيف/مرافق بصحبة العضو');
            }
        });
    }

    public function down(): void
    {
        Schema::table('academy_student_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('academy_student_subscriptions', 'guest_visits_total')) {
                $table->dropColumn(['guest_visits_total', 'guest_visits_used']);
            }
        });

        Schema::table('gym_gate_entries', function (Blueprint $table) {
            if (Schema::hasColumn('gym_gate_entries', 'is_guest')) {
                $table->dropColumn('is_guest');
            }
        });
    }
};
