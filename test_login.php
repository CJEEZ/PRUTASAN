<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Testing Login System ===\n\n";

// Check if users exist
$users = \App\Models\User::limit(5)->get();
echo "Total users in database: " . \App\Models\User::count() . "\n";
echo "Sample users:\n";
foreach ($users as $user) {
    echo "- {$user->email} (Role: {$user->role}, Seller Status: {$user->seller_status})\n";
}

echo "\n=== Test User for Login ===\n";

// Try to find the user from the screenshot
$testUser = \App\Models\User::where('email', 'clarencejohn@02@gmail.com')
    ->orWhere('email', 'clarencejohn@02')
    ->first();

if ($testUser) {
    echo "✓ Test user found: {$testUser->name} ({$testUser->email})\n";
    echo "  Role: {$testUser->role}\n";
    echo "  Seller Status: " . ($testUser->seller_status ?? 'null') . "\n";
    echo "  Password hash exists: " . (!empty($testUser->password) ? "YES" : "NO") . "\n";
} else {
    echo "✗ Test user not found. Creating test user...\n";
    try {
        $newUser = \App\Models\User::create([
            'name' => 'Clarence John',
            'email' => 'clarencejohn@02@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'seller',
            'seller_status' => null,
            'email_verified_at' => now(),
        ]);
        echo "✓ Test user created successfully\n";
        echo "  Email: {$newUser->email}\n";
        echo "  Password: password123\n";
    } catch (Exception $e) {
        echo "✗ Error creating user: {$e->getMessage()}\n";
    }
}

echo "\n=== Routes Check ===\n";
$routeCollection = \Route::getRoutes();
$routes = [
    'seller.register' => false,
    'seller.register.store' => false,
    'login' => false,
];

foreach ($routeCollection as $route) {
    if (isset($routes[$route->getName()])) {
        $routes[$route->getName()] = true;
    }
}

foreach ($routes as $name => $exists) {
    echo ($exists ? "✓" : "✗") . " Route: {$name}\n";
}

echo "\nDone!\n";
