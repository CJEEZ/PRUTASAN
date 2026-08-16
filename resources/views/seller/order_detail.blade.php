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
                <div class="mt-2 text-sm">Status: <span class="font-semibold">{{ ucfirst($order->status) }}</span></div>
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
            <div class="mb-4">
                <form method="POST" action="{{ route('seller.orders.update', $order) }}">
                    @csrf
                    @method('PATCH')
                    <label class="text-xs text-gray-600">Update Status</label>
                    <select name="status" class="border rounded px-2 py-1 text-sm">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="ml-2 px-3 py-1 bg-orange-600 text-white rounded">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
