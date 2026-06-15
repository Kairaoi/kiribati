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
        $ministry = Ministry::where('code', 'MICT')->first();
        $dtoDivision = Division::where('name', 'Digital Transformation Office')->first();
        $marineDivision = Division::where('name', 'Marine Division')->first();
        $corporateDivision = Division::where('name', 'Corporate Services')->first();

        $systemAdmin->update(['ministry_id' => $ministry->id]);
        $systemAdmin->update(['division_id' => $dtoDivision->id]);
        $systemAdmin->save();

        if (!$ministry) {
            $this->command->error('MICT ministry not found. Please run Ministry Seeder first.');
            return;
        }

        // Now update the admin user with the ministry_id
        $adminUser->ministry_id = $ministry->id;
        $adminUser->save();

        $user1 = User::withTrashed()->updateOrCreate(
            ['email' => 'secretary@mict.gov.ki'],
            [
                'first_name' => 'Mitateti',
                'last_name' => 'Mote',
                'password' => Hash::make('secretary'),
                'designation' => 'Secretary',
                'ministry_id' => $ministry->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user1->assignRole('sro');
        // $user1->assignRole('review-officer');
        // $user1->assignRole('ministry-admin');
        $ministry->save();

        $user2 = User::withTrashed()->updateOrCreate(
            ['email' => 'ds@mict.gov.ki'],
            [
                'first_name' => 'Aoniba',
                'last_name' => 'Riare',
                'password' => Hash::make('ds'),
                'designation' => 'Deputy Secretary',
                'ministry_id' => $ministry->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user2->assignRole('ministry-admin');

        $user3 = User::withTrashed()->updateOrCreate(
            ['email' => 'sas@mict.gov.ki'],
            [
                'first_name' => 'Betty',
                'last_name' => 'Mapuola',
                'password' => Hash::make('sas'),
                'designation' => 'Senior Administrative Officer',
                'ministry_id' => $ministry->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user3->assignRole('ministry-admin');

        $user4 = User::withTrashed()->updateOrCreate(
            ['email' => 'om@mict.gov.ki'],
            [
                'first_name' => 'Ngaaia',
                'last_name' => 'Toabwa',
                'password' => Hash::make('om'),
                'designation' => 'Office Manager',
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
                'designation' => 'ICT Officer',
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
                'designation' => 'Marine Officer',
                'deleted_at' => null
            ]
        );
        $user6->assignRole('user');


        $user7 = User::withTrashed()->updateOrCreate(
            ['email' => 'kairaoi@mict.gov.ki'],
            [
                'first_name' => 'Kairaoi',
                'last_name' => 'Ientumoa',
                'password' => Hash::make('kairaoi'),
                'ministry_id' => $ministry->id,
                'division_id' => $dtoDivision->id,
                'designation' => 'Marine Officer',
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
                'ministry_id' => $ministry->id,
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
                'ministry_id' => $ministry->id,
                'division_id' => $corporateDivision->id,
                'deleted_at' => null
            ]
        );
        $user9->assignRole('registry');
    }
}