<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemadmin = Role::create(['name' => 'system-admin']);
        $ministryadmin = Role::create(['name' => 'ministry-admin']);
        $registry = Role::create(['name' => 'registry']);
        $sro = Role::create(['name' => 'sro']);
        $user = Role::create(['name' => 'user']);
        $review_officer = Role::create(['name' => 'review-officer']);

        Permission::create(['name' => 'view-admin-dashboard']);
        Permission::create(['name' => 'view-registry-dashboard']);
        Permission::create(['name' => 'view-user-dashboard']);
        Permission::create(['name' => 'dispatch-file']);
        Permission::create(['name' => 'create-file']);
        Permission::create(['name' => 'approve-dispatch']);
        Permission::create(['name' => 'assign-officer']);
        Permission::create(['name' => 'view-all-files']);
        Permission::create(['name' => 'view-assigned-files']);
        Permission::create(['name' => 'review-file']);

        $ministryadmin->givePermissionTo(['view-admin-dashboard', 
                                  'view-registry-dashboard', 
                                  'view-user-dashboard', 
                                  'approve-dispatch',
                                  'assign-officer']);

        $registry->givePermissionTo(['view-registry-dashboard', 
                                     'view-user-dashboard',
                                     'dispatch-file',
                                     'create-file']);

        $user->givePermissionTo(['view-user-dashboard',
                                 'create-file']);

        $review_officer->givePermissionTo(['review-file']);

    }
}
