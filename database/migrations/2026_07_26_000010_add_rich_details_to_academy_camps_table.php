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
        if (Schema::hasTable('academy_camps')) {
            Schema::table('academy_camps', function (Blueprint $table) {
                if (!Schema::hasColumn('academy_camps', 'room_features')) {
                    $table->text('room_features')->nullable()->after('description');
                }
                if (!Schema::hasColumn('academy_camps', 'venue_features')) {
                    $table->text('venue_features')->nullable()->after('room_features');
                }
                if (!Schema::hasColumn('academy_camps', 'program_itinerary')) {
                    $table->text('program_itinerary')->nullable()->after('venue_features');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('academy_camps')) {
            Schema::table('academy_camps', function (Blueprint $table) {
                $table->dropColumn(['room_features', 'venue_features', 'program_itinerary']);
            });
        }
    }
};
