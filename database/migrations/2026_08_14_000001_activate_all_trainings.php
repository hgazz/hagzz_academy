<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('trainings')->update(['active' => 1]);
    }

    public function down(): void
    {
    }
};
