@extends('layouts.app')

@section('content')
<div class="pt-0">

    <!-- Hero Banner Section -->
    <div class="rounded-2xl bg-orange-600 text-white py-8 sm:py-10 md:py-12 mb-0 shadow-lg">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-6 md:flex-row md:gap-8 items-center justify-between">
            <div class="md:w-1/2 space-y-4 text-center md:text-left">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight leading-snug">
                    Fresh Seasonal Fruits
                </h1>
                <p class="text-base sm:text-lg font-light text-white text-opacity-95 max-w-lg mx-auto md:mx-0">
                    Delivered straight from the farms of Victoria, Oriental Mindoro. Experience the sweetest, freshest tropical fruits at your doorstep!
                </p>
                <div class="flex flex-wrap justify-start gap-1 pt-2 sm:gap-2 sm:pt-3">
                    <!-- Quick Access Buttons (responsive sizes) -->
                    <div class="flex h-12 w-12 flex-col items-center justify-center rounded-lg bg-orange-500 p-1 text-center shadow-md sm:h-16 sm:w-16 md:h-20 md:w-20">
                        <span class="text-lg sm:text-xl">🥭</span><span class="mt-0.5 text-[8px] font-medium sm:text-[9px]">All Fruits</span>
                    </div>
                    <div class="flex h-12 w-12 flex-col items-center justify-center rounded-lg bg-orange-500 p-1 text-center shadow-md sm:h-16 sm:w-16 md:h-20 md:w-20">
                        <span class="text-lg sm:text-xl">🍌</span><span class="mt-0.5 text-[8px] font-medium sm:text-[9px]">Tropical</span>
                    </div>
                    <div class="flex h-12 w-12 flex-col items-center justify-center rounded-lg bg-orange-500 p-1 text-center shadow-md sm:h-16 sm:w-16 md:h-20 md:w-20">
                        <span class="text-lg sm:text-xl">🥥</span><span class="mt-0.5 text-[8px] font-medium sm:text-[9px]">Seasonal</span>
                    </div>
                </div>
            </div>
            <div class="w-full md:w-1/2 mt-2 md:mt-0 md:pl-8 relative">
                <!-- Image for the hero section (responsive heights) -->
                <div class="w-full h-36 sm:h-48 md:h-64 rounded-xl overflow-hidden shadow-2xl">
                    <img src="{{ asset('Screenshot 2026-07-04 102403.png') }}"
                         alt="Fresh tropical fruits"
                         class="w-full h-full object-cover object-center">
                </div>
            </div>
        </div>
    </div>

    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Category & Filter Bar -->
        <div class="flex flex-col items-stretch justify-between gap-2 pt-3 pb-3 sm:flex-row sm:flex-wrap sm:items-center">
            <div class="category-scroll flex min-w-0 items-center gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible sm:pb-0">
                <span class="mr-1 shrink-0 text-xs font-semibold text-gray-700">Categories:</span>

                <a href="{{ route('home') }}"
                   class="shrink-0 whitespace-nowrap rounded-lg px-2 py-1 text-xs font-medium transition duration-150 @if(!request('category')) bg-orange-600 text-white shadow-md @else bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700 @endif">
                    All
                </a>

                <a href="{{ route('home', ['category' => 'tropical']) }}"
                   class="shrink-0 whitespace-nowrap rounded-lg px-2 py-1 text-xs font-medium transition duration-150 @if(request('category') === 'tropical') bg-orange-600 text-white shadow-md @else bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700 @endif">
                    Tropical
                </a>

                <a href="{{ route('home', ['category' => 'seasonal']) }}"
                   class="shrink-0 whitespace-nowrap rounded-lg px-2 py-1 text-xs font-medium transition duration-150 @if(request('category') === 'seasonal') bg-orange-600 text-white shadow-md @else bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700 @endif">
                    Seasonal
                </a>

                <a href="{{ route('home', ['category' => 'exotic']) }}"
                   class="shrink-0 whitespace-nowrap rounded-lg px-2 py-1 text-xs font-medium transition duration-150 @if(request('category') === 'exotic') bg-orange-600 text-white shadow-md @else bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700 @endif">
                    Exotic
                </a>

                <a href="{{ route('home', ['category' => 'arindo']) }}"
                         class="shrink-0 whitespace-nowrap rounded-lg px-2 py-1 text-xs font-medium transition duration-150 @if(request('category') === 'arindo') bg-orange-600 text-white shadow-md @else bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700 @endif">
                    Arindo
                </a>
            </div>

            <!-- Right Side: Product Count -->
            <div class="mt-3 sm:mt-0">
                <p class="text-sm font-medium text-gray-600">
                    <span class="font-bold text-orange-600">{{ $products->count() }}</span> Products Available
                </p>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="product-grid grid grid-cols-3 gap-1 sm:gap-2 md:grid-cols-3 md:gap-4 lg:grid-cols-4 xl:grid-cols-5 pb-12 pt-4 border-t border-gray-200">
            @forelse ($products as $product)
                @include('components.product_card', ['product' => $product])
            @empty
                <div class="col-span-full bg-white rounded-xl shadow-inner p-10 text-center">
                    <p class="text-xl font-semibold text-gray-500">
                        No products found.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
