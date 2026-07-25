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
        if (!Schema::hasTable('partner_user_branches')) {
            Schema::create('partner_user_branches', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('branch_id');

                $table->foreign('user_id')->references('id')->on('partner_users')->onDelete('cascade');
                $table->foreign('branch_id')->references('id')->on('academies')->onDelete('cascade');
                $table->primary(['user_id', 'branch_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_user_branches');
    }
};
