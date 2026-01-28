<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Add new permissions
Permission::firstOrCreate(['name' => 'manage users']);
Permission::firstOrCreate(['name' => 'manage roles']);
Permission::firstOrCreate(['name' => 'send broadcast']);

// Give admin all new permissions
$admin = Role::findByName('admin');
$admin->givePermissionTo(['manage users', 'manage roles', 'send broadcast']);

echo "Permissions added successfully!\n";
