<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Camps Primary Table
        if (!Schema::hasTable('academy_camps')) {
            Schema::create('academy_camps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academy_id');
                $table->unsignedBigInteger('sport_id')->nullable();
                $table->string('title_ar');
                $table->string('title_en')->nullable();
                $table->enum('type', ['domestic', 'international'])->default('domestic');
                $table->unsignedBigInteger('country_id')->nullable();
                $table->string('city_name')->nullable();
                $table->string('venue_name')->nullable();
                $table->string('hotel_name')->nullable();
                $table->date('starts_on');
                $table->date('ends_on');
                $table->date('registration_deadline')->nullable();
                $table->unsignedInteger('capacity')->default(0);
                $table->decimal('price', 10, 2)->default(0.00);
                $table->decimal('deposit_amount', 10, 2)->default(0.00);
                $table->string('currency_code', 10)->default('EGP');
                $table->json('included_services')->nullable();
                $table->boolean('visa_required')->default(false);
                $table->enum('status', ['draft', 'upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
                $table->text('description')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('academy_id')->references('id')->on('academies')->onDelete('cascade');
                $table->foreign('sport_id')->references('id')->on('sports')->onDelete('set null');
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            });
        }

        // 2. Camp Staff / Supervisors Pivot Table
        if (!Schema::hasTable('academy_camp_supervisors')) {
            Schema::create('academy_camp_supervisors', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academy_camp_id');
                $table->unsignedBigInteger('coach_id')->nullable();
                $table->unsignedBigInteger('partner_user_id')->nullable();
                $table->string('role')->default('supervisor');
                $table->string('notes')->nullable();
                $table->timestamps();

                $table->foreign('academy_camp_id')->references('id')->on('academy_camps')->onDelete('cascade');
                $table->foreign('coach_id')->references('id')->on('coaches')->onDelete('set null');
            });
        }

        // 3. Camp Participants Table
        if (!Schema::hasTable('academy_camp_participants')) {
            Schema::create('academy_camp_participants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academy_camp_id');
                $table->unsignedBigInteger('academy_student_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name');
                $table->string('phone');
                $table->string('emergency_phone')->nullable();
                $table->string('passport_number')->nullable();
                $table->date('passport_expiry')->nullable();
                $table->enum('visa_status', ['not_required', 'pending', 'issued', 'rejected'])->default('not_required');
                $table->string('tshirt_size', 20)->nullable();
                $table->text('medical_notes')->nullable();
                $table->string('room_number', 50)->nullable();
                $table->decimal('total_fee', 10, 2)->default(0.00);
                $table->decimal('paid_amount', 10, 2)->default(0.00);
                $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
                $table->enum('status', ['registered', 'confirmed', 'attended', 'cancelled'])->default('registered');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('academy_camp_id')->references('id')->on('academy_camps')->onDelete('cascade');
                $table->foreign('academy_student_id')->references('id')->on('academy_students')->onDelete('set null');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 4. Camp Specific Expenses Table
        if (!Schema::hasTable('academy_camp_expenses')) {
            Schema::create('academy_camp_expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academy_camp_id');
                $table->unsignedBigInteger('category_id')->nullable();
                $table->string('title');
                $table->decimal('amount', 10, 2)->default(0.00);
                $table->string('currency_code', 10)->default('EGP');
                $table->date('expense_date');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('academy_camp_id')->references('id')->on('academy_camps')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academy_camp_expenses');
        Schema::dropIfExists('academy_camp_participants');
        Schema::dropIfExists('academy_camp_supervisors');
        Schema::dropIfExists('academy_camps');
    }
};
