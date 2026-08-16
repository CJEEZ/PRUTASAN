<?php
// Usage: php scripts/simulate_rider.php <order_id> <lat> <lng> [status] [note]
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orderId = $argv[1] ?? null;
$lat = $argv[2] ?? null;
$lng = $argv[3] ?? null;
$status = $argv[4] ?? null;
$note = $argv[5] ?? 'simulator';

if (!$orderId || !$lat || !$lng) {
    echo "Usage: php scripts/simulate_rider.php <order_id> <lat> <lng> [status] [note]\n";
    exit(1);
}

use App\Models\Order;
use App\Events\RiderLocationUpdated;

$order = Order::find($orderId);
if (!$order) {
    echo "Order {$orderId} not found\n";
    exit(1);
}

$order->driver_latitude = $lat;
$order->driver_longitude = $lng;
if ($status && strtoupper($status) !== 'KEEP') {
    $order->status = $status;
}
$order->save();

event(new RiderLocationUpdated($order, (float)$lat, (float)$lng, $status, $note));

echo "Dispatched RiderLocationUpdated for order {$orderId} ({$lat},{$lng})\n";
