<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\National\Eregistry\Ministry;

class MictUserSeeder extends Seeder
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
                'name' => 'System',
               
                'password' => Hash::make('Admin@123'),
                // Don't set ministry_id yet
            ]
        );

        // Create the MICT ministry
        $ministry = Ministry::firstOrCreate(
            ['code' => 'MICT'],
            [
                'name' => 'Ministry of Communication and Transport',
                'description' => 'Responsible for communication and transportation.',
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

        // Assign admin role to the system admin
        $adminUser->assignRole('admin');

        // Users data
        $users = [
            ['John', '', 'Doe', 'password123', 'john.doe@mict.gov.ki'],
            ['Jane', '', 'Smith', 'securepassword!', 'jane.smith@mict.gov.ki'],
            ['Alice', '', 'Johnson', 'password321', 'alice.johnson@mict.gov.ki'],
            ['Bob', '', 'Brown', 'password!123', 'bob.brown@mict.gov.ki'],
            ['Charlie', '', 'Davis', 'charlie1234', 'charlie.davis@mict.gov.ki'],
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