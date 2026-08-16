<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'clarencejohn@02@gmail.com';
$password = 'password123';
$user = User::where('email', $email)->first();
if (! $user) {
    echo "USER_NOT_FOUND\n";
    exit(1);
}
$ok = Hash::check($password, $user->password);
echo "USER_FOUND: {$user->email}\n";
echo "PASSWORD_MATCH:" . ($ok ? 'yes' : 'no') . "\n";
echo "USER_ROLE:" . ($user->role ?? '(none)') . "\n";
