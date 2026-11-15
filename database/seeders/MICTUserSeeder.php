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

        // Create the initial admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@system.gov.ki'],
            [
                'first_name' => 'System',
                'last_name' => '',
                'password' => Hash::make('admin'),
            ]
        );

        $systemAdmin = User::where('email', 'admin@system.gov.ki')->first();
        $systemAdmin->assignRole('system-admin');

        // get the MICT organisation
        $organisation = Organisation::where('id', 16)->first();
        $dtoDivision = Division::where('name', 'Digital Transformation Office')->first();
        $marineDivision = Division::where('name', 'Marine Division')->first();
        $corporateDivision = Division::where('name', 'Corporate Services')->first();

        $systemAdmin->update(['organisation_id' => $organisation->id]);
        $systemAdmin->update(['division_id' => $dtoDivision->id]);
        $systemAdmin->save();

        if (!$organisation) {
            $this->command->error('MICT organisation not found. Please run organisation Seeder first.');
            return;
        }

        // Now update the admin user with the organisation_id
        $adminUser->organisation_id = $organisation->id;
        $adminUser->save();

        $user1 = User::withTrashed()->updateOrCreate(
            ['email' => 'benny@mict.gov.ki'],
            [
                'first_name' => 'Benny',
                'last_name' => 'Teuea',
                'password' => Hash::make('benny'),
                'designation' => 'Secretary',
                'organisation_id' => $organisation->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user1->assignRole('user');
        // $user1->assignRole('review-officer');
        // $user1->assignRole('organisation-admin');
        $organisation->save();

        $user2 = User::withTrashed()->updateOrCreate(
            ['email' => 'aoniba@mict.gov.ki'],
            [
                'first_name' => 'Aoniba',
                'last_name' => 'Riare',
                'password' => Hash::make('ds'),
                'designation' => 'Deputy Secretary',
                'organisation_id' => $organisation->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user2->assignRole('user');

        $user3 = User::withTrashed()->updateOrCreate(
            ['email' => 'betty@mict.gov.ki'],
            [
                'first_name' => 'Betty',
                'last_name' => 'Mapuola',
                'password' => Hash::make('sas'),
                'designation' => 'Senior Administrative Officer',
                'organisation_id' => $organisation->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user3->assignRole('user');

        $user4 = User::withTrashed()->updateOrCreate(
            ['email' => 'omtest@mict.gov.ki'],
            [
                'first_name' => 'Ngaaia',
                'last_name' => 'Toabwa',
                'password' => Hash::make('om'),
                'designation' => 'Office Manager',
                'organisation_id' => $organisation->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user4->assignRole('registry');


        $user5 = User::withTrashed()->updateOrCreate(
            ['email' => 'wayne@mict.gov.ki'],
            [
                'first_name' => 'Wayne',
                'last_name' => 'Reiher',
                'password' => Hash::make('wayne'),
                'organisation_id' => $organisation->id,
                'division_id' => $dtoDivision->id,
                'designation' => 'Director of ICT',
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
                'designation' => 'ICT Officer',
                'deleted_at' => null
            ]
        );
        $user6->assignRole('user');


        $user7 = User::withTrashed()->updateOrCreate(
            ['email' => 'ngaaia@mict.gov.ki'],
            [
                'first_name' => 'Ngaaia',
                'last_name' => 'Toabwa',
                'password' => Hash::make('ngaaia'),
                'organisation_id' => $organisation->id,
                'division_id' => $dtoDivision->id,
                'designation' => 'IT Officer',
                'deleted_at' => null
            ]
        );
        $user7->assignRole('user');


        $user8 = User::withTrashed()->updateOrCreate(
            ['email' => 'terianna@mict.gov.ki'],
            [
                'first_name' => 'Terianna',
                'last_name' => 'Kourabi',
                'password' => Hash::make('terianna'),
                'organisation_id' => $organisation->id,
                'division_id' => $dtoDivision->id,
                'designation' => 'Cyber Security Officer',
                'deleted_at' => null
            ]
        );
        $user8->assignRole('user');


        $user9 = User::withTrashed()->updateOrCreate(
            ['email' => 'tangaua@mict.gov.ki'],
            [
                'first_name' => 'Tangaua',
                'last_name' => 'Tiaon',
                'password' => Hash::make('tangaua'),
                'designation' => 'Registry Officer',
                'organisation_id' => $organisation->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user9->assignRole('registry');
    }
}