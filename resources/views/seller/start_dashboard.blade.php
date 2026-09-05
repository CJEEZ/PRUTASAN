@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex min-h-screen items-center justify-center p-4 sm:p-6" style="background:#f8fafc;">
    <div class="mx-auto w-full max-w-4xl rounded-lg bg-white p-5 text-center shadow-md sm:p-6 md:p-10">
    <img src="{{ asset('ORNOSFARM_LOGOS.png') }}" alt="Ornos Farm" class="mx-auto mb-4 h-12 w-auto object-contain md:h-20">

    <h1 class="text-2xl font-semibold mb-2">Welcome to FruitExpress Seller</h1>
        <p class="text-gray-600 mb-6">Start selling on FruitExpress — create your shop and manage products, orders and payouts.</p>

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
