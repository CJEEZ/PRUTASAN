@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2">Seller Approval</h1>
            <p class="text-gray-600">Please provide your shop information for admin approval</p>
        </div>

        <!-- Status Messages -->
        @if ($user->seller_status === 'pending')
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h2 class="text-lg font-semibold text-yellow-800 mb-2">⏳ Request Pending</h2>
                <p class="text-yellow-700">Your seller approval request is under review. The admin will contact you shortly.</p>
                <p class="text-sm text-yellow-600 mt-2">Submitted on: {{ $user->seller_request_date?->format('M d, Y') }}</p>
            </div>
        @elseif ($user->seller_status === 'approved')
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <h2 class="text-lg font-semibold text-green-800 mb-2">✅ Approved!</h2>
                <p class="text-green-700">Your seller account has been approved. You can now access your seller dashboard.</p>
                <a href="{{ route('seller.dashboard') }}" class="inline-block mt-3 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Go to Dashboard</a>
            </div>
        @elseif ($user->seller_status === 'rejected')
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h2 class="text-lg font-semibold text-red-800 mb-2">❌ Request Rejected</h2>
                <p class="text-red-700 mb-2">{{ $user->seller_rejection_reason ?? 'Your seller approval request has been rejected.' }}</p>
                <button type="button" onclick="document.getElementById('resubmit-form').classList.toggle('hidden')" class="text-red-600 font-semibold hover:underline">Submit new request</button>
            </div>
        @endif

        <!-- Request Form -->
        @if ($user->seller_status === null || $user->seller_status === 'rejected')
            <div id="resubmit-form" class="bg-white rounded-lg shadow-lg p-6 sm:p-8">
                <form method="POST" action="{{ route('seller.approval.store') }}" class="space-y-6">
                    @csrf

                    <!-- Shop Name -->
                    <div>
                        <label for="shop_name" class="block text-sm font-semibold text-gray-700 mb-2">Shop Name</label>
                        <input type="text" id="shop_name" name="shop_name" placeholder="Your shop name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                               value="{{ old('shop_name', $user->shop_name) }}">
                        @error('shop_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Type -->
                    <div>
                        <label for="business_type" class="block text-sm font-semibold text-gray-700 mb-2">Business Type</label>
                        <select id="business_type" name="business_type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">-- Select Business Type --</option>
                            <option value="fruits" {{ old('business_type', $user->business_type) === 'fruits' ? 'selected' : '' }}>Fresh Fruits</option>
                            <option value="vegetables" {{ old('business_type', $user->business_type) === 'vegetables' ? 'selected' : '' }}>Vegetables</option>
                            <option value="organic" {{ old('business_type', $user->business_type) === 'organic' ? 'selected' : '' }}>Organic Products</option>
                            <option value="processed" {{ old('business_type', $user->business_type) === 'processed' ? 'selected' : '' }}>Processed Foods</option>
                            <option value="other" {{ old('business_type', $user->business_type) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('business_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Shop Description -->
                    <div>
                        <label for="shop_description" class="block text-sm font-semibold text-gray-700 mb-2">Shop Description</label>
                        <textarea id="shop_description" name="shop_description" rows="5" placeholder="Tell us about your shop and products..." required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('shop_description', $user->shop_description) }}</textarea>
                        @error('shop_description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition">
                        Submit Approval Request
                    </button>
                </form>
            </div>
        @endif

        <!-- Logout Link -->
        <div class="text-center mt-8">
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="text-gray-600 hover:text-gray-800 underline">Log Out</button>
            </form>
        </div>
    </div>
</div>
@endsection
