<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|url|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'unit' => 'required|string',
            'stock' => 'required|integer|min:0',
        ]);

        unset($validated['image']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image_url'] = asset('storage/' . $path);
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function show(Product $product): View
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|url|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'unit' => 'required|string',
            'stock' => 'required|integer|min:0',
        ]);

        unset($validated['image']);

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($product->image_url);
            $path = $request->file('image')->store('products', 'public');
            $validated['image_url'] = asset('storage/' . $path);
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteStoredImage($product->image_url);
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

    private function deleteStoredImage(?string $imageUrl): void
    {
        if (!$imageUrl) {
            return;
        }

        $path = parse_url($imageUrl, PHP_URL_PATH);
        if (!$path || !Str::startsWith($path, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, '/storage/'));
    }
}
