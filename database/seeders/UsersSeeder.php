<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@crm.com',
            'password' => Hash::make('password'),
            'role' => 'admin', // Legacy column
        ]);
        $admin->assignRole('admin');

        // Create Manager User
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@crm.com',
            'password' => Hash::make('password'),
            'role' => 'staff', // Legacy column
        ]);
        $manager->assignRole('manager');

        // Create Agent User
        $agent = User::create([
            'name' => 'Agent User',
            'email' => 'agent@crm.com',
            'password' => Hash::make('password'),
            'role' => 'staff', // Legacy column
        ]);
        $agent->assignRole('agent');
    }
}
