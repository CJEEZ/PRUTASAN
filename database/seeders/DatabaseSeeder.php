<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

/**
 * Run the application's database seeders.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the custom seeders
        $this->call([
            ProductSeeder::class,
        ]);

        // Keep only the canonical admin account for admin access
        User::where('role', 'admin')
            ->where('email', '!=', 'admin@fruitexpress.com')
            ->delete();

        User::updateOrCreate(
            ['email' => 'admin@fruitexpress.com'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'password' => bcrypt('admin123456'),
                'email_verified_at' => now(),
            ]
        );
    }
}
