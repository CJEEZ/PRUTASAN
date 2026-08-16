@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <!-- Payment Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg shadow-lg p-8 mb-8">
            <div class="flex items-center gap-4 mb-4">
                <img src="https://i.pinimg.com/736x/f5/7a/4a/f57a4a9d5f8669ae55e0dba262171cdc.jpg" alt="GCash" class="w-16 h-16 object-contain bg-white rounded-lg p-2">
                <div>
                    <h1 class="text-3xl font-bold mb-2">GCash Payment</h1>
                    <p class="text-orange-100">Order #{{ $order->order_number }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Payment Details -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Order Summary</h2>

                    <div class="space-y-3 mb-6 pb-6 border-b">
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $item->product->name }}</p>
                                    <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                </div>
                                <p class="font-semibold text-gray-800">₱{{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>₱{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span>₱{{ number_format($order->shipping, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t">
                            <span>Total to Pay</span>
                            <span class="text-orange-600">₱{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-2">Delivery Address</h3>
                        <address class="text-gray-600 not-italic text-sm">
                            <p>{{ $order->full_name }}</p>
                            <p>{{ $order->street_address }}, {{ $order->barangay }}</p>
                            <p>{{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
                            <p class="mt-2">{{ $order->phone }}</p>
                        </address>
                    </div>
                </div>

                <!-- GCash Payment Instructions -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <img src="https://i.pinimg.com/736x/f5/7a/4a/f57a4a9d5f8669ae55e0dba262171cdc.jpg" alt="GCash" class="w-8 h-8 object-contain">
                        <h3 class="font-bold text-blue-900">How to Pay via GCash</h3>
                    </div>
                    <ol class="text-blue-800 text-sm space-y-2 list-decimal list-inside">
                        <li>Open your GCash app or website</li>
                        <li>Select "Pay Bills" or "Scan QR"</li>
                        <li>Enter amount: <span class="font-bold">₱{{ number_format($order->total, 2) }}</span></li>
                        <li>Complete the payment in GCash</li>
                        <li>Copy your GCash Reference Number</li>
                        <li>Paste the reference number below</li>
                    </ol>
                </div>

                <!-- Payment Confirmation Form -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Confirm Payment</h3>

                    <form action="{{ route('payment.confirm-gcash', $order->id) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                GCash Reference Number *
                            </label>
                            <input
                                type="text"
                                name="gcash_reference"
                                placeholder="e.g., G123456789"
                                value="{{ old('gcash_reference') }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            >
                            @error('gcash_reference')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <input type="hidden" name="amount" value="{{ $order->total }}">

                        <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                            <p class="text-sm text-gray-600">
                                Make sure you've successfully sent <span class="font-bold text-orange-600">₱{{ number_format($order->total, 2) }}</span> via GCash before confirming.
                            </p>
                        </div>

                        <div class="flex gap-4">
                            <a href="{{ route('profile.show') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold">
                                Back
                            </a>
                            <button
                                type="submit"
                                class="flex-1 px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold"
                            >
                                Confirm Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Status Sidebar -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                    <div class="mb-6">
                        <div class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">
                            Pending Payment
                        </div>
                    </div>

                    <div class="space-y-4 text-sm text-gray-600">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-orange-600"></i>
                            </div>
                            <span>Order Created</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-gray-400"></i>
                            </div>
                            <span>Awaiting Payment</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-box text-gray-400"></i>
                            </div>
                            <span>Processing</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-truck text-gray-400"></i>
                            </div>
                            <span>Shipping</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t">
                        <form action="{{ route('payment.cancel-gcash', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this payment?');">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-semibold">
                                Cancel Payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
