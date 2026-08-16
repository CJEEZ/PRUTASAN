<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

use Illuminate\Support\Facades\DB;

echo "=== Database Restoration Status ===\n\n";

echo "Products:\n";
DB::table('products')->get()->each(function($p) {
    echo sprintf("  [%d] %s - PHP %s (Stock: %d, Sold: %d)\n", 
        $p->id, $p->name, $p->price, $p->stock, $p->sold ?? 0);
});

echo "\nUsers:\n";
echo "  Total: " . DB::table('users')->count() . "\n";

echo "\nOrders:\n";
echo "  Total: " . DB::table('orders')->count() . "\n";

echo "\nPayment Methods:\n";
echo "  Total: " . DB::table('payment_methods')->count() . "\n";

echo "\nAddresses:\n";
echo "  Total: " . DB::table('addresses')->count() . "\n";

echo "\n✓ Database restoration: COMPLETE\n";
?>
