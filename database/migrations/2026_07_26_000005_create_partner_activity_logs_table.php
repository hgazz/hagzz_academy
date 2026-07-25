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
        if (!Schema::hasTable('partner_activity_logs')) {
            Schema::create('partner_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academy_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name')->nullable();
                $table->string('action');
                $table->text('description')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->foreign('academy_id')->references('id')->on('academies')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('partner_users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_activity_logs');
    }
};
