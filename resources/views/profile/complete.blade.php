<!--
    This page forces new users (customers) to complete their profile
    by providing a shipping address before they can access the rest of the site.
    It uses the same fields as the checkout modal (address.png).
-->
@extends('layouts.guest')

@section('title', 'Complete Your Profile')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-xl mt-6 px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-xl">

        <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome! Complete Your Profile</h2>
        <p class="text-gray-600 mb-6">
            Please enter your primary shipping address so you can start placing orders for the freshest fruits.
        </p>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.complete.store') }}" class="space-y-4">
            @csrf

            <!-- Full Name -->
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                <input type="text" id="full_name" name="full_name" value="{{ old('full_name', Auth::user()->full_name) }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-3 border focus:border-orange-500 focus:ring-orange-500" required>
            </div>
            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-3 border focus:border-orange-500 focus:ring-orange-500" required>
            </div>
            <!-- Street Address -->
            <div>
                <label for="street_address" class="block text-sm font-medium text-gray-700">Street Address *</label>
                <input type="text" id="street_address" name="street_address" placeholder="House/Unit No., Building, Street Name" value="{{ old('street_address') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-3 border focus:border-orange-500 focus:ring-orange-500" required>
            </div>

            <!-- Location Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Barangay -->
                <div>
                    <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay</label>
                    <input type="text" id="barangay" name="barangay" placeholder="Enter barangay" value="{{ old('barangay') }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-3 border focus:border-orange-500 focus:ring-orange-500">
                </div>
                <!-- City/Municipality -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City/Municipality *</label>
                    <input type="text" id="city" name="city" placeholder="e.g. Victoria" value="{{ old('city') }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-3 border focus:border-orange-500 focus:ring-orange-500" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Province -->
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700">Province *</label>
                    <input type="text" id="province" name="province" placeholder="e.g. Oriental Mindoro" value="{{ old('province') }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-3 border focus:border-orange-500 focus:ring-orange-500" required>
                </div>
                <!-- Postal Code -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code *</label>
                    <input type="text" id="postal_code" name="postal_code" placeholder="e.g. 5205" value="{{ old('postal_code') }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-3 border focus:border-orange-500 focus:ring-orange-500" required>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <button type="submit" class="w-full py-3 bg-orange-600 text-white font-bold rounded-lg hover:bg-orange-700 transition duration-200 shadow-md">
                    Save and Start Shopping
                </button>
            </div>
        </form>

    </div>
</div>
@endsection