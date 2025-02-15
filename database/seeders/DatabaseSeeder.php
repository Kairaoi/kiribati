<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,      // Run first to create users
            MinistriesSeeder::class,  // Run second to create ministries
            DivisionSeeder::class,  // Run last to create divisions
            FileTypesSeeder::class,  
            FolderSeeder::class,  
            // FileSeeder::class,
           
           
            // MovementSeeder::class,  
        ]);
    }
}