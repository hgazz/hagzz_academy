<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_student_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_student_id')->constrained('academy_students')->cascadeOnDelete();
            $table->foreignId('academy_group_id')->nullable()->constrained('academy_groups')->nullOnDelete();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('active');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['academy_student_id', 'status']);
            $table->index(['academy_group_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_student_subscriptions');
    }
};
