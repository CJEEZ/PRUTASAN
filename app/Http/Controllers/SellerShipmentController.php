<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\TrackingService;
use App\Events\RiderLocationUpdated;

class SellerShipmentController extends Controller
{
    protected TrackingService $trackingService;

    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function store(Request $request, Order $order)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }

        // Ensure the order includes items from this seller
        if (! $order->hasSeller($user->id)) {
            abort(403);
        }

        $data = $request->validate([
            'tracking_number' => ['required','string','max:255'],
            'carrier' => ['nullable','string','max:255'],
        ]);

        $shipment = Shipment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'tracking_number' => $data['tracking_number'],
                'carrier' => $data['carrier'] ?? null,
                'status' => 'shipped',
                'shipped_at' => now(),
            ]
        );

        // Update order status using tracking service
        $this->trackingService->updateOrderStatus(
            $order,
            'shipped',
            null,
            'Order has been shipped. Tracking number: ' . $data['tracking_number']
        );

        return redirect()->back()->with('success', 'Shipment tracking saved.');
    }

    public function show(Request $request, Order $order)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }

        if (! $order->hasSeller($user->id)) {
            abort(403);
        }

        $shipment = $order->shipment;
        return view('seller.track', compact('order','shipment'));
    }

    /**
     * List shipments for the logged-in seller.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }
        $sellerId = $user->id;
        // Shipments for orders containing seller's products
        $shipments = Shipment::whereHas('order.items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->with(['order'])->orderBy('shipped_at', 'desc')->paginate(20);
        return view('seller.shipments', compact('shipments'));
    }

    /**
     * Update shipment status (mark as shipped/delivered).
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }
        $shipment = Shipment::with('order')->findOrFail($id);
        $sellerId = $user->id;
        $hasSeller = $shipment->order && $shipment->order->hasSeller($sellerId);
        if (! $hasSeller) {
            abort(403);
        }
        $data = $request->validate([
            'status' => ['required', 'string'],
        ]);
        $shipment->status = $data['status'];
        $shipment->save();
        return redirect()->route('seller.shipments')->with('success', 'Shipment updated.');
    }

    /**
     * Update tracking location (for drivers/delivery updates)
     */
    public function updateTrackingLocation(Request $request, Order $order)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }

        if (! $order->hasSeller($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'location' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'nullable|string',
        ]);

        // Update order with driver location
        $updates = [];
        if (isset($data['latitude']) && isset($data['longitude'])) {
            $updates['driver_latitude'] = $data['latitude'];
            $updates['driver_longitude'] = $data['longitude'];
        }
        if (isset($data['status'])) {
            $updates['status'] = $data['status'];
        }
        if ($updates) {
            $order->update($updates);
        }

        // Log tracking update
        $this->trackingService->logTrackingUpdate(
            $order,
            $data['status'] ?? $order->status,
            $data['location'] ?? null,
            'Location update from driver',
            $data['latitude'] ?? null,
            $data['longitude'] ?? null
        );

        // Broadcast the updated rider location immediately.
        Event::dispatch(new RiderLocationUpdated(
            $order,
            $data['latitude'] ?? $order->driver_latitude,
            $data['longitude'] ?? $order->driver_longitude,
            $data['status'] ?? $order->status,
            $data['location'] ?? null,
        ));

        return response()->json([
            'success' => true,
            'message' => 'Tracking location updated successfully',
        ]);
    }
}
