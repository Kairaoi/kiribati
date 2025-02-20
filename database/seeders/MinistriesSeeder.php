<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinistriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ministries = [
            'Administration' => [
                '1/1 Policy and action plans (portfolio)',
                '1/2 MELAD Circular',
                '1/3 Staff list/Workforce plan',
                '1/4 Temporary Staff Appointments',
                '1/5 Work Attached'
            ],
            'Agriculture & Livestock Division' => [
                '2/1 Livestock matters',
                '2/2 Research',
                '2/3 Progress report',
                '2/4 World food day',
                '2/5 Quarantine matters'
            ],
            'Environment & Conservation Division' => [
                '3/1 ECD Management',
                '3/1(a) GEF Operational focal point',
                '3/1(b) SPREP Correspondence circular',
                '3/1(c) GEF 5',
                '3/1(d) ECD Staff Meeting'
            ],
            'Lands & Management Division' => [
                '4/1 Administration Matters',
                '4/2 Lands Issues',
                '4/3 Public Complain',
                '4/4 Land Rent',
                '4/5 Line & Phoenix lease & Sublease on State land'
            ],
            'Ministry of Education' => [
                '14/1 Circular',
                '14/2 USP Correspondences',
                '14/3 Kiribati Teachers College',
                '14/4 School Programme',
                '14/5 Panelist/Interview Matters'
            ],
            'Ministry of Health' => [
                '9/1 Circular',
                '9/2 Panelist',
                '9/3 Health Promotion Development',
                '9/4 Kiribati National Health Day',
                '9/5 Meetings/Workshops/Trainings etc'
            ]
        ];

        foreach ($ministries as $name => $folders) {
            // Insert ministry and get the generated ministry ID
            $ministryId = DB::table('ministries')->insertGetId([
                'name' => $name,
                'code' => 'M' . rand(1, 100), // Dynamically generating a unique code
                'description' => 'Description for ' . $name,
                'is_active' => true,
                'created_by' => 2,
                'updated_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert folders related to the ministry
            foreach ($folders as $folder) {
                // Extract folder number and possible subfolder part (e.g., 3/1(a) for subfolders)
                preg_match('/(\d+)\/(\d+)([a-zA-Z]*)/', $folder, $matches);
                $folderNumber = $matches[2] ?? null;
                $subFolderPart = $matches[3] ?? '';

                if ($folderNumber) {
                    // Generate the base folder number, e.g., "1", "2", etc.
                    $uniqueFolderNumber = $folderNumber . ($subFolderPart ? '.' . ord($subFolderPart) : '');

                    // Check if the folder already exists for the given ministry and number
                    $existingFolder = DB::table('folders')
                        ->where('ministry_id', $ministryId)
                        ->where('folder_number', $uniqueFolderNumber)
                        ->first();

                    // If a folder with this number exists, append a counter
                    $counter = 1;
                    while ($existingFolder) {
                        $uniqueFolderNumber = $folderNumber . '.' . $counter++;
                        $existingFolder = DB::table('folders')
                            ->where('ministry_id', $ministryId)
                            ->where('folder_number', $uniqueFolderNumber)
                            ->first();
                    }

                    // Insert the folder with the unique folder number
                    DB::table('folders')->insert([
                        'ministry_id' => $ministryId,
                        'folder_number' => $uniqueFolderNumber, // Folder numbers like 1, 1.1, 1.2, etc.
                        'folder_name' => $folder,
                        'category' => $name,
                        'folder_description' => 'Documents related to ' . $folder,
                        'is_active' => true,
                        'created_by' => 2,
                        'updated_by' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
