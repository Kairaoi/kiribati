<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use Spatie\Permission\Contracts\Permission;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            "role-list", "role-create", "role-edit", "role-delete",
            "user-list", "user-create", "user-edit", "user-delete",
            "outwardFile-list", "outwardFile-create", "outwardFile-edit", "outwardFile-delete",
            "outwardFile-approve", "outwardFile-reject", "outwardFile-send",
            "inwardFile-list", "inwardFile-delete", "inwardFile-asign-officer", "inwardFile-asign-fileIndex"
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
