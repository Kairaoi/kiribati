<?php

namespace Database\Seeders;

use App\Models\National\Eregistry\Division;
use App\Models\National\Eregistry\Ministry;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MOEUserSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create roles: admin, registry, and user
        $roles = ['admin', 'registry', 'user'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }


        //GET THE MFED MINISTRY
        $ministry = Ministry::where('code', 'MOE')->first();
        $headquartersDivision = Division::where('name', 'Headquarters')
            ->where('ministry_id', $ministry->id)->first();
        $primaryDivision = Division::where('name', 'Primary Division')
            ->where('ministry_id', $ministry->id)->first();
        $jssDivision = Division::where('name', 'Junior Secondary School Division')
            ->where('ministry_id', $ministry->id)->first();
        $sssDivision = Division::where('name', 'Scholarship and Senior Secondary Unit')
            ->where('ministry_id', $ministry->id)->first();   


        if (!$ministry) {
            $this->command->error('MOE ministry not found. Please run Ministry Seeder first.');
            return;
        }

        // Now update the admin user with the organisation_id
        // $adminUser->organisation_id = $organisation->id;
        // $adminUser->save();

        // // Assign admin role to the Organisation of Finance admin
        // $adminUser->assignRole('admin');

        // Users data (roles now are admin, registry, or user)
        

         $user1 = User::withTrashed()->updateOrCreate(
            ['email' => 'secretary@moe.gov.ki'],
            [
                'first_name' => 'Roreti',
                'last_name' => 'David',
                'password' => Hash::make('sec'),
                'ministry_id' => $ministry->id,
                'division_id' => $headquartersDivision->id,
                'deleted_at' => null
            ]
        );
        $user1->assignRole('admin');
        // $ministry->review_officer_id = $user1->id;
        // $ministry->save();

        $user2 = User::withTrashed()->updateOrCreate(
            ['email' => 'ds@moe.gov.ki'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Austin',
                'password' => Hash::make('ds'),
                'ministry_id' => $ministry->id,
                'division_id' => $headquartersDivision->id,
                'deleted_at' => null
            ]
        );
        $user2->assignRole('admin');

        $user3 = User::withTrashed()->updateOrCreate(
            ['email' => 'sss.officer@moe.gov.ki'],
            [
                'first_name' => 'Tematang',
                'last_name' => 'Tekanene',
                'password' => Hash::make('ds'),
                'ministry_id' => $ministry->id,
                'division_id' => $sssDivision->id,
                'deleted_at' => null
            ]
        );
        $user3->assignRole('user');

        $user4 = User::withTrashed()->updateOrCreate(
            ['email' => 'jss.officer@moe.gov.ki'],
            [
                'first_name' => 'Turia',
                'last_name' => 'Toabwa',
                'password' => Hash::make('custom'),
                'ministry_id' => $ministry->id,
                'division_id' => $jssDivision->id,
                'deleted_at' => null
            ]
        );
        $user4->assignRole('user');

        $user5 = User::withTrashed()->updateOrCreate(
            ['email' => 'om@moe.gov.ki'],
            [
                'first_name' => 'Anre',
                'last_name' => 'Toabwa',
                'password' => Hash::make('om'),
                'ministry_id' => $ministry->id,
                'division_id' => $headquartersDivision->id,
                'deleted_at' => null
            ]
        );
        $user5->assignRole('registry');

    }
}

