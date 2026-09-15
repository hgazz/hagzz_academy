<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('partner_expenses', 'coach_id')) {
                $table->unsignedBigInteger('coach_id')->nullable()->after('category_id');
                $table->foreign('coach_id')->references('id')->on('coaches')->nullOnDelete();
            }

            if (!Schema::hasColumn('partner_expenses', 'is_external_coach')) {
                $table->boolean('is_external_coach')->default(false)->after('coach_id');
            }

            if (!Schema::hasColumn('partner_expenses', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('is_external_coach');
                $table->foreign('branch_id')->references('id')->on('addresses')->nullOnDelete();
            }

            if (!Schema::hasColumn('partner_expenses', 'expense_type')) {
                $table->string('expense_type', 40)->default('general')->after('period_type');
            }

            if (!Schema::hasColumn('partner_expenses', 'payment_method')) {
                $table->string('payment_method', 40)->nullable()->after('expense_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('partner_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('partner_expenses', 'coach_id')) {
                $table->dropForeign(['coach_id']);
                $table->dropColumn('coach_id');
            }
            if (Schema::hasColumn('partner_expenses', 'is_external_coach')) {
                $table->dropColumn('is_external_coach');
            }
            if (Schema::hasColumn('partner_expenses', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
            if (Schema::hasColumn('partner_expenses', 'expense_type')) {
                $table->dropColumn('expense_type');
            }
            if (Schema::hasColumn('partner_expenses', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};
