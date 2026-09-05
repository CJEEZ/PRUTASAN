@extends('layouts.admin')

@section('page_title', 'Dashboard Overview')
@section('page_subtitle', 'System Statistics & Key Metrics')

@section('content')
<!-- Stats Grid -->
<div class="mb-4 grid grid-cols-2 gap-1 sm:mb-6 sm:gap-3 md:grid-cols-2 lg:grid-cols-4">
    <div class="stat-card p-2 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium sm:text-sm">Total Orders</p>
                <p class="mt-1 text-lg font-bold text-gray-900 sm:text-2xl">{{ $totalOrders }}</p>
                <p class="mt-0.5 text-[10px] text-green-600 sm:text-xs"><i class="fas fa-arrow-up mr-1"></i>+12% from last month</p>
            </div>
            <div class="rounded-lg bg-blue-100 p-1.5 sm:p-2">
                <i class="fas fa-shopping-cart text-base text-blue-600 sm:text-lg"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-2 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium sm:text-sm">Total Revenue</p>
                <p class="mt-1 text-lg font-bold text-gray-900 sm:text-2xl">₱{{ number_format($totalRevenue, 0) }}</p>
                <p class="mt-0.5 text-[10px] text-green-600 sm:text-xs"><i class="fas fa-arrow-up mr-1"></i>+8% from last month</p>
            </div>
            <div class="rounded-lg bg-green-100 p-1.5 sm:p-2">
                <i class="fas fa-coins text-base text-green-600 sm:text-lg"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-2 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium sm:text-sm">Pending Orders</p>
                <p class="mt-1 text-lg font-bold text-yellow-600 sm:text-2xl">{{ $pendingOrders }}</p>
                <p class="mt-0.5 text-[10px] text-yellow-600 sm:text-xs"><i class="fas fa-clock mr-1"></i>Need Attention</p>
            </div>
            <div class="rounded-lg bg-yellow-100 p-1.5 sm:p-2">
                <i class="fas fa-hourglass-half text-base text-yellow-600 sm:text-lg"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-2 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium sm:text-sm">Total Customers</p>
                <p class="mt-1 text-lg font-bold text-gray-900 sm:text-2xl">{{ $totalCustomers }}</p>
                <p class="mt-0.5 text-[10px] text-green-600 sm:text-xs"><i class="fas fa-arrow-up mr-1"></i>+5 new this week</p>
            </div>
            <div class="rounded-lg bg-purple-100 p-1.5 sm:p-2">
                <i class="fas fa-users text-base text-purple-600 sm:text-lg"></i>
            </div>
        </div>
    </div>
</div>

        <!-- Additional Stats -->
        <div class="mb-6 grid grid-cols-2 gap-2 sm:gap-3 md:grid-cols-3">
            <!-- Completed Orders -->
            <div class="rounded-lg bg-white p-3 shadow sm:p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Completed Orders</p>
                        <p class="mt-1 text-lg font-bold text-gray-900 sm:text-xl">{{ $completedOrders }}</p>
                    </div>
                    <div class="rounded-lg bg-green-100 p-2">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="rounded-lg bg-white p-3 shadow sm:p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Customers</p>
                        <p class="mt-1 text-lg font-bold text-gray-900 sm:text-xl">{{ $totalCustomers }}</p>
                    </div>
                    <div class="rounded-lg bg-indigo-100 p-2">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20H1v-2a6 6 0 016-6v0"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Order Status Summary -->
            <div class="col-span-2 rounded-lg bg-white p-3 shadow sm:p-4 md:col-span-1">
                <p class="mb-3 text-xs font-medium text-gray-500">Order Status Breakdown</p>
                <div class="grid grid-cols-2 gap-x-3 gap-y-1 text-[10px] sm:text-xs">
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
        <div class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-lg bg-white p-4 shadow transition hover:shadow-lg">
                <h3 class="mb-1 text-base font-bold text-gray-900">Product Management</h3>
                <p class="mb-3 text-xs text-gray-600">Add, edit, or remove products from your catalog</p>
                <a href="{{ route('admin.products.index') }}" class="text-orange-600 hover:text-orange-700 font-medium">
                    Manage Products →
                </a>
            </div>

            <div class="rounded-lg bg-white p-4 shadow transition hover:shadow-lg">
                <h3 class="mb-1 text-base font-bold text-gray-900">Order Management</h3>
                <p class="mb-3 text-xs text-gray-600">View and update order statuses</p>
                <a href="{{ route('admin.orders.index') }}" class="text-orange-600 hover:text-orange-700 font-medium">
                    Manage Orders →
                </a>
            </div>

            <div class="rounded-lg bg-white p-4 shadow transition hover:shadow-lg">
                <h3 class="mb-1 text-base font-bold text-gray-900">Customer Inquiries</h3>
                <p class="mb-3 text-xs text-gray-600">View customer messages and communication</p>
                <a href="{{ route('admin.inquiries.index') }}" class="text-orange-600 hover:text-orange-700 font-medium">
                    View Inquiries →
                </a>
            </div>


        </div>
    </div>
</div>
@endsection
