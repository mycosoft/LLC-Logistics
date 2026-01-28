<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $staffRole = Role::create(['name' => 'staff']);

        // Create permissions
        $permissions = [
            'manage clients',
            'manage shipments',
            'manage status updates',
            'send notifications',
            'send bulk messages',
            'view reports',
            'manage users',
            'manage roles',
            'send broadcast',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Admin role with all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        // Create Staff role with limited permissions
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'manage shipments',
            'manage status updates',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Admin role: Full access');
        $this->command->info('Staff role: Limited access');
    }
}
