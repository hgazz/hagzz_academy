<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('partner_expenses')) {
            Schema::create('partner_expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academy_id');
                $table->unsignedBigInteger('category_id');
                $table->string('title');
                $table->decimal('amount', 12, 2);
                $table->string('currency', 10)->default('SAR');
                $table->date('expense_date');
                $table->enum('period_type', ['daily', 'monthly', 'quarterly', 'annual'])->default('monthly');
                $table->string('approved_by')->nullable();
                $table->text('notes')->nullable();
                $table->string('receipt_image')->nullable();
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->timestamps();

                $table->foreign('academy_id')->references('id')->on('academies')->onDelete('cascade');
                $table->foreign('category_id')->references('id')->on('partner_expense_categories')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_expenses');
    }
};
