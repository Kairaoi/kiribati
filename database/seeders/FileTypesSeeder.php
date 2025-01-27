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
                'name' => 'Inward',
                'description' => 'Files that are received from external sources',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Outward',
                'description' => 'Files that are sent to external parties',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
