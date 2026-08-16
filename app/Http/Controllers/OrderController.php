<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * Cancel an order (only if it's still pending).
     */
    public function cancel(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access to this order.');
        }

        // Only allow cancellation if order is pending
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot cancel an order that is not pending.');
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                // Restore stock for each item (if stock is tracked)
                foreach ($order->items as $item) {
                    $p = \App\Models\Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if ($p && $p->stock !== null) {
                        $p->stock = $p->stock + $item->quantity;
                        $p->save();
                    }
                }

                $order->update(['status' => 'cancelled']);
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Unable to cancel order: ' . $e->getMessage());
        }

        return redirect()->route('profile.show')->with('success', 'Order ' . $order->order_number . ' has been cancelled and stock restored.');
    }

    /**
     * Return order details as JSON for modal (shipping, items, tracking, map).
     */
    public function ajaxDetails(Order $order)
    {
        // Only allow if user owns the order
        if ($order->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $shipment = $order->shipment;

        $latitude = $order->latitude;
        $longitude = $order->longitude;

        $driverLatitude = $order->driver_latitude;
        $driverLongitude = $order->driver_longitude;

        $fullAddress = trim(sprintf('%s, %s, %s, %s %s, Philippines',
            $order->street_address,
            $order->barangay,
            $order->city,
            $order->province,
            $order->postal_code
        ));

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'street_address' => $order->street_address,
            'barangay' => $order->barangay,
            'city' => $order->city,
            'province' => $order->province,
            'postal_code' => $order->postal_code,
            'order_address' => $fullAddress,
            'phone' => $order->phone,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'total' => $order->total,
            'items' => $order->items->map(function ($item) {
                return [
                    'product_name' => $item->product->name ?? 'Unknown',
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ];
            }),
            'shipment' => $shipment ? [
                'tracking_number' => $shipment->tracking_number,
                'carrier' => $shipment->carrier,
                'status' => $shipment->status,
                'shipped_at' => $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d') : null,
            ] : null,
            'driver_latitude' => $driverLatitude,
            'driver_longitude' => $driverLongitude,
        ]);
    }

    public function buyAgain(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access to this order.');
        }

        $itemsAdded = 0;
        foreach ($order->items as $item) {
            if (!$item->product) {
                continue;
            }

            $quantity = $item->quantity;
            $product = $item->product;
            $currentQty = $this->cartService->getCart()->get($product->id, 0);
            $nextQty = $currentQty + $quantity;

            if ($product->stock !== null && $nextQty > $product->stock) {
                $quantity = max(0, $product->stock - $currentQty);
            }

            if ($quantity > 0) {
                $this->cartService->add($product->id, $quantity);
                $itemsAdded += $quantity;
            }
        }

        if ($itemsAdded === 0) {
            return redirect()->route('cart.show')->with('error', 'Unable to add items from this order to the cart. They may be out of stock.');
        }

        return redirect()->route('cart.show')->with('success', 'Order items added to cart successfully.');
    }

    public function requestReturn(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access to this order.');
        }

        if ($order->status !== 'delivered') {
            return redirect()->back()->with('error', 'Return requests are only available for delivered orders.');
        }

        $order->status = 'return_requested';
        $order->save();

        // Update shipment status if exists
        if ($order->shipment) {
            $order->shipment->update(['status' => 'returning the products']);
        }

        // Create notification for the user
        \App\Models\Notification::create([
            'user_id' => $order->user_id,
            'type' => 'return_request',
            'title' => 'Return Request Submitted',
            'message' => 'Your return request for order ' . $order->order_number . ' has been submitted. We will process it shortly.',
            'order_id' => $order->id,
        ]);

        return redirect()->route('profile.show')->with('success', 'Return request submitted. We will process it shortly.');
    }
}
