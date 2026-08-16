<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select("SHOW TABLES LIKE 'sessions'");
if (count($tables) === 0) {
    echo "sessions table missing\n";
} else {
    echo "sessions table exists\n";
}

$users = DB::select("SELECT id, email, role FROM users WHERE role='admin'");
if (count($users) === 0) {
    echo "no admin user found\n";
} else {
    foreach ($users as $user) {
        echo "admin user: {$user->id}, {$user->email}, {$user->role}\n";
    }
}
