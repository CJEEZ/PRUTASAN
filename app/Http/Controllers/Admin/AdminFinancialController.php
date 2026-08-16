<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class AdminFinancialController extends Controller
{
    public function index(): View
    {
        $totalRevenue = Order::sum('total');
        $monthlyRevenue = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $totalOrders = Order::count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Calculate estimated seller commissions (10% of total revenue)
        $sellerCommissions = $totalRevenue * 0.10;

        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.financial.index', compact(
            'totalRevenue',
            'monthlyRevenue',
            'totalOrders',
            'avgOrderValue',
            'sellerCommissions',
            'recentOrders'
        ));
    }

    public function revenue(): View
    {
        // Revenue by period
        $revenueByMonth = Order::selectRaw('MONTH(created_at) as month, SUM(total) as revenue')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->get();

        return view('admin.financial.revenue', compact('revenueByMonth'));
    }

    public function commissions(): View
    {
        $sellers = User::where('role', 'seller')
            ->with(['orders' => function ($q) {
                $q->sum('total');
            }])
            ->paginate(15);

        return view('admin.financial.commissions', compact('sellers'));
    }
}
