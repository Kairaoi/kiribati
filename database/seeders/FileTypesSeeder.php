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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'General',
                'description' => 'Miscellaneous or General documents that do not fit into specific categories.',
                'code' => 'GEN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Memorandom of Understanding (MoU)',
                'description' => 'An agreement between parties outlining mutual understanding.',
                'code' => 'MOU',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Circular',
                'description' => 'Official communications to be distributed across divisions, divisions, organisations, the public.',
                'code' => 'CIR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Conference',
                'description' => null,
                'code' => 'CNF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Seminar',
                'description' => null,
                'code' => 'SMR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Workshop',
                'description' => null,
                'code' => 'WSP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Meeting',
                'description' => null,
                'code' => 'MTG',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Advertisement',
                'description' => 'Inform other organisations/organisations about services, products, or events.',
                'code' => 'ADV',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Report',
                'description' => 'Progress reports, annual reports, evaluation reports, research report and other types of reports.',
                'code' => 'RPT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Audit Document',
                'description' => 'Reports on financial or operational audits.',
                'code' => 'AUD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'name' => 'Tender',
                'description' => 'Tender notices, bid submissions, evaluation results.',
                'code' => 'TND',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Procurement',
                'description' => 'Quotations, evaluation reports, award justifications, contracts',
                'code' => 'PRC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Briefing Note',
                'description' => 'Document to inform or prepare officials for decisions, meetings or speeches.',
                'code' => 'BRF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Policy',
                'description' => 'Official  rules, procedures, and guidelines.',
                'code' => 'POL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Contract/Agreement',
                'description' => 'Legal binding documents between parties.',
                'code' => 'CON',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Regulation',
                'description' => 'Legally binding rules or directives made and maintained by an authority.',
                'code' => 'REG',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Guideline',
                'description' => 'A non-binding document that provides recommendations or advice.',
                'code' => 'GDL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Decision Paper',
                'description' => 'Official records of decisions made or to propose decisions.',
                'code' => 'DCP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Invoice',
                'description' => 'A bill for goods or services.',
                'code' => 'INV',	
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quotation',
                'description' => 'Price estimates for procurement.',
                'code' => 'QT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Purchase Order',
                'description' => 'Authorization for procurement.',
                'code' => 'PO',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'General/Recurrent Budget',
                'description' => 'Annual budget documents, including estimates and allocations.',
                'code' => 'GB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Development Budget',
                'description' => 'Capital expenditure plans and allocations.',
                'code' => 'DB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Press Release',
                'description' => 'Official statements for media.',
                'code' => 'PR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Presentation',
                'code' => 'PPT',
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Form',
                'description' => null,
                'code' => 'FRM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ministerial Tour Document',
                'description' => 'Records related to ministerial tours such as itineraries, reports, community feedback and press coverage.',
                'code' => 'MTD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Receipt',
                'description' => null,
                'code' => 'RCT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Others',
                'description' => null,
                'code' => 'OTH',
                'created_at' => now(),
                'updated_at' => now(),
            ],


        ]);
    }
}
