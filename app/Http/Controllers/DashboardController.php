<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the user dashboard with category filtering.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $category = $request->query('category');

        // Get all products with category relation
        $query = Product::with('category');

        // Filter by category name (tropical, seasonal, exotic, arindo)
        if ($category) {
            if ($category === 'tropical') {
                $query->whereHas('category', function ($q) {
                    $q->where('slug', 'tropical');
                });
            } elseif ($category === 'seasonal') {
                $query->where('is_seasonal', true);
            } elseif ($category === 'exotic') {
                $query->where('is_exotic', true);
            } elseif ($category === 'arindo') {
                $query->where('is_arindo', true)
                    ->where('arindo_status', 'available_for_arindo');
            }
        }

        $products = $query->get();
        $arindoProducts = Product::with('seller')
            ->where('is_arindo', true)
            ->where('arindo_status', 'available_for_arindo')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $communicationMessages = $user
            ? Inquiry::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('email', $user->email);
            })->latest()->take(3)->get()
            : collect();

        return view('dashboard', compact('user', 'products', 'category', 'arindoProducts', 'communicationMessages'));
    }
}
