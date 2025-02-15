<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert sample movement records
        DB::table('movements')->insert([
            [
                'file_id' => 1, // Ensure this file exists
                'from_ministry_id' => 1, // Ensure this ministry exists
                'to_ministry_id' => 2, // Ensure this ministry exists
                'from_user_id' => 1, // Ensure this user exists
                'to_user_id' => 2, // Ensure this user exists
                'to_division_id' => 1, // Ensure this division exists
                'movement_start_date' => Carbon::create('2025', '01', '21', '10', '00', '00'),
                'movement_end_date' => Carbon::create('2025', '02', '20', '17', '00', '00'),
                'read_status' => false,
                'comments' => 'File moved for review in the education ministry.',
                'required_action' => 'Review and approve policy update.',
                'action_taken' => 'File reviewed, awaiting final approval.',
                'status' => 'in_progress',
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null, // No soft delete in this case
            ],
            [
                'file_id' => 2, // Ensure this file exists
                'from_ministry_id' => 2, // Ensure this ministry exists
                'to_ministry_id' => 3, // Ensure this ministry exists
                'from_user_id' => 2, // Ensure this user exists
                'to_user_id' => 3, // Ensure this user exists
                'to_division_id' => null, // No division assigned for this case
                'movement_start_date' => Carbon::create('2025', '02', '11', '09', '00', '00'),
                'movement_end_date' => null, // No end date yet
                'read_status' => false,
                'comments' => 'Quarterly report forwarded for further action.',
                'required_action' => 'Review financial position.',
                'action_taken' => 'Awaiting review.',
                'status' => 'pending',
                'created_by' => 3, // Ensure this user exists
                'updated_by' => 3, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null, // No soft delete in this case
            ],
            [
                'file_id' => 3, // Ensure this file exists
                'from_ministry_id' => 3, // Ensure this ministry exists
                'to_ministry_id' => 1, // Ensure this ministry exists
                'from_user_id' => 3, // Ensure this user exists
                'to_user_id' => 4, // Ensure this user exists
                'to_division_id' => 2, // Ensure this division exists
                'movement_start_date' => Carbon::create('2025', '03', '02', '15', '30', '00'),
                'movement_end_date' => null, // No end date yet
                'read_status' => true,
                'comments' => 'File received for ministry’s health update.',
                'required_action' => 'Provide comments on the report.',
                'action_taken' => 'Comments submitted.',
                'status' => 'completed',
                'created_by' => 4, // Ensure this user exists
                'updated_by' => 4, // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null, // No soft delete in this case
            ],
            // Add more movement records as necessary
        ]);
    }
}
