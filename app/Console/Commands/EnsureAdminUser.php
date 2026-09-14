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

        if (User::where('email', $email)->exists()) {
            $this->info('Canonical admin already exists.');
            return self::SUCCESS;
        }

        if (! is_string($password) || strlen($password) < 12) {
            $this->error('ADMIN_PASSWORD must be set and contain at least 12 characters.');
            return self::FAILURE;
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
