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
        $systemAdmin = Role::create(['name' => 'system-admin']);
        $admin = Role::create(['name' => 'admin']);
        $registry = Role::create(['name' => 'registry']);
        $user = Role::create(['name' => 'user']);


        Permission::create(['name' => 'view-admin-dashboard']);
        Permission::create(['name' => 'view-registry-dashboard']);
        Permission::create(['name' => 'view-user-dashboard']);
        Permission::create(['name' => 'dispatch-file']);
        Permission::create(['name' => 'create-file']);
        Permission::create(['name' => 'approve-dispatch']);
        Permission::create(['name' => 'assign-officer']);

        $admin->givePermissionTo(['view-admin-dashboard', 
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

    }
}
