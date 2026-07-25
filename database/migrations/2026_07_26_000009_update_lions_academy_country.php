<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Find Egypt country ID
        $egyptCountry = DB::table('countries')->where('iso2', 'EG')->orWhere('currency_code', 'EGP')->first();

        if ($egyptCountry) {
            // Update academies with commercial_name containing lions or email containing lions to Egypt country_id
            DB::table('academies')
                ->where(function ($query) {
                    $query->where('commercial_name', 'LIKE', '%lions%')
                          ->orWhere('commercial_name', 'LIKE', '%ليونز%')
                          ->orWhere('email', 'LIKE', '%lions%');
                })
                ->update(['country_id' => $egyptCountry->id]);
        }
    }

    public function down(): void
    {
    }
};
