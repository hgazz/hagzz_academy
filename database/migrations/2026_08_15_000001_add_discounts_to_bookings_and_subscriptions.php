<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('venue_bookings')) {
            Schema::table('venue_bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('venue_bookings', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0)->after('paid_amount');
                }
                if (!Schema::hasColumn('venue_bookings', 'discount_reason')) {
                    $table->string('discount_reason', 255)->nullable()->after('discount_amount');
                }
                if (!Schema::hasColumn('venue_bookings', 'discount_approved_by')) {
                    $table->string('discount_approved_by', 255)->nullable()->after('discount_reason');
                }
                if (!Schema::hasColumn('venue_bookings', 'discount_approved_at')) {
                    $table->dateTime('discount_approved_at')->nullable()->after('discount_approved_by');
                }
            });
        }

        if (Schema::hasTable('academy_student_subscriptions')) {
            Schema::table('academy_student_subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('academy_student_subscriptions', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0)->after('amount');
                }
                if (!Schema::hasColumn('academy_student_subscriptions', 'discount_reason')) {
                    $table->string('discount_reason', 255)->nullable()->after('discount_amount');
                }
                if (!Schema::hasColumn('academy_student_subscriptions', 'discount_approved_by')) {
                    $table->string('discount_approved_by', 255)->nullable()->after('discount_reason');
                }
                if (!Schema::hasColumn('academy_student_subscriptions', 'discount_approved_at')) {
                    $table->dateTime('discount_approved_at')->nullable()->after('discount_approved_by');
                }
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0)->after('paid_amount');
                }
                if (!Schema::hasColumn('invoices', 'discount_reason')) {
                    $table->string('discount_reason', 255)->nullable()->after('discount_amount');
                }
                if (!Schema::hasColumn('invoices', 'discount_approved_by')) {
                    $table->string('discount_approved_by', 255)->nullable()->after('discount_reason');
                }
                if (!Schema::hasColumn('invoices', 'discount_approved_at')) {
                    $table->dateTime('discount_approved_at')->nullable()->after('discount_approved_by');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('venue_bookings')) {
            Schema::table('venue_bookings', function (Blueprint $table) {
                $table->dropColumn(['discount_amount', 'discount_reason', 'discount_approved_by', 'discount_approved_at']);
            });
        }

        if (Schema::hasTable('academy_student_subscriptions')) {
            Schema::table('academy_student_subscriptions', function (Blueprint $table) {
                $table->dropColumn(['discount_amount', 'discount_reason', 'discount_approved_by', 'discount_approved_at']);
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn(['discount_amount', 'discount_reason', 'discount_approved_by', 'discount_approved_at']);
            });
        }
    }
};
