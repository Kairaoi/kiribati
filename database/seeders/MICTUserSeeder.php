<?php

namespace Database\Seeders;

use App\Models\National\Eregistry\Division;
use App\Models\National\Eregistry\Organisation;
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
        $organisation = Organisation::where('code', 'MICT')->first();
        $dtoDivision = Division::where('name', 'Digital Transformation Office')->first();
        $marineDivision = Division::where('name', 'Marine Division')->first();
        $corporateDivision = Division::where('name', 'Corporate Services')->first();


        $admin->update(['organisation_id' => $organisation->id]);
        $admin->update(['division_id' => $dtoDivision->id]);
        $admin->save();

        if (!$organisation) {
            $this->command->error('MICT organisation not found. Please run Organisation Seeder first.');
            return;
        }


        // Now update the admin user with the organisation_id
        $adminUser->organisation_id = $organisation->id;
        $adminUser->save();

        // Assign admin role to the system admin
        $adminUser->assignRole('admin');


        $user1 = User::withTrashed()->updateOrCreate(
            ['email' => 'secretary@mict.gov.ki'],
            [
                'first_name' => 'Mitateti',
                'last_name' => 'Mote',
                'password' => Hash::make('secretary'),
                'organisation_id' => $organisation->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user1->assignRole('admin');
        $organisation->review_officer_id = $user1->id;
        $organisation->save();

        $user2 = User::withTrashed()->updateOrCreate(
            ['email' => 'ds@mict.gov.ki'],
            [
                'first_name' => 'Aoniba',
                'last_name' => 'Riare',
                'password' => Hash::make('ds'),
                'organisation_id' => $organisation->id,
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
                'organisation_id' => $organisation->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user3->assignRole('admin');


        $user4 = User::withTrashed()->updateOrCreate(
            ['email' => 'om@mict.gov.ki'],
            [
                'first_name' => 'Tebikia',
                'last_name' => 'John',
                'password' => Hash::make('om'),
                'organisation_id' => $organisation->id,
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
                'organisation_id' => $organisation->id,
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
                'organisation_id' => $organisation->id,
                'division_id' => $dtoDivision->id,
                'deleted_at' => null
            ]
        );
        $user6->assignRole('user');

    }
}