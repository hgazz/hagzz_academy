<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_student_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academy_student_subscription_id');
            $table->decimal('amount', 10, 2);
            $table->date('paid_at');
            $table->enum('method', ['cash', 'bank_transfer', 'card', 'online', 'other'])->default('cash');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('academy_student_subscription_id', 'student_payments_subscription_fk')
                ->references('id')
                ->on('academy_student_subscriptions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_student_payments');
    }
};
