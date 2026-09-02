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
        $hod = Role::create(['name' => 'hod']);
        $user = Role::create(['name' => 'user']);
        $review_officer = Role::create(['name' => 'review-officer']);

        $systemAdminPermissions = [
            // Ministries
            'ministries.view-any',
            'ministries.create',
            'ministries.update',
            'ministries.activate',
            'ministries.deactivate',

            // Ministry administrators
            'ministry-admins.view-any',
            'ministry-admins.create',
            'ministry-admins.update',
            'ministry-admins.activate',
            'ministry-admins.deactivate',
            'ministry-admins.reset-password',

            // Users
            'users.view-any',
            'users.view',
            'users.update',
            'users.activate',
            'users.deactivate',
            'users.reset-password',
            'users.transfer-ministry',

            // Roles
            'roles.view-any',
            'roles.create',
            'roles.update',
            'roles.assign',
            'permissions.view-any',

            // Organisation structure
            'divisions.view-any',
            'divisions.create',
            'divisions.update',
            'divisions.activate',
            'units.view-any',
            'units.create',
            'units.update',
            'units.activate',

            // Organisations
            'organisations.view-any',
            'organisations.create',
            'organisations.update',
            'organisation-types.manage',

            // Global configuration
            'system-file-types.manage',
            'system-categories.manage',
            'system-templates.manage',
            'system-settings.manage',

            // Governance and monitoring
            'audit-logs.view-any',
            'system-reports.view',
            'system-health.view',
            'failed-jobs.manage',
            'user-sessions.revoke',
        ];

        
        $operationalPermissions = [
            'files.create',
            'files.review',
            'files.approve',
            'files.sign',
            'files.dispatch',
            'files.receive',
            'files.assign',
            'files.close',
        ];

      

    }
}
