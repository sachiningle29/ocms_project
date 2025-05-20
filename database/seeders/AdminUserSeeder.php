<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => 'Super@dmin123',
                'is_admin' => false
            ],
            [
                'name' => 'System Admin',
                'email' => 'sysadmin@example.com',
                'password' => 'Sys@dmin456',
                'is_admin' => true
            ],
            [
                'name' => 'Operations Admin',
                'email' => 'opsadmin@example.com',
                'password' => '0ps@dmin789',
                'is_admin' => true
            ],
            // Original admin remains
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => 'admin123',
                'is_admin' => false
            ]
        ];

        foreach ($admins as $admin) {
            User::create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'email_verified_at' => now(),
                'password' => Hash::make($admin['password']),
                'is_admin' => true
            ]);
        }
    }
}