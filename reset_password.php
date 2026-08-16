<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'clarencejohn@02')->first();
if ($user) {
    $user->password = \Illuminate\Support\Facades\Hash::make('password123');
    $user->save();
    echo "✓ Password updated successfully\n";
    echo "Email: {$user->email}\n";
    echo "New Password: password123\n";
    echo "\nYou can now login with these credentials:\n";
    echo "- Email: clarencejohn@02\n";
    echo "- Password: password123\n";
} else {
    echo "✗ User not found\n";
}
