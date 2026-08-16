<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the product catalog with filtering and search capabilities.
     */
    public function index(Request $request): View
    {
        // --- 1. Determine Filter State from Request ---
        $categorySlug = $request->query('category');
        $isSeasonal = $request->boolean('seasonal');
        $isExotic = $request->boolean('exotic');
        $searchTerm = $request->query('search'); // NEW: Get the search term

        // Determine the currently selected category slug for highlighting (default to 'all')
        if ($categorySlug) {
             $selectedCategorySlug = $categorySlug;
        } else {
             $selectedCategorySlug = 'all';
        }

        // --- 2. Build the Product Query ---
        $query = Product::query()->with('category')
            ->where(function ($q) {
                // Show products that are not assigned to a seller OR whose seller is admin-approved
                $q->whereNull('seller_id')
                  ->orWhereHas('seller', function ($q2) {
                      $q2->whereNotNull('email_verified_at');
                  });
            });

        // Apply Search Filter (NEW LOGIC)
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                // Search product name OR description
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Apply Category Filter
        if ($categorySlug && $categorySlug !== 'all') {
            $selectedCategory = Category::where('slug', $categorySlug)->first();
            if ($selectedCategory) {
                $query->where('category_id', $selectedCategory->id);
            }
        }

        // Apply Seasonal Filter
        if ($isSeasonal) {
            $query->where('is_seasonal', true);
        }

        // Apply Exotic Filter
        if ($isExotic) {
            $query->where('is_exotic', true);
        }
        
        // Always order by latest product
        $products = $query->latest()->get();

        // --- 3. Pass Data to View ---
        return view('catalog.index', [
            'products' => $products,
            'selectedCategorySlug' => $selectedCategorySlug,
            'isSeasonal' => $isSeasonal,
            'isExotic' => $isExotic,
            'searchTerm' => $searchTerm, // Pass the search term back to the view
        ]);
    }
}