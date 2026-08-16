@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="min-h-screen bg-[#07170f] text-slate-100">
    <div class="flex flex-col xl:flex-row gap-6 p-6">
        <aside class="hidden xl:block w-80 rounded-[2rem] bg-[#0d2f1b]/90 border border-slate-800 shadow-2xl p-6 backdrop-blur-xl">
            @include('seller._sidebar')
        </aside>

        <div class="flex-1 space-y-6">
            @if(session('success'))
                <div class="rounded-3xl border border-emerald-600/30 bg-emerald-600/10 p-4 text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="rounded-[2rem] bg-gradient-to-r from-emerald-700 via-emerald-800 to-slate-900 p-8 shadow-2xl border border-emerald-600/20">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="space-y-3">
                        <span class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-sm text-emerald-100 uppercase tracking-[0.3em]">Seller Dashboard</span>
                        <h1 class="text-4xl font-semibold text-white">Welcome back, {{ auth()->user()->name ?? 'Seller' }}</h1>
                        <p class="max-w-2xl text-sm leading-6 text-emerald-100/80">Track orders, inventory, financials, and seller activity from one place.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('seller.products.add') }}" class="inline-flex items-center justify-center rounded-3xl bg-white text-slate-900 px-5 py-3 font-semibold shadow-lg shadow-emerald-800/30 hover:bg-emerald-100">Add Product</a>
                        <a href="{{ route('seller.income') }}" class="inline-flex items-center justify-center rounded-3xl border border-emerald-300/40 px-5 py-3 text-emerald-200 hover:bg-emerald-500/20">View Income</a>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-[2rem] border border-slate-800 bg-[#102d1c] p-6 shadow-xl">
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Total revenue</p>
                    <p class="mt-4 text-4xl font-semibold text-white">₱{{ number_format($totalSales, 2) }}</p>
                    <p class="mt-3 text-sm text-slate-400">Revenue from products sold through your store.</p>
                </div>
                <div class="rounded-[2rem] border border-slate-800 bg-[#112e20] p-6 shadow-xl">
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Pending orders</p>
                    <p class="mt-4 text-4xl font-semibold text-white">{{ $pendingOrdersCount }}</p>
                    <p class="mt-3 text-sm text-slate-400">Orders waiting for fulfillment.</p>
                </div>
                <div class="rounded-[2rem] border border-slate-800 bg-[#102d1c] p-6 shadow-xl">
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Low stock items</p>
                    <p class="mt-4 text-4xl font-semibold text-white">{{ $lowStockItems }}</p>
                    <p class="mt-3 text-sm text-slate-400">Products under 5 units.</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
                <div class="rounded-[2rem] border border-slate-800 bg-[#102c1f] p-6 shadow-2xl">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <p class="text-sm uppercase tracking-[0.3em] text-emerald-300">Inventory Management</p>
                            <h2 class="mt-3 text-2xl font-semibold text-white">Your product catalog</h2>
                        </div>
                        <a href="{{ route('seller.products') }}" class="text-sm text-emerald-300 hover:text-white">Manage products</a>
                    </div>
                    <div class="overflow-x-auto rounded-[1.5rem] border border-slate-800 bg-[#0f291f] p-4">
                        <table class="min-w-full border-separate border-spacing-y-3 text-left">
                            <thead>
                                <tr class="text-sm uppercase tracking-[0.15em] text-slate-400">
                                    <th class="pb-3 px-4">Product</th>
                                    <th class="pb-3 px-4">Stock</th>
                                    <th class="pb-3 px-4">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventory as $product)
                                    <tr class="rounded-3xl border border-slate-800 bg-[#122f24]">
                                        <td class="px-4 py-4 text-sm font-medium text-white">{{ $product->name }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-300">{{ $product->stock }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-300">₱{{ number_format($product->price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-slate-500">No products found. Add your first product to get started.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-800 bg-[#0d2c1e] p-6 shadow-2xl">
                        <div class="flex items-center justify-between gap-4 mb-5">
                            <div>
                                <p class="text-sm uppercase tracking-[0.3em] text-emerald-300">Financials Overview</p>
                                <h2 class="mt-3 text-2xl font-semibold text-white">Wallet balance</h2>
                            </div>
                            <span class="rounded-full bg-emerald-600/15 px-4 py-2 text-sm text-emerald-200">{{ $bankAccountLinked ? 'Bank linked' : 'No bank account' }}</span>
                        </div>
                        <div class="grid gap-4">
                            <div class="rounded-3xl bg-[#122f25] border border-slate-800 p-5">
                                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Available balance</p>
                                <p class="mt-3 text-3xl font-semibold text-white">₱{{ number_format($totalSales, 2) }}</p>
                            </div>
                            <div class="rounded-3xl bg-[#122f25] border border-slate-800 p-5">
                                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Last 30 days</p>
                                <p class="mt-3 text-3xl font-semibold text-white">₱{{ number_format($totalRevenueLast30Days, 2) }}</p>
                            </div>
                            <div class="rounded-3xl bg-[#122f25] border border-slate-800 p-5">
                                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Recent activity</p>
                                <p class="mt-3 text-xl font-semibold text-white">{{ $recentActivities->count() }} updates</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-800 bg-[#0d2c1e] p-6 shadow-2xl">
                        <div class="flex items-center justify-between gap-4 mb-5">
                            <div>
                                <p class="text-sm uppercase tracking-[0.3em] text-emerald-300">Order status tracking</p>
                                <h2 class="mt-3 text-2xl font-semibold text-white">Recent orders</h2>
                            </div>
                            <a href="{{ route('seller.orders') }}" class="text-sm text-emerald-300 hover:text-white">View all</a>
                        </div>

                        <div class="space-y-4">
                            @forelse($recentOrders->take(4) as $order)
                                <div class="rounded-3xl border border-slate-800 bg-[#122f25] p-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <p class="text-sm text-slate-400">Order #{{ $order->order_number ?? $order->id }}</p>
                                            <p class="mt-2 text-white font-semibold">{{ $order->user->name ?? 'Customer' }}</p>
                                            <p class="text-xs text-slate-500">{{ $order->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-semibold text-white">₱{{ number_format($order->total, 2) }}</p>
                                            <p class="text-sm {{ $order->status === 'pending' ? 'text-amber-300' : 'text-emerald-300' }}">{{ ucfirst($order->status) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-slate-400">No recent orders available.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            @if($topProducts->count() > 0)
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Top Performing Products</h3>
                        <p class="text-sm text-gray-500">Your best-selling items this month</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-yellow-400 to-orange-500 text-white">🏆 Bestsellers</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($topProducts as $index => $product)
                        <div class="relative bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 rounded-2xl p-4 hover:shadow-lg transition-shadow">
                            @if($index === 0)
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg">👑</div>
                            @elseif($index === 1)
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg">🥈</div>
                            @elseif($index === 2)
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg">🥉</div>
                            @endif
                            <div class="text-center mb-3">
                                <div class="w-16 h-16 bg-white rounded-xl mx-auto mb-2 flex items-center justify-center shadow-sm">
                                    <span class="text-2xl">{{ ['🥑', '🍎', '🍊'][$index] ?? '🛍️' }}</span>
                                </div>
                                <h4 class="font-semibold text-gray-900 text-sm">{{ optional($product->product)->name ?? 'Unknown Product' }}</h4>
                            </div>
                            <div class="space-y-2 text-center">
                                <div class="text-xs text-gray-600">Revenue</div>
                                <div class="text-lg font-bold text-green-600">₱{{ number_format($product->total_revenue, 2) }}</div>
                                <div class="text-xs text-gray-500">{{ $product->total_quantity }} units sold</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Recent Transactions</h3>
                        <p class="text-sm text-gray-500">Latest seller orders and payment activity.</p>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium">Date</th>
                                <th class="px-4 py-3 text-left font-medium">Type / Description</th>
                                <th class="px-4 py-3 text-left font-medium">Amount</th>
                                <th class="px-4 py-3 text-left font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td class="px-4 py-4">{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-4">Order Payment #{{ $order->order_number ?? $order->id }}</td>
                                    <td class="px-4 py-4 text-emerald-600 font-semibold">+ ₱{{ number_format($order->total, 2) }}</td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">{{ ucfirst($order->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-gray-500">No transactions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="xl:hidden w-full px-6">
        @include('seller._mobile_nav')
    </div>
</div>
@endsection
