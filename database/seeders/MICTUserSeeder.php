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

class MICTUserSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create roles first
        $roles = ['admin', 'editor', 'viewer'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Create the initial admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@system.gov.ki'],
            [
                'first_name' => 'System',
                'last_name' => '',
                'password' => Hash::make('Admin@123'),
                // Don't set organisation_id yet
            ]
        );

        $admin = User::where('email', 'admin@system.gov.ki')->first();

        // get the MICT organisation
        $ministry = Ministry::where('code', 'MICT')->first();
        $dtoDivision = Division::where('name', 'Digital Transformation Office')->first();
        $marineDivision = Division::where('name', 'Marine Division')->first();
        $corporateDivision = Division::where('name', 'Corporate Services')->first();


        $admin->update(['ministry_id' => $ministry->id]);
        $admin->update(['division_id' => $dtoDivision->id]);
        $admin->save();

        if (!$ministry) {
            $this->command->error('MICT ministry not found. Please run Ministry Seeder first.');
            return;
        }


        // Now update the admin user with the ministry_id
        $adminUser->ministry_id = $ministry->id;
        $adminUser->save();

        // Assign admin role to the system admin
        $adminUser->assignRole('admin');


        $user1 = User::withTrashed()->updateOrCreate(
            ['email' => 'secretary@mict.gov.ki'],
            [
                'first_name' => 'Mitateti',
                'last_name' => 'Mote',
                'password' => Hash::make('secretary'),
                'ministry_id' => $ministry->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user1->assignRole('sro');
        $user1->assignRole('review-officer');
        $ministry->save();

        $user2 = User::withTrashed()->updateOrCreate(
            ['email' => 'ds@mict.gov.ki'],
            [
                'first_name' => 'Aoniba',
                'last_name' => 'Riare',
                'password' => Hash::make('ds'),
                'ministry_id' => $ministry->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user2->assignRole('admin');

        $user3 = User::withTrashed()->updateOrCreate(
            ['email' => 'sas@mict.gov.ki'],
            [
                'first_name' => 'Betty',
                'last_name' => 'Mapuola',
                'password' => Hash::make('sas'),
                'ministry_id' => $ministry->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user3->assignRole('admin');


        $user4 = User::withTrashed()->updateOrCreate(
            ['email' => 'om@mict.gov.ki'],
            [
                'first_name' => 'Tiiro',
                'last_name' => 'Tongaiaba',
                'password' => Hash::make('om'),
                'ministry_id' => $ministry->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user4->assignRole('registry');


        $user5 = User::withTrashed()->updateOrCreate(
            ['email' => 'alice@mict.gov.ki'],
            [
                'first_name' => 'Alice',
                'last_name' => 'Wonderland',
                'password' => Hash::make('alice'),
                'ministry_id' => $ministry->id,
                'division_id' => $marineDivision->id,
                'deleted_at' => null
            ]
        );
        $user5->assignRole('user');

        $user6 = User::withTrashed()->updateOrCreate(
            ['email' => 'rheisel@mict.gov.ki'],
            [
                'first_name' => 'Rheisel',
                'last_name' => 'Teataa',
                'password' => Hash::make('rheisel'),
                'ministry_id' => $ministry->id,
                'division_id' => $dtoDivision->id,
                'deleted_at' => null
            ]
        );
        $user6->assignRole('user');

    }
}