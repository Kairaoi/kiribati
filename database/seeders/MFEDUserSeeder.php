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

class MFEDUserSeeder extends Seeder
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
        $ministry = Ministry::where('code', 'MFED')->first();
        $headquartersDivision = Division::where('name', 'Headquarters')
            ->where('ministry_id', $ministry->id)->first();
        $accountDivision = Division::where('name', 'Accounts Section')
            ->where('ministry_id', $ministry->id)->first();
        $customsDivision = Division::where('name', 'Customs Division')
            ->where('ministry_id', $ministry->id)->first();


        if (!$ministry) {
            $this->command->error('MFED ministry not found. Please run Ministry Seeder first.');
            return;
        }

        // Now update the admin user with the ministry_id
        // $adminUser->ministry_id = $ministry->id;
        // $adminUser->save();

        // // Assign admin role to the Ministry of Finance admin
        // $adminUser->assignRole('admin');

        // Users data (roles now are admin, registry, or user)
        

        $user1 = User::withTrashed()->updateOrCreate(
            ['email' => 'secretary@mfed.gov.ki'],
            [
                'first_name' => 'Domingo',
                'last_name' => 'Kabunare',
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
            ['email' => 'ds@mfed.gov.ki'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Austen',
                'password' => Hash::make('ds'),
                'ministry_id' => $ministry->id,
                'division_id' => $headquartersDivision->id,
                'deleted_at' => null
            ]
        );
        $user2->assignRole('admin');

        $user3 = User::withTrashed()->updateOrCreate(
            ['email' => 'sas@mfed.gov.ki'],
            [
                'first_name' => 'Samuel',
                'last_name' => 'Adams',
                'password' => Hash::make('sas'),
                'ministry_id' => $ministry->id,
                'division_id' => $headquartersDivision->id,
                'deleted_at' => null
            ]
        );
        $user3->assignRole('admin');

        $user4 = User::withTrashed()->updateOrCreate(
            ['email' => 'custom@mfed.gov.ki'],
            [
                'first_name' => 'Teringa',
                'last_name' => 'Toabwa',
                'password' => Hash::make('custom'),
                'ministry_id' => $ministry->id,
                'division_id' => $customsDivision->id,
                'deleted_at' => null
            ]
        );
        $user4->assignRole('user');
        
        $user5 = User::withTrashed()->updateOrCreate(
            ['email' => 'om@mfed.gov.ki'],
            [
                'first_name' => 'Tetobi',
                'last_name' => 'Mariko',
                'password' => Hash::make('om'),
                'ministry_id' => $ministry->id,
                'division_id' => $headquartersDivision->id,
                'deleted_at' => null
            ]
        );
        $user5->assignRole('registry');

    }
}

