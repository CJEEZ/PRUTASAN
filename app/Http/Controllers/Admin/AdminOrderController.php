<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user']);

        // Search by id, order_number, user name or email
        if ($q = $request->input('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('id', $q)
                  ->orWhere('order_number', 'like', "%{$q}%")
                  ->orWhereHas('user', function ($u) use ($q) {
                      $u->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($payment = $request->input('payment_status')) {
            $query->where('payment_status', $payment);
        }

        $query->orderBy('created_at', 'desc');

        // Export handling (GET with export=1 and optional selected[])
        if ($request->boolean('export')) {
            $selected = $request->input('selected', []);

            $orders = count($selected) ? $query->whereIn('id', $selected)->get() : $query->get();

            $filename = 'orders_export_' . now()->format('Ymd_His') . '.csv';

            $response = new StreamedResponse(function () use ($orders) {
                $handle = fopen('php://output', 'w');
                // Header row
                fputcsv($handle, ['ID', 'Order Number', 'Customer', 'Email', 'Total', 'Status', 'Payment Status', 'Placed At']);

                foreach ($orders as $order) {
                    fputcsv($handle, [
                        $order->id,
                        $order->order_number,
                        optional($order->user)->name ?? $order->full_name,
                        optional($order->user)->email ?? '',
                        number_format($order->total ?? ($order->total_amount ?? 0), 2),
                        $order->status,
                        $order->payment_status ?? 'pending',
                        $order->created_at->toDateTimeString(),
                    ]);
                }

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

            return $response;
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(int $id): View
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, int $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready_for_pickup,confirmed,processing,packed,shipped,in_transit,out_for_delivery,to_receive,delivered,cancelled,return_requested',
            'payment_status' => 'nullable|in:pending,paid,failed',
            'gcash_reference' => 'nullable|string|max:100',
        ]);

        // Update order status
        $order->status = $validated['status'];

        // Update payment status if provided
        if (isset($validated['payment_status'])) {
            $order->payment_status = $validated['payment_status'];

            // Set payment confirmed timestamp if marked as paid
            if ($validated['payment_status'] === 'paid' && !$order->payment_confirmed_at) {
                $order->payment_confirmed_at = now();
            }
        }

        // Update GCash reference if provided
        if (isset($validated['gcash_reference'])) {
            $order->gcash_reference = $validated['gcash_reference'];
        }

        $order->save();

        if (in_array($validated['status'], ['ready_for_pickup', 'shipped', 'in_transit', 'out_for_delivery', 'to_receive', 'delivered'], true)) {
            $shipment = $order->shipment ?: new Shipment(['order_id' => $order->id]);
            $shipment->tracking_number = $shipment->tracking_number ?: 'F2W-' . $order->order_number;
            $shipment->status = $validated['status'] === 'to_receive' ? 'out_for_delivery' : $validated['status'];
            $shipment->shipped_at = $shipment->shipped_at ?: now();
            $shipment->save();
        }

        if ($order->user_id) {
            Notification::create([
                'user_id' => $order->user_id,
                'type' => 'order_update',
                'title' => 'Order status updated',
                'message' => 'Your order ' . $order->order_number . ' is now ' . str_replace('_', ' ', $validated['status']) . '.',
                'order_id' => $order->id,
            ]);
        }

        return redirect()->back()->with('success', 'Order updated successfully.');
    }
}
