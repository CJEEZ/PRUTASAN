<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Keep only the canonical admin account
User::where('role', 'admin')
    ->where('email', '!=', 'admin@fruitexpress.com')
    ->delete();

$admin = User::updateOrCreate(
    ['email' => 'admin@fruitexpress.com'],
    [
        'name' => 'Admin User',
        'email' => 'admin@fruitexpress.com',
        'password' => Hash::make('admin123456'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]
);

echo "✅ Canonical admin account created/updated successfully!\n";
echo "Email: {$admin->email}\n";
echo "Password: admin123456\n";
echo "Role: {$admin->role}\n";
