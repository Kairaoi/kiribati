<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\National\Eregistry\Ministry;

class MoeUserSeeder extends Seeder
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

        // Create the initial admin user for the Ministry of Education
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@moe.gov.ki'],
            [
                'name' => 'Education',
                
                'password' => Hash::make('Admin@123'),
                // Don't set ministry_id yet
            ]
        );

        // Create the Ministry of Education
        $ministry = Ministry::firstOrCreate(
            ['code' => 'MOE'],
            [
                'name' => 'Ministry of Education',
                'description' => 'Responsible for educational development and policies.',
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

        // Assign admin role to the Ministry of Education admin
        $adminUser->assignRole('admin');

        // Users data
        $users = [
            ['Emma', '', 'Scholar', 'password123', 'emma.scholar@moe.gov.ki'],
            ['Noah', '', 'Tutor', 'securepassword!', 'noah.tutor@moe.gov.ki'],
            ['Ava', '', 'Learn', 'password321', 'ava.learn@moe.gov.ki'],
            ['Mason', '', 'Teach', 'password!123', 'mason.teach@moe.gov.ki'],
            ['Sophia', '', 'Study', 'sophia1234', 'sophia.study@moe.gov.ki'],
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
