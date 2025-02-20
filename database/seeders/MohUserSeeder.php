<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\National\Eregistry\Ministry;

class MohUserSeeder extends Seeder
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

        // Create the initial admin user for Ministry of Health
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@moh.gov.ki'],
            [
                'name' => 'Health',
            
                'password' => Hash::make('Admin@123'),
                // Don't set ministry_id yet
            ]
        );

        // Create the Ministry of Health
        $ministry = Ministry::firstOrCreate(
            ['code' => 'MOH'],
            [
                'name' => 'Ministry of Health',
                'description' => 'Responsible for public health policies and services.',
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

        // Assign admin role to the Ministry of Health admin
        $adminUser->assignRole('admin');

        // Users data
        $users = [
            ['Sarah', '', 'Connor', 'password123', 'sarah.connor@moh.gov.ki'],
            ['Michael', '', 'Smith', 'securepassword!', 'michael.smith@moh.gov.ki'],
            ['Laura', '', 'Williams', 'password321', 'laura.williams@moh.gov.ki'],
            ['David', '', 'Johnson', 'password!123', 'david.johnson@moh.gov.ki'],
            ['Emma', '', 'Brown', 'emma1234', 'emma.brown@moh.gov.ki'],
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
