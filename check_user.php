<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Checking User: clarencejohn@12 ===\n\n";

$user = \App\Models\User::where('email', 'clarencejohn@12')->first();

if ($user) {
    echo "✓ User found!\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: {$user->role}\n";
    echo "  Seller Status: " . ($user->seller_status ?? 'null') . "\n";
    
    // Test login
    $credentials = [
        'email' => 'clarencejohn@12',
        'password' => 'password123'
    ];
    
    $authenticated = \Illuminate\Support\Facades\Auth::attempt($credentials);
    echo "\n  Login Test with 'password123': " . ($authenticated ? "✓ WORKS" : "✗ FAILS") . "\n";
    
    if (!$authenticated) {
        // Try some other common passwords
        $testPasswords = ['12345678', 'seller123', 'test123', 'pass123'];
        foreach ($testPasswords as $pwd) {
            if (\Illuminate\Support\Facades\Hash::check($pwd, $user->password)) {
                echo "  Actual password appears to be: $pwd\n";
                break;
            }
        }
    }
} else {
    echo "✗ User 'clarencejohn@12' NOT found\n";
    echo "\nAvailable users:\n";
    $users = \App\Models\User::where('role', 'seller')->get();
    foreach ($users as $u) {
        echo "- {$u->email}\n";
    }
}

echo "\nDone!\n";
