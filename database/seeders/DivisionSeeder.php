<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    public function run()
    {
        DB::table('divisions')->insert([
            // OB
            ['ministry_id' => 1, 'name' => 'Headquarters', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 1, 'name' => 'Accounts section', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 1, 'name' => 'Cabinet Secretariat', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 1, 'name' => 'State House', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 1, 'name' => 'Climate Change and Disaster Risk management Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 1, 'name' => 'Kiribati Meteorological Services', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 1, 'name' => 'Kiritimati Meteorological Services', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 1, 'name' => 'Supernumerary Posts', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 1, 'name' => 'Communication Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 1, 'name' => 'Supernumerary Posts 2', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],

            // PSO
            ['ministry_id' => 2, 'name' => 'Policy Development and Support Services', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 2, 'name' => 'Planning Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 2, 'name' => 'ICT Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 2, 'name' => 'Human Resource Management Centre', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 2, 'name' => 'Public Sector Performance Management Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 2, 'name' => 'Integrity and Corruption Control Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 2, 'name' => 'Supernumerary Posts', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],

            // Judiciary
            ['ministry_id' => 3, 'name' => 'Headquarters', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 3, 'name' => 'Account Section', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 3, 'name' => 'Magistrate Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 3, 'name' => 'Technical Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 3, 'name' => 'Supernumerary Posts', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],

            // KPS
            ['ministry_id' => 4, 'name' => 'Headquarters', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 4, 'name' => 'Account Section', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 4, 'name' => 'Civilian Staff', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 4, 'name' => 'Kiritimati Branch', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 4, 'name' => 'Supernumerary Posts', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],

            // PSC
            ['ministry_id' => 5, 'name' => 'Headquarters', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 5, 'name' => 'Account Section', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            
            // MFAI
            ['ministry_id' => 6, 'name' => 'Admin, Policy and Support services', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Multilateral Affairs', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Kiribati Permanent Mission to the UN', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Bilateral Affairs', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Consular Affairs', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Protocol and Ceremonial Affairs', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Kiribati High Commission in New Zealand', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Kiribati High Commission in Australia', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Asia Pacific', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Kiribati Embassy Peoples Republic of China', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Kiribati High Commission Fiji', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Kiribati Geneva Mission', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'APS Supernumerary Posts', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Immigration Services', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 6, 'name' => 'Kiritimati Branch', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            
            // MCIA
            ['ministry_id' => 7, 'name' => 'Headquarters', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 7, 'name' => 'Account Section', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 7, 'name' => 'ICT Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 7, 'name' => 'Election Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 7, 'name' => 'Cultural Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 7, 'name' => 'Local Government Section', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 7, 'name' => 'Outer Island Maintenance Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 7, 'name' => 'Rural Planning Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 7, 'name' => 'Supernumerary Posts', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],

            // MELAD
            ['ministry_id' => 8, 'name' => 'Headquarters', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 8, 'name' => 'Account Section', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 8, 'name' => 'Agriculture Division', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 8, 'name' => 'Accounts Section', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 8, 'name' => 'Kiritimati Branch', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 8, 'name' => 'Environment and Conservation Division', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 8, 'name' => 'Lands Management Division', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 8, 'name' => 'Kiritimati Branch 2', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 8, 'name' => 'Wildlife Unit', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 8, 'name' => 'Supernumerary Posts', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            
            // Parliament
            ['ministry_id' => 9, 'name' => 'Headquarters', 'location' => 'Ambo', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 9, 'name' => 'Account Section', 'location' => 'Ambo', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 9, 'name' => 'Parilament Committee', 'location' => 'Ambo', 'created_at' => now(), 'updated_at' => now(),],

            // MTCIC
            ['ministry_id' => 10, 'name' => 'Headquarters', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Accounts Section', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'ICT and BIU Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Business Promotion Centre', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Quality Promotion Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Investment Promotion Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Trade Promotion Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Industry Development and Promotion Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Business Promotion Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Business Regulatory Centre', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Consumer Protection Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Meteorology Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Cooperative and Credit Union Regulatory and Compliance Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Intellectual Property Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Business and companies registry and compliance division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Tourism Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Support Unit (Kiritimati Division)', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Business Promotion Unit', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 10, 'name' => 'Business Regulatory Unit', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            
            // KNAO
            ['ministry_id' => 11, 'name' => 'Headquarters', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 11, 'name' => 'Account Section', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 11, 'name' => 'Information Communication Technology Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 11, 'name' => 'Central Government Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 11, 'name' => 'Local Government Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 11, 'name' => 'State Owned Entities Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 11, 'name' => 'Project Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 11, 'name' => 'Performance Audit and HR Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            
            // OAG
            ['ministry_id' => 12, 'name' => 'Corporate Services Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 12, 'name' => 'Account Section', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 12, 'name' => 'Drafting Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 12, 'name' => 'Civil Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 12, 'name' => 'Criminal Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            
            // MFOR
            ['ministry_id' => 13, 'name' => 'Corporate Services Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 13, 'name' => 'Account Section', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 13, 'name' => 'Information Communication Technology Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 13, 'name' => 'Coastal Fisheries Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 13, 'name' => 'Accounts Section', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 13, 'name' => 'Te Tia Akawa', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 13, 'name' => 'Seafood Verification Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 13, 'name' => 'Oceanic Fisheries Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 13, 'name' => 'Geo Science Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 13, 'name' => 'Planning and Development Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 13, 'name' => 'Kiritimati Branch', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 13, 'name' => 'Supernumerary Posts', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            
            // MHMS
            ['ministry_id' => 14, 'name' => 'Headquarters', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Planning Unit', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Accounts Section', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Dental Services', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Health Information Services', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Hospital Services', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Laboratory Services', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Health Promotion Services', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Public Health Services', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Rehabilitation unit', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Eye Division', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Pharmacy and Medical Services', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Medical Imaging Services', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Biomedical Engineering Division', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Support Services', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Kiritimati Branch', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Nursing Services', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'School of Nursing and Health', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Hospital Services - Southern Kiribati Hospital (SKH)', 'location' => 'Tabiteuea', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Nursing Services (SKH)', 'location' => 'Tabiteuea', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Administration (SKH)', 'location' => 'Tabiteuea', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Support Services (SKH)', 'location' => 'Tabiteuea', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 14, 'name' => 'Supernumerary Posts', 'location' => 'Nawerewere', 'created_at' => now(), 'updated_at' => now(),],
            
            // MOE
            ['ministry_id' => 15, 'name' => 'Headquarters', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Accounts Section Headquarters', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Policy, Planning, Research and Development Division', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Scholarship and Senior Secondary Unit', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Curriculum Development and Assessment Division', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Kiribati Qualification Agency', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Junior Secondary School Division', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'KGV and EBS Secondary School', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Accounts Section KGV&EBS', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Kiribati Teachers College', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Accounts Section KTC', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Library Section', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Accounts Section Library', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Archives Section', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Accounts Section Archives', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Primary Section', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Teabike High School', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Accounts Section Teabike', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Early Childhood Care and Education', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 15, 'name' => 'Supernumerary Posts', 'location' => 'Bikenibeu', 'created_at' => now(), 'updated_at' => now(),],
            
            // MICT
            ['ministry_id' => 16, 'name' => 'Corporate Services', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Accounts Section', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Digital Transformation Office', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Research and Development Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Civil Aviation Division', 'location' => 'Tabaonga', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Airport Services Division', 'location' => 'Bonriki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Kiritimati Branch', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Fire Division (Tarawa)', 'location' => 'Bonriki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Government Printery', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Marine Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Accounts Section 2', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Kiribati Post', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 16, 'name' => 'Supernumeracy Posts', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],

            // MFED
            ['ministry_id' => 17, 'name' => 'Headquarters', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 17, 'name' => 'Accounts Section', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 17, 'name' => 'Accounting Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 17, 'name' => 'Customs Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 17, 'name' => 'Internal Audit Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 17, 'name' => 'Central Procurement Office', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 17, 'name' => 'National Economic Planning Office', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 17, 'name' => 'Statistic Office', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 17, 'name' => 'Taxation Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 17, 'name' => 'Information Technology Services Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 17, 'name' => 'Supernumerary Posts', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            
            // MISE
            ['ministry_id' => 18, 'name' => 'Headquarters', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Accounts Section', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'ICT Unit', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Division of Engineering', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Quality Control and Inspection Unit', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Civil Engineering Section', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Building and Furnishing (Construction Section)', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Building and Maintenance Division (Merging Joinery and Construction Sections)', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Building and Furnishing (Joinery Section)', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Energy Division', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Architectural Services', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Cost Planning Services', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Planning Project Unit', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Water and Sanitation Engineering Services', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 18, 'name' => 'Supernumerary Posts', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],

            // MEHR
            ['ministry_id' => 19, 'name' => 'Headquarters', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 19, 'name' => 'Accounts Section', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 19, 'name' => 'Marine Training Centre', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 19, 'name' => 'Labour Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 19, 'name' => 'Kiribati Institute of Technology', 'location' => 'Betio', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 19, 'name' => 'Accounts Section', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 19, 'name' => 'Kiritimati Branch', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 19, 'name' => 'Supernumerary Posts', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            
            // MLPID
            ['ministry_id' => 20, 'name' => 'Headquarters', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Accounts Section', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Information Technology Unit', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Development Planning Unit', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Linnix Agency Office (Tarawa)', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Construction and Joinery Section', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Civil and Technical Section', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Quality and Inspection Unit', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Water Sanitation Division', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Energy Planning Division', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Housing Division', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Solar Salt', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Heavy Machinery and Mechanical Division', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 20, 'name' => 'Supernumerary Posts', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],

            // MWYSSA
            ['ministry_id' => 21, 'name' => 'Headquarters', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'Accounts Section', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'Women Development Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'Social Welfare Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'Welfare and Counselling Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'Informations and Public Relations unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'Sports Development Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'Youth Development Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'NGO Development Unit', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'Elderly', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'Disability Inclusion Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'Kiritimati Branch', 'location' => 'Kiritimati', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 21, 'name' => 'Supernumerary Posts', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],

            // MOJ
            ['ministry_id' => 22, 'name' => 'Administration and Policy Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 22, 'name' => 'Office of the Superintendent', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 22, 'name' => 'Office of the Registrar General', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 22, 'name' => 'Human Rights Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 22, 'name' => 'Office of the Public Legal Services', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 22, 'name' => 'Information Technology Division', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 22, 'name' => 'Supernumerary Posts', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            
            // LC
            ['ministry_id' => 23, 'name' => 'Headquarter', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            ['ministry_id' => 23, 'name' => 'Supernumerary Posts', 'location' => 'Bairiki', 'created_at' => now(), 'updated_at' => now(),],
            
        ]);

    }
}
