<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin Pertamina',
            'email' => 'admin@pertamina.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '081234567890',
        ]);

        // Create Regular User
        User::create([
            'name' => 'User Operator',
            'email' => 'user@pertamina.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'status' => 'active',
            'phone' => '081234567891',
        ]);

        // Create Viewer
        User::create([
            'name' => 'Viewer Staff',
            'email' => 'viewer@pertamina.com',
            'password' => Hash::make('viewer123'),
            'role' => 'viewer',
            'status' => 'active',
            'phone' => '081234567892',
        ]);

        $this->command->info(' Default users created successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info(' ADMIN:');
        $this->command->info('   Email: admin@pertamina.com');
        $this->command->info('   Password: admin123');
        $this->command->info('');
        $this->command->info(' USER:');
        $this->command->info('   Email: user@pertamina.com');
        $this->command->info('   Password: user123');
        $this->command->info('');
        $this->command->info('👁️  VIEWER:');
        $this->command->info('   Email: viewer@pertamina.com');
        $this->command->info('   Password: viewer123');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}