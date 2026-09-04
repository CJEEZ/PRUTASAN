@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full p-6 gap-6" style="min-height:70vh;">
    <aside class="hidden md:block w-64 bg-white rounded shadow p-4">
        @include('seller._sidebar')
    </aside>
    <div class="md:hidden w-full">
        @include('seller._mobile_nav')
    </div>
    <div class="flex-1">
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Order Details</h2>
            <div class="mb-4">
                <div class="text-sm text-gray-500">Order #{{ $order->order_number ?? $order->id }} • {{ $order->created_at->format('Y-m-d') }}</div>
                <div class="font-semibold">{{ $order->full_name ?? 'Customer' }} — ₱{{ number_format($order->total,2) }}</div>
                @php
                    $sellerStatusLabels = ['pending' => 'Pending', 'preparing' => 'Preparing', 'ready_for_pickup' => 'To Ship', 'cancelled' => 'Cancelled', 'in_transit' => 'In Transit', 'out_for_delivery' => 'Out for Delivery', 'delivered' => 'Delivered'];
                @endphp
                <div class="mt-2 text-sm">Status: <span class="font-semibold">{{ $sellerStatusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}</span></div>
            </div>
            <div class="mb-4">
                <h3 class="text-sm font-semibold mb-2">Items</h3>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-2 px-3">Product</th>
                            <th class="py-2 px-3">Qty</th>
                            <th class="py-2 px-3">Price</th>
                            <th class="py-2 px-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="py-2 px-3">{{ $item->product->name }}</td>
                            <td class="py-2 px-3">{{ $item->quantity }}</td>
                            <td class="py-2 px-3">₱{{ number_format($item->price,2) }}</td>
                            <td class="py-2 px-3">₱{{ number_format($item->subtotal,2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mb-4">
                <h3 class="text-sm font-semibold mb-2">Shipping</h3>
                <div class="text-sm text-gray-700">{{ $order->street_address }}, {{ $order->barangay }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</div>
                <div class="text-sm text-gray-700">Phone: {{ $order->phone }}</div>
            </div>
            <div class="mb-4">
                <h3 class="text-sm font-semibold mb-2">Shipment</h3>
                @if($order->shipment)
                    <div>Tracking #: {{ $order->shipment->tracking_number }}</div>
                    <div>Carrier: {{ $order->shipment->carrier }}</div>
                    <div>Status: {{ ucfirst($order->shipment->status) }}</div>
                    <div>Shipped At: {{ $order->shipment->shipped_at ? $order->shipment->shipped_at->format('Y-m-d') : '-' }}</div>
                @else
                    <div>No shipment info yet.</div>
                @endif
            </div>
            <div class="mb-4 rounded border border-blue-100 bg-blue-50 p-3 text-sm text-blue-800">
                Seller action status controls whether an order is pending, preparing, To Ship, or cancelled. After a rider claims it, the rider controls delivery status and live location.
            </div>
            @if(! $order->shipment || ! in_array($order->shipment->status, ['in_transit', 'out_for_delivery', 'delivered'], true))
                <form method="POST" action="{{ route('seller.orders.status', $order->id) }}" class="rounded border border-orange-100 bg-orange-50 p-4">
                    @csrf @method('PATCH')
                    <label class="text-xs font-semibold text-gray-700">Seller action status</label>
                    <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                        <select name="status" class="min-h-[42px] flex-1 rounded border border-gray-300 px-3 text-sm">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending · New order</option>
                            <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing · Accepted and packing</option>
                            <option value="ready_for_pickup" {{ $order->status === 'ready_for_pickup' ? 'selected' : '' }}>To Ship · Rider can collect</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled · Unable to fulfill</option>
                        </select>
                        <button class="min-h-[42px] rounded bg-orange-600 px-4 py-2 text-sm font-semibold text-white">Save action status</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
