<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert sample file access data
        DB::table('file_access')->insert([
            [
                'file_id' => 1, // Ensure this file_id exists
                'ministry_id' => 1, // Ensure this ministry_id exists
                'division_id' => 1, // Ensure this division_id exists
                'access_type' => 'view',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'file_id' => 2, // Ensure this file_id exists
                'ministry_id' => 2, // Ensure this ministry_id exists
                'division_id' => 2, // Ensure this division_id exists
                'access_type' => 'edit',
                'is_active' => true,
                'created_by' => 3, // Ensure this user exists
                'updated_by' => 3, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'file_id' => 3, // Ensure this file_id exists
                'ministry_id' => 3, // Ensure this ministry_id exists
                'division_id' => 3, // Ensure this division_id exists
                'access_type' => 'full',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Add more file access records as necessary
        ]);
    }
}
