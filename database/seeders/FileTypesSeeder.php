<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('file_types')->insert([
            [
                'name' => 'Letter',
                'description' => 'Formal communication between ministries, organizations, or individuals.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Memo',
                'description' => 'Internal communication within a ministry or department.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Circular',
                'description' => 'Information shared with multiple recipients, often policy updates.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Notice',
                'description' => 'Official announcements or instructions.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Directive',
                'description' => 'Orders or instructions from higher authorities.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Report',
                'description' => 'A detailed document on a subject, such as financial or performance reports.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Minutes',
                'description' => 'Records of meetings.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Agenda',
                'description' => 'List of topics for meetings.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Briefing Note',
                'description' => 'A short summary of key points on an issue.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Policy Document',
                'description' => 'Guidelines or procedures.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Contract/Agreement',
                'description' => 'Legal binding documents between parties.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Regulation/Guideline',
                'description' => 'Rules or standards to be followed.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Decision Paper',
                'description' => 'Official records of decisions made.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Invoice',
                'description' => 'A bill for goods or services.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quotation',
                'description' => 'Price estimates for procurement.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Purchase Order',
                'description' => 'Authorization for procurement.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Budget Document',
                'description' => 'Financial planning records.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Press Release',
                'description' => 'Official statements for media.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Presentation',
                'description' => 'Slide decks or visual reports.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Form',
                'description' => 'Templates for official data collection (e.g., license applications).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
