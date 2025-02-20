<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\National\Eregistry\Ministry;

class MofUserSeeder extends Seeder
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

        // Create the initial admin user for the Ministry of Finance
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@mof.gov.ki'],
            [
                'name' => 'Finance',
               
                'password' => Hash::make('Admin@123'),
                // Don't set ministry_id yet
            ]
        );

        // Create the Ministry of Finance
        $ministry = Ministry::firstOrCreate(
            ['code' => 'MOF'],
            [
                'name' => 'Ministry of Finance',
                'description' => 'Handles government finances and economic policies.',
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

        // Assign admin role to the Ministry of Finance admin
        $adminUser->assignRole('admin');

        // Users data
        $users = [
            ['David', '', 'Accountant', 'password123', 'david.accountant@mof.gov.ki'],
            ['Emma', '', 'Auditor', 'securepassword!', 'emma.auditor@mof.gov.ki'],
            ['Lucas', '', 'Economist', 'password321', 'lucas.economist@mof.gov.ki'],
            ['Mia', '', 'Treasurer', 'password!123', 'mia.treasurer@mof.gov.ki'],
            ['Noah', '', 'Analyst', 'noah1234', 'noah.analyst@mof.gov.ki'],
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
