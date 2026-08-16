<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Debugging Login Issue ===\n\n";

// Check if the new user exists
$user = \App\Models\User::where('email', 'clarencejohn@01')->first();

if (!$user) {
    echo "❌ User 'clarencejohn@01' NOT found in database\n";
    echo "\nListing all users:\n";
    $allUsers = \App\Models\User::all();
    foreach ($allUsers as $u) {
        echo "- {$u->email} (Role: {$u->role})\n";
    }
    exit(1);
}

echo "✓ User found: {$user->name} ({$user->email})\n";
echo "  Role: {$user->role}\n";
echo "  Seller Status: " . ($user->seller_status ?? 'null') . "\n";
echo "  Password Hash Length: " . strlen($user->password) . "\n";

// Try to manually test login
echo "\n=== Testing Login Manually ===\n";
$testPassword = 'password123'; // Common test password

// First check if this was the password used during signup
echo "Checking password hash...\n";
if (\Illuminate\Support\Facades\Hash::check($testPassword, $user->password)) {
    echo "✓ Password matches: {$testPassword}\n";
} else {
    echo "❌ Password does NOT match: {$testPassword}\n";
    echo "\nThe issue is likely:\n";
    echo "1. The signup form didn't properly hash the password\n";
    echo "2. Or the password stored is incorrect\n";
    echo "\nLet me check the password field value...\n";
    echo "Password hash: " . $user->password . "\n";
}

// Check if the POST login endpoint is working
echo "\n=== Checking Login Route ===\n";
$routes = \Route::getRoutes();
$loginRouteFound = false;
foreach ($routes as $route) {
    if ($route->getName() === 'login' && $route->getMethod() === 'POST') {
        echo "✓ Login POST route found\n";
        echo "  URI: " . $route->uri() . "\n";
        echo "  Controller: " . $route->getActionName() . "\n";
        $loginRouteFound = true;
        break;
    }
}

if (!$loginRouteFound) {
    echo "❌ Login POST route NOT found\n";
}

echo "\nDone!\n";
