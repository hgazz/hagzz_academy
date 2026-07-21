<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id')->unique()->constrained('academies')->cascadeOnDelete();
            $table->string('business_account_id')->nullable();
            $table->string('phone_number_id')->nullable()->unique();
            $table->string('display_phone_number')->nullable();
            $table->text('access_token')->nullable();
            $table->text('app_secret')->nullable();
            $table->text('verify_token')->nullable();
            $table->string('default_country_code', 5)->default('20');
            $table->string('status')->default('pending');
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_webhook_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
            $table->string('name');
            $table->string('audience')->default('custom');
            $table->string('purpose')->default('notification');
            $table->string('message_type')->default('template');
            $table->string('template_name')->nullable();
            $table->string('template_language', 12)->nullable();
            $table->json('template_parameters')->nullable();
            $table->text('body')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['academy_id', 'created_at']);
        });

        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
            $table->string('contact_type')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->string('contact_name');
            $table->string('phone', 25);
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('last_inbound_at')->nullable();
            $table->timestamp('service_window_expires_at')->nullable();
            $table->timestamps();
            $table->unique(['academy_id', 'phone']);
            $table->index(['academy_id', 'last_message_at']);
            $table->index(['contact_type', 'contact_id']);
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('whatsapp_campaigns')->nullOnDelete();
            $table->string('provider_message_id')->nullable()->unique();
            $table->string('direction');
            $table->string('message_type')->default('text');
            $table->text('body')->nullable();
            $table->string('template_name')->nullable();
            $table->string('status')->default('queued');
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['academy_id', 'created_at']);
            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('whatsapp_campaigns');
        Schema::dropIfExists('whatsapp_channels');
    }
};
