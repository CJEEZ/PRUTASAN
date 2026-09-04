@extends('layouts.app')

@section('content')
<style>
    header { display: none; }
</style>

<div class="min-h-screen bg-gray-100">
    <div class="w-full mx-auto px-4 sm:px-8 lg:px-12 py-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-orange-600 hover:text-orange-700 inline-flex items-center text-sm font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-3">Order #{{ $order->order_number }}</h1>
                <p class="text-sm text-gray-600 mt-1">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div class="flex flex-col gap-3">
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($order->status) }}
                </span>
                <form method="POST" action="{{ route('admin.orders.update', $order->id) }}" class="flex gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="px-3 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready_for_pickup" {{ $order->status === 'ready_for_pickup' ? 'selected' : '' }}>Ready for pickup</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="in_transit" {{ $order->status === 'in_transit' ? 'selected' : '' }}>In transit</option>
                        <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Out for delivery</option>
                        <option value="to_receive" {{ $order->status === 'to_receive' ? 'selected' : '' }}>To receive</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="return_requested" {{ $order->status === 'return_requested' ? 'selected' : '' }}>Return Requested</option>
                    </select>
                    <button type="submit" class="px-4 py-1 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold text-sm whitespace-nowrap">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Items</h2>
                    <div class="text-sm text-gray-600">{{ $order->items->count() }} item(s)</div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $item->product->name ?? 'Product deleted' }}</div>
                                        @if($item->product)
                                            <div class="text-xs text-gray-500">{{ $item->product->unit }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3">₱{{ number_format($item->price, 2) }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Customer</h2>
                    <p class="text-sm font-semibold text-gray-900">{{ $order->full_name ?? $order->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
                    @if($order->phone)
                        <p class="text-sm text-gray-600 mt-1">{{ $order->phone }}</p>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Shipping</h2>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        <div>{{ $order->street_address }}</div>
                        <div>{{ $order->barangay }}</div>
                        <div>{{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Totals</h2>
                    <div class="space-y-2 text-sm text-gray-700">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>₱{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span>₱{{ number_format($order->shipping, 2) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-900 text-base pt-2 border-t">
                            <span>Total</span>
                            <span>₱{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Payment Method</h2>
                    <div class="space-y-4">
                        <div class="inline-block px-4 py-2 rounded-lg text-sm font-semibold
                            @if($order->payment_method === 'cod') bg-blue-100 text-blue-800
                            @elseif($order->payment_method === 'gcash') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $order->payment_method === 'cod' ? 'Cash on Delivery (COD)' : ($order->payment_method === 'gcash' ? 'GCash' : ucfirst($order->payment_method)) }}
                        </div>

                        <!-- Payment Status Badge -->
                        @if($order->payment_method === 'gcash')
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                                <div class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                    @if($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->payment_status === 'paid') bg-green-100 text-green-800
                                    @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($order->payment_status ?? 'pending') }}
                                </div>
                            </div>

                            <!-- GCash Payment Form -->
                            @if($order->payment_status !== 'paid')
                                <form method="POST" action="{{ route('admin.orders.update', $order->id) }}" class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-3">
                                        <label for="gcash_reference" class="block text-sm font-medium text-gray-700 mb-1">GCash Reference Number</label>
                                        <input type="text" name="gcash_reference" id="gcash_reference"
                                            value="{{ old('gcash_reference', $order->gcash_reference) }}"
                                            placeholder="Enter GCash transaction ID"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                    <div class="mb-3">
                                        <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Mark Payment As</label>
                                        <select name="payment_status" id="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="paid">Paid</option>
                                            <option value="failed">Failed</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="status" value="{{ $order->status }}">
                                    <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold text-sm">
                                        Confirm GCash Payment
                                    </button>
                                </form>
                            @else
                                <div class="mt-4 p-4 bg-green-50 rounded-lg">
                                    <p class="text-sm text-green-800 mb-2"><strong>Payment Confirmed</strong></p>
                                    <p class="text-sm text-gray-700">Reference: <strong>{{ $order->gcash_reference ?? 'N/A' }}</strong></p>
                                    <p class="text-sm text-gray-600">Confirmed on: {{ $order->payment_confirmed_at?->format('M d, Y h:i A') }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
