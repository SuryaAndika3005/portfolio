<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin login for the /admin panel. Set ADMIN_EMAIL / ADMIN_PASSWORD
        // in your .env before running this, then re-seed to update them.
        // Plain password here on purpose — the User model's 'password' cast
        // is 'hashed', so it hashes automatically on save. Hashing it again
        // here would double-hash it and lock you out.
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => 'Admin',
                'password' => env('ADMIN_PASSWORD', 'password'),
            ]
        );

        $this->call([
            PortfolioSeeder::class,
            ExperienceSeeder::class,
        ]);
    }
}
