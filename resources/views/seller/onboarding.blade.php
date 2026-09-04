@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="min-h-screen p-6" style="background:#f8fafc;">
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-md p-6 md:p-10 mx-auto">
        <img src="{{ asset('ORNOSFARM_LOGOS.png') }}" alt="Ornos Farm" class="h-12 md:h-20 mx-auto mb-6 w-auto object-contain">

        <h1 class="text-3xl font-bold mb-2 text-center">Ready to Become a Seller?</h1>
        <p class="text-gray-600 mb-8 text-center">Join thousands of sellers on Fruit2Web. Grow your business and reach more customers.</p>

        <div class="mb-8 space-y-4">
            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-orange-600">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Easy Setup</h3>
                    <p class="text-gray-600">Set up your seller account in minutes</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-orange-600">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Manage Products</h3>
                    <p class="text-gray-600">List and manage your products easily</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-orange-600">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Get Paid Securely</h3>
                    <p class="text-gray-600">Receive payments directly to your bank account</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-orange-600">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Track Orders</h3>
                    <p class="text-gray-600">Monitor orders and manage shipments in real-time</p>
                </div>
            </div>
        </div>

        <form action="{{ route('seller.onboarding.process') }}" method="POST" class="mt-8">
            @csrf

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-900 mb-2">What's Next?</h3>
                <p class="text-sm text-blue-800">Click the button below to complete your seller registration. You'll need to provide:</p>
                <ul class="text-sm text-blue-800 mt-2 list-disc list-inside">
                    <li>Shop name and description</li>
                    <li>Business information</li>
                    <li>Bank account details for payouts</li>
                </ul>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('home') }}" class="flex-1 px-6 py-3 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition text-center">
                    Go Back
                </a>
                <button type="submit" class="flex-1 px-6 py-3 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition">
                    Start Seller Registration
                </button>
            </div>
        </form>

        <p class="text-center text-gray-500 text-sm mt-6">
            Have questions? <a href="#" class="text-orange-600 hover:underline">Contact support</a>
        </p>
    </div>
</div>
@endsection
