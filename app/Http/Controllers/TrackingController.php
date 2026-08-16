<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    /**
     * Show the tracking page for a specific order.
     */
    public function show(Order $order)
    {
        // Check if user owns this order or is an admin
        if (Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        if (Auth::user()->role !== 'admin' && ! $order->isTrackable()) {
            return redirect()->route('profile.show')->with('error', 'Tracking is only available after your order has shipped.');
        }

        // Get shipment data
        $shipment = $order->shipment;

        // Prepare tracking timeline
        $timeline = $this->getTrackingTimeline($order);

        // Prepare full address
        $fullAddress = trim(sprintf('%s, %s, %s, %s %s, Philippines',
            $order->street_address,
            $order->barangay,
            $order->city,
            $order->province,
            $order->postal_code
        ));

        // Check if we have location data for map
        $hasLocation = !empty($order->latitude) && !empty($order->longitude);
        $hasDriverLocation = !empty($order->driver_latitude) && !empty($order->driver_longitude);

        // Get status badge info
        $statusInfo = $this->getStatusInfo($order->status);

        return view('tracking.show', [
            'order' => $order,
            'shipment' => $shipment,
            'timeline' => $timeline,
            'fullAddress' => $fullAddress,
            'hasLocation' => $hasLocation,
            'hasDriverLocation' => $hasDriverLocation,
            'statusInfo' => $statusInfo,
        ]);
    }

    /**
     * Public tracking without login (using order number and postal code for verification)
     */
    public function publicTrack(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string',
            'postal_code' => 'required|string',
        ]);

        $order = Order::where('order_number', $validated['order_number'])
            ->where('postal_code', $validated['postal_code'])
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found. Please check your order number and postal code.');
        }

        return redirect()->route('tracking.show', $order);
    }

    /**
     * API endpoint for AJAX tracking updates
     */
    public function getTrackingData(Order $order)
    {
        // Check if user owns this order or is an admin
        if (Auth::check() && Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (Auth::check() && Auth::user()->role !== 'admin' && ! $order->isTrackable()) {
            return response()->json(['error' => 'Tracking is only available after your order has shipped.'], 403);
        }

        $shipment = $order->shipment;
        $timeline = $this->getTrackingTimeline($order);
        $statusInfo = $this->getStatusInfo($order->status);

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'status_info' => $statusInfo,
            'shipment' => $shipment ? [
                'tracking_number' => $shipment->tracking_number,
                'carrier' => $shipment->carrier,
                'status' => $shipment->status,
                'shipped_at' => $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d H:i') : null,
            ] : null,
            'timeline' => $timeline,
            'total' => $order->total,
            'items_count' => $order->items->count(),
            'driver_latitude' => $order->driver_latitude,
            'driver_longitude' => $order->driver_longitude,
            'customer_latitude' => $order->latitude,
            'customer_longitude' => $order->longitude,
        ]);
    }

    /**
     * Build tracking timeline based on order status
     */
    private function getTrackingTimeline(Order $order): array
    {
        $timeline = [];

        // Order placed
        $timeline[] = [
            'status' => 'placed',
            'label' => 'Order Placed',
            'description' => 'Your order has been confirmed',
            'timestamp' => $order->created_at->format('Y-m-d H:i'),
            'completed' => true,
            'icon' => 'check-circle',
        ];

        // Confirmed/Processing
        if (in_array($order->status, ['confirmed', 'processing', 'packed', 'shipped', 'in_transit', 'out_for_delivery', 'to_receive', 'delivered'])) {
            $timeline[] = [
                'status' => 'confirmed',
                'label' => 'Order Confirmed',
                'description' => 'Seller confirmed your order',
                'timestamp' => $order->updated_at->format('Y-m-d H:i'),
                'completed' => true,
                'icon' => 'check-circle',
            ];
        }

        // Packed
        if (in_array($order->status, ['packed', 'shipped', 'in_transit', 'out_for_delivery', 'to_receive', 'delivered'])) {
            $timeline[] = [
                'status' => 'packed',
                'label' => 'Packed',
                'description' => 'Your items are being packed',
                'timestamp' => $order->updated_at->format('Y-m-d H:i'),
                'completed' => true,
                'icon' => 'check-circle',
            ];
        }

        // Shipped/In Transit
        if ($order->shipment) {
            $shipment = $order->shipment;
            if (in_array($order->status, ['shipped', 'in_transit', 'out_for_delivery', 'to_receive', 'delivered'])) {
                $timeline[] = [
                    'status' => 'shipped',
                    'label' => 'Shipped',
                    'description' => 'Your package is on the way',
                    'timestamp' => $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d H:i') : $order->updated_at->format('Y-m-d H:i'),
                    'completed' => true,
                    'icon' => 'check-circle',
                    'carrier' => $shipment->carrier,
                    'tracking_number' => $shipment->tracking_number,
                ];
            }

            // In Transit / Out for Delivery
            if (in_array($order->status, ['in_transit', 'out_for_delivery', 'to_receive', 'delivered'])) {
                $timeline[] = [
                    'status' => 'in_transit',
                    'label' => 'In Transit',
                    'description' => 'Package is in transit to your location',
                    'timestamp' => null,
                    'completed' => in_array($order->status, ['out_for_delivery', 'to_receive', 'delivered']),
                    'icon' => 'truck',
                    'current_location' => $order->current_location,
                ];
            }

            // Out for Delivery / To Receive
            if (in_array($order->status, ['out_for_delivery', 'to_receive', 'delivered'])) {
                $timeline[] = [
                    'status' => 'out_for_delivery',
                    'label' => $order->status === 'to_receive' ? 'To Receive - Arriving Soon!' : 'Out for Delivery',
                    'description' => $order->status === 'to_receive' ? 'Your package is arriving soon - track live location' : 'Driver is on the way to deliver your package',
                    'timestamp' => null,
                    'completed' => $order->status === 'delivered',
                    'icon' => $order->status === 'to_receive' ? 'location-dot' : 'location-dot',
                ];
            }
        }

        // Delivered
        if ($order->status === 'delivered') {
            $timeline[] = [
                'status' => 'delivered',
                'label' => 'Delivered',
                'description' => 'Your package has been delivered',
                'timestamp' => $order->updated_at->format('Y-m-d H:i'),
                'completed' => true,
                'icon' => 'check-circle',
            ];
        }

        // Return requested
        if ($order->status === 'return_requested') {
            $timeline[] = [
                'status' => 'return_requested',
                'label' => 'Return Requested',
                'description' => 'You have requested a return',
                'timestamp' => $order->updated_at->format('Y-m-d H:i'),
                'completed' => false,
                'icon' => 'undo',
            ];
        }

        // Cancelled
        if ($order->status === 'cancelled') {
            $timeline[] = [
                'status' => 'cancelled',
                'label' => 'Order Cancelled',
                'description' => 'Your order has been cancelled',
                'timestamp' => $order->updated_at->format('Y-m-d H:i'),
                'completed' => true,
                'icon' => 'times-circle',
            ];
        }

        return $timeline;
    }

    /**
     * Get status info for display
     */
    private function getStatusInfo(string $status): array
    {
        $statusMap = [
            'pending' => [
                'label' => 'Pending',
                'color' => 'yellow',
                'icon' => 'hourglass-half',
                'description' => 'Waiting for seller confirmation',
            ],
            'confirmed' => [
                'label' => 'Confirmed',
                'color' => 'blue',
                'icon' => 'check',
                'description' => 'Order confirmed by seller',
            ],
            'processing' => [
                'label' => 'Processing',
                'color' => 'blue',
                'icon' => 'cogs',
                'description' => 'Preparing your order',
            ],
            'packed' => [
                'label' => 'Packed',
                'color' => 'blue',
                'icon' => 'box',
                'description' => 'Your items are packed and ready',
            ],
            'shipped' => [
                'label' => 'Shipped',
                'color' => 'indigo',
                'icon' => 'truck',
                'description' => 'Your package is on the way',
            ],
            'in_transit' => [
                'label' => 'In Transit',
                'color' => 'indigo',
                'icon' => 'truck',
                'description' => 'Package in transit',
            ],
            'out_for_delivery' => [
                'label' => 'Out for Delivery',
                'color' => 'indigo',
                'icon' => 'location-dot',
                'description' => 'Driver is heading to your location',
            ],
            'to_receive' => [
                'label' => 'To Receive',
                'color' => 'red',
                'icon' => 'truck',
                'description' => 'Arriving soon - Order is on the way to you',
            ],
            'delivered' => [
                'label' => 'Delivered',
                'color' => 'green',
                'icon' => 'check-circle',
                'description' => 'Order delivered',
            ],
            'return_requested' => [
                'label' => 'Return Requested',
                'color' => 'orange',
                'icon' => 'undo',
                'description' => 'Return request pending',
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'color' => 'red',
                'icon' => 'times-circle',
                'description' => 'Order cancelled',
            ],
        ];

        return $statusMap[$status] ?? [
            'label' => ucfirst(str_replace('_', ' ', $status)),
            'color' => 'gray',
            'icon' => 'question-circle',
            'description' => 'Unknown status',
        ];
    }
}
