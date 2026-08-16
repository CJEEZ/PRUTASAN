<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class PaymentController extends Controller
{
    /**
     * Display GCash payment page for the given order
     */
    public function showGCashPayment($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verify order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // If payment is already confirmed, redirect to success
        if ($order->payment_status === 'paid') {
            return redirect()->route('profile.show')->with('success', 'Order already paid!');
        }

        return view('payment.gcash', compact('order'));
    }

    /**
     * Process GCash payment confirmation
     */
    public function confirmGCashPayment(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verify order belongs to authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Validate the request
        $validated = $request->validate([
            'gcash_reference' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        // Verify amount matches order total
        if ((float)$validated['amount'] != (float)$order->total) {
            return redirect()->back()->with('error', 'Payment amount does not match order total!');
        }

        try {
            // In a real implementation, verify payment with GCash API
            // For now, we'll mark it as paid
            
            $order->update([
                'payment_status' => 'paid',
                'gcash_reference' => $validated['gcash_reference'],
                'payment_confirmed_at' => now(),
                'status' => 'confirmed', // Change order status to confirmed
            ]);

            Log::info("GCash Payment Confirmed: Order #{$order->order_number}, Amount: ₱{$order->total}, Reference: {$validated['gcash_reference']}");

            return redirect()->route('profile.show')->with('success', "Payment confirmed! Order #{$order->order_number} has been updated.");
        } catch (\Throwable $e) {
            Log::error('GCash payment confirmation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment confirmation failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle GCash payment failure/cancellation
     */
    public function cancelGCashPayment($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Mark as failed
        $order->update(['payment_status' => 'failed']);

        Log::info("GCash Payment Cancelled: Order #{$order->order_number}");

        return redirect()->route('profile.show')->with('error', "Payment for Order #{$order->order_number} was cancelled. Please try again or use another payment method.");
    }

    /**
     * Webhook endpoint for GCash payment callback (for real integration)
     */
    public function gcashWebhook(Request $request)
    {
        // In a real implementation, verify the webhook signature
        Log::info('GCash Webhook received:', $request->all());

        // This would be called by GCash to notify about payment status
        // For now, it's just logged
        
        return response()->json(['status' => 'received']);
    }
}
