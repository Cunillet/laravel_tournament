<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ScoringSystem;
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
        // User::factory(10)->create();

        User::factory()->create([
            'nickname' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'nickname' => 'Admin',
            'email' => 'admin@admin.local',
            'password' => bcrypt('123Admin'),
            'role' => 0,
        ]);

        $this->call(ScoringSystemSeeder::class);
    }
}
