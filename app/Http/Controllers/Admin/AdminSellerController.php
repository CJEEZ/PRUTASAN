<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSellerController extends Controller
{
    /**
     * Display list of all sellers
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'seller');

        // Search by name, email, or phone
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'approved') {
                $query->where(function ($q) {
                    $q->where('seller_status', 'approved')
                      ->orWhere('is_approved', true)
                      ->orWhereNotNull('email_verified_at');
                });
            } elseif ($status === 'pending') {
                $query->where(function ($q) {
                    $q->where('seller_status', 'pending')
                      ->orWhere(function ($sub) {
                          $sub->whereNull('seller_status')
                              ->where(function ($sub2) {
                                  $sub2->whereNull('is_approved')->orWhere('is_approved', false);
                              })
                              ->whereNull('email_verified_at');
                      });
                });
            } elseif ($status === 'rejected') {
                $query->where('seller_status', 'rejected');
            }
        }

        // Filter by registration date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $sellers = $query->withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get stats
        $totalSellers = User::where('role', 'seller')->count();
        $approvedSellers = User::where('role', 'seller')
            ->where(function ($query) {
                $query->where('seller_status', 'approved')
                      ->orWhere('is_approved', true)
                      ->orWhere(function ($sub) {
                          $sub->whereNull('seller_status')
                              ->whereNotNull('email_verified_at');
                      });
            })->count();
        $pendingSellers = User::where('role', 'seller')
            ->where(function ($query) {
                $query->where('seller_status', 'pending')
                      ->orWhere(function ($sub) {
                          $sub->whereNull('seller_status')
                              ->where(function ($sub2) {
                                  $sub2->whereNull('is_approved')->orWhere('is_approved', false);
                              })
                              ->whereNull('email_verified_at');
                      });
            })->count();
        $rejectedSellers = User::where('role', 'seller')
            ->where('seller_status', 'rejected')
            ->count();
        $deletedSellers = User::onlyTrashed()->where('role', 'seller')->count();

        // Calculate total sales (can be enhanced later with actual sales data)
        $totalSales = 0; // Placeholder for now

        return view('admin.sellers.index', compact(
            'sellers',
            'totalSellers',
            'approvedSellers',
            'pendingSellers',
            'rejectedSellers',
            'deletedSellers',
            'totalSales'
        ));
    }

    /**
     * Display seller history list
     */
    public function history(Request $request): View
    {
        $query = User::onlyTrashed()->where('role', 'seller');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $deletedSellers = $query->orderBy('deleted_at', 'desc')->paginate(15);

        return view('admin.sellers.history', compact('deletedSellers'));
    }

    /**
     * Display seller details
     */
    public function show(User $seller): View
    {
        // Get seller's products
        $products = Product::where('seller_id', $seller->id)->latest()->get();
        $totalProducts = $products->count();
        $activeProducts = $products->where('status', 'active')->count();

        // Calculate sales stats using order items for this seller
        $orders = \App\Models\Order::whereHas('items.product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->latest()->get();

        // Total sales for this seller (sum of order item subtotals belonging to seller)
        $totalSales = \App\Models\OrderItem::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->sum('subtotal');

        $totalOrders = $orders->count();

        return view('admin.sellers.show', compact(
            'seller',
            'products',
            'totalProducts',
            'activeProducts',
            'totalSales',
            'totalOrders'
        ));
    }

    /**
     * Show edit form
     */
    public function edit(User $seller): View
    {
        return view('admin.sellers.edit', compact('seller'));
    }

    /**
     * Update seller information
     */
    public function update(Request $request, User $seller)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $seller->id,
            'phone_number' => 'nullable|string|max:20',
            'shipping_address' => 'nullable|string',
        ]);

        $seller->update($validated);

        return redirect()->route('admin.sellers.show', ['seller' => $seller->id])
            ->with('success', 'Seller updated successfully!');
    }

    /**
     * Approve seller (activate account)
     */
    public function approve(User $seller)
    {
        $seller->update([
            'email_verified_at' => now(),
            'seller_status' => 'approved',
            'is_approved' => true,
        ]);

        return redirect()->route('admin.sellers.show', ['seller' => $seller->id])
            ->with('success', 'Seller approved successfully!');
    }

    /**
     * Reject/Deactivate seller
     */
    public function reject(User $seller)
    {
        $seller->update([
            'email_verified_at' => null,
            'seller_status' => 'rejected',
            'is_approved' => false,
        ]);

        return redirect()->route('admin.sellers.show', ['seller' => $seller->id])
            ->with('success', 'Seller deactivated!');
    }

    /**
     * Delete seller
     */
    public function destroy(User $seller)
    {
        // Soft-delete the seller and keep the user visible in history.
        $seller->delete();

        return redirect()->route('admin.sellers.index')
            ->with('success', 'Seller account moved to history successfully!');
    }

    /**
     * Restore soft deleted seller from history.
     */
    public function restore($id)
    {
        $seller = User::withTrashed()->where('role', 'seller')->findOrFail($id);
        $seller->restore();

        return redirect()->route('admin.sellers.history')
            ->with('success', 'Seller account restored successfully!');
    }

    /**
     * Export sellers to CSV
     */
    public function export()
    {
        $sellers = User::where('role', 'seller')->get();

        $filename = 'sellers_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($sellers) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Products', 'Status', 'Registered']);

            // Data rows
            foreach ($sellers as $seller) {
                $productCount = Product::where('seller_id', $seller->id)->count();
                $status = ucfirst($seller->computed_seller_status ?? 'Pending');

                fputcsv($file, [
                    $seller->id,
                    $seller->name,
                    $seller->email,
                    $seller->phone_number,
                    $productCount,
                    $status,
                    $seller->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
