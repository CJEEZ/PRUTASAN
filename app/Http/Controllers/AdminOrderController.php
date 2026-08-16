<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    /**
     * Apply the 'auth' and 'can:access-admin' middleware.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:access-admin']);
    }

    /**
     * Display a listing of the orders.
     */
    public function index()
    {
        $orders = Order::with(['user'])->latest()->paginate(25);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order details.
     */
    public function show(string $id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the specified order's status (e.g., to shipped or delivered).
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,shipped,delivered,cancelled,return_requested'],
        ]);

        $order = Order::findOrFail($id);
        $order->status = $validated['status'];
        $order->save();

        return redirect()->route('admin.orders.index')
                         ->with('status', "Order {$order->order_number} status updated to {$order->status}.");
    }
}
