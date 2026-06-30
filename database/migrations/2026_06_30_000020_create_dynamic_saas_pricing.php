<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            if (!Schema::hasColumn('countries', 'iso2')) $table->char('iso2', 2)->nullable()->unique()->after('name');
            if (!Schema::hasColumn('countries', 'currency_code')) $table->char('currency_code', 3)->nullable()->after('iso2');
        });
        Schema::table('academies', function (Blueprint $table) {
            if (!Schema::hasColumn('academies', 'country_id')) $table->foreignId('country_id')->nullable()->after('branch_to')->constrained('countries')->nullOnDelete();
        });
        if (!Schema::hasTable('saas_plan_prices')) {
            Schema::create('saas_plan_prices', function (Blueprint $table) {
                $table->id(); $table->foreignId('saas_plan_id')->constrained('saas_plans')->cascadeOnDelete();
                $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete(); $table->char('currency_code', 3);
                $table->decimal('monthly_price', 12, 2); $table->decimal('annual_price', 12, 2); $table->decimal('tax_rate', 5, 2)->default(0);
                $table->boolean('tax_included')->default(false); $table->boolean('active')->default(true); $table->timestamps();
                $table->unique(['saas_plan_id', 'country_id']);
            });
        }
        Schema::table('tenant_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('tenant_subscriptions', 'saas_plan_price_id')) $table->foreignId('saas_plan_price_id')->nullable()->after('saas_plan_id')->constrained('saas_plan_prices')->nullOnDelete();
            if (!Schema::hasColumn('tenant_subscriptions', 'price_amount')) $table->decimal('price_amount', 12, 2)->nullable()->after('custom_price');
            if (!Schema::hasColumn('tenant_subscriptions', 'currency_code')) $table->char('currency_code', 3)->nullable()->after('price_amount');
            if (!Schema::hasColumn('tenant_subscriptions', 'tax_rate')) $table->decimal('tax_rate', 5, 2)->default(0)->after('currency_code');
            if (!Schema::hasColumn('tenant_subscriptions', 'tax_included')) $table->boolean('tax_included')->default(false)->after('tax_rate');
        });
    }
    public function down(): void {}
};
