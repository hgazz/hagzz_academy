<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id')->constrained('academies')->cascadeOnDelete();
            $table->foreignId('sport_id')->nullable()->constrained('sports')->nullOnDelete();
            $table->string('home_team_name');
            $table->string('opponent_name');
            $table->date('competition_date');
            $table->time('starts_at')->nullable();
            $table->string('venue')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->unsignedSmallInteger('home_score')->nullable();
            $table->unsignedSmallInteger('opponent_score')->nullable();
            $table->text('result_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['academy_id', 'competition_date']);
            $table->index(['sport_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_competitions');
    }
};
