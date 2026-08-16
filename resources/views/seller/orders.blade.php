@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full p-6 gap-6" style="min-height:70vh;">
    <aside class="hidden md:block w-64 bg-white rounded shadow p-4">
        @include('seller._sidebar')
    </aside>

    <div class="flex-1 space-y-6">
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
                            <div class="text-sm">Status: <span class="font-semibold">{{ ucfirst($order->status) }}</span></div>
                            @if($order->shipment)
                                <div class="mt-2">Tracking: <a href="{{ route('seller.orders.track', $order) }}" class="text-orange-600 hover:underline">{{ $order->shipment->tracking_number }}</a></div>
                            @else
                                <div class="mt-2">No tracking yet</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('seller.orders.detail', $order->id) }}" class="px-4 py-2 bg-orange-600 text-white rounded">View Details</a>
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
