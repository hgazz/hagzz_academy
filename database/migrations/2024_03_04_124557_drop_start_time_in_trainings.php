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
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
            $table->unsignedBigInteger('max_players');
            $table->enum('level', ['Beginner','Intermediate','Advanced']);
            $table->enum('gender', ['All','Men','Women']);
            $table->enum('age_group', ['All','Kids','Juniors','Adults']);
            $table->foreignId('address_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            //
        });
    }
};
