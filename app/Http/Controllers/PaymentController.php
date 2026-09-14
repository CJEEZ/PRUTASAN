<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Services\PayMongoService;

class PaymentController extends Controller
{
    public function payMongoSuccess(Request $request, $orderId, PayMongoService $payMongo)
    {
        $order = Order::findOrFail($orderId);

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $sessionId = $request->string('checkout_session_id')->toString();
        if (!$sessionId || $sessionId !== $order->paymongo_checkout_session_id) {
            return redirect()->route('profile.show')->with('error', 'The payment session could not be verified.');
        }

        try {
            $session = $payMongo->retrieveCheckoutSession($sessionId);
            if (!$payMongo->isCheckoutPaid($session)) {
                return redirect()->route('profile.show')->with('error', 'Payment has not been completed yet.');
            }

            $this->markOrderPaid($order);

            return redirect()->route('profile.show')->with('success', "Payment confirmed! Order #{$order->order_number} has been updated.");
        } catch (\Throwable $e) {
            Log::error('PayMongo success verification failed: ' . $e->getMessage());
            return redirect()->route('profile.show')->with('error', 'Payment verification is temporarily unavailable.');
        }
    }

    public function payMongoCancel($orderId)
    {
        $order = Order::findOrFail($orderId);
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return redirect()->route('profile.show')->with('error', "Payment for Order #{$order->order_number} was cancelled.");
    }

    public function payMongoWebhook(Request $request, PayMongoService $payMongo)
    {
        $payload = $request->getContent();
        $signature = $request->header('Paymongo-Signature');
        $webhookSecret = config('services.paymongo.webhook_secret');

        if (!$webhookSecret || !$this->validPayMongoSignature($payload, $signature, $webhookSecret)) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $event = json_decode($payload, true);
        $eventData = $event['data']['attributes']['data'] ?? [];
        $eventType = $event['data']['attributes']['type'] ?? '';
        $metadata = $eventData['attributes']['metadata'] ?? [];
        $order = !empty($metadata['order_id'])
            ? Order::find($metadata['order_id'])
            : Order::where('paymongo_checkout_session_id', $eventData['id'] ?? null)->first();

        if ($order && str_contains($eventType, 'paid')) {
            $this->markOrderPaid($order);
        }

        return response()->json(['received' => true]);
    }

    private function markOrderPaid(Order $order): void
    {
        if ($order->payment_status === 'paid') {
            return;
        }

        $order->update([
            'payment_status' => 'paid',
            'payment_confirmed_at' => now(),
            'status' => 'confirmed',
        ]);
    }

    private function validPayMongoSignature(string $payload, ?string $header, string $secret): bool
    {
        if (!$header) {
            return false;
        }

        $parts = collect(explode(',', $header))->mapWithKeys(function (string $part): array {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, null);
            return [$key => $value];
        });
        $timestamp = $parts->get('t');
        $signature = $parts->get('li') ?: $parts->get('te');

        return $timestamp && $signature && hash_equals($signature, hash_hmac('sha256', $timestamp . '.' . $payload, $secret));
    }

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
