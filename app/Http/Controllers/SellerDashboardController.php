<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inquiry;
use Carbon\Carbon;

class SellerDashboardController extends Controller
{
    /**
     * Show seller income breakdown and payout history.
     */
    public function myIncome(Request $request)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }
        $sellerId = $user->id;

        // Total sales
        $totalSales = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->sum('subtotal');

        // Sales analytics data
        $now = Carbon::now();

        // Monthly sales for the last 12 months
        $monthlySales = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthTotal = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            })->whereHas('order', function ($query) use ($month) {
                $query->whereYear('created_at', $month->year)
                      ->whereMonth('created_at', $month->month);
            })->sum('subtotal');

            $monthlySales[] = [
                'month' => $month->format('M Y'),
                'amount' => $monthTotal,
                'orders' => \App\Models\Order::whereHas('items.product', function ($q) use ($sellerId) {
                    $q->where('seller_id', $sellerId);
                })->whereYear('created_at', $month->year)
                  ->whereMonth('created_at', $month->month)
                  ->distinct()->count(),
            ];
        }

        // Weekly sales for the last 8 weeks
        $weeklySales = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            $weekTotal = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            })->whereHas('order', function ($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('created_at', [$weekStart, $weekEnd]);
            })->sum('subtotal');

            $weeklySales[] = [
                'week' => 'Week ' . $weekStart->format('W'),
                'amount' => $weekTotal,
                'period' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
            ];
        }

        // Top selling products
        $topProducts = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_quantity'), \DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_revenue', 'desc')
            ->take(10)
            ->get();

        // Sales by category
        $salesByCategory = \App\Models\OrderItem::select('products.category_id', \DB::raw('SUM(order_items.subtotal) as total_revenue'), \DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_id', $sellerId)
            ->with('product.category')
            ->groupBy('products.category_id')
            ->orderBy('total_revenue', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->product->category->name ?? 'Unknown',
                    'revenue' => $item->total_revenue,
                    'quantity' => $item->total_quantity,
                ];
            });

        // Recent sales (last 30 days)
        $recentSales = \App\Models\Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })
        ->with(['items.product', 'user'])
        ->where('created_at', '>=', $now->copy()->subDays(30))
        ->orderBy('created_at', 'desc')
        ->take(20)
        ->get();

        // Performance metrics
        $thisMonth = $now->copy()->startOfMonth();
        $lastMonth = $now->copy()->subMonth()->startOfMonth();

        $thisMonthRevenue = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->whereHas('order', function ($query) use ($thisMonth) {
            $query->where('created_at', '>=', $thisMonth);
        })->sum('subtotal');

        $lastMonthRevenue = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->whereHas('order', function ($query) use ($lastMonth, $thisMonth) {
            $query->where('created_at', '>=', $lastMonth)
                  ->where('created_at', '<', $thisMonth);
        })->sum('subtotal');

        $revenueGrowth = $lastMonthRevenue > 0 ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        // Average order value
        $totalOrders = \App\Models\Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->count();

        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Payout history (example: last 10 payouts)
        $payouts = [];
        // TODO: If you have a Payout model, fetch payouts for this seller
        // $payouts = \App\Models\Payout::where('seller_id', $sellerId)->orderBy('created_at', 'desc')->take(10)->get();

        // Get seller record for bank account info
        $seller = $user->seller ?? $user;
        $payoutHistory = []; // TODO: Implement payout history retrieval

        return view('seller.income', compact(
            'seller',
            'totalSales',
            'payouts',
            'payoutHistory',
            'monthlySales',
            'weeklySales',
            'topProducts',
            'salesByCategory',
            'recentSales',
            'thisMonthRevenue',
            'lastMonthRevenue',
            'revenueGrowth',
            'averageOrderValue',
            'totalOrders'
        ));
    }
    /**
     * Show edit product form for seller.
     */
    public function editProduct($id)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }
        $product = \App\Models\Product::where('seller_id', $user->id)->findOrFail($id);
        $categories = \App\Models\Category::all();
        return view('seller.product_edit', compact('product', 'categories'));
    }

    /**
     * Update product for seller.
     */
    public function updateProduct(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }
        $product = \App\Models\Product::where('seller_id', $user->id)->findOrFail($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'string'],
            'image_url' => ['nullable', 'string'],
            'is_arindo' => ['nullable', 'boolean'],
            'loan_amount' => ['nullable', 'numeric', 'min:0'],
            'term_years' => ['nullable', 'integer', 'min:1'],
            'expiration_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'map_location' => ['nullable', 'string', 'max:255'],
            'crop_yield_description' => ['nullable', 'string'],
            'land_photo_urls' => ['nullable', 'string'],
            'soil_report_url' => ['nullable', 'url'],
            'legal_document_url' => ['nullable', 'url'],
        ]);
        $landPhotoUrls = collect(explode("\n", $data['land_photo_urls'] ?? ''))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $data['land_photo_urls'] = $landPhotoUrls;
        $data['is_arindo'] = $request->has('is_arindo');
        if ($data['is_arindo'] && $product->arindo_status !== 'available_for_arindo') {
            $data['arindo_status'] = 'pending_verification';
        }
        $product->update($data);
        return redirect()->route('seller.products')->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product for seller.
     */
    public function deleteProduct($id)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }
        $product = \App\Models\Product::where('seller_id', $user->id)->findOrFail($id);
        $product->delete();
        return redirect()->route('seller.products')->with('success', 'Product deleted successfully!');
    }
    /**
     * Display seller dashboard with sales summary and inventory.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Only users with role "seller" may access this dashboard
        if (! $user || ($user->role !== 'seller')) {
            abort(403);
        }

        // Sales summary only for this seller
        $sellerId = $user->id;

        // Seller's products
        $inventory = Product::where('seller_id', $sellerId)
            ->select('id', 'name', 'stock', 'price')
            ->orderBy('name')
            ->get();

        $communicationTickets = Inquiry::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->orWhere('target_role', 'seller');
        })->latest()->take(4)->get();

        // Total revenue for seller: sum of order_item.subtotal where product.seller_id = seller
        $totalSales = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->sum('subtotal');

        // Count distinct orders that include seller's products
        $ordersCount = \App\Models\Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->distinct()->count();

        $pendingOrdersCount = \App\Models\Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->whereIn('status', ['pending', 'processing'])->distinct()->count();

        $lowStockItems = Product::where('seller_id', $sellerId)
            ->where('stock', '<=', 5)
            ->count();

        $totalRevenueLast30Days = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->whereHas('order', function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        })->sum('subtotal');

        $startMonth = Carbon::now()->startOfMonth()->subMonths(5);
        $monthlySales = [];
        for ($i = 0; $i < 6; $i++) {
            $month = $startMonth->copy()->addMonths($i);
            $monthTotal = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            })->whereHas('order', function ($query) use ($month) {
                $query->whereYear('created_at', $month->year)
                      ->whereMonth('created_at', $month->month);
            })->sum('subtotal');

            $monthlySales[] = [
                'label' => $month->format('M'),
                'amount' => $monthTotal,
            ];
        }

        $bankAccountLinked = false;

        // Recent orders that include this seller's products
        $recentOrders = \App\Models\Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->orderBy('created_at', 'desc')->take(5)->get();

        // Additional seller center features data
        $totalProducts = Product::where('seller_id', $sellerId)->count();
        $categoriesCount = Product::where('seller_id', $sellerId)
            ->distinct('category_id')
            ->count('category_id');
        $lowStockProducts = Product::where('seller_id', $sellerId)
            ->where('stock', '<=', 5)
            ->count();
        $outOfStockProducts = Product::where('seller_id', $sellerId)
            ->where('stock', '=', 0)
            ->count();

        // Top performing products (by revenue)
        $topProducts = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            })
            ->select('product_id', \DB::raw('SUM(quantity) as total_quantity'), \DB::raw('SUM(subtotal) as total_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->take(3)
            ->get();

        // Recent activities (orders and product updates)
        $recentActivities = collect();

        // Recent orders
        $recentOrderActivities = \App\Models\Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })
        ->select('id', 'created_at', 'status')
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get()
        ->map(function ($order) {
            return [
                'type' => 'order',
                'title' => 'New Order #' . $order->id,
                'description' => 'Order status: ' . ucfirst($order->status),
                'time' => $order->created_at,
                'icon' => '📦'
            ];
        });

        $recentActivities = $recentActivities->merge($recentOrderActivities);

        // Recent product additions
        $recentProductActivities = Product::where('seller_id', $sellerId)
            ->select('id', 'name', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get()
            ->map(function ($product) {
                return [
                    'type' => 'product',
                    'title' => 'New Product Added',
                    'description' => $product->name,
                    'time' => $product->created_at,
                    'icon' => '🛍️'
                ];
            });

        $recentActivities = $recentActivities->merge($recentProductActivities)->sortByDesc('time')->take(5);

        // Get seller record for bank account info
        $seller = $user->seller ?? $user;
        $paymentMethods = $user->paymentMethods()->latest()->get();

        // Render user-facing seller dashboard
        return view('seller.dashboard', compact(
            'seller',
            'totalSales',
            'ordersCount',
            'recentOrders',
            'inventory',
            'pendingOrdersCount',
            'lowStockItems',
            'totalRevenueLast30Days',
            'monthlySales',
            'bankAccountLinked',
            'totalProducts',
            'categoriesCount',
            'lowStockProducts',
            'outOfStockProducts',
            'topProducts',
            'recentActivities',
            'communicationTickets',
            'paymentMethods'
        ));
    }

    /**
     * Show seller orders listing (only orders containing this seller's products)
     */
    public function orders(Request $request)
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'seller') {
            abort(403);
        }

        $sellerId = $user->id;

        $orders = \App\Models\Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->with('shipment')->orderBy('created_at', 'desc')->paginate(20);

        return view('seller.orders', compact('orders'));
    }

    /**
     * Show seller order detail (only orders containing this seller's products)
     */
    public function orderDetail($id)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }
        $sellerId = $user->id;
        $order = \App\Models\Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->with(['items.product', 'shipment'])->findOrFail($id);
        return view('seller.order_detail', compact('order'));
    }

    /**
     * Update seller order status.
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }
        $sellerId = $user->id;
        $order = \App\Models\Order::whereHas('items.product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->findOrFail($id);
        $data = $request->validate([
            'status' => ['required', 'string'],
        ]);
        $order->status = $data['status'];
        $order->save();
        return redirect()->route('seller.orders.detail', $order->id)->with('success', 'Order status updated.');
    }

    /**
     * Placeholder withdraw handler.
     */
    public function withdraw(Request $request)
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'seller') {
            abort(403);
        }

        // In a full implementation we'd create a Withdrawal/Transaction record,
        // validate available balance, and queue payout processing.

        return redirect()->route('seller.dashboard')->with('success', 'Withdrawal request received. Our team will process it shortly.');
    }

    /**
     * Show the Start Selling dashboard inside the user profile.
     * If the user is already a seller show quick stats and link to full seller dashboard.
     */
    public function start(Request $request)
    {
        $user = Auth::user();
        // Basic info for non-sellers: show welcome CTA
        if (! $user || $user->role !== 'seller') {
            return view('seller.start_dashboard', ['user' => $user]);
        }
        // For existing sellers redirect to the main seller dashboard (user-facing)
        return redirect()->route('seller.dashboard');
    }

    /**
     * Show seller onboarding page.
     */
    public function onboarding(Request $request)
    {
        $user = Auth::user();

        // Only authenticated users can access onboarding
        if (! $user) {
            return redirect()->route('register')->with('error', 'Please log in to become a seller.');
        }

        // If already a seller, redirect to dashboard
        if ($user->role === 'seller') {
            return redirect()->route('seller.dashboard');
        }

        // Show onboarding page for non-sellers
        return view('seller.onboarding', ['user' => $user]);
    }

    /**
     * Process seller onboarding - convert user to seller role.
     */
    public function processOnboarding(Request $request)
    {
        $user = Auth::user();

        // Only authenticated users can process onboarding
        if (! $user) {
            return redirect()->route('login');
        }

        // If already a seller, redirect to dashboard
        if ($user->role === 'seller') {
            return redirect()->route('seller.dashboard');
        }

        // Update user role to seller
        $user->update(['role' => 'seller']);

        // Redirect to seller approval page
        return redirect()->route('seller.approval.show')->with('success', 'You are now a seller! Please complete your seller profile.');
    }

    /**
     * Show seller product listing.
     */
    public function products(Request $request)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }
        $sellerId = $user->id;
        $products = \App\Models\Product::where('seller_id', $sellerId)->orderBy('name')->paginate(20);
        return view('seller.products', compact('products'));
    }

    /**
     * Show add product form for seller.
     */
    public function addProduct()
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }
        $categories = \App\Models\Category::all();
        return view('seller.product_add', compact('categories'));
    }

    /**
     * Show seller Arindo property listings.
     */
    public function arindoProperties()
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }

        $sellerId = $user->id;
        $properties = Product::where('seller_id', $sellerId)
            ->where('is_arindo', true)
            ->orderByDesc('created_at')
            ->get();

        $summary = [
            'total' => $properties->count(),
            'pending' => $properties->where('arindo_status', 'pending_verification')->count(),
            'active' => $properties->where('arindo_status', 'available_for_arindo')->count(),
            'pawned' => $properties->where('arindo_status', 'pawned')->count(),
        ];

        return view('seller.arindo_properties', compact('properties', 'summary'));
    }

    /**
     * Store new product for seller.
     */
    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'string'],
            'image_url' => ['nullable', 'string'],
            'is_arindo' => ['nullable', 'boolean'],
            'loan_amount' => ['nullable', 'numeric', 'min:0'],
            'term_years' => ['nullable', 'integer', 'min:1'],
            'expiration_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'map_location' => ['nullable', 'string', 'max:255'],
            'crop_yield_description' => ['nullable', 'string'],
            'land_photo_urls' => ['nullable', 'string'],
            'soil_report_url' => ['nullable', 'url'],
            'legal_document_url' => ['nullable', 'url'],
        ]);
        $landPhotoUrls = collect(explode("\n", $data['land_photo_urls'] ?? ''))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $data['land_photo_urls'] = $landPhotoUrls;
        $data['seller_id'] = $user->id;
        $data['is_arindo'] = $request->has('is_arindo');
        $data['arindo_status'] = $data['is_arindo'] ? 'pending_verification' : 'available';
        \App\Models\Product::create($data);
        return redirect()->route('seller.products')->with('success', 'Product added successfully!');
    }
}

