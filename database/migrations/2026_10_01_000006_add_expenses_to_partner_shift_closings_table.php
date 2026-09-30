<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('partner_shift_closings')) {
            Schema::table('partner_shift_closings', function (Blueprint $table) {
                if (!Schema::hasColumn('partner_shift_closings', 'total_cash_expenses_system')) {
                    $table->decimal('total_cash_expenses_system', 12, 2)->default(0)->after('total_cash_system');
                }
                if (!Schema::hasColumn('partner_shift_closings', 'total_expenses_system')) {
                    $table->decimal('total_expenses_system', 12, 2)->default(0)->after('total_cash_expenses_system');
                }
                if (!Schema::hasColumn('partner_shift_closings', 'net_cash_expected_system')) {
                    $table->decimal('net_cash_expected_system', 12, 2)->default(0)->after('total_expenses_system');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('partner_shift_closings')) {
            Schema::table('partner_shift_closings', function (Blueprint $table) {
                $columns = ['total_cash_expenses_system', 'total_expenses_system', 'net_cash_expected_system'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('partner_shift_closings', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
