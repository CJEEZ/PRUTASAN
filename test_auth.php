<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Testing Authentication ===\n\n";

// Get the test user
$user = \App\Models\User::where('email', 'clarencejohn@02')->first();

if (!$user) {
    echo "Test user not found\n";
    exit(1);
}

echo "Test User: {$user->name} ({$user->email})\n";
echo "Role: {$user->role}\n";
echo "Password Hash Length: " . strlen($user->password) . "\n";

// Test authentication with common passwords
$testPasswords = [
    'password',
    'password123',
    '12345678',
    'seller123',
    'test123',
];

echo "\nTesting common passwords:\n";
$authenticated = false;
foreach ($testPasswords as $pwd) {
    if (\Illuminate\Support\Facades\Hash::check($pwd, $user->password)) {
        echo "✓ Password MATCH: {$pwd}\n";
        $authenticated = true;
        break;
    } else {
        echo "✗ Password fail: {$pwd}\n";
    }
}

if (!$authenticated) {
    echo "\n⚠️  No matching password found in common passwords.\n";
    echo "You may need to reset the password or use the correct password.\n";
    echo "To set password to 'password123', run:\n";
    echo "  UPDATE users SET password = '" . \Illuminate\Support\Facades\Hash::make('password123') . "' WHERE email = 'clarencejohn@02';\n";
} else {
    echo "\n✓ Authentication should work with the matched password!\n";
}

echo "\nDone!\n";
