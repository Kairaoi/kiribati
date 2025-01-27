<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert sample folders data
        DB::table('folders')->insert([
            [
                'ministry_id' => 1, // Ensure this ministry exists
                'folder_number' => 1,
                'folder_name' => 'Health Reports',
                'category' => 'Reports', // Category for grouping
                'folder_description' => 'Contains all health-related reports and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 2, // Ensure this ministry exists
                'folder_number' => 1,
                'folder_name' => 'Educational Documents',
                'category' => 'Policies',
                'folder_description' => 'Contains educational policies, curriculum, and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 1, // Ensure this ministry exists
                'folder_number' => 2,
                'folder_name' => 'Financial Reports',
                'category' => 'Reports',
                'folder_description' => 'Contains financial documents and reports for the ministry.',
                'is_active' => true,
                'created_by' => 3, // Ensure this user exists
                'updated_by' => 3, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 3, // Ensure this ministry exists
                'folder_number' => 1,
                'folder_name' => 'HR Documents',
                'category' => 'Human Resources',
                'folder_description' => 'Contains human resource-related documents including payroll.',
                'is_active' => true,
                'created_by' => 3, // Ensure this user exists
                'updated_by' => 3, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
