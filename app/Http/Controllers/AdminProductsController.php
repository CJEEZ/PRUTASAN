<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminProductsController extends Controller
{
    /**
     * Apply the 'auth' and 'can:access-admin' middleware.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:access-admin']);
    }

    /**
     * Display a listing of the products.
     */
    public function index()
    {
        // Fetch all products with their category relationship for display
        $products = Product::with('category')->latest()->get();

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        // You would create a view 'admin.products.create' for the form here
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        // Simple validation example
        $request->validate([
            'name' => 'required|max:255|unique:products',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            // ... other validation fields
        ]);

        Product::create($request->all());

        return redirect()->route('admin.products.index')
                         ->with('status', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        // You would create a view 'admin.products.edit' for the form here
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Simple validation example
        $request->validate([
            'name' => 'required|max:255|unique:products,name,' . $product->id,
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            // ... other validation fields
        ]);
        
        $product->update($request->all());

        return redirect()->route('admin.products.index')
                         ->with('status', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('status', 'Product deleted successfully.');
    }
}