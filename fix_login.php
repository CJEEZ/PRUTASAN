<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Fixing New Seller Login Issue ===\n\n";

$user = \App\Models\User::where('email', 'clarencejohn@01')->first();

if ($user) {
    // Reset password to known value
    $newPassword = 'password123';
    $user->password = \Illuminate\Support\Facades\Hash::make($newPassword);
    $user->save();
    
    echo "✓ Password reset for {$user->email}\n";
    echo "  New password: {$newPassword}\n";
    echo "\nTry logging in with:\n";
    echo "  Email: clarencejohn@01\n";
    echo "  Password: password123\n";
} else {
    echo "✗ User not found\n";
}
