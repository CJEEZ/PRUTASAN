<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class EnsureAdminUser extends Command
{
    protected $signature = 'admin:ensure';

    protected $description = 'Create the canonical admin account when it is missing';

    public function handle(): int
    {
        $email = env('ADMIN_EMAIL', 'admin@fruitexpress.com');
        $password = env('ADMIN_PASSWORD');
        $admin = User::withTrashed()->where('email', $email)->first();

        if ($admin?->isAdmin()) {
            if ($admin->trashed()) {
                $admin->restore();
                $this->info('Canonical admin account restored.');
            } else {
                $this->info('Canonical admin already exists.');
            }

            return self::SUCCESS;
        }

        if ($admin) {
            $this->error("Cannot create the admin account because {$email} belongs to a non-admin user.");
            return self::FAILURE;
        }

        if (! is_string($password) || strlen($password) < 12) {
            $this->warn('ADMIN_PASSWORD is missing or too short; skipping admin creation. Configure it in the hosting environment to create the canonical admin.');
            return self::SUCCESS;
        }

        User::create([
            'name' => env('ADMIN_NAME', 'Admin User'),
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->info('Canonical admin account created.');
        return self::SUCCESS;
    }
}
