<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\TrackingHistory;
use Carbon\Carbon;

class TrackingService
{
    /**
     * Log a tracking update for an order/shipment.
     */
    public function logTrackingUpdate(
        Order $order,
        string $status,
        string $location = null,
        string $description = null,
        ?float $latitude = null,
        ?float $longitude = null
    ): TrackingHistory {
        $shipment = $order->shipment;

        return TrackingHistory::create([
            'order_id' => $order->id,
            'shipment_id' => $shipment?->id,
            'status' => $status,
            'location' => $location,
            'description' => $description,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'timestamp' => now(),
        ]);
    }

    /**
     * Update order status and log tracking event.
     */
    public function updateOrderStatus(
        Order $order,
        string $newStatus,
        string $location = null,
        string $description = null,
        ?float $latitude = null,
        ?float $longitude = null
    ): void {
        // Update order status
        $order->update(['status' => $newStatus]);

        // Update current location if provided
        if ($location) {
            $order->update(['current_location' => $location]);
        }

        // Update driver location if provided
        if ($latitude && $longitude) {
            $order->update([
                'driver_latitude' => $latitude,
                'driver_longitude' => $longitude,
            ]);
        }

        // Log tracking history
        $this->logTrackingUpdate($order, $newStatus, $location, $description, $latitude, $longitude);
    }

    /**
     * Get tracking timeline for an order.
     */
    public function getTrackingTimeline(Order $order): array
    {
        $histories = $order->trackingHistory()->get();
        $timeline = [];

        foreach ($histories as $history) {
            $timeline[] = [
                'status' => $history->status,
                'location' => $history->location,
                'description' => $history->description,
                'timestamp' => $history->timestamp->format('Y-m-d H:i'),
                'latitude' => $history->latitude,
                'longitude' => $history->longitude,
            ];
        }

        return $timeline;
    }

    /**
     * Check if an order is still in transit.
     */
    public function isInTransit(Order $order): bool
    {
        return in_array($order->status, [
            'shipped',
            'in_transit',
            'out_for_delivery',
        ]);
    }

    /**
     * Get estimated delivery based on current status and historical data.
     */
    public function getEstimatedDelivery(Order $order): ?Carbon
    {
        if ($order->status === 'delivered') {
            return null; // Already delivered
        }

        // Simple estimation based on status
        // This can be enhanced with actual data from courier API
        $now = now();

        return match ($order->status) {
            'pending' => $now->addDays(1),
            'confirmed' => $now->addDays(1),
            'processing' => $now->addDays(2),
            'packed' => $now->addDays(2),
            'shipped' => $now->addDays(3),
            'in_transit' => $now->addDays(2),
            'out_for_delivery' => $now->addHours(4),
            default => null,
        };
    }

    /**
     * Get tracking statistics for dashboard.
     */
    public function getTrackingStats(): array
    {
        $totalOrders = Order::count();
        $inTransit = Order::whereIn('status', ['shipped', 'in_transit', 'out_for_delivery'])->count();
        $delivered = Order::where('status', 'delivered')->count();
        $pending = Order::where('status', 'pending')->count();

        return [
            'total' => $totalOrders,
            'in_transit' => $inTransit,
            'delivered' => $delivered,
            'pending' => $pending,
        ];
    }
}
