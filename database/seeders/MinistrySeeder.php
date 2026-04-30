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
            ['identity_organisation_id' => '1', 'organisation_type_id' => '1',   'name' => 'Office of Te Beretitenti',                                'code' => 'OB',    'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '2', 'organisation_type_id' => '1',   'name' => 'Public Service Office',                                   'code' => 'PSO',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '3','organisation_type_id' => '1',   'name' => 'Judiciary',                                               'code' => 'JU',    'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '4','organisation_type_id' => '1',   'name' => 'Kiribati Police Service',                                 'code' => 'KPS',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '5','organisation_type_id' => '1',   'name' => 'Public Service Commission',                               'code' => 'PSC',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '6','organisation_type_id' => '1',   'name' => 'Ministry of Foreign Affairs and Immigration',             'code' => 'MFAI',  'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '7','organisation_type_id' => '1',   'name' => 'Ministry of Culture and Internal Affairs',                'code' => 'MCIA',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '8','organisation_type_id' => '1',   'name' => 'Ministry of Environment, Lands and Agricultural Development', 'code' => 'MELAD', 'location' => 'Bikenibeu',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '9','organisation_type_id' => '1',   'name' => 'House of Parliament',                                     'code' => 'HOP',     'location' => 'Ambo',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '10','organisation_type_id' => '1',   'name' => 'Ministry of Tourism, Commerce, Industry and Cooperatives', 'code' => 'MTCIC', 'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '11','organisation_type_id' => '1',   'name' => 'Kiribati National Audit Office',                          'code' => 'KNAO',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '12','organisation_type_id' => '1',   'name' => 'Office of the Attorney General',                          'code' => 'OAG',    'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '13','organisation_type_id' => '1',   'name' => 'Ministry of Fisheries and Ocean Resources',               'code' => 'MFOR',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '14','organisation_type_id' => '1',   'name' => 'Ministry of Health and Medical Services',                 'code' => 'MHMS',   'location' => 'Nawerewere',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '15','organisation_type_id' => '1',   'name' => 'Ministry of Education',                                   'code' => 'MOE',    'location' => 'Bikenibeu',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '16','organisation_type_id' => '1',   'name' => 'Ministry of Information, Communications and Transport',    'code' => 'MICT',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '17','organisation_type_id' => '1',   'name' => 'Ministry of Finance and Economic Development',            'code' => 'MFED',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '18','organisation_type_id' => '1',   'name' => 'Ministry of Infrastructure and Sustainable Energy',        'code' => 'MISE',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '19','organisation_type_id' => '1',   'name' => 'Ministry of Employment and Human Resource',               'code' => 'MEHR',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '20','organisation_type_id' => '1',   'name' => 'Ministry of Line and Phoenix Islands Development',        'code' => 'MLPID',  'location' => 'Kiritimati',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '21','organisation_type_id' => '1',   'name' => 'Ministry of Women, Youth, Sport and Social Affairs',      'code' => 'MWYSSA', 'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '22','organisation_type_id' => '1',   'name' => 'Ministry of Justice',                                     'code' => 'MOJ',    'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['identity_organisation_id' => '23','organisation_type_id' => '1',   'name' => 'Leadership Commission',                                   'code' => 'LC',     'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
       
     
        ]);

    }
}
