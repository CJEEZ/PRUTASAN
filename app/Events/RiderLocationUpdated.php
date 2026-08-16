<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class RiderLocationUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public Order $order;
    public ?float $driver_latitude;
    public ?float $driver_longitude;
    public string $status;
    public ?string $location;

    public function __construct(Order $order, ?float $driver_latitude, ?float $driver_longitude, string $status, ?string $location = null)
    {
        $this->order = $order;
        $this->driver_latitude = $driver_latitude;
        $this->driver_longitude = $driver_longitude;
        $this->status = $status;
        $this->location = $location;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('orders.'.$this->order->id);
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'driver_latitude' => $this->driver_latitude,
            'driver_longitude' => $this->driver_longitude,
            'status' => $this->status,
            'location' => $this->location,
        ];
    }
}
