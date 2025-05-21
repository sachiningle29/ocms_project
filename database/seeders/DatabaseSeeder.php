<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call other seeders
        $this->call([
            AdminUserSeeder::class,
        ]);

        // Regular test user with complete fields
   /*     User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('user123'), 
            'is_admin' => false
        ]);
    */
    }
}