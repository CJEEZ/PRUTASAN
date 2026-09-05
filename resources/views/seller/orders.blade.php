@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full p-6 gap-6" style="min-height:70vh;">
    <aside class="hidden md:block w-64 bg-white rounded shadow p-4">
        @include('seller._sidebar')
    </aside>

    <div class="flex-1 space-y-3">
        <div class="md:hidden">
            @include('seller._mobile_nav')
        </div>

        <div class="bg-white rounded shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Orders</h2>

            @forelse($orders as $order)
                <div class="border rounded p-4 mb-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-sm text-gray-500">Order #{{ $order->order_number ?? $order->id }} • {{ $order->created_at->format('Y-m-d') }}</div>
                            <div class="font-semibold">{{ $order->full_name ?? 'Customer' }} — ₱{{ number_format($order->total,2) }}</div>
                        </div>
                        <div class="text-right">
                            @php
                                $sellerStatusLabels = ['pending' => 'Pending', 'preparing' => 'Preparing', 'ready_for_pickup' => 'To Ship', 'cancelled' => 'Cancelled', 'in_transit' => 'In Transit', 'out_for_delivery' => 'Out for Delivery', 'delivered' => 'Delivered'];
                            @endphp
                            <div class="text-sm">Status: <span class="font-semibold">{{ $sellerStatusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}</span></div>
                            @if($order->shipment)
                                <div class="mt-2">Tracking: <a href="{{ route('seller.orders.track', $order) }}" class="text-orange-600 hover:underline">{{ $order->shipment->tracking_number }}</a></div>
                            @else
                                <div class="mt-2">No tracking yet</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('seller.orders.detail', $order->id) }}" class="px-4 py-2 bg-orange-600 text-white rounded">View Details</a>
                        @if(! $order->shipment || ! in_array($order->shipment->status, ['in_transit', 'out_for_delivery', 'delivered'], true))
                            <form action="{{ route('seller.orders.status', $order->id) }}" method="POST" class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-[1fr_auto] sm:items-end">
                                @csrf @method('PATCH')
                                <div>
                                    <label class="text-xs text-gray-600">Seller action status</label>
                                    <select name="status" class="w-full rounded border border-gray-300 p-2 text-sm">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending · New order</option>
                                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing · Accepted and packing</option>
                                        <option value="ready_for_pickup" {{ $order->status === 'ready_for_pickup' ? 'selected' : '' }}>To Ship · Rider can collect</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled · Unable to fulfill</option>
                                    </select>
                                </div>
                                <button class="min-h-[42px] rounded bg-orange-600 px-4 py-2 text-sm font-semibold text-white">Update status</button>
                            </form>
                        @else
                            <p class="mt-3 text-xs text-gray-500">The rider now controls delivery updates.</p>
                        @endif
                        @if(! $order->shipment)
                            <form action="{{ route('seller.orders.ship', $order) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-end">
                                @csrf
                                <div>
                                    <label class="text-xs text-gray-600">Tracking Number</label>
                                    <input name="tracking_number" class="w-full border rounded p-2" required>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Carrier</label>
                                    <input name="carrier" class="w-full border rounded p-2">
                                </div>
                                <div>
                                    <button class="w-full sm:w-auto px-4 py-2 bg-orange-600 text-white rounded">Add Tracking</button>
                                </div>
                            </form>
                        @else
                            <a href="{{ route('seller.orders.track', $order) }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded">View Tracking</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-gray-500">No orders found.</div>
            @endforelse

            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>
</div>
@endsection
