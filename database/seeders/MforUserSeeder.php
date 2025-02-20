<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\National\Eregistry\Ministry;

class MforUserSeeder extends Seeder
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

        // Create the initial admin user for the Ministry of Fisheries and Ocean Resources
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@mfor.gov.ki'],
            [
                'name' => 'Fisheries',
               
                'password' => Hash::make('Admin@123'),
                // Don't set ministry_id yet
            ]
        );

        // Create the Ministry of Fisheries and Ocean Resources
        $ministry = Ministry::firstOrCreate(
            ['code' => 'MFOR'],
            [
                'name' => 'Ministry of Fisheries and Ocean Resources',
                'description' => 'Responsible for the management of fisheries and ocean resources.',
                'is_active' => true,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Now update the admin user with the ministry_id
        $adminUser->ministry_id = $ministry->id;
        $adminUser->save();

        // Assign admin role to the Fisheries and Ocean Resources admin
        $adminUser->assignRole('admin');

        // Users data
        $users = [
            ['Daniel', '', 'Fisher', 'password123', 'daniel.fisher@mfor.gov.ki'],
            ['Olivia', '', 'Waters', 'securepassword!', 'olivia.waters@mfor.gov.ki'],
            ['Ethan', '', 'Marine', 'password321', 'ethan.marine@mfor.gov.ki'],
            ['Sophia', '', 'Coral', 'password!123', 'sophia.coral@mfor.gov.ki'],
            ['Liam', '', 'Reef', 'liam1234', 'liam.reef@mfor.gov.ki'],
        ];

        // Create/update users
        foreach ($users as $user) {
            $userObj = User::withTrashed()->updateOrCreate(
                ['email' => $user[4]],
                [
                    'name' => $user[0],
                
                    'password' => Hash::make($user[3]),
                    'ministry_id' => $ministry->id,
                    'deleted_at' => null
                ]
            );

            // Assign roles to users
            foreach ($roles as $roleName) {
                if (!$userObj->hasRole($roleName)) {
                    $this->command->info("    Assigning role {$roleName} to user {$userObj->id}");
                    $userObj->assignRole($roleName);
                }
            }
        }
    }
}
