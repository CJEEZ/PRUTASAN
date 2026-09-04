@extends('layouts.app')

@section('content')
<style>
    header { display: none; }
</style>
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="w-full mx-auto px-4 sm:px-8 lg:px-12 py-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                        <img src="{{ asset('ORNOSFARM_LOGOS.png') }}" alt="Ornos Farm" class="h-16 md:h-20 w-auto mr-3 object-contain">
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Seller Dashboard</h1>
                        <p class="mt-2 text-sm text-gray-600">Welcome back, {{ Auth::user()->name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-3 md:mt-0">
                    <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition">
                        <i class="fas fa-user-circle text-xl mr-2"></i>
                        <span class="text-sm font-semibold">Profile</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center">
                            <i class="fas fa-sign-out-alt text-lg mr-2"></i>
                            <span class="text-sm font-semibold">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full mx-auto px-4 sm:px-8 lg:px-12 py-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Orders -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Orders</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalOrders }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Revenue</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">₱{{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Pending Orders</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $pendingOrders }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Products</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalProducts }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10"></path>
                        </svg>
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
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $completedOrders }}</p>
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
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $totalCustomers }}</p>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ $order->order_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $order->items->count() }} item(s)
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-orange-600">
                                    ₱{{ number_format($order->total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
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
                <p class="text-gray-600 text-sm mb-4">View customer details and communication</p>
                <a href="{{ route('home') }}" class="text-orange-600 hover:text-orange-700 font-medium">
                    View Customers →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
