<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClientTypes; // <-- Import your Model
use Illuminate\Support\Facades\DB; // <-- Optional, for truncate

class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Optional: Truncate the table to start fresh every time you run the seeder
        // This prevents duplicate entries if you run it multiple times.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ClientTypes::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Define the data to be inserted
        $types = [
            ['name' => 'Retail'],
            ['name' => 'Lodging'],
            ['name' => 'Recreation'],
            ['name' => 'Restaurant'],
            ['name' => 'Real Estate'],
            ['name' => 'Service'],
            ['name' => 'Non-profit'],
            ['name' => 'Gallery'],
            ['name' => 'Health Care'],
            ['name' => 'Entertainment'],
            ['name' => 'Manufacturing'],
            ['name' => 'Education'],
        ];

        // Insert the data into the database
        foreach ($types as $type) {
            ClientTypes::create($type);
        }
    }
}