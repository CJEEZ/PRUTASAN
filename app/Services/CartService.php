<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected const CART_KEY = 'shopping_cart';

    /**
     * Retrieves the current cart contents from the session.
     * The cart is stored as an associative array: [product_id => quantity].
     */
    public function getCart(): Collection
    {
        return collect(Session::get(self::CART_KEY, []));
    }

    /**
     * Saves the updated cart back to the session.
     */
    protected function saveCart(Collection $cart): void
    {
        Session::put(self::CART_KEY, $cart->toArray());
    }

    /**
     * Adds a product to the cart or increments its quantity.
     *
     * @param int $productId
     * @param int $quantity
     */
    public function add(int $productId, int $quantity = 1): void
    {
        $cart = $this->getCart();

        // Increment the quantity if the item already exists
        $currentQty = (int)$cart->get($productId, 0);
        $newQuantity = $currentQty + $quantity;

        $this->setQuantity($productId, $newQuantity);
    }

    /**
     * Sets the quantity of a product in the cart.
     * This is useful for direct quantity updates on the cart page.
     *
     * @param int $productId
     * @param int $quantity
     */
    public function setQuantity(int $productId, int $quantity): void
    {
        $cart = $this->getCart();
        $quantity = (int)$quantity;

        if ($quantity > 0) {
            $cart->put($productId, $quantity);
        } else {
            // Remove if quantity drops to zero or less
            $cart->forget($productId);
        }

        $this->saveCart($cart);
    }

    /**
     * Removes a specific item completely from the cart.
     *
     * @param int $productId
     */
    public function remove(int $productId): void
    {
        $cart = $this->getCart();
        $cart->forget($productId);
        $this->saveCart($cart);
    }

    /**
     * Clears the entire cart.
     */
    public function clear(): void
    {
        Session::forget(self::CART_KEY);
    }

    /**
     * Gets the total number of unique items in the cart (for the cart icon badge).
     */
    public function getCount(): int
    {
        return $this->getCart()->count();
    }

    /**
     * Gets the total quantity of all items in the cart.
     */
    public function getTotalQuantity(): int
    {
        return (int)$this->getCart()->sum();
    }
}