@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-blue-50 to-white min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Track Your Order
            </h1>
            <p class="text-xl text-gray-600">
                Enter your order details to check the status and location of your delivery
            </p>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <form method="POST" action="{{ route('tracking.public') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="order_number" class="block text-sm font-semibold text-gray-700 mb-2">
                        Order Number
                    </label>
                    <input
                        type="text"
                        name="order_number"
                        id="order_number"
                        placeholder="e.g., ORD-2024-001234"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('order_number') border-red-500 @enderror"
                        value="{{ old('order_number') }}"
                        required
                    >
                    @error('order_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-semibold text-gray-700 mb-2">
                        Postal Code
                    </label>
                    <input
                        type="text"
                        name="postal_code"
                        id="postal_code"
                        placeholder="e.g., 4027"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('postal_code') border-red-500 @enderror"
                        value="{{ old('postal_code') }}"
                        required
                    >
                    @error('postal_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 text-lg"
                >
                    <i class="fas fa-search mr-2"></i> Track Package
                </button>
            </form>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-4xl text-blue-600 mb-3">
                    <i class="fas fa-boxes"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Real-time Updates</h3>
                <p class="text-gray-600 text-sm">Get instant updates on your package status and location</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-4xl text-green-600 mb-3">
                    <i class="fas fa-map-location-dot"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Live Map Tracking</h3>
                <p class="text-gray-600 text-sm">See exactly where your delivery is on our interactive map</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-4xl text-orange-600 mb-3">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">24/7 Support</h3>
                <p class="text-gray-600 text-sm">Need help? Our support team is here to assist you</p>
            </div>
        </div>

        <!-- Need Help Section -->
        <div class="mt-12 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Need Help Finding Your Order?</h3>
            <ul class="space-y-2 text-gray-700">
                <li>
                    <strong>Find your Order Number:</strong> Check your confirmation email for the order number. It usually starts with "ORD-".
                </li>
                <li>
                    <strong>Find your Postal Code:</strong> Check the postal code of your delivery address - it's the 4-digit code at the end of your address.
                </li>
                <li>
                    <strong>Login to Your Account:</strong> <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Sign in</a> to view all your orders in one place.
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
