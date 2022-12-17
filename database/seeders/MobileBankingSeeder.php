<?php

namespace Database\Seeders;

use App\Models\Accounts\MobileBanking;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MobileBankingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MobileBanking::create([
            'name' => 'Bkash'
        ]);

        MobileBanking::create([
            'name' => 'Rocket'
        ]);

        MobileBanking::create([
            'name' => 'Nagad'
        ]);

        MobileBanking::create([
            'name' => 'Ucash'
        ]);
    }
}
