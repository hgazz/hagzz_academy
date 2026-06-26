<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method', 40)->nullable();
            }

            if (!Schema::hasColumn('invoices', 'payment_method_other')) {
                $table->string('payment_method_other')->nullable();
            }
        });

        Schema::table('academy_student_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('academy_student_payments', 'method_other')) {
                $table->string('method_other')->nullable();
            }
        });

        DB::statement("ALTER TABLE academy_student_payments MODIFY method ENUM('cash','instapay','fawry','app_online','bank_transfer','card','online','other') NOT NULL DEFAULT 'cash'");
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'payment_method_other')) {
                $table->dropColumn('payment_method_other');
            }

            if (Schema::hasColumn('invoices', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });

        Schema::table('academy_student_payments', function (Blueprint $table) {
            if (Schema::hasColumn('academy_student_payments', 'method_other')) {
                $table->dropColumn('method_other');
            }
        });

        DB::statement("ALTER TABLE academy_student_payments MODIFY method ENUM('cash','bank_transfer','card','online','other') NOT NULL DEFAULT 'cash'");
    }
};
