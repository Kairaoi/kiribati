<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert sample files data
        DB::table('files')->insert([
            [
                'folder_id' => 1, // Ensure this folder exists
                'ministry_id' => 1, // Ensure this ministry exists
                'division_id' => 1, // Ensure this division exists
                'name' => 'Health Report January 2025',
                'path' => 'uploads/health_reports/jan_2025_report.pdf',
                'receive_date' => Carbon::create('2025', '01', '20'),
                'letter_date' => Carbon::create('2025', '01', '15'),
                'letter_ref_no' => 'HR-001-JAN2025',
                'details' => 'This report contains data on health trends for January 2025.',
                'from_details_name' => 'Ministry of Health',
                'to_details_person_name' => 'Dr. John Doe',
                'comments' => 'Report includes monthly statistics and analysis.',
                'security_level' => 'confidential',
                'circulation_status' => true,
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'file_type_id' => 1, // Ensure this file type exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'folder_id' => 2, // Ensure this folder exists
                'ministry_id' => 2, // Ensure this ministry exists
                'division_id' => 2, // Ensure this division exists
                'name' => 'Educational Policy Update February 2025',
                'path' => 'uploads/educational_documents/feb_2025_policy.pdf',
                'receive_date' => Carbon::create('2025', '02', '10'),
                'letter_date' => Carbon::create('2025', '02', '05'),
                'letter_ref_no' => 'EP-002-FEB2025',
                'details' => 'The update contains changes to the national education curriculum.',
                'from_details_name' => 'Ministry of Education',
                'to_details_person_name' => 'Mr. Robert Smith',
                'comments' => 'Includes new directives and curriculum standards.',
                'security_level' => 'internal',
                'circulation_status' => false,
                'is_active' => true,
                'created_by' => 2, // Ensure this user exists
                'updated_by' => 2, // Ensure this user exists
                'file_type_id' => 2, // Ensure this file type exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'folder_id' => 3, // Ensure this folder exists
                'ministry_id' => 3, // Ensure this ministry exists
                'division_id' => 3, // Ensure this division exists
                'name' => 'Quarterly Financial Report Q1 2025',
                'path' => 'uploads/financial_reports/q1_2025_report.pdf',
                'receive_date' => Carbon::create('2025', '03', '01'),
                'letter_date' => Carbon::create('2025', '02', '28'),
                'letter_ref_no' => 'FR-003-Q1-2025',
                'details' => 'This report summarizes the government financial position for Q1 2025.',
                'from_details_name' => 'Ministry of Finance',
                'to_details_person_name' => 'Mr. Michael Green',
                'comments' => 'Detailed financial statistics and projections included.',
                'security_level' => 'strictly_confidential',
                'circulation_status' => true,
                'is_active' => true,
                'created_by' => 3, // Ensure this user exists
                'updated_by' => 3, // Ensure this user exists
                'file_type_id' => 1, // Ensure this file type exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more file records as necessary
        ]);
    }
}
