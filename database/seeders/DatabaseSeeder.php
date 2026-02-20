<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\BoardingDefaultsSeeder;
use Database\Seeders\MadrasaDemoSeeder;
use Database\Seeders\TransactionsTypeSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ Transaction types MUST seed (Phase 1)
        $this->call([
            TransactionsTypeSeeder::class,
            MadrasaDemoSeeder::class,
            BoardingDefaultsSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
