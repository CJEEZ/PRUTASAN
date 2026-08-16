<?php
// Usage: php scripts/render_tracking_view.php <order_id>
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use Illuminate\Support\Facades\View;

$orderId = $argv[1] ?? null;
if (! $orderId) {
    echo "Usage: php scripts/render_tracking_view.php <order_id>\n";
    exit(1);
}

$order = Order::find($orderId);
if (! $order) {
    echo "Order {$orderId} not found\n";
    exit(1);
}

// Prepare data similar to TrackingController::show
$shipment = $order->shipment;
$fullAddress = trim(sprintf('%s, %s, %s, %s %s, Philippines',
    $order->street_address,
    $order->barangay,
    $order->city,
    $order->province,
    $order->postal_code
));

$hasLocation = !empty($order->latitude) && !empty($order->longitude);
$hasDriverLocation = !empty($order->driver_latitude) && !empty($order->driver_longitude);

// Minimal statusInfo for rendering
$statusInfo = ['label' => ucfirst($order->status), 'color' => 'gray', 'icon' => 'info-circle', 'description' => ''];

echo View::make('tracking.show', [
    'order' => $order,
    'shipment' => $shipment,
    'timeline' => [],
    'fullAddress' => $fullAddress,
    'hasLocation' => $hasLocation,
    'hasDriverLocation' => $hasDriverLocation,
    'statusInfo' => $statusInfo,
])->render();
