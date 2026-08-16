<?php
require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

$user = User::factory()->create([
    'email' => 'probe@example.com',
    'password' => Hash::make('password123'),
    'role' => 'seller',
]);

$result = Auth::attempt(['email' => 'probe@example.com', 'password' => 'password123']);
var_dump('attempt', $result);
var_dump('check', Auth::check());
var_dump('id', Auth::id());
var_dump('user', Auth::user()?->email);
