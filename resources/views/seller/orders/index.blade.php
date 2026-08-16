@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Manage Orders</h1>

    <div class="bg-white shadow rounded p-4">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="text-gray-600">
                    <th class="py-2">Order #</th>
                    <th class="py-2">Customer</th>
                    <th class="py-2">Total</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Tracking</th>
                    <th class="py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr class="border-t">
                    <td class="py-2">{{ $order->order_number ?? $order->id }}</td>
                    <td class="py-2">{{ $order->full_name }}</td>
                    <td class="py-2">₱{{ number_format($order->total, 2) }}</td>
                    <td class="py-2">{{ ucfirst($order->status) }}</td>
                    <td class="py-2">{{ $order->tracking_number ?? '—' }} <br><span class="text-xs text-gray-500">{{ $order->tracking_status ?? '' }}</span></td>
                    <td class="py-2"><a href="{{ route('seller.orders.show', $order) }}" class="text-orange-600 font-semibold">Manage</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
