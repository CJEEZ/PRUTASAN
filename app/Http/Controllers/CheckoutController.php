<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;

class CheckoutController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the checkout page with cart details.
     */
    public function show()
    {
        $user = Auth::user();

        $directBuy = Session::get('direct_buy_checkout');
        $directBuyProductId = Session::get('direct_buy_product_id');
        $directBuyQuantity = (int) Session::get('direct_buy_quantity', 1);

        if ($directBuy && ($directBuy['product_id'] ?? $directBuyProductId)) {
            $productId = $directBuy['product_id'] ?? $directBuyProductId;
            $quantity = (int) ($directBuy['quantity'] ?? $directBuyQuantity);
            $product = Product::find($productId);

            if ($product) {
                $cartItems = collect([
                    (object) [
                        'product' => $product,
                        'quantity' => $quantity,
                        'subtotal' => $product->price * $quantity,
                    ],
                ]);
            } else {
                $cartItems = collect([]);
            }
        } else {
            // Load cart items from session
            $cartData = $this->cartService->getCart();
            $productIds = $cartData->keys()->toArray();

            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $cartItems = $cartData->map(function ($quantity, $productId) use ($products) {
                $product = $products->get($productId);
                if (!$product) {
                    return null;
                }

                return (object) [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
            })->filter();
        }

        // If cart is empty, send back to cart page
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum('subtotal');
        $shipping = 50;
        $total = $subtotal + $shipping;

        return view('checkout.show', [
            'user' => $user,
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    /**
     * Handle the submission of the checkout form and process the order.
     */
    public function directBuy(Request $request, Product $product)
    {
        $productId = $product->id;
        $quantity = (int) $request->input('quantity', 1);

        $productModel = Product::find($productId);
        if (! $productModel) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $currentQty = $this->cartService->getCart()->get($productId, 0);
        $newQty = $currentQty + $quantity;

        if ($productModel->stock !== null && $newQty > $productModel->stock) {
            return redirect()->back()->with('error', 'Only ' . $productModel->stock . ' item(s) left in stock.');
        }

        Session::put('direct_buy_checkout', [
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
        Session::put('direct_buy_product_id', $productId);
        Session::put('direct_buy_quantity', $quantity);

        return redirect()->route('checkout.show')->with('success', 'Checkout is ready for your selected item.');
    }

    public function cancel()
    {
        $directBuyProductId = Session::get('direct_buy_product_id');
        $directBuyQuantity = (int) Session::get('direct_buy_quantity', 1);

        if ($directBuyProductId) {
            $this->cartService->add($directBuyProductId, $directBuyQuantity);
        }

        Session::forget(['direct_buy_checkout', 'direct_buy_product_id', 'direct_buy_quantity']);

        return redirect()->route('cart.show')->with('info', 'Checkout canceled. Your item was added to the cart.');
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'payment_method' => 'required|in:cod,gcash',
        ]);

        $orderNumber = 'F2W-' . time();

        $directBuy = Session::get('direct_buy_checkout');
        $directBuyProductId = Session::get('direct_buy_product_id');
        $directBuyQuantity = (int) Session::get('direct_buy_quantity', 1);

        if ($directBuy && ($directBuy['product_id'] ?? $directBuyProductId)) {
            $productId = $directBuy['product_id'] ?? $directBuyProductId;
            $quantity = (int) ($directBuy['quantity'] ?? $directBuyQuantity);
            $product = Product::find($productId);

            if ($product) {
                $cartItems = collect([
                    (object) [
                        'product' => $product,
                        'quantity' => $quantity,
                        'subtotal' => $product->price * $quantity,
                    ],
                ]);
            } else {
                $cartItems = collect([]);
            }
        } else {
            // Get cart data
            $cartData = $this->cartService->getCart();
            $productIds = $cartData->keys()->toArray();

            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $cartItems = $cartData->map(function ($quantity, $productId) use ($products) {
                $product = $products->get($productId);
                if (!$product) {
                    return null;
                }
                return (object) [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
            })->filter();
        }

        $subtotal = $cartItems->sum('subtotal');
        $shipping = 50;
        $total = $subtotal + $shipping;

        // Use transaction to ensure order, items and stock updates are saved together
        try {
            DB::transaction(function () use ($orderNumber, $validated, $subtotal, $shipping, $total, $cartItems) {
                // Lock and check stock for each product first
                foreach ($cartItems as $item) {
                    $p = Product::where('id', $item->product->id)->lockForUpdate()->first();
                    if (! $p) {
                        throw new \Exception('Product not found: ' . $item->product->id);
                    }

                    // If stock is tracked (not null), ensure enough quantity
                    if ($p->stock !== null && $item->quantity > $p->stock) {
                        throw new \Exception('Insufficient stock for ' . $p->name . '. Only ' . $p->stock . ' left.');
                    }
                }

                // Create order
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => $orderNumber,
                    'status' => 'pending',
                    'payment_method' => $validated['payment_method'],
                    'full_name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'street_address' => $validated['address'],
                    'barangay' => $validated['barangay'],
                    'city' => $validated['city'],
                    'province' => $validated['province'],
                    'postal_code' => $validated['postal_code'],
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $total,
                ]);

                // Create order items and decrement stock
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product->id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'subtotal' => $item->subtotal,
                    ]);

                    $p = Product::where('id', $item->product->id)->lockForUpdate()->first();
                    if ($p && $p->stock !== null) {
                        $p->stock = max(0, $p->stock - $item->quantity);
                        $p->save();
                    }
                }

                Log::info("Order Created: User ID " . Auth::id() . ", Order #$orderNumber, Total: ₱$total");
            });
        } catch (\Throwable $e) {
            Log::error('Checkout failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to place order: ' . $e->getMessage());
        }

        if (! $directBuy && ! $directBuyProductId) {
            $this->cartService->clear();
        }

        Session::forget(['direct_buy_checkout', 'direct_buy_product_id', 'direct_buy_quantity']);

        // Redirect to profile where users can track their orders
        return redirect()->route('profile.show')->with('success', "Order #$orderNumber has been placed successfully! You can track it below.");
    }
}
