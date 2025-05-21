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
                'name' => 'Operations Admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin',
                'is_admin' => true
            ],
            // Original admin remains
            [
                'name' => 'test',
                'email' => 'test@gmail.com',
                'password' => 'test123',
                'is_admin' => false
            ]
        ];

        foreach ($admins as $admin) {
            User::create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'email_verified_at' => now(),
                'password' => Hash::make($admin['password']),
                'is_admin' => $admin['is_admin'],
            ]);
        }
    }
}