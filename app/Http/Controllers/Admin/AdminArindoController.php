<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminArindoController extends Controller
{
    public function index()
    {
        $pendingCount = Product::where('is_arindo', true)
            ->where('arindo_status', 'pending_verification')
            ->count();

        $activeCount = Product::where('is_arindo', true)
            ->where('arindo_status', 'available_for_arindo')
            ->count();

        $expiringAgreements = Product::where('is_arindo', true)
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [now(), now()->addDays(90)])
            ->orderBy('expiration_date')
            ->get();

        $listings = Product::with('seller')
            ->where('is_arindo', true)
            ->orderByRaw("FIELD(arindo_status, 'pending_verification', 'available_for_arindo', 'pawned', 'available_for_harvest', 'rejected')")
            ->get();

        return view('admin.arindo.index', compact('pendingCount', 'activeCount', 'expiringAgreements', 'listings'));
    }

    public function show(Product $product)
    {
        if (! $product->is_arindo) {
            abort(404);
        }

        return view('admin.arindo.show', compact('product'));
    }

    public function verify(Request $request, Product $product)
    {
        if (! $product->is_arindo) {
            abort(404);
        }

        $product->update([
            'arindo_status' => 'available_for_arindo',
            'arindo_verified_at' => now(),
        ]);

        return back()->with('success', 'Arindo listing verified successfully.');
    }

    public function reject(Request $request, Product $product)
    {
        if (! $product->is_arindo) {
            abort(404);
        }

        $product->update(['arindo_status' => 'rejected']);

        return back()->with('success', 'Arindo listing has been rejected.');
    }
}
