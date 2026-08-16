@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="min-h-screen p-6" style="background:#f8fafc;">
    <div class="max-w-4xl w-full bg-white rounded-lg shadow-md p-6 md:p-10 text-center mx-auto">
    <img src="{{ asset('A_Digital_Marketplace_for_Seasonal_Fruit_Distribution_for_Ornos_Farm-removebg-preview.png') }}" alt="Ornos Farm" class="h-12 md:h-20 mx-auto mb-4 w-auto object-contain">

    <h1 class="text-2xl font-semibold mb-2">Welcome to Fruit2Web Seller</h1>
        <p class="text-gray-600 mb-6">Start selling on Fruit2Web — create your shop and manage products, orders and payouts.</p>

        @auth
            @if(Auth::user()->role === 'seller')
                <a href="{{ route('seller.dashboard') }}" class="px-6 py-3 bg-green-600 text-white rounded-md">Go to Seller Dashboard</a>
            @else
                <a href="{{ route('seller.onboarding') }}" class="px-6 py-3 bg-orange-600 text-white rounded-md">Start Registration</a>
            @endif
        @else
            <a href="{{ route('register', ['seller' => 1]) }}" class="px-6 py-3 bg-orange-600 text-white rounded-md">Start Registration</a>
        @endauth
    </div>
</div>
@endsection
