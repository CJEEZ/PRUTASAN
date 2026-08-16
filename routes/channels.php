<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes();

Broadcast::channel('orders.{order}', function ($user, Order $order) {
    return $user->id === $order->user_id || ($user->role ?? null) === 'admin';
});
