<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks')->delete();

        $banks = [
            ['name' => 'Bangladesh Bank', 'category' => 'Central Bank'],
            ['name' => 'Sonali Bank', 'category' => 'State-owned Commercial'],
            ['name' => 'Agrani Bank', 'category' => 'State-owned Commercial'],
            ['name' => 'Rupali Bank', 'category' => 'State-owned Commercial'],
            ['name' => 'Janata Bank', 'category' => 'State-owned Commercial'],
            ['name' => 'BRAC Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Dutch Bangla Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Eastern Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'United Commercial Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Mutual Trust Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Dhaka Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Islami Bank Bangladesh Ltd', 'category' => 'Private Commercial'],
            ['name' => 'Uttara Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Pubali Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'IFIC Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'National Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'The City Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'NCC Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Mercantile Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Southeast Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Prime Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Social Islami Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Standard Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Al-Arafah Islami Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'One Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Exim Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'First Security Islami Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Bank Asia Limited', 'category' => 'Private Commercial'],
            ['name' => 'The Premier Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Bangladesh Commerce Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Trust Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Jamuna Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Shahjalal Islami Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'ICB Islamic Bank', 'category' => 'Private Commercial'],
            ['name' => 'AB Bank', 'category' => 'Private Commercial'],
            ['name' => 'Jubilee Bank Limited', 'category' => 'Private Commercial'],
            ['name' => 'Karmasangsthan Bank', 'category' => 'Specialized Development'],
            ['name' => 'Bangladesh Krishi Bank', 'category' => 'Specialized Development'],
            ['name' => 'Progoti Bank', 'category' => ''],
            ['name' => 'Rajshahi Krishi Unnayan Bank', 'category' => 'Specialized Development'],
            ['name' => 'BangladeshDevelopment Bank Ltd', 'category' => 'Specialized Development'],
            ['name' => 'Bangladesh Somobay Bank Limited', 'category' => 'Specialized Development'],
            ['name' => 'Grameen Bank', 'category' => 'Specialized Development'],
            ['name' => 'BASIC Bank Limited', 'category' => 'Specialized Development'],
            ['name' => 'Ansar VDP Unnyan Bank', 'category' => 'Specialized Development'],
            ['name' => 'The Dhaka Mercantile Co-operative Bank Limited(DMCBL)', 'category' => 'Specialized Development'],
            ['name' => 'Citibank', 'category' => 'Foreign Commercial'],
            ['name' => 'HSBC', 'category' => 'Foreign Commercial'],
            ['name' => 'Standard Chartered Bank', 'category' => 'Foreign Commercial'],
            ['name' => 'CommercialBank of Ceylon', 'category' => 'Foreign Commercial'],
            ['name' => 'State Bank of India', 'category' => 'Foreign Commercial'],
            ['name' => 'WooriBank', 'category' => 'Foreign Commercial'],
            ['name' => 'Bank Alfalah', 'category' => 'Foreign Commercial'],
            ['name' => 'National Bank of Pakistan', 'category' => 'Foreign Commercial'],
            ['name' => 'ICICI Bank', 'category' => 'Foreign Commercial'],
            ['name' => 'Habib Bank Limited', 'category' => 'Foreign Commercial']
        ];

        DB::table('banks')->insert($banks);
    }
}
