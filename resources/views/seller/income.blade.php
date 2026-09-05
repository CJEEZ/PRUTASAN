@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full min-w-0 gap-2 p-2 sm:gap-6 sm:p-6" style="min-height:70vh;">
    <aside class="hidden md:block w-64 bg-white rounded shadow p-4">
        @include('seller._sidebar')
    </aside>
    <div class="flex-1 space-y-3">
        <div class="w-full min-w-0">
            @include('seller._mobile_nav')
        </div>

        <!-- Header -->
        <div class="rounded-2xl bg-gradient-to-r from-green-600 to-blue-600 p-4 text-white sm:rounded-3xl sm:p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="mb-1 text-2xl font-bold sm:mb-2 sm:text-3xl">Sales Analytics</h1>
                    <p class="text-sm text-green-100 sm:text-base">Track your business performance and revenue insights</p>
                </div>
                <div class="hidden md:block">
                    <svg class="w-16 h-16 text-white/20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="seller-income-metrics grid grid-cols-2 gap-2 sm:gap-3 lg:grid-cols-4 lg:gap-4">
            <div class="min-h-[80px] min-w-0 rounded-xl border border-gray-100 bg-white p-2 shadow-sm sm:min-h-[88px] sm:p-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-600 sm:text-sm">Total Revenue</p>
                        <p class="mt-1 text-xl font-bold text-gray-900 sm:text-2xl">₱{{ number_format($totalSales, 2) }}</p>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-green-100 sm:h-10 sm:w-10">
                        <svg class="h-5 w-5 text-green-600 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="min-h-[80px] min-w-0 rounded-xl border border-gray-100 bg-white p-2 shadow-sm sm:min-h-[88px] sm:p-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-600 sm:text-sm">Total Orders</p>
                        <p class="mt-1 text-xl font-bold text-gray-900 sm:text-2xl">{{ number_format($totalOrders) }}</p>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 sm:h-10 sm:w-10">
                        <svg class="h-5 w-5 text-blue-600 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="min-h-[80px] min-w-0 rounded-xl border border-gray-100 bg-white p-2 shadow-sm sm:min-h-[88px] sm:p-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-600 sm:text-sm">Avg Order Value</p>
                        <p class="mt-1 text-xl font-bold text-gray-900 sm:text-2xl">₱{{ number_format($averageOrderValue, 2) }}</p>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-purple-100 sm:h-10 sm:w-10">
                        <svg class="h-5 w-5 text-purple-600 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="min-h-[80px] min-w-0 rounded-xl border border-gray-100 bg-white p-2 shadow-sm sm:min-h-[88px] sm:p-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-600 sm:text-sm">Revenue Growth</p>
                        <p class="mt-1 text-xl font-bold {{ $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} sm:text-2xl">
                            {{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}%
                        </p>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $revenueGrowth >= 0 ? 'bg-green-100' : 'bg-red-100' }} sm:h-10 sm:w-10">
                        <svg class="h-5 w-5 {{ $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $revenueGrowth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 gap-3 sm:gap-6 lg:grid-cols-2">
            <!-- Monthly Sales Chart -->
            <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm sm:rounded-2xl sm:p-6">
                <div class="mb-3 flex items-center justify-between gap-2 sm:mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 sm:text-lg">Monthly Revenue</h3>
                        <p class="text-xs text-gray-600 sm:text-sm">Last 12 months performance</p>
                    </div>
                    <span class="inline-flex shrink-0 items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 sm:px-3 sm:text-sm">
                        12 Months
                    </span>
                </div>
                <div class="h-44 min-h-0 sm:h-64 sm:min-h-[14rem]">
                    <canvas id="monthlySalesChart" class="block h-full w-full"></canvas>
                </div>
            </div>

            <!-- Weekly Sales Chart -->
            <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm sm:rounded-2xl sm:p-6">
                <div class="mb-3 flex items-center justify-between gap-2 sm:mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 sm:text-lg">Weekly Revenue</h3>
                        <p class="text-xs text-gray-600 sm:text-sm">Last 8 weeks trend</p>
                    </div>
                    <span class="inline-flex shrink-0 items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 sm:px-3 sm:text-sm">
                        8 Weeks
                    </span>
                </div>
                <div class="h-44 min-h-0 sm:h-64 sm:min-h-[14rem]">
                    <canvas id="weeklySalesChart" class="block h-full w-full"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products and Categories -->
        <div class="grid grid-cols-1 gap-3 sm:gap-6 lg:grid-cols-2">
            <!-- Top Selling Products -->
            <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm sm:rounded-2xl sm:p-6">
                <div class="mb-3 flex items-center justify-between sm:mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 sm:text-lg">Top Selling Products</h3>
                        <p class="text-xs text-gray-600 sm:text-sm">Best performing items</p>
                    </div>
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="space-y-4">
                    @forelse($topProducts as $index => $product)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-orange-400 to-pink-400 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->product->name ?? 'Unknown Product' }}</p>
                                    <p class="text-sm text-gray-600">{{ $product->total_quantity }} units sold</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">₱{{ number_format($product->total_revenue, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p>No sales data available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Sales by Category -->
            <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm sm:rounded-2xl sm:p-6">
                <div class="mb-3 flex items-center justify-between sm:mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 sm:text-lg">Revenue by Category</h3>
                        <p class="text-xs text-gray-600 sm:text-sm">Performance across categories</p>
                    </div>
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="space-y-4">
                    @forelse($salesByCategory as $category)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-blue-400 to-purple-400 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr($category['category'], 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $category['category'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $category['quantity'] }} units sold</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">₱{{ number_format($category['revenue'], 2) }}</p>
                                <p class="text-xs text-gray-500">{{ number_format(($category['revenue'] / $totalSales) * 100, 1) }}% of total</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 7V3a2 2 0 012-2z"/>
                            </svg>
                            <p>No category data available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm sm:rounded-2xl sm:p-6">
            <div class="mb-3 flex items-center justify-between gap-2 sm:mb-6">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 sm:text-lg">Recent Sales</h3>
                    <p class="text-xs text-gray-600 sm:text-sm">Latest transactions from the past 30 days</p>
                </div>
                <span class="inline-flex shrink-0 items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800 sm:px-3 sm:text-sm">
                    Last 30 Days
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="whitespace-nowrap px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 sm:px-6 sm:py-3 sm:text-xs">Order</th>
                            <th class="whitespace-nowrap px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 sm:px-6 sm:py-3 sm:text-xs">Customer</th>
                            <th class="whitespace-nowrap px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 sm:px-6 sm:py-3 sm:text-xs">Products</th>
                            <th class="whitespace-nowrap px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 sm:px-6 sm:py-3 sm:text-xs">Amount</th>
                            <th class="whitespace-nowrap px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 sm:px-6 sm:py-3 sm:text-xs">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentSales as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-3 py-2 sm:px-6 sm:py-4">
                                    <div class="text-xs font-medium text-gray-900 sm:text-sm">#{{ $order->order_number ?? $order->id }}</div>
                                    <div class="text-xs text-gray-500 sm:text-sm">{{ ucfirst($order->status) }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-2 sm:px-6 sm:py-4">
                                    <div class="text-xs font-medium text-gray-900 sm:text-sm">{{ $order->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 sm:text-sm">{{ $order->user->email ?? '' }}</div>
                                </td>
                                <td class="px-3 py-2 sm:px-6 sm:py-4">
                                    <div class="text-xs text-gray-900 sm:text-sm">
                                        @foreach($order->items as $item)
                                            <div class="mb-1">{{ $item->product->name ?? 'Unknown' }} ({{ $item->quantity }})</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-2 text-xs font-medium text-gray-900 sm:px-6 sm:py-4 sm:text-sm">
                                    ₱{{ number_format($order->total, 2) }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-2 text-xs text-gray-500 sm:px-6 sm:py-4 sm:text-sm">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-500 sm:px-6 sm:py-12">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p>No recent sales in the last 30 days</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payout History (Legacy Section) -->
        @if(count($payouts) > 0)
        <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm sm:rounded-2xl sm:p-6">
            <div class="mb-3 flex items-center justify-between sm:mb-6">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 sm:text-lg">Payout History</h3>
                    <p class="text-xs text-gray-600 sm:text-sm">Your withdrawal records</p>
                </div>
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="whitespace-nowrap px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 sm:px-6 sm:py-3 sm:text-xs">Date</th>
                            <th class="whitespace-nowrap px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 sm:px-6 sm:py-3 sm:text-xs">Amount</th>
                            <th class="whitespace-nowrap px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 sm:px-6 sm:py-3 sm:text-xs">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payouts as $payout)
                        <tr>
                            <td class="whitespace-nowrap px-3 py-2 text-xs font-medium text-gray-900 sm:px-6 sm:py-4 sm:text-sm">{{ $payout->created_at->format('Y-m-d') }}</td>
                            <td class="whitespace-nowrap px-3 py-2 text-xs text-gray-900 sm:px-6 sm:py-4 sm:text-sm">₱{{ number_format($payout->amount, 2) }}</td>
                            <td class="whitespace-nowrap px-3 py-2 sm:px-6 sm:py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $payout->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Chart.js for analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Sales Chart
    const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
    const monthlySalesData = @json($monthlySales);

    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlySalesData.map(item => item.month),
            datasets: [{
                label: 'Revenue',
                data: monthlySalesData.map(item => item.amount),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Weekly Sales Chart
    const weeklyCtx = document.getElementById('weeklySalesChart').getContext('2d');
    const weeklySalesData = @json($weeklySales);

    new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: weeklySalesData.map(item => item.week),
            datasets: [{
                label: 'Revenue',
                data: weeklySalesData.map(item => item.amount),
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
