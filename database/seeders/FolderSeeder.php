<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FolderMoeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ministry of Education ID
        $ministryId = 4; // Ensure this ministry exists in the ministries table

        // Ministry of Education Folder Index
        $folders = [
            ['folder_number' => 1, 'folder_name' => 'Circular'],
            ['folder_number' => 2, 'folder_name' => 'USP Correspondences'],
            ['folder_number' => 3, 'folder_name' => 'Kiribati Teachers College'],
            ['folder_number' => 4, 'folder_name' => 'School Programme'],
            ['folder_number' => 5, 'folder_name' => 'Panelist/Interview Matters'],
            ['folder_number' => 6, 'folder_name' => 'Junior Secondary Schools (JSS)'],
            ['folder_number' => 7, 'folder_name' => 'Primary Schools'],
            ['folder_number' => 8, 'folder_name' => 'Tender Proposals'],
            ['folder_number' => 9, 'folder_name' => 'CDRC Matters'],
            ['folder_number' => 10, 'folder_name' => 'Meetings/Workshops/Trainings etc'],
        ];

        // Insert folders into the database
        foreach ($folders as $folder) {
            DB::table('folders')->insert([
                'ministry_id' => $ministryId,
                'folder_number' => $folder['folder_number'],
                'folder_name' => $folder['folder_name'],
                'category' => 'Education', // Example category
                'folder_description' => 'Folder related to ' . $folder['folder_name'],
                'is_active' => true,
                'created_by' => 1, // Ensure this user exists
                'updated_by' => 1, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
