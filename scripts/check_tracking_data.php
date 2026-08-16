<?php
// Usage: php scripts/check_tracking_data.php <order_id>
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\TrackingController;
use App\Models\Order;
use Illuminate\Http\Request;

$orderId = $argv[1] ?? null;
if (! $orderId) {
    echo "Usage: php scripts/check_tracking_data.php <order_id>\n";
    exit(1);
}

$order = Order::find($orderId);
if (! $order) {
    echo "Order {$orderId} not found\n";
    exit(1);
}

$controller = new TrackingController();
$response = $controller->getTrackingData($order);

// $response may be a JsonResponse
if (method_exists($response, 'getContent')) {
    echo $response->getContent();
} else {
    echo json_encode($response);
}
