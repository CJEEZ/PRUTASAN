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
            <h2 class="text-xl font-semibold mb-4">Shipment Tracking</h2>

            <div class="mb-4 text-sm text-gray-500">
                Order #{{ $order->order_number ?? $order->id }} • {{ $order->created_at->format('Y-m-d') }}
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="bg-gray-50 rounded p-4 border">
                    <h3 class="font-semibold mb-2">Order Details</h3>
                    <div>Customer: {{ $order->full_name ?? 'Customer' }}</div>
                    <div>Total: ₱{{ number_format($order->total, 2) }}</div>
                    <div>Status: {{ ucfirst($order->status) }}</div>
                </div>
                <div class="bg-gray-50 rounded p-4 border">
                    <h3 class="font-semibold mb-2">Delivery Address</h3>
                    <div>{{ $order->street_address }}, {{ $order->barangay }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</div>
                    <div class="mt-2">Phone: {{ $order->phone }}</div>
                </div>
            </div>

            <div class="mt-6 bg-gray-50 rounded p-4 border">
                <h3 class="font-semibold mb-3">Shipment Information</h3>
                @if($shipment)
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div><span class="font-semibold">Tracking #:</span> {{ $shipment->tracking_number }}</div>
                        <div><span class="font-semibold">Carrier:</span> {{ $shipment->carrier ?? '-' }}</div>
                        <div><span class="font-semibold">Status:</span> {{ ucfirst($shipment->status) }}</div>
                        <div><span class="font-semibold">Shipped At:</span> {{ $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d') : '-' }}</div>
                    </div>
                @else
                    <div class="text-gray-600">No shipment record was found for this order.</div>
                @endif
            </div>

            <div class="mt-6">
                <a href="{{ route('seller.orders.detail', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Back to Order</a>
                <a href="{{ route('seller.shipments') }}" class="inline-flex items-center px-4 py-2 ml-2 bg-orange-600 text-white rounded hover:bg-orange-700">View All Shipments</a>
            </div>
        </div>
    </div>
</div>
@endsection

