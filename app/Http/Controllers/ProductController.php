<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('seller')->where('is_active', true);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('title', 'like', "%{$q}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('products.index', compact('products','categories'));
    }

    public function show(Product $product)
    {
        $product->load(['seller', 'category', 'reviews.user'])->loadAvg('reviews', 'rating');

        return view('products.show', compact('product'));
    }

    public function storeReview(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $product->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return redirect()->route('products.show', $product)
            ->with('success', 'Your rating and comment have been saved.');
    }
}
