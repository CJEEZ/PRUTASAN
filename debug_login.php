<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Debugging New Seller Account ===\n\n";

// Check if the new user exists
$user = \App\Models\User::where('email', 'clarencejohn@01')->first();

if ($user) {
    echo "✓ User found: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: {$user->role}\n";
    echo "  Seller Status: " . ($user->seller_status ?? 'null') . "\n";
    echo "  Password Hash exists: " . (!empty($user->password) ? "YES (length: " . strlen($user->password) . ")" : "NO") . "\n";
    echo "\n--- Testing common passwords ---\n";
    
    // Test with common signup passwords
    $passwords = ['password123', 'Password123', 'test123', 'seller123', '12345678'];
    $found = false;
    
    foreach ($passwords as $pwd) {
        if (\Illuminate\Support\Facades\Hash::check($pwd, $user->password)) {
            echo "✓ Password match found: {$pwd}\n";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "✗ No password matches. User might have used a different password.\n";
        echo "\nTo debug, check the signup form - what password was entered?\n";
    }
} else {
    echo "✗ User 'clarencejohn@01' not found in database.\n";
    echo "The signup process may have failed silently.\n\n";
    
    // Show recent users
    echo "Recent seller users:\n";
    $recentSellers = \App\Models\User::where('role', 'seller')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($recentSellers as $u) {
        echo "- {$u->email} (Created: {$u->created_at})\n";
    }
}

echo "\nDone!\n";
