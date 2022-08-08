<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UtilityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UtilityCategory::create([
            'name' => 'Floor covering',
            'status' => 1,
            'description' => 'Lorem ipsum',
            'created_at' => now(),
        ]);
    }
}
