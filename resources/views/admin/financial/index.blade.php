@extends('layouts.admin')

@section('page_title', 'Financial & Revenue')
@section('page_subtitle', 'Business Analytics & Revenue Reports')

@section('content')
<!-- Revenue Stats -->
<div class="mb-6 grid grid-cols-2 gap-2 sm:gap-3 md:grid-cols-4">
    <div class="stat-card p-3 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Revenue</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 sm:text-2xl">₱{{ number_format($totalRevenue, 0) }}</p>
                <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up mr-1"></i>All time</p>
            </div>
                <div class="rounded-lg bg-green-100 p-2">
                    <i class="fas fa-coins text-lg text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-3 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">This Month</p>
                <p class="mt-1 text-xl font-bold text-gray-900 sm:text-2xl">₱{{ number_format($monthlyRevenue, 0) }}</p>
                <p class="text-xs text-gray-600 mt-1">{{ now()->format('F Y') }}</p>
            </div>
                <div class="rounded-lg bg-blue-100 p-2">
                    <i class="fas fa-calendar text-lg text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-3 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Orders</p>
                <p class="mt-1 text-xl font-bold text-gray-900 sm:text-2xl">{{ $totalOrders }}</p>
                <p class="text-xs text-gray-600 mt-1">Completed orders</p>
            </div>
                <div class="rounded-lg bg-purple-100 p-2">
                    <i class="fas fa-shopping-cart text-lg text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-3 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Avg Order Value</p>
                <p class="mt-1 text-xl font-bold text-gray-900 sm:text-2xl">₱{{ number_format($avgOrderValue, 0) }}</p>
                <p class="text-xs text-gray-600 mt-1">Per transaction</p>
            </div>
                <div class="rounded-lg bg-orange-100 p-2">
                    <i class="fas fa-chart-line text-lg text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-2 gap-3 mb-6 sm:gap-4 sm:mb-8">
    <!-- Revenue Chart -->
    <div class="stat-card p-2">
        <h3 class="text-sm font-semibold text-gray-900 mb-2">Monthly Revenue Trend</h3>
        <canvas id="revenueChart" class="w-full h-24"></canvas>
    </div>

    <!-- Order Distribution -->
    <div class="stat-card p-2">
        <h3 class="text-sm font-semibold text-gray-900 mb-2">Revenue Distribution</h3>
        <div class="max-w-sm mx-auto">
            <canvas id="distributionChart" class="w-full h-24"></canvas>
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
                    <th class="text-left py-2 px-3 text-xs text-gray-600 font-semibold">Order ID</th>
                    <th class="text-left py-2 px-3 text-xs text-gray-600 font-semibold">Customer</th>
                    <th class="text-right py-2 px-3 text-xs text-gray-600 font-semibold">Amount</th>
                    <th class="text-center py-2 px-3 text-xs text-gray-600 font-semibold">Date</th>
                    <th class="text-center py-2 px-3 text-xs text-gray-600 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentOrders as $order)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="py-2 px-3 text-xs font-semibold text-green-600">
                        <a href="{{ route('admin.orders.show', $order->id) }}">
                            #{{ $order->order_number }}
                        </a>
                    </td>
                    <td class="py-2 px-3 text-xs text-gray-900">{{ $order->user->name ?? 'N/A' }}</td>
                    <td class="py-2 px-3 text-xs text-right font-bold text-gray-900">
                        ₱{{ number_format($order->total, 2) }}
                    </td>
                    <td class="py-2 px-3 text-center text-gray-600 text-xs">
                        {{ $order->created_at->format('M d, Y H:i') }}
                    </td>
                    <td class="py-2 px-3 text-center">
                        <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded-full
                            @if($order->status == 'delivered') bg-green-100 text-green-700
                            @elseif($order->status == 'shipped') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500 text-sm">
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
                    legend: {
                        position: 'bottom',
                        labels: {
                            generateLabels(chart) {
                                const dataset = chart.data.datasets[0];
                                const total = dataset.data.reduce((sum, value) => sum + value, 0);

                                return chart.data.labels.map((label, index) => ({
                                    text: `${label}: ${((dataset.data[index] / total) * 100).toFixed(0)}%`,
                                    fillStyle: dataset.backgroundColor[index],
                                    strokeStyle: dataset.borderColor,
                                    lineWidth: dataset.borderWidth,
                                    hidden: !chart.getDataVisibility(index),
                                    index,
                                }));
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                const values = context.dataset.data;
                                const total = values.reduce((sum, value) => sum + value, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);

                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
