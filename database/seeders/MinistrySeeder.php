<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinistrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ministries')->insert([

            //ministries
            ['identity_organisation_id' => '1', 'organisation_type_id' => '1',   'name' => 'Office of Te Beretitenti',                                'code' => 'OB',   'reviewer_title' => 'Secretary',  'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '2', 'organisation_type_id' => '1',   'name' => 'Public Service Office',                                   'code' => 'PSO',  'reviewer_title' => 'Secretary',  'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '3','organisation_type_id' => '1',   'name' => 'Judiciary',                                               'code' => 'JU',    'reviewer_title' => 'Chief Justice',  'address' => 'Betio, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '4','organisation_type_id' => '1',   'name' => 'Kiribati Police Service',                                 'code' => 'KPS',   'reviewer_title' => 'Commissioner of Police',  'address' => 'Betio, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '5','organisation_type_id' => '1',   'name' => 'Public Service Commission',                               'code' => 'PSC',   'reviewer_title' => 'Chairperson',  'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '6','organisation_type_id' => '1',   'name' => 'Ministry of Foreign Affairs and Immigration',             'code' => 'MFAI',  'reviewer_title' => 'Secretary',  'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '7','organisation_type_id' => '1',   'name' => 'Ministry of Culture and Internal Affairs',                'code' => 'MCIA',  'reviewer_title' => 'Secretary',   'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '8','organisation_type_id' => '1',   'name' => 'Ministry of Environment, Lands and Agricultural Development', 'code' => 'MELAD', 'reviewer_title' => 'Secretary',   'address' => 'Bikenibeu, Tarawa',  'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '9','organisation_type_id' => '1',   'name' => 'House of Parliament',                                     'code' => 'HOP',     'reviewer_title' => 'Clerk to Parliament',  'address' => 'Ambo, Tarawa',  'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '10','organisation_type_id' => '1',   'name' => 'Ministry of Tourism, Commerce, Industry and Cooperatives', 'code' => 'MTCIC',  'reviewer_title' => 'Secretary', 'address' => 'Betio, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '11','organisation_type_id' => '1',   'name' => 'Kiribati National Audit Office',                          'code' => 'KNAO',    'reviewer_title' => 'Auditor General', 'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '12','organisation_type_id' => '1',   'name' => 'Office of the Attorney General',                          'code' => 'OAG',     'reviewer_title' => 'Attorney General', 'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '13','organisation_type_id' => '1',   'name' => 'Ministry of Fisheries and Ocean Resources',               'code' => 'MFOR',    'reviewer_title' => 'Secretary', 'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1', 'phone' => '75021099', 'email' => 'null', 'website' => 'www.mfor.gov.ki', 'po_box' => '64', 'logo_path' => null],
            ['identity_organisation_id' => '14','organisation_type_id' => '1',   'name' => 'Ministry of Health and Medical Services',                 'code' => 'MHMS',    'reviewer_title' => 'Secretary', 'address' => 'Nawerewere, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '15','organisation_type_id' => '1',   'name' => 'Ministry of Education',                                   'code' => 'MOE',     'reviewer_title' => 'Secretary', 'address' => 'Bikenibeu, Tarawa',  'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '16','organisation_type_id' => '1',   'name' => 'Ministry of Information, Communications and Transport',    'code' => 'MICT',    'reviewer_title' => 'Secretary', 'address' => 'Betio, Tarawa',  'created_by' => '1', 'updated_by' => '1', 'phone' => '74026003', 'email' => 'info@mict.gov.ki', 'website' => 'www.mict.gov.ki', 'po_box' => '487', 'logo_path' => null],
            ['identity_organisation_id' => '17','organisation_type_id' => '1',   'name' => 'Ministry of Finance and Economic Development',            'code' => 'MFED',    'reviewer_title' => 'Secretary', 'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '18','organisation_type_id' => '1',   'name' => 'Ministry of Infrastructure and Sustainable Energy',        'code' => 'MISE',    'reviewer_title' => 'Secretary', 'address' => 'Betio, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '19','organisation_type_id' => '1',   'name' => 'Ministry of Employment and Human Resource',               'code' => 'MEHR',    'reviewer_title' => 'Secretary', 'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '20','organisation_type_id' => '1',   'name' => 'Ministry of Line and Phoenix Islands Development',        'code' => 'MLPID',   'reviewer_title' => 'Secretary', 'address' => 'Kiritimati',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '21','organisation_type_id' => '1',   'name' => 'Ministry of Women, Youth, Sport and Social Affairs',      'code' => 'MWYSSA',  'reviewer_title' => 'Secretary', 'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '22','organisation_type_id' => '1',   'name' => 'Ministry of Justice',                                     'code' => 'MOJ',     'reviewer_title' => 'Secretary', 'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['identity_organisation_id' => '23','organisation_type_id' => '1',   'name' => 'Leadership Commission',                                   'code' => 'LC',      'reviewer_title' => 'Chairperson', 'address' => 'Bairiki, Tarawa',  'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
       
     
        ]);

    }
}
