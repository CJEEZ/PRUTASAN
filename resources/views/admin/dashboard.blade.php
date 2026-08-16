@extends('layouts.admin')

@section('page_title', 'Dashboard Overview')
@section('page_subtitle', 'System Statistics & Key Metrics')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Orders</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ $totalOrders }}</p>
                <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up mr-1"></i>+12% from last month</p>
            </div>
            <div class="bg-blue-100 p-4 rounded-lg">
                <i class="fas fa-shopping-cart text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Revenue</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">₱{{ number_format($totalRevenue, 0) }}</p>
                <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up mr-1"></i>+8% from last month</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg">
                <i class="fas fa-coins text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Pending Orders</p>
                <p class="text-2xl sm:text-3xl font-bold text-yellow-600 mt-2">{{ $pendingOrders }}</p>
                <p class="text-xs text-yellow-600 mt-1"><i class="fas fa-clock mr-1"></i>Need Attention</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg">
                <i class="fas fa-hourglass-half text-2xl text-yellow-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Customers</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ $totalCustomers }}</p>
                <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up mr-1"></i>+5 new this week</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <i class="fas fa-users text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Completed Orders -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Completed Orders</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-2">{{ $completedOrders }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Customers</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-2">{{ $totalCustomers }}</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20H1v-2a6 6 0 016-6v0"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Order Status Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-500 text-sm font-medium mb-4">Order Status Breakdown</p>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pending:</span>
                        <span class="font-semibold text-yellow-600">{{ $ordersByStatus['pending'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Confirmed:</span>
                        <span class="font-semibold text-blue-600">{{ $ordersByStatus['confirmed'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipped:</span>
                        <span class="font-semibold text-purple-600">{{ $ordersByStatus['shipped'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivered:</span>
                        <span class="font-semibold text-green-600">{{ $ordersByStatus['delivered'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cancelled:</span>
                        <span class="font-semibold text-red-600">{{ $ordersByStatus['cancelled'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Return Requested:</span>
                        <span class="font-semibold text-orange-600">{{ $ordersByStatus['return_requested'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Recent Orders</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-3 sm:px-6 py-4 whitespace-normal sm:whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ $order->order_number }}
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-normal sm:whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ optional($order->user)->name ?? 'Unknown Customer' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ optional($order->user)->email ?? 'No email available' }}
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-normal sm:whitespace-nowrap text-sm text-gray-600">
                                    {{ $order->items->count() }} item(s)
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-normal sm:whitespace-nowrap text-sm text-gray-600">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-normal sm:whitespace-nowrap text-sm font-semibold text-orange-600">
                                    ₱{{ number_format($order->total, 2) }}
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-normal sm:whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-normal sm:whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-orange-600 hover:text-orange-700 font-medium">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No orders yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Management Links -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Product Management</h3>
                <p class="text-gray-600 text-sm mb-4">Add, edit, or remove products from your catalog</p>
                <a href="{{ route('admin.products.index') }}" class="text-orange-600 hover:text-orange-700 font-medium">
                    Manage Products →
                </a>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Order Management</h3>
                <p class="text-gray-600 text-sm mb-4">View and update order statuses</p>
                <a href="{{ route('admin.orders.index') }}" class="text-orange-600 hover:text-orange-700 font-medium">
                    Manage Orders →
                </a>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Customer Inquiries</h3>
                <p class="text-gray-600 text-sm mb-4">View customer messages and communication</p>
                <a href="{{ route('admin.inquiries.index') }}" class="text-orange-600 hover:text-orange-700 font-medium">
                    View Inquiries →
                </a>
            </div>


        </div>
    </div>
</div>
@endsection
