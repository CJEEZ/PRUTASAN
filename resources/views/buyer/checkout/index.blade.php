@extends('layouts.app')

@section('content')

{{-- Your main product page content goes here, rendered underneath the modal overlay --}}

{{-- Checkout Modal Overlay --}}
<div class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">

        {{-- Modal Header --}}
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-900">Checkout</h3>
            <button type="button" onclick="document.getElementById('checkout-modal').remove()"
                    class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <p class="text-sm text-gray-500 mt-2 mb-4">
            Please provide your shipping address and contact information
        </p>

        {{-- Checkout Form --}}
        <form action="{{ route('checkout.place_order') }}" method="POST">
            @csrf

            {{-- Checkout inputs --}}
            <div class="mb-4">
                <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $lastAddress?->full_name) }}"
                    placeholder="Juan Dela Cruz" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div class="mb-4">
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $lastAddress?->phone_number) }}"
                    placeholder="09123456789" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div class="mb-4">
                <label for="street_address" class="block text-sm font-medium text-gray-700">Street Address *</label>
                <input type="text" name="street_address" id="street_address" value="{{ old('street_address', $lastAddress?->street_address) }}"
                    placeholder="House/Unit No., Building, Street Name" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div class="mb-4">
                <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay *</label>
                <input type="text" name="barangay" id="barangay" value="{{ old('barangay', $lastAddress?->barangay) }}"
                    placeholder="Enter barangay" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            {{-- City/Province/Postal Code Section --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="city_municipality" class="block text-sm font-medium text-gray-700">City/Municipality *</label>
                    <input type="text" name="city_municipality" id="city_municipality" value="{{ old('city_municipality', $lastAddress?->city_municipality) }}"
                        placeholder="Victoria" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                </div>

                <div class="mb-4">
                    <label for="province" class="block text-sm font-medium text-gray-700">Province *</label>
                    <input type="text" name="province" id="province" value="{{ old('province', $lastAddress?->province) }}"
                        placeholder="Oriental Mindoro" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
            </div>

            <div class="mb-4">
                <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code *</label>
                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $lastAddress?->postal_code) }}"
                    placeholder="5205" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            {{-- Total Amount Display --}}
            <div class="mt-6 pt-3 border-t flex justify-between items-center">
                <span class="text-lg font-semibold text-gray-800">Total Amount:</span>
                <span class="text-xl font-bold text-orange-600">₱{{ number_format($total, 2) }}</span>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('checkout-modal').remove()"
                        class="px-4 py-2 text-gray-700 font-semibold rounded-lg border border-gray-300 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition shadow-md">
                    Place Order
                </button>
            </div>
        </form>
    </div>
</div>

{{-- This part represents the Shopping Cart Sidebar shown in address.png --}}
<aside class="fixed right-0 top-0 h-full w-80 bg-white border-l shadow-2xl p-4">
    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Shopping Cart</h3>
    {{-- Loop through $cartItems here --}}
    <div class="space-y-4 h-96 overflow-y-auto">
        @foreach($cartItems as $item)
            {{-- Cart Item Block --}}
            <div class="flex items-start justify-between">
                <div class="flex">
                    <img src="{{ $item->attributes->image ? asset('storage/' . $item->attributes->image) : 'https://placehold.co/100x100/FF7F00/ffffff?text=No+Image' }}" alt="{{ $item->attributes->image ? 'Product image for ' . $item->name : 'No image available for ' . $item->name }}" class="w-12 h-12 object-cover rounded-md mr-3" onerror="this.onerror=null;this.src='https://placehold.co/100x100/FF7F00/ffffff?text=No+Image';">
                    <div>
                        <p class="text-sm font-medium">{{ $item->name }} ({{ $item->quantity }} piece)</p>
                        <p class="text-xs text-orange-600">₱{{ number_format($item->price, 2) }}</p>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-red-600 transition">🗑️</button>
            </div>
        @endforeach
    </div>

    {{-- Totals at the bottom --}}
    <div class="mt-4 pt-4 border-t">
        <div class="flex justify-between text-sm"><span>Subtotal</span><span>₱{{ number_format($subtotal, 2) }}</span></div>
        <div class="flex justify-between text-sm"><span>Shipping</span><span>₱{{ number_format(50.00, 2) }}</span></div>
        <div class="flex justify-between text-lg font-bold mt-2"><span>Total</span><span class="text-orange-600">₱{{ number_format($total, 2) }}</span></div>
    </div>
</aside>

@endsection
