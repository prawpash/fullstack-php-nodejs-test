<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Fixed accounts for each role, used during development.
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        User::factory()->manager()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        // Random users for development.
        User::factory()->manager()->count(2)->create();
        User::factory()->count(2)->create();
    }
}
