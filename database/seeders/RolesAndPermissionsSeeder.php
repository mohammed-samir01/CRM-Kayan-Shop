<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'view dashboard',
            'view leads',
            'create leads',
            'edit leads',
            'delete leads',
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view campaigns',
            'create campaigns',
            'edit campaigns',
            'delete campaigns',
            'view reports',
            'manage users',
            'view permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // create roles and assign created permissions

        // Sales Agent
        $role = Role::create(['name' => 'agent']);
        $role->givePermissionTo([
            'view dashboard',
            'view leads',
            'create leads',
            'edit leads',
            'view orders',
            'create orders',
            'view products',
            'view campaigns'
        ]);

        // Manager
        $role = Role::create(['name' => 'manager']);
        $role->givePermissionTo([
            'view dashboard',
            'view leads',
            'create leads',
            'edit leads',
            'view orders',
            'create orders',
            'edit orders',
            'view products',
            'create products',
            'edit products',
            'view campaigns',
            'create campaigns',
            'edit campaigns',
            'view reports'
        ]);

        // Admin
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
    }
}
