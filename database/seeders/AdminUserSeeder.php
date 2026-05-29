<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@llclogistics.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Create default staff user
        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@llclogistics.com',
            'password' => Hash::make('password'),
        ]);
        $staff->assignRole('staff');

        $this->command->info('Admin and Staff users created successfully!');
        $this->command->info('Admin: admin@llclogistics.com / password');
        $this->command->info('Staff: staff@llclogistics.com / password');
    }
}
