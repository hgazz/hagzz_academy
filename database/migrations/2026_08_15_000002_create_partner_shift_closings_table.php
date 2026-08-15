<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('partner_shift_closings')) {
            Schema::create('partner_shift_closings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
                $table->foreignId('partner_user_id')->nullable()->constrained('partner_users')->nullOnDelete();
                $table->string('closed_by_name', 255);
                $table->string('shift_title', 100)->default('وردية اليوم');
                $table->dateTime('started_at');
                $table->dateTime('closed_at');
                $table->decimal('total_cash_system', 12, 2)->default(0);
                $table->decimal('total_card_system', 12, 2)->default(0);
                $table->decimal('total_instapay_system', 12, 2)->default(0);
                $table->decimal('total_fawry_system', 12, 2)->default(0);
                $table->decimal('total_bank_system', 12, 2)->default(0);
                $table->decimal('total_other_system', 12, 2)->default(0);
                $table->decimal('total_discounts_system', 12, 2)->default(0);
                $table->decimal('total_collected_system', 12, 2)->default(0);
                $table->decimal('actual_cash_counted', 12, 2)->default(0);
                $table->decimal('cash_difference', 12, 2)->default(0);
                $table->string('next_shift_receiver', 255)->nullable();
                $table->text('notes')->nullable();
                $table->string('status', 30)->default('closed');
                $table->timestamps();

                $table->index(['academy_id', 'closed_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_shift_closings');
    }
};
