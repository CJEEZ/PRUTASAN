<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Login Route Configuration ===\n\n";

// Get all routes
$routes = \Route::getRoutes();

echo "Checking all routes matching 'login':\n";
$found = false;
foreach ($routes as $route) {
    $name = $route->getName() ?? 'no-name';
    $uri = $route->uri();
    
    if (strpos($uri, 'login') !== false || strpos($name, 'login') !== false) {
        echo "✓ Route: $name\n";
        echo "  URI: $uri\n";
        echo "  Methods: " . implode(', ', $route->methods) . "\n";
        $found = true;
    }
}

if (!$found) {
    echo "❌ No login routes found!\n";
}

echo "\n=== Checking Middleware on Login Route ===\n";

// Check if guest middleware is applied to POST login
$postLoginRoute = null;
foreach ($routes as $route) {
    if ($route->uri() === 'login' && in_array('POST', $route->methods)) {
        $postLoginRoute = $route;
        break;
    }
}

if ($postLoginRoute) {
    echo "✓ POST /login route found\n";
    echo "Middleware: " . implode(', ', $postLoginRoute->middleware() ?? []) . "\n";
} else {
    echo "❌ POST /login route NOT found\n";
}

// Test user authentication manually
echo "\n=== Manual Authentication Test ===\n";
$user = \App\Models\User::where('email', 'clarencejohn@01')->first();

if ($user) {
    // Simulate what Auth::attempt() does
    $credentials = [
        'email' => 'clarencejohn@01',
        'password' => 'password123'
    ];
    
    $authenticated = \Illuminate\Support\Facades\Auth::attempt($credentials);
    
    if ($authenticated) {
        echo "✓ Authentication successful!\n";
        $authUser = \Illuminate\Support\Facades\Auth::user();
        echo "  Logged in as: {$authUser->name} ({$authUser->email})\n";
    } else {
        echo "❌ Authentication failed!\n";
        echo "  Email: {$credentials['email']}\n";
        echo "  Password: {$credentials['password']}\n";
        echo "\n  User exists: YES\n";
        echo "  Password hash check: " . (\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password) ? 'PASS' : 'FAIL') . "\n";
    }
} else {
    echo "❌ User not found\n";
}

echo "\nDone!\n";
