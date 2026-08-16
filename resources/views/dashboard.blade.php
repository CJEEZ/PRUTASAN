@extends('layouts.app')

@section('content')
<div class="pt-0">

    <!-- Hero Banner Section -->
    <div class="bg-orange-600 text-white py-16 mb-0 shadow-lg">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between">
            <div class="md:w-1/2 space-y-4 text-center md:text-left">
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-snug">
                    Fresh Seasonal Fruits
                </h1>
                <p class="text-lg font-light text-white text-opacity-95 max-w-lg mx-auto md:mx-0">
                    Delivered straight from the farms of Victoria, Oriental Mindoro. Experience the sweetest, freshest tropical fruits at your doorstep!
                </p>
                <div class="flex flex-wrap gap-4 pt-6 justify-center md:justify-start">
                    <!-- Quick Access Buttons (responsive sizes) -->
                    <a href="{{ route('catalog.index') }}" class="flex flex-col items-center justify-center w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 bg-orange-500 rounded-lg p-2 text-center shadow-md hover:bg-orange-700 transition duration-200">
                        <span class="text-3xl">🥭</span><span class="text-xs font-medium mt-1">All Fruits</span>
                    </a>
                    <a href="{{ route('catalog.index', ['category' => 'tropical']) }}" class="flex flex-col items-center justify-center w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 bg-orange-500 rounded-lg p-2 text-center shadow-md hover:bg-orange-700 transition duration-200">
                        <span class="text-3xl">🍌</span><span class="text-xs font-medium mt-1">Tropical</span>
                    </a>
                    <a href="{{ route('catalog.index', ['seasonal' => 1]) }}" class="flex flex-col items-center justify-center w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 bg-orange-500 rounded-lg p-2 text-center shadow-md hover:bg-orange-700 transition duration-200">
                        <span class="text-3xl">🥥</span><span class="text-xs font-medium mt-1">Seasonal</span>
                    </a>
                </div>
            </div>
            <div class="md:w-1/2 mt-8 md:mt-0 md:pl-10 relative">
                <!-- Image for the hero section (responsive heights) -->
                <div class="h-48 sm:h-64 md:h-80 rounded-xl overflow-hidden shadow-2xl">
                    <img src="{{ asset('Screenshot 2026-07-04 102403.png') }}"
                         alt="Fresh tropical fruits"
                         class="w-full h-full object-cover object-center">
                </div>
            </div>
        </div>
    </div>

    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Category & Filter Bar -->
        <div class="flex flex-col sm:flex-row flex-wrap items-center justify-between pt-4 pb-4">
            <div class="flex flex-wrap gap-3 items-center">
                <span class="font-semibold text-gray-700 mr-1 text-sm">Categories:</span>

                <a href="{{ route('home') }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition duration-150 @if(!request('category')) bg-orange-600 text-white shadow-md @else bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700 @endif">
                    All
                </a>

                <a href="{{ route('home', ['category' => 'tropical']) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition duration-150 @if(request('category') === 'tropical') bg-orange-600 text-white shadow-md @else bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700 @endif">
                    Tropical
                </a>

                <a href="{{ route('home', ['category' => 'seasonal']) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition duration-150 @if(request('category') === 'seasonal') bg-orange-600 text-white shadow-md @else bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700 @endif">
                    Seasonal
                </a>

                <a href="{{ route('home', ['category' => 'exotic']) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition duration-150 @if(request('category') === 'exotic') bg-orange-600 text-white shadow-md @else bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700 @endif">
                    Exotic
                </a>

                <a href="{{ route('home', ['category' => 'arindo']) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition duration-150 @if(request('category') === 'arindo') bg-orange-600 text-white shadow-md @else bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700 @endif">
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
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6 pb-12 pt-4 border-t border-gray-200">
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
