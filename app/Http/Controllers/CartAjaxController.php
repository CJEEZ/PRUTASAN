<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;

class CartAjaxController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Fetch cart items for AJAX sidebar rendering.
     */
    public function getItems(): JsonResponse
    {
        $cartData = $this->cartService->getCart();
        $productIds = $cartData->keys()->toArray();

        if (empty($productIds)) {
            return response()->json([
                'items' => [],
                'total' => 0,
                'count' => 0,
            ]);
        }

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = $cartData->map(function ($quantity, $productId) use ($products) {
            $product = $products->get($productId);

            if (!$product) {
                return null;
            }

            $quantity = (int)$quantity;
            $price = (float)$product->price;
            $subtotal = $price * $quantity;

            return [
                'id' => $productId,
                'name' => $product->name,
                'image_url' => $product->image_url,
                'price' => $price,
                'unit' => $product->unit,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        })->filter()->values();

        $total = (float)$cartItems->sum('subtotal');
        $count = (int)$cartData->sum();

        return response()->json([
            'items' => $cartItems,
            'total' => $total,
            'count' => $count,
        ]);
    }
}
