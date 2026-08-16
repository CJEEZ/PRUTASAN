<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::find(1);
$user->password = \Illuminate\Support\Facades\Hash::make('password123');
$user->save();
echo "User 1 password updated\n";
