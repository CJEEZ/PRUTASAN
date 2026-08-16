<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Inquiry;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // Guard against missing tables (e.g., fresh setup)
        if (!Schema::hasTable('orders') || !Schema::hasTable('products') || !Schema::hasTable('users')) {
            $products = collect();
            $orders = collect();

            $totalOrders = 0;
            $totalRevenue = 0.00;
            $pendingOrders = 0;
            $completedOrders = 0;
            $totalCustomers = 0;
            $totalProducts = 0;

            $ordersByStatus = [
                'pending' => 0,
                'confirmed' => 0,
                'shipped' => 0,
                'delivered' => 0,
                'cancelled' => 0,
                'return_requested' => 0,
            ];

            $recentOrders = collect();
            $communicationInquiries = collect();
            $communicationStats = [
                'unread' => 0,
                'announcements' => 0,
                'pending' => 0,
            ];
        } else {
            // Efficient aggregates using DB queries
            $totalOrders = Order::count();
            $totalRevenue = (float) Order::sum('total');
            $pendingOrders = Order::where('status', 'pending')->count();
            $completedOrders = Order::where('status', 'delivered')->count();
            $totalCustomers = User::where('role', '!=', 'admin')->count();
            $totalProducts = Product::count();

            // Order status breakdown via grouping
            $statusCounts = Order::select('status', \DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $ordersByStatus = [
                'pending' => $statusCounts['pending'] ?? 0,
                'confirmed' => $statusCounts['confirmed'] ?? 0,
                'shipped' => $statusCounts['shipped'] ?? 0,
                'delivered' => $statusCounts['delivered'] ?? 0,
                'cancelled' => $statusCounts['cancelled'] ?? 0,
                'return_requested' => $statusCounts['return_requested'] ?? 0,
            ];

            // Recent orders with relationships
            $recentOrders = Order::with(['user', 'items'])->latest()->take(10)->get();

            $products = Product::with('category')->get();
            $orders = Order::with('user', 'items')->latest()->take(100)->get();
            $communicationInquiries = Inquiry::latest()->take(5)->get();
            $communicationStats = [
                'unread' => Inquiry::where('is_read', false)->count(),
                'announcements' => Inquiry::where('category', 'announcement')->count(),
                'pending' => Inquiry::where('status', 'new')->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'products',
            'orders',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'completedOrders',
            'totalCustomers',
            'totalProducts',
            'ordersByStatus',
            'recentOrders',
            'communicationInquiries',
            'communicationStats'
        ));
    }
}
