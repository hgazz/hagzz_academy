<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('academies', 'business_type')) {
            Schema::table('academies', function (Blueprint $table) {
                $table->string('business_type', 20)->default('academy')->after('role')->index();
            });
        }

        if (!Schema::hasTable('saas_plans')) {
            Schema::create('saas_plans', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->json('name');
                $table->decimal('monthly_price', 10, 2)->default(0);
                $table->decimal('annual_price', 10, 2)->default(0);
                $table->unsignedInteger('max_venues')->default(1);
                $table->unsignedInteger('max_spaces')->default(1);
                $table->unsignedInteger('max_staff')->default(1);
                $table->json('features')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tenant_subscriptions')) {
            Schema::create('tenant_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
                $table->foreignId('saas_plan_id')->nullable()->constrained('saas_plans')->nullOnDelete();
                $table->string('billing_cycle', 20)->default('monthly');
                $table->string('status', 20)->default('active');
                $table->decimal('custom_price', 10, 2)->nullable();
                $table->date('starts_at');
                $table->date('ends_at')->nullable();
                $table->date('trial_ends_at')->nullable();
                $table->boolean('auto_renew')->default(false);
                $table->timestamps();
                $table->index(['academy_id', 'status']);
            });
        }

        if (!Schema::hasTable('venues')) {
            Schema::create('venues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
                $table->json('name');
                $table->string('phone')->nullable();
                $table->string('address');
                $table->string('timezone', 60)->default('Africa/Cairo');
                $table->string('currency', 3)->default('EGP');
                $table->boolean('active')->default(true);
                $table->timestamps();
                $table->index(['academy_id', 'active']);
            });
        }

        if (!Schema::hasTable('venue_spaces')) {
            Schema::create('venue_spaces', function (Blueprint $table) {
                $table->id();
                $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
                $table->foreignId('sport_id')->nullable()->constrained('sports')->nullOnDelete();
                $table->json('name');
                $table->json('description')->nullable();
                $table->string('space_type', 40)->default('court');
                $table->unsignedInteger('capacity')->nullable();
                $table->unsignedInteger('slot_minutes')->default(60);
                $table->decimal('hourly_price', 10, 2);
                $table->time('opens_at')->default('08:00:00');
                $table->time('closes_at')->default('23:00:00');
                $table->boolean('active')->default(true);
                $table->timestamps();
                $table->index(['venue_id', 'active']);
            });
        }

        if (!Schema::hasTable('venue_customers')) {
            Schema::create('venue_customers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('phone');
                $table->string('email')->nullable();
                $table->timestamps();
                $table->unique(['academy_id', 'phone']);
            });
        }

        if (!Schema::hasTable('venue_bookings')) {
            Schema::create('venue_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
                $table->foreignId('venue_space_id')->constrained('venue_spaces')->cascadeOnDelete();
                $table->foreignId('venue_customer_id')->constrained('venue_customers')->restrictOnDelete();
                $table->string('reference', 40)->unique();
                $table->string('booking_type', 30)->default('individual');
                $table->string('title')->nullable();
                $table->dateTime('starts_at');
                $table->dateTime('ends_at');
                $table->string('status', 20)->default('confirmed');
                $table->string('source', 20)->default('dashboard');
                $table->decimal('total_amount', 10, 2);
                $table->decimal('paid_amount', 10, 2)->default(0);
                $table->string('payment_method', 40)->default('cash');
                $table->string('payment_method_other')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['academy_id', 'starts_at']);
                $table->index(['venue_space_id', 'starts_at', 'ends_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_bookings');
        Schema::dropIfExists('venue_customers');
        Schema::dropIfExists('venue_spaces');
        Schema::dropIfExists('venues');
        Schema::dropIfExists('tenant_subscriptions');
        Schema::dropIfExists('saas_plans');

        if (Schema::hasColumn('academies', 'business_type')) {
            Schema::table('academies', fn (Blueprint $table) => $table->dropColumn('business_type'));
        }
    }
};
