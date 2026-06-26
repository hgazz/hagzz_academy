<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_competition_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_competition_id')->constrained('academy_competitions')->cascadeOnDelete();
            $table->foreignId('academy_student_id')->constrained('academy_students')->cascadeOnDelete();
            $table->string('role')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['academy_competition_id', 'academy_student_id'], 'academy_competition_player_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_competition_players');
    }
};
