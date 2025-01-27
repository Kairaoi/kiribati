<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OutWardFilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert sample out_ward_files data
        DB::table('out_ward_files')->insert([
            [
                'folder_id' => 1, // Ensure this folder ID exists in the 'folders' table
                'ministry_id' => 1, // Ensure this ministry ID exists in the 'ministries' table
                'division_id' => 1, // Ensure this division ID exists in the 'divisions' table
                'name' => 'Health Report January 2025',
                'path' => 'uploads/health_reports/jan_2025_report.pdf',
                'send_date' => '2025-01-20',
                'letter_date' => '2025-01-15',
                'letter_ref_no' => 'HR-001-JAN2025',
                'details' => 'This report contains data on health trends for January 2025.',
                'from_details_name' => 'Dr. John Doe',
                'to_details_name' => 'Dr. Jane Smith',
                'security_level' => 'confidential',
                'circulation_status' => true,
                'is_active' => true,
                'created_by' => 2, // Ensure this user ID exists in the 'users' table
                'updated_by' => 2, // Ensure this user ID exists in the 'users' table
                'file_type_id' => 1, // Ensure this file type ID exists in the 'file_types' table
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'folder_id' => 2, // Ensure this folder ID exists
                'ministry_id' => 2, // Ensure this ministry ID exists
                'division_id' => 2, // Ensure this division ID exists
                'name' => 'Educational Policy Update February 2025',
                'path' => 'uploads/educational_documents/feb_2025_policy.pdf',
                'send_date' => '2025-02-05',
                'letter_date' => '2025-02-01',
                'letter_ref_no' => 'EP-002-FEB2025',
                'details' => 'The update contains changes to the national education curriculum.',
                'from_details_name' => 'Mr. Robert Smith',
                'to_details_name' => 'Mr. William Johnson',
                'security_level' => 'internal',
                'circulation_status' => false,
                'is_active' => true,
                'created_by' => 3, // Ensure this user ID exists
                'updated_by' => 3, // Ensure this user ID exists
                'file_type_id' => 2, // Ensure this file type ID exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'folder_id' => 3, // Ensure this folder ID exists
                'ministry_id' => 3, // Ensure this ministry ID exists
                'division_id' => 3, // Ensure this division ID exists
                'name' => 'Quarterly Financial Report Q1 2025',
                'path' => 'uploads/financial_reports/q1_2025_report.pdf',
                'send_date' => '2025-03-01',
                'letter_date' => '2025-02-28',
                'letter_ref_no' => 'FR-003-Q1-2025',
                'details' => 'This report summarizes the government financial position for Q1 2025.',
                'from_details_name' => 'Mr. Michael Green',
                'to_details_name' => 'Ms. Laura White',
                'security_level' => 'strictly_confidential',
                'circulation_status' => true,
                'is_active' => true,
                'created_by' => 3, // Ensure this user ID exists
                'updated_by' => 3, // Ensure this user ID exists
                'file_type_id' => 1, // Ensure this file type ID exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more data as needed
        ]);
    }
}
