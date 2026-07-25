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
        if (!Schema::hasTable('partner_users')) {
            Schema::create('partner_users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academy_id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('password');
                $table->boolean('is_owner')->default(false);
                $table->boolean('access_all_branches')->default(true);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->rememberToken();
                $table->timestamps();

                $table->foreign('academy_id')->references('id')->on('academies')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_users');
    }
};
