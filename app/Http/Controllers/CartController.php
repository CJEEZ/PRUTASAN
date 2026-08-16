<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    protected CartService $cartService;

    /**
     * Inject the CartService.
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
        
        // FIX: The global 'auth' middleware has been removed from the CartController
        // to allow guest users to add, view, update, and remove items from the cart,
        // using the session-based cart functionality provided by CartService.
        // Authentication is now only required for the final checkout process.
    }

    /**
     * Display the contents of the shopping cart.
     */
    // Route: GET /cart (name: cart.show in web.php)
    public function show(): View 
    {
        // Get the raw cart data (Product ID => Quantity)
        $cartData = $this->cartService->getCart();
        
        // Get the IDs of the products in the cart
        $productIds = $cartData->keys()->toArray();

        // Fetch the corresponding Product models (if there are any)
        $products = !empty($productIds) 
            ? Product::whereIn('id', $productIds)->get()->keyBy('id')
            : collect([]);

        // Prepare the list of items for the view
        $cartItems = $cartData->map(function ($quantity, $productId) use ($products) {
            $product = $products->get($productId);

            // Handle case where product might have been deleted from DB
            if (!$product) {
                return null;
            }

            return (object) [
                'product' => $product,
                'quantity' => (int)$quantity,
                'subtotal' => (float)$product->price * (int)$quantity,
            ];
        })->filter(); // Remove nulls if products were missing

        // Calculate the grand total
        $total = $cartItems->sum('subtotal');

        return view('cart.index', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }
    
    // Note: The previous index() method has been renamed to show() to match 
    // the Laravel resource naming convention and your web.php definition (Route::get('/', 'show')->name('show');)
    public function index(): View
    {
        return $this->show();
    }



    public function add(Request $request, Product $product = null): RedirectResponse
    {
        // If Product model binding failed or was not used, validate product_id from the request body
        $productId = $product ? $product->id : $request->input('product_id');

        $request->validate([
            // Only validate if productId wasn't obtained via route model binding
            'product_id' => $productId ? 'nullable' : 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1', 
        ]);
        
        // If product ID still isn't set, use the one from the request validation
        if (!$productId) {
            $productId = $request->input('product_id');
        }

        $quantity = $request->input('quantity', 1);

        // Fetch product to check stock
        $productModel = Product::find($productId);
        if (! $productModel) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        // Current quantity already in cart
        $currentQty = $this->cartService->getCart()->get($productId, 0);
        $newQty = $currentQty + $quantity;

        if ($productModel->stock !== null && $newQty > $productModel->stock) {
            return redirect()->back()->with('error', 'Only ' . $productModel->stock . ' item(s) left in stock.');
        }

        // Add the specified quantity to the existing count
        $this->cartService->add($productId, $quantity);

        return redirect()->back()->with('success', 'Item added to cart successfully!');
    }
    
    // Alias the store method to add (as defined in your routes)
    public function store(Request $request): RedirectResponse
    {
        return $this->add($request);
    }

    /**
     * Update: Set the quantity of a specific item in the cart.
     */
    // Route: PUT /cart/update/{product} (name: cart.update in web.php)
    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0', // 0 quantity will remove the item
        ]);

        $productId = $product->id;
        $newQuantity = $request->input('quantity');

        // Use the dedicated CartService method to set or remove the item
        if ($newQuantity > 0) {
            $this->cartService->setQuantity($productId, $newQuantity);
            $message = 'Cart quantity updated successfully.';
        } else {
            // If quantity is 0, remove the item entirely
            $this->cartService->remove($productId);
            $message = 'Item removed from cart.';
        }

        return redirect()->route('cart.show')->with('success', $message);
    }

    /**
     * Remove: Remove a specific item completely from the cart.
     */
    // Route: DELETE /cart/remove/{product} (name: cart.remove in web.php)
    public function remove(Product $product): RedirectResponse
    {
        // Use model binding to get the product ID
        $this->cartService->remove($product->id);

        return redirect()->route('cart.show')->with('success', 'Item removed from cart.');
    }
    
    // Alias the destroy method to remove (as defined in your routes)
    public function destroy(Product $product): RedirectResponse
    {
        return $this->remove($product);
    }

    /**
     * Clear: Remove all items from the cart.
     */
    public function clear(): RedirectResponse
    {
        $this->cartService->clear();
        return redirect()->route('cart.show')->with('success', 'Cart cleared successfully.');
    }
}