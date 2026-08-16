<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'clarencejohn@12')->first();

if ($user) {
    // Set password to a known value
    $newPassword = 'password123';
    $user->password = \Illuminate\Support\Facades\Hash::make($newPassword);
    $user->save();
    
    echo "✓ Password reset successfully!\n";
    echo "Email: {$user->email}\n";
    echo "Password: {$newPassword}\n";
    
    // Verify it works
    $test = \Illuminate\Support\Facades\Auth::attempt([
        'email' => 'clarencejohn@12',
        'password' => $newPassword
    ]);
    
    echo "Login test: " . ($test ? "✓ WORKS" : "✗ FAILS") . "\n";
} else {
    echo "User not found\n";
}
