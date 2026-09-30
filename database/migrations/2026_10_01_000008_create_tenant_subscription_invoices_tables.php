<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tenant_subscription_invoices')) {
            Schema::create('tenant_subscription_invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_subscription_id')
                    ->nullable()
                    ->constrained('tenant_subscriptions', 'id', 'tsi_sub_fk')
                    ->nullOnDelete();
                $table->foreignId('academy_id')
                    ->constrained('academies', 'id', 'tsi_acad_fk')
                    ->cascadeOnDelete();
                $table->string('invoice_number', 50);
                $table->date('period_starts_at')->nullable();
                $table->date('period_ends_at')->nullable();
                $table->date('issued_at')->nullable();
                $table->date('due_at')->nullable();
                $table->decimal('list_amount', 12, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('subtotal_amount', 12, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->string('currency_code', 10)->default('EGP');
                $table->string('status', 30)->default('issued');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['academy_id', 'status'], 'tsi_acad_stat_idx');
                $table->index('invoice_number', 'tsi_inv_num_idx');
            });
        }

        if (!Schema::hasTable('tenant_subscription_payments')) {
            Schema::create('tenant_subscription_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_subscription_invoice_id')
                    ->constrained('tenant_subscription_invoices', 'id', 'tsp_inv_fk')
                    ->cascadeOnDelete();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('payment_method', 50)->nullable();
                $table->string('payment_reference', 100)->nullable();
                $table->dateTime('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->string('status', 30)->default('completed');
                $table->timestamps();

                $table->index('tenant_subscription_invoice_id', 'tsp_inv_id_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_subscription_payments');
        Schema::dropIfExists('tenant_subscription_invoices');
    }
};
