<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents; 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Education',
                'description' => 'Documents related to educational institutions, programs, and policies.',
                'code' => 'EDU',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Health',
                'description' => 'Documents related to health services, policies, and programs.',
                'code' => 'HLT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance',
                'description' => 'Documents related to financial transactions, budgets, and reports.',
                'code' => 'FIN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Environment',
                'description' => 'Documents related to environmental policies, conservation efforts, and sustainability.',
                'code' => 'ENV',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Construction',
                'description' => 'Documents related to building new or expanding facilities (building, bridge, complex)',
                'code' => 'CON',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Agriculture',
                'description' => 'Documents related to agricultural policies, programs, and research.',
                'code' => 'AGR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tourism',
                'description' => 'Documents related to tourism policies, programs, and marketing.',
                'code' => 'TRM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Land Transport & Road Infrastructure',
                'description' => 'Documents related to vehicles, roads, driver licensing, public transport, traffic management, and road safety.',
                'code' => 'TRP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aviation and Air Transport',
                'description' => 'Covers civil aviation, air traffic control, pilot licensing, aircraft regulation, and airport infrastructure.',
                'code' => 'AIR',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Public Works',
                'description' => 'Documents related to public works projects, maintenance, and development.',
                'code' => 'PWK',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Public Safety',
                'description' => 'Documents related to public safety policies, programs, and emergency services.',
                'code' => 'PSF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Culture',
                'description' => 'Documents related to cultural policies, programs, and heritage.',
                'code' => 'CUL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Justice',
                'description' => 'Documents related to legal matters, court proceedings, and justice policies.',
                'code' => 'JUS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Foreign Affairs',
                'description' => 'Documents related to international relations, treaties, and diplomacy.',
                'code' => 'FOR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Employment',
                'description' => 'Documents related to employment policies, labor relations, and workforce development.',
                'code' => 'EMP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Energy',
                'description' => 'Documents related to electricity, fuel supply, renewable energy, and energy policies.',
                'code' => 'ENG',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ICT & Telecommunications',
                'description' => 'Documents related to telecommunications policies, projects, and services.',
                'code' => 'ICT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Trade',
                'description' => 'Documents related to trade policies, agreements, and economic development.',
                'code' => 'TRA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Defense',
                'description' => 'Documents related to national defense policies, programs, and security.',
                'code' => 'DEF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Housing',
                'description' => 'Documents related to housing policies, programs, and development.',
                'code' => 'HOU',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Water Resources',
                'description' => 'Documents related to water resource management, policies, and projects.',
                'code' => 'WAT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sports',
                'description' => 'Documents related to sports policies, programs, and events.',
                'code' => 'SPO',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Youth Affairs',
                'description' => 'Documents related to youth policies, programs, and initiatives.',
                'code' => 'YAF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Social Affairs',
                'code' => 'SOC',
                'description' => 'Files related to community development, social programs, public welfare, and social services initiatives.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Disaster Management',
                'description' => 'Documents related to disaster preparedness, response, and recovery.',
                'code' => 'DIS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Research and Development',
                'description' => 'Documents related to research projects, findings, and innovations.',
                'code' => 'R&D',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Outer Islands Matters',
                'description' => 'Documents related to outer islands',
                'code' => 'OIM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marine and Maritime Affairs',
                'description' => 'Documents related to shipping, vessel registration, ports, search and rescue (SAR), 
                                  marine safety, maritime law and navigation.',
                'code' => 'MMA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Miscellaneous',
                'description' => 'Documents that do not fit into other categories.',
                'code' => 'MIS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Consultancy Services',
                'description' => 'Documents related to external professional advice, strategic planning, technical expertise.',
                'code' => 'CTS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Goods',
                'description' => 'Documents related to the procurement, acquisition, delivery, or management of tangible physical items such as vehicles, equipment, stationery, and supplies.',
                'code' => 'GDS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Others',
                'description' => 'Documents that do not fit into other categories.',
                'code' => 'OTH',
                'created_at' => now(),
                'updated_at' => now(),
            ],



            
        ]);
    }
}
