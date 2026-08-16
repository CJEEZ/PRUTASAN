@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-3 xs:p-4 sm:p-6 lg:p-8 mt-6 xs:mt-10 mb-20 safe-bottom">
    <h1 class="text-2xl xs:text-3xl sm:text-4xl font-extrabold text-gray-800 mb-3 xs:mb-6 border-b pb-2">Checkout</h1>
    <p class="text-sm xs:text-base text-gray-600 mb-6 xs:mb-10">Please provide your shipping address and contact information to complete your order.</p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 xs:gap-6 lg:gap-8">
        <!-- Shipping Form (2/3 width on desktop) -->
        <div class="lg:col-span-2 bg-white p-4 xs:p-6 rounded-lg xs:rounded-xl shadow-lg border border-gray-100">
            <h2 class="text-xl xs:text-2xl font-semibold text-gray-800 mb-4 xs:mb-5">Shipping Details</h2>
            
            <form action="{{ route('checkout.process') }}" method="POST" novalidate>
                @csrf
                
                <!-- Full Name -->
                <div class="mb-4 xs:mb-5">
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" required value="Juan Dela Cruz"
                           class="mt-1 w-full px-3 xs:px-4 py-3 xs:py-3 bg-gray-50 border border-gray-300 rounded-lg text-base xs:text-sm focus:border-orange-500 focus:ring-orange-500 outline-none min-h-touch-target">
                </div>

                <!-- Phone Number -->
                <div class="mb-4 xs:mb-5">
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                    <input type="tel" id="phone_number" name="phone_number" required value="09123456789"
                           class="mt-1 w-full px-3 xs:px-4 py-3 xs:py-3 bg-gray-50 border border-gray-300 rounded-lg text-base xs:text-sm focus:border-orange-500 focus:ring-orange-500 outline-none min-h-touch-target">
                    <p class="text-xs text-gray-400 mt-2">Format: 09XXXXXXXXX</p>
                </div>

                <!-- Street Address -->
                <div class="mb-4 xs:mb-5">
                    <label for="street_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address *</label>
                    <input type="text" id="street_address" name="street_address" required placeholder="House/Unit No., Building, Street Name"
                           class="mt-1 w-full px-3 xs:px-4 py-3 xs:py-3 bg-gray-50 border border-gray-300 rounded-lg text-base xs:text-sm focus:border-orange-500 focus:ring-orange-500 outline-none min-h-touch-target">
                </div>

                <!-- Barangay -->
                <div class="mb-4 xs:mb-5">
                    <label for="barangay" class="block text-sm font-medium text-gray-700 mb-1">Barangay *</label>
                    <input type="text" id="barangay" name="barangay" required placeholder="Enter barangay"
                           class="mt-1 w-full px-3 xs:px-4 py-3 xs:py-3 bg-gray-50 border border-gray-300 rounded-lg text-base xs:text-sm focus:border-orange-500 focus:ring-orange-500 outline-none min-h-touch-target">
                </div>
                
                <!-- City/Province/Postal Code Grid -->
                <div class="grid grid-cols-1 xs:grid-cols-2 gap-3 xs:gap-4 mb-4 xs:mb-5">
                    <!-- City/Municipality -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City/Municipality *</label>
                        <input type="text" id="city" name="city" required value="Victoria"
                               class="mt-1 w-full px-3 xs:px-4 py-3 xs:py-3 bg-gray-50 border border-gray-300 rounded-lg text-base xs:text-sm focus:border-orange-500 focus:ring-orange-500 outline-none min-h-touch-target">
                    </div>
                    
                    <!-- Province -->
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Province *</label>
                        <input type="text" id="province" name="province" required value="Oriental Mindoro"
                               class="mt-1 w-full px-3 xs:px-4 py-3 xs:py-3 bg-gray-50 border border-gray-300 rounded-lg text-base xs:text-sm focus:border-orange-500 focus:ring-orange-500 outline-none min-h-touch-target">
                    </div>
                </div>

                <!-- Postal Code -->
                <div class="mb-6 xs:mb-8">
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code *</label>
                    <input type="text" id="postal_code" name="postal_code" required value="5205"
                           class="mt-1 w-full px-3 xs:px-4 py-3 xs:py-3 bg-gray-50 border border-gray-300 rounded-lg text-base xs:text-sm focus:border-orange-500 focus:ring-orange-500 outline-none min-h-touch-target">
                </div>

                <div class="flex flex-col xs:flex-row gap-3 xs:gap-4 justify-end pt-4 xs:pt-6 border-t">
                    <a href="{{ route('catalog.index') }}" class="px-4 xs:px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition text-center min-h-touch-target flex items-center justify-center">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 xs:px-8 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition shadow-md text-center min-h-touch-target flex items-center justify-center">
                        Place Order (₱{{ number_format(1600.00 + 50.00, 2) }})
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Order Summary (1/3 width on desktop) -->
        <div class="lg:col-span-1 bg-white p-4 xs:p-6 rounded-lg xs:rounded-xl shadow-lg border border-gray-100">
            <h2 class="text-xl xs:text-2xl font-semibold text-gray-800 mb-4 xs:mb-5">Order Summary</h2>
            
            <div class="space-y-3 xs:space-y-4">
                <!-- Sample Product 1 -->
                <div class="flex justify-between items-center text-xs xs:text-sm gap-2">
                    <span class="text-gray-600 flex-1">Fresh Pineapple Queen (1 piece) x 1</span>
                    <span class="font-medium text-gray-800 flex-shrink-0">₱100.00</span>
                </div>
                <!-- Sample Product 2 -->
                <div class="flex justify-between items-center text-xs xs:text-sm gap-2">
                    <span class="text-gray-600 flex-1">Sweet Latundan Banana (1 dozen) x 1</span>
                    <span class="font-medium text-gray-800 flex-shrink-0">₱60.00</span>
                </div>
            </div>
            
            <div class="mt-4 xs:mt-5 pt-4 xs:pt-5 border-t border-gray-200 space-y-3 xs:space-y-4">
                <div class="flex justify-between text-sm xs:text-base">
                    <span class="text-gray-700">Subtotal:</span>
                    <span class="font-medium text-gray-800">₱160.00</span>
                </div>
                <div class="flex justify-between text-base">
                    <span class="text-gray-700">Shipping:</span>
                    <span class="font-medium text-gray-800">₱50.00</span>
                </div>
                <div class="flex justify-between text-xl font-bold pt-2 border-t border-dashed border-gray-300">
                    <span class="text-gray-900">Total Amount:</span>
                    <span class="text-orange-600">₱210.00</span>
                </div>
            </div>
            
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Payment Method</h3>
                <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    <span class="text-green-800 font-semibold">Cash on Delivery (COD)</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection