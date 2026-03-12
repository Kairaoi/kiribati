<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganisationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('organisation_types')->insert([
            [
                'name' => 'Ministry',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'State Owned Enterprise',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Diplomatic Mission',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'International Organisation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'name' => 'Private Sector',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'name' => 'Non-Governmental Organisation',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'name' => 'Community Based Organisation',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            [
                'name' => 'Religious Organisation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Other',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
