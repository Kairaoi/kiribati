<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            MictUserSeeder::class,      // Run first to create users
            MohUserSeeder::class,  // Run second to create ministries
            MofUserSeeder::class,  // Run last to create divisions
            FileTypesSeeder::class,  
            MoeUserSeeder::class,  
            // DivisionSeeder::class,
            // FoldSeeder::class,
               // FileSeeder::class,
           
           
            MinistriesSeeder::class,  
        ]);
    }
}