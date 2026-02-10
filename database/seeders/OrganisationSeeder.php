<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('organisations')->insert([

            //ministries
            ['organisation_type_id' => '1',   'name' => 'Office of Te Beretitenti',                                'code' => 'OB',    'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Public Service Office',                                   'code' => 'PSO',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Judiciary',                                               'code' => 'JU',    'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Kiribati Police Service',                                 'code' => 'KPS',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Public Service Commission',                               'code' => 'PSC',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Foreign Affairs and Immigration',             'code' => 'MFAI',  'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Culture and Internal Affairs',                'code' => 'MCIA',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Environment, Lands and Agricultural Development', 'code' => 'MELAD', 'location' => 'Bikenibeu',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'House of Parliament',                                     'code' => 'HOP',     'location' => 'Ambo',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Tourism, Commerce, Industry and Cooperatives', 'code' => 'MTCIC', 'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Kiribati National Audit Office',                          'code' => 'KNAO',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Office of the Attorney General',                          'code' => 'OAG',    'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Fisheries and Ocean Resources',               'code' => 'MFOR',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Health and Medical Services',                 'code' => 'MHMS',   'location' => 'Nawerewere',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Education',                                   'code' => 'MOE',    'location' => 'Bikenibeu',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Information, Communications and Transport',    'code' => 'MICT',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Finance and Economic Development',            'code' => 'MFED',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Infrastructure and Sustainable Energy',        'code' => 'MISE',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Employment and Human Resource',               'code' => 'MEHR',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Line and Phoenix Islands Development',        'code' => 'MLPID',  'location' => 'Kiritimati',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Women, Youth, Sport and Social Affairs',      'code' => 'MWYSSA', 'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Ministry of Justice',                                     'code' => 'MOJ',    'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '1',   'name' => 'Leadership Commission',                                   'code' => 'LC',     'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
        
        
            //SOEs with their commented oversight ministry 
            //MICT
            ['organisation_type_id' => '2',   'name' => 'Air Kiribati Limited',                                   'code' => 'AKL',    'location' => 'Bonriki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Airport Kiribati Authority',                             'code' => 'AKA',    'location' => 'Bonriki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Broadcasting and Publications Authority',                'code' => 'BPA',    'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Bwebweriki Net Limited ',                                'code' => 'BNL',    'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Kiribati Ports Authority',                               'code' => 'KPA',    'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Kiribati Land Transport Authority',                      'code' => 'KLTA',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Kiribati National Shipping Line',                        'code' => 'KNSL',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],

            //MFOR
            ['organisation_type_id' => '2',   'name' => 'Central Pacific Producers Ltd',                          'code' => 'CPPL',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Te Atinimarawa Company Limited',                         'code' => 'TACL',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],

            //MFED            
            ['organisation_type_id' => '2',   'name' => 'Development Bank of Kiribati',                           'code' => 'DBK',    'location' => 'Bairiki', 'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Kiribati Insurance Corporation',                         'code' => 'KIC',    'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],

            //PSO
            ['organisation_type_id' => '2',   'name' => 'Kiribati Housing Corporation',                           'code' => 'KHC',    'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            
            //MISE
            ['organisation_type_id' => '2',   'name' => 'Kiribati Oil Co. Ltd',                                   'code' => 'KOIL',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Kiribati Green Energy Solutions Company Ltd',            'code' => 'KGES',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Plant and Vehicle Unit',                                 'code' => 'PVU',    'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Public Utilities Board',                                 'code' => 'PUB',    'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],

            //MTCIC
            ['organisation_type_id' => '2',   'name' => 'Kiribati Coconut Development Limited',                   'code' => 'KCDL',   'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '2',   'name' => 'Tourism Authority of Kiribati',                          'code' => 'TAK',    'location' => 'Betio',  'created_by' => '1', 'updated_by' => '1'],




            //Diplomatic Missions
            ['organisation_type_id' => '3',   'name' => 'Australian High Commision',                              'code' => 'AHC',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '3',   'name' => 'New Zealand High Commision',                             'code' => 'NZHC',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '3',   'name' => "Embassy of the People's Republic of China",              'code' => 'EC',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],



            //International Organisations
            ['organisation_type_id' => '4',   'name' => 'United Nations Development Programme',                   'code' => 'UNDP',  'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '4',   'name' => 'World Health Organisation',                              'code' => 'WHO',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '4',   'name' => 'Food and Agriculture Organisation',                      'code' => 'FAO',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '4',   'name' => 'Pacific Community (SPC)',                                'code' => 'SPC',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '4',   'name' => 'United Nations International Children\'s Emergency Fund','code' => 'UNICEF','location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '4',   'name' => "World Bank Group",                                      'code' => 'WBG',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '4',   'name' => "Asian Development Bank",                                'code' => 'ADB',   'location' => 'Bairiki',  'created_by' => '1', 'updated_by' => '1'],
            ['organisation_type_id' => '4',   'name' => "International Monetary Fund",                           'code' =>  'IMF',  'location' =>  'Bairiki',  'created_by' =>  '1',  'updated_by' =>  '1'],
            ['organisation_type_id' => '4',   'name' => "Other International Organisation",                      'code' =>  'OTG',  'location' =>  'Bairiki',  'created_by' =>  '1',  'updated_by' =>  '1'],



            //Religious Organisations
            ['organisation_type_id' => '8',   'name' => 'Catholic Church',                                       'code' =>  'RM',  'location' =>  'Bairiki',  'created_by' =>  '1',  'updated_by' =>  '1'],
            ['organisation_type_id' => '8',   'name' => 'Kiribati Uniting Church',                               'code' =>  'KUC',  'location' =>  'Bairiki',  'created_by' =>  '1',  'updated_by' =>  '1'],
            ['organisation_type_id' => '8',   'name' => 'Kiribati Protestant Church',                            'code' =>  'KPC',  'location' =>  'Bairiki',  'created_by' =>  '1',  'updated_by' =>  '1'],
            ['organisation_type_id' => '8',   'name' => 'The Church of Jesus Christ of Latter-day Saints',       'code' =>  'LDS',  'location' =>  'Bairiki',  'created_by' =>  '1',  'updated_by' =>  '1'],
            ['organisation_type_id' => '8',   'name' => 'Bahai Faith',                                           'code' =>  'BF',  'location' =>  'Bairiki',  'created_by' =>  '1',  'updated_by' =>  '1'],
            ['organisation_type_id' => '8',   'name' => 'Seventh-day Adventist Church',                          'code' =>  'SDA',  'location' =>  'Bairiki',  'created_by' =>  '1',  'updated_by' =>  '1'],
            ['organisation_type_id' => '8',   'name' => 'Other Religion',                                        'code' =>  'OR',  'location' =>  'Bairiki',  'created_by' =>  '1',  'updated_by' =>  '1'],

        ]);

    }
}
