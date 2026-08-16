<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminStockController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')
            ->select('id', 'name', 'stock', 'price', 'category_id', 'created_at')
            ->paginate(15);

        // Stock level breakdown
        $lowStock = Product::where('stock', '<=', 5)->count();
        $outOfStock = Product::where('stock', 0)->count();
        $totalProducts = Product::count();
        $totalStockValue = Product::sum(\DB::raw('stock * price'));

        return view('admin.stock.index', compact(
            'products',
            'lowStock',
            'outOfStock',
            'totalProducts',
            'totalStockValue'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return back()->with('success', 'Stock updated successfully!');
    }
}
