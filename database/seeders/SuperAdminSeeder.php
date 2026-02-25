<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@lrms.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@lrms.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super admin created successfully!');
        $this->command->info('Email: admin@lrms.com');
        $this->command->info('Password: password');
        $this->command->warn('Please change this password in production!');
    }
}
