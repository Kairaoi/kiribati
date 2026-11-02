<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\National\Eregistry\Organisation;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            OrganisationTypeSeeder::class,
            OrganisationSeeder::class,
            DivisionSeeder::class,
            CategorySeeder::class,
            MICTUserSeeder::class,  
            MFEDUserSeeder::class,  
            FileTypesSeeder::class,
            MOEUserSeeder::class,
        ]);
    }
}
