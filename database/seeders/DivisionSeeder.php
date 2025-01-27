<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    public function run()
    {
        DB::table('divisions')->insert([
            [
                'ministry_id' => 1,  // Assuming '1' is a valid ministry ID
                'name' => 'Public Health',
                'code' => 'PH001',
                'description' => 'Responsible for public health policies and initiatives.',
                'is_active' => true,
                'created_by' => 2, // Ensure user ID 1 exists in the users table
                'updated_by' => 2, // Ensure user ID 1 exists in the users table
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 1,  // Assuming '1' is a valid ministry ID
                'name' => 'Environmental Health',
                'code' => 'EH001',
                'description' => 'Manages environmental health and safety regulations.',
                'is_active' => true,
                'created_by' => 2, // Ensure user ID 1 exists in the users table
                'updated_by' => 2, // Ensure user ID 1 exists in the users table
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 2,  // Assuming '2' is a valid ministry ID
                'name' => 'Educational Development',
                'code' => 'ED001',
                'description' => 'Handles educational policies and development programs.',
                'is_active' => true,
                'created_by' => 2, // Ensure user ID 1 exists in the users table
                'updated_by' => 2, // Ensure user ID 1 exists in the users table
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 3,  // Assuming '3' is a valid ministry ID
                'name' => 'Government Finance',
                'code' => 'GF001',
                'description' => 'Responsible for managing government financial matters.',
                'is_active' => true,
                'created_by' => 2, // Ensure user ID 1 exists in the users table
                'updated_by' => 2 , // Ensure user ID 1 exists in the users table
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
