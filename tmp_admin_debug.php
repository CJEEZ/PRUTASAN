<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

$user = User::where('role', 'admin')->first();
if (! $user) {
    echo "no admin user\n";
    exit(1);
}

echo "email: {$user->email}\n";
echo "role: {$user->role}\n";
echo "password hash: {$user->password}\n";
echo "password valid: ";
echo Hash::check('password', $user->password) ? "yes\n" : "no\n";

echo "Auth attempt: ";
$attempt = Auth::attempt(['email' => $user->email, 'password' => 'password']);
echo $attempt ? "success\n" : "failed\n";

$authenticated = Auth::check() ? 'yes' : 'no';
echo "Auth check: {$authenticated}\n";
if (Auth::check()) {
    echo "Authenticated user role: " . Auth::user()->role . "\n";
}
