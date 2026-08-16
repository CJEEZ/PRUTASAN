<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--secret=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user with secret key verification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the secret key from environment or option
        $secretKey = $this->option('secret') ?: env('ADMIN_SECRET_KEY', 'change-me-secret-key-123');

        // Ask for secret key
        $inputSecret = $this->secret('Enter the secret key to create admin account');

        if ($inputSecret !== $secretKey) {
            $this->error('❌ Invalid secret key!');
            return 1;
        }

        // Restrict admin creation to the canonical admin account only
        $name = $this->ask('Admin name', 'Admin');
        $email = 'admin@fruitexpress.com';

        $existingAdmin = User::where('role', 'admin')->where('email', '!=', $email)->first();
        if ($existingAdmin) {
            $this->error('❌ Only the canonical admin account is allowed. Other admin accounts are blocked.');
            return 1;
        }

        // Check if email already exists
        if (User::where('email', $email)->exists()) {
            $this->error("❌ Email already exists!");
            return 1;
        }

        $password = $this->secret('Admin password');
        $confirmPassword = $this->secret('Confirm password');

        if ($password !== $confirmPassword) {
            $this->error('❌ Passwords do not match!');
            return 1;
        }

        if (strlen($password) < 6) {
            $this->error('❌ Password must be at least 6 characters!');
            return 1;
        }

        // Create admin user
        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->info('✅ Admin account created successfully!');
        $this->info("📧 Email: {$admin->email}");
        $this->info("👤 Name: {$admin->name}");
        $this->info("🔐 Role: {$admin->role}");
        $this->info("\n✨ You can now login at: http://localhost:8000/admin/login");

        return 0;
    }
}
