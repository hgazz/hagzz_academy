<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('partner_expenses')) {
            Schema::table('partner_expenses', function (Blueprint $table) {
                if (!Schema::hasColumn('partner_expenses', 'exchange_rate')) {
                    $table->decimal('exchange_rate', 10, 4)->default(1.0000)->after('currency');
                }
                if (!Schema::hasColumn('partner_expenses', 'base_amount')) {
                    $table->decimal('base_amount', 12, 2)->nullable()->after('exchange_rate');
                }
                if (!Schema::hasColumn('partner_expenses', 'base_currency')) {
                    $table->string('base_currency', 10)->nullable()->after('base_amount');
                }
            });

            // Backfill base_amount for existing records
            DB::statement("UPDATE partner_expenses SET base_amount = amount, base_currency = currency WHERE base_amount IS NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('partner_expenses')) {
            Schema::table('partner_expenses', function (Blueprint $table) {
                $table->dropColumn(['exchange_rate', 'base_amount', 'base_currency']);
            });
        }
    }
};
