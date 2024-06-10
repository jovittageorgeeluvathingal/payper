<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TransactionPurpose;

class TransactionPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
       public function run()
    {
        $purposes = [
            ['name' => 'Send Money', 'charge' => 25],
            ['name' => 'Gift', 'charge' => 50],
            ['name' => 'Birthday', 'charge' => 70],
            ['name' => 'Emergency', 'charge' => 0],
            ['name' => 'Donation', 'charge' => 0],
            ['name' => 'Utilities', 'charge' => 0],
        ];
        foreach ($purposes as $purpose) {
            TransactionPurpose::create($purpose);
        }
    }
}
