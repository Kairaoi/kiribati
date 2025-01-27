<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinistriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ministries')->insert([
            [
                'name' => 'Ministry of Health',
                'code' => 'MOH',
                'description' => 'Responsible for public health policies and services.',
                'is_active' => true,
                'created_by' => 2, // Replace with an existing user ID
                'updated_by' => 2, // Replace with an existing user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ministry of Education',
                'code' => 'MOE',
                'description' => 'Responsible for educational development and policies.',
                'is_active' => true,
                'created_by' => 2, // Replace with an existing user ID
                'updated_by' => 2, // Replace with an existing user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ministry of Finance',
                'code' => 'MOF',
                'description' => 'Handles government finances and economic policies.',
                'is_active' => true,
                'created_by' => 2, // Replace with an existing user ID
                'updated_by' => 2, // Replace with an existing user ID
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more ministries as needed
        ]);
    }
}
