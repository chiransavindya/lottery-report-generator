<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FinalUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'sadmin.dlb@lrms.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'admin.dlb@lrms.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Operator
        User::updateOrCreate(
            ['email' => 'ops.dlb@lrms.com'],
            [
                'name' => 'Operator User',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
