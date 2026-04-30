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
                'description' => 'External communication documents',
                'code' => 'LET',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Memorandom of Understanding',
                'description' => 'An agreement between parties outlining mutual understanding.',
                'code' => 'MOU',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Circular',
                'description' => 'Official communications to be distributed across divisions, divisions, organisations, the public.',
                'code' => 'CIR',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Conference',
                'description' => 'Documents related to conferences',
                'code' => 'CNF',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Seminar',
                'description' => 'Documents for seminars and presentations',
                'code' => 'SMR',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Workshop',
                'description' => 'Documents for workshops and training sessions',
                'code' => 'WSP',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Meeting',
                'description' => 'Documents for meetings and discussions',
                'code' => 'MTG',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Advertisement',
                'description' => 'Inform other organisations/organisations about services, products, or events.',
                'code' => 'ADV',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Report',
                'description' => 'Progress reports, annual reports, evaluation reports, research report and other types of reports.',
                'code' => 'RPT',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tender',
                'description' => 'Tender notices, bid submissions, evaluation results.',
                'code' => 'TND',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Procurement',
                'description' => 'Quotations, evaluation reports, award justifications, contracts',
                'code' => 'PRC',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Briefing Note',
                'description' => 'Document to inform or prepare officials for decisions, meetings or speeches.',
                'code' => 'BRF',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Policy',
                'description' => 'Official  rules, procedures, and guidelines.',
                'code' => 'POL',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Contract/Agreement',
                'description' => 'Legal binding documents between parties.',
                'code' => 'CON',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Regulation & Acts',
                'description' => 'Legally binding rules or directives made and maintained by an authority.',
                'code' => 'REG',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Guideline',
                'description' => 'A non-binding document that provides recommendations or advice.',
                'code' => 'GDL',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Decision Paper',
                'description' => 'Official records of decisions made or to propose decisions.',
                'code' => 'DCP',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Invoice',
                'description' => 'A bill for goods or services.',
                'code' => 'INV',	
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quotation',
                'description' => 'Price estimates for procurement.',
                'code' => 'QT',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Purchase Order',
                'description' => 'Authorization for procurement.',
                'code' => 'PO',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Recurrent Budget',
                'description' => 'Annual budget documents, including estimates and allocations.',
                'code' => 'GB',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Development Budget',
                'description' => 'Capital expenditure plans and allocations.',
                'code' => 'DB',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Press Release',
                'description' => 'Official statements for media.',
                'code' => 'PR',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],   
            [
                'name' => 'General',
                'description' => 'Documents that do not fit into other categories.',
                'code' => 'GEN',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],   
            [
                'name' => 'Ministerial Tour',
                'description' => 'Documents related to ministerial tours.',
                'code' => 'MT',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],  
            [
                'name' => 'In-service Training',
                'description' => 'Documents related to in-service training programs.',
                'code' => 'IST',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], 
            [
                'name' => 'Trainings/Scholarships',
                'description' => 'Documents related to training and scholarship programs.',
                'code' => 'TS',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                'name' => 'Customer Service',
                'description' => 'Documents related to customer services, including feedback, complaints, and service improvement plans.',
                'code' => 'CS',
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],



        ]);
    }
}
