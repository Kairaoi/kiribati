<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MinistrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ministries')->insert([

            //ministries
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '1', 'organisation_type_id' => '1', 'reviewer_title' => 'Secretary',         'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '2', 'organisation_type_id' => '1', 'reviewer_title' => 'Secretary',         'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '3','organisation_type_id' => '1',  'reviewer_title' => 'Chief Justice',      'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '4','organisation_type_id' => '1',  'reviewer_title' => 'Commissioner of Police', 'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '5','organisation_type_id' => '1',  'reviewer_title' => 'Chairperson',        'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '6','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',         'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '7','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',         'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '8','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',         'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '9','organisation_type_id' => '1',  'reviewer_title' => 'Clerk to Parliament', 'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '10','organisation_type_id' => '1', 'reviewer_title' => 'Secretary',         'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '11','organisation_type_id' => '1', 'reviewer_title' => 'Auditor General',   'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '12','organisation_type_id' => '1', 'reviewer_title' => 'Attorney General',  'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '13','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',        'created_by' => '1', 'updated_by' => '1', 'phone' => '75021099', 'email' => 'null', 'website' => 'www.mfor.gov.ki', 'po_box' => '64', 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '14','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',        'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '15','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',        'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '16','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',        'created_by' => '1', 'updated_by' => '1', 'phone' => '74026003', 'email' => 'info@mict.gov.ki', 'website' => 'www.mict.gov.ki', 'po_box' => '487', 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '17','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',        'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '18','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',        'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '19','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',        'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '20','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',        'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '21','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',        'created_by' => '1', 'updated_by' => '1',  'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(), 'identity_organisation_id' => '22','organisation_type_id' => '1',  'reviewer_title' => 'Secretary',        'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
            ['uuid' => (string) Str::uuid(),'identity_organisation_id' => '23','organisation_type_id' => '1',  'reviewer_title' => 'Chairperson',      'created_by' => '1', 'updated_by' => '1', 'phone' => null, 'email' => null, 'website' => null, 'po_box' => null, 'logo_path' => null],
       
     
        ]);

    }
}
