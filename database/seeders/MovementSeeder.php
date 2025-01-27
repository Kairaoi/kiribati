<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert sample movements data
        DB::table('movements')->insert([
            [
                'file_id' => 1,  // Ensure this file_id exists
                'from_ministry_id' => 1,  // Ensure this ministry_id exists
                'to_ministry_id' => 2,  // Ensure this ministry_id exists
                'from_division_id' => 1,  // Ensure this division_id exists
                'to_division_id' => 2,  // Ensure this division_id exists
                'from_user_id' => 3,  // Ensure this user_id exists
                'to_user_id' => 2,  // Ensure this user_id exists
                'movement_start_date' => '2025-01-20 10:00:00',
                'movement_end_date' => '2025-01-20 15:00:00',
                'read_status' => false,
                'comments' => 'File moved from Ministry of Health to Ministry of Education for review.',
                'status' => 'completed',
                'created_by' => 2,  // Ensure this user exists
                'updated_by' => 2,  // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'file_id' => 2,  // Ensure this file_id exists
                'from_ministry_id' => 2,  // Ensure this ministry_id exists
                'to_ministry_id' => 3,  // Ensure this ministry_id exists
                'from_division_id' => 2,  // Ensure this division_id exists
                'to_division_id' => 3,  // Ensure this division_id exists
                'from_user_id' => 2,  // Ensure this user_id exists
                'to_user_id' => 3,  // Ensure this user_id exists
                'movement_start_date' => '2025-02-01 09:00:00',
                'movement_end_date' => '2025-02-01 17:00:00',
                'read_status' => false,
                'comments' => 'Educational documents transferred from Ministry of Education to Ministry of Finance.',
                'status' => 'in_progress',
                'created_by' => 2,  // Ensure this user exists
                'updated_by' => 2,  // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'file_id' => 3,  // Ensure this file_id exists
                'from_ministry_id' => 1,  // Ensure this ministry_id exists
                'to_ministry_id' => 3,  // Ensure this ministry_id exists
                'from_division_id' => 3,  // Ensure this division_id exists
                'to_division_id' => 1,  // Ensure this division_id exists
                'from_user_id' => 3,  // Ensure this user_id exists
                'to_user_id' => 2,  // Ensure this user_id exists
                'movement_start_date' => '2025-03-01 08:30:00',
                'movement_end_date' => '2025-03-01 12:00:00',
                'read_status' => true,
                'comments' => 'Quarterly financial report moved from Ministry of Finance to Ministry of Health.',
                'status' => 'pending',
                'created_by' => 3,  // Ensure this user exists
                'updated_by' => 3,  // Ensure this user exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
