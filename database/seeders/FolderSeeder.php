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
                'folder_number' => 10,
                'folder_name' => 'Circular',
                'category' => 'Reports', // Category for grouping
                'folder_description' => 'Contains all health-related reports and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 1, // Ensure this ministry exists
                'folder_number' => 20,
                'folder_name' => 'Panelist',
                'category' => 'Reports', // Category for grouping
                'folder_description' => 'Contains all health-related reports and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 1, // Ensure this ministry exists
                'folder_number' => 30,
                'folder_name' => 'Health Promotion Development',
                'category' => 'Reports', // Category for grouping
                'folder_description' => 'Contains all health-related reports and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 1, // Ensure this ministry exists
                'folder_number' => 40,
                'folder_name' => 'Kiribati National Health Day',
                'category' => 'Reports', // Category for grouping
                'folder_description' => 'Contains all health-related reports and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 1, // Ensure this ministry exists
                'folder_number' => 50,
                'folder_name' => 'Meetings/Workshops/Trainings etc',
                'category' => 'Reports', // Category for grouping
                'folder_description' => 'Contains all health-related reports and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 1, // Ensure this ministry exists
                'folder_number' => 60,
                'folder_name' => 'Tender Proposals',
                'category' => 'Reports', // Category for grouping
                'folder_description' => 'Contains all health-related reports and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 1, // Ensure this ministry exists
                'folder_number' => 70,
                'folder_name' => 'WHO Matters',
                'category' => 'Reports', // Category for grouping
                'folder_description' => 'Contains all health-related reports and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 1, // Ensure this ministry exists
                'folder_number' => 80,
                'folder_name' => 'General',
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
                'folder_name' => 'Circular',
                'category' => 'Policies',
                'folder_description' => 'Contains educational policies, curriculum, and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 2, // Ensure this ministry exists
                'folder_number' => 2,
                'folder_name' => 'USP Correspondences',
                'category' => 'Policies',
                'folder_description' => 'Contains educational policies, curriculum, and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 2, // Ensure this ministry exists
                'folder_number' => 3,
                'folder_name' => 'Kiribati Teachers College',
                'category' => 'Policies',
                'folder_description' => 'Contains educational policies, curriculum, and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 2, // Ensure this ministry exists
                'folder_number' => 4,
                'folder_name' => 'School Programme',
                'category' => 'Policies',
                'folder_description' => 'Contains educational policies, curriculum, and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 2, // Ensure this ministry exists
                'folder_number' => 5,
                'folder_name' => 'Panelist/Interview Matters',
                'category' => 'Policies',
                'folder_description' => 'Contains educational policies, curriculum, and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 2, // Ensure this ministry exists
                'folder_number' => 6,
                'folder_name' => 'Junior Secondary Schools(JSS)',
                'category' => 'Policies',
                'folder_description' => 'Contains educational policies, curriculum, and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 2, // Ensure this ministry exists
                'folder_number' => 7,
                'folder_name' => 'Primary Schools',
                'category' => 'Policies',
                'folder_description' => 'Contains educational policies, curriculum, and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 2, // Ensure this ministry exists
                'folder_number' => 8,
                'folder_name' => 'Tender Proposals',
                'category' => 'Policies',
                'folder_description' => 'Contains educational policies, curriculum, and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 2, // Ensure this ministry exists
                'folder_number' => 9,
                'folder_name' => 'CDRC Matters',
                'category' => 'Policies',
                'folder_description' => 'Contains educational policies, curriculum, and documents.',
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_id' => 2, // Ensure this ministry exists
                'folder_number' => 10,
                'folder_name' => 'Meetings/Workshops/Trainings,etc',
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
