<?php

namespace Database\Seeders;

use App\Models\TClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i <= 10 ; $i++) {
            TClass::create([
                'title'=>fake()->title(),
                'subtitle'=>fake()->title(),
                'date'=>fake()->date(),
                'start_time'=> fake()->time(),
                'end_time'=> fake()->time(),
                'training_id'=> 15
            ]);
        }

    }
}
