@extends('layouts.admin')

@section('page_title', 'Financial & Revenue')
@section('page_subtitle', 'Business Analytics & Revenue Reports')

@section('content')
<!-- Revenue Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Revenue</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">₱{{ number_format($totalRevenue, 0) }}</p>
                <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up mr-1"></i>All time</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg">
                <i class="fas fa-coins text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">This Month</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">₱{{ number_format($monthlyRevenue, 0) }}</p>
                <p class="text-xs text-gray-600 mt-1">{{ now()->format('F Y') }}</p>
            </div>
            <div class="bg-blue-100 p-4 rounded-lg">
                <i class="fas fa-calendar text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Orders</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalOrders }}</p>
                <p class="text-xs text-gray-600 mt-1">Completed orders</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <i class="fas fa-shopping-cart text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Avg Order Value</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">₱{{ number_format($avgOrderValue, 0) }}</p>
                <p class="text-xs text-gray-600 mt-1">Per transaction</p>
            </div>
            <div class="bg-orange-100 p-4 rounded-lg">
                <i class="fas fa-chart-line text-2xl text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue Chart -->
    <div class="stat-card">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Revenue Trend</h3>
        <canvas id="revenueChart"></canvas>
    </div>

    <!-- Order Distribution -->
    <div class="stat-card">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Distribution</h3>
        <div class="max-w-sm mx-auto">
            <canvas id="distributionChart" class="w-full h-72"></canvas>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="stat-card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
        <a href="{{ route('admin.orders.index') }}" class="text-green-600 hover:text-green-700 text-sm font-semibold">
            View All →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Order ID</th>
                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Customer</th>
                    <th class="text-right py-4 px-6 text-gray-600 font-semibold">Amount</th>
                    <th class="text-center py-4 px-6 text-gray-600 font-semibold">Date</th>
                    <th class="text-center py-4 px-6 text-gray-600 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentOrders as $order)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="py-4 px-6 font-semibold text-green-600">
                        <a href="{{ route('admin.orders.show', $order->id) }}">
                            #{{ $order->order_number }}
                        </a>
                    </td>
                    <td class="py-4 px-6 text-gray-900">{{ $order->user->name ?? 'N/A' }}</td>
                    <td class="py-4 px-6 text-right font-bold text-gray-900">
                        ₱{{ number_format($order->total, 2) }}
                    </td>
                    <td class="py-4 px-6 text-center text-gray-600 text-sm">
                        {{ $order->created_at->format('M d, Y H:i') }}
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                            @if($order->status == 'delivered') bg-green-100 text-green-700
                            @elseif($order->status == 'shipped') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-500">
                        No transactions yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Revenue',
                    data: [25000, 30000, 28000, 35000, 40000, 38000, 42000, 45000, 43000, 48000, 50000, 52000],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Distribution Chart
    const distributionCtx = document.getElementById('distributionChart');
    if (distributionCtx) {
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Platform Revenue', 'Seller Commission'],
                datasets: [{
                    data: [70, 30],
                    backgroundColor: ['#10B981', '#EF4444'],
                    borderColor: '#fff',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
</script>
@endsection
