<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\TrackingService;

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
                'status' => 'ready_for_pickup',
                'shipped_at' => now(),
            ]
        );

        $order->update(['status' => 'ready_for_pickup']);

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

}
