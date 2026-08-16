<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;

$sellers = User::where('role', 'seller')->get(['id', 'name', 'email', 'email_verified_at']);

if ($sellers->isEmpty()) {
    echo "No sellers found.\n";
} else {
    echo "ID | Name | Email | Status\n";
    echo str_repeat("-", 80) . "\n";
    foreach ($sellers as $seller) {
        $status = $seller->email_verified_at ? 'APPROVED' : 'PENDING';
        printf("%d | %s | %s | %s\n", $seller->id, $seller->name, $seller->email, $status);
    }
}
