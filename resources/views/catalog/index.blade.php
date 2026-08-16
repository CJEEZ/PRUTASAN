@extends('layouts.app')

@section('content')
<div class="pt-0">

    <!-- Hero Banner Section -->
    <div class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-orange-500 text-white py-8 sm:py-12 lg:py-16 mb-0 shadow-lg rounded-b-3xl">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-8 md:flex-row md:items-center md:justify-between">
            <div class="md:w-1/2 space-y-4 text-center md:text-left">
                <div class="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-3 py-1 text-sm font-medium backdrop-blur">
                    <i class="fas fa-leaf mr-2"></i>
                    Farm-fresh produce delivered daily
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-snug">
                    Fresh Seasonal Fruits
                </h1>
                <p class="text-base sm:text-lg font-light text-white/95 max-w-xl mx-auto md:mx-0">
                    Delivered straight from the farms of Victoria, Oriental Mindoro. Experience the sweetest, freshest tropical fruits at your doorstep.
                </p>
                <div class="grid grid-cols-3 gap-3 pt-4 sm:flex sm:flex-wrap sm:justify-start sm:gap-4">
                    <a href="{{ route('catalog.index', ['search' => $searchTerm ?: null]) }}" class="flex flex-col items-center justify-center min-h-[5.5rem] rounded-xl bg-white/15 p-2 text-center shadow-md backdrop-blur transition duration-200 hover:bg-white/25">
                        <span class="text-2xl sm:text-3xl">🥭</span><span class="mt-1 text-[11px] font-semibold sm:text-xs">All Fruits</span>
                    </a>
                    <a href="{{ route('catalog.index', ['category' => 'tropical', 'search' => $searchTerm ?: null]) }}" class="flex flex-col items-center justify-center min-h-[5.5rem] rounded-xl bg-white/15 p-2 text-center shadow-md backdrop-blur transition duration-200 hover:bg-white/25">
                        <span class="text-2xl sm:text-3xl">🍌</span><span class="mt-1 text-[11px] font-semibold sm:text-xs">Tropical</span>
                    </a>
                    <a href="{{ route('catalog.index', ['seasonal' => 1, 'search' => $searchTerm ?: null]) }}" class="flex flex-col items-center justify-center min-h-[5.5rem] rounded-xl bg-white/15 p-2 text-center shadow-md backdrop-blur transition duration-200 hover:bg-white/25">
                        <span class="text-2xl sm:text-3xl">🥥</span><span class="mt-1 text-[11px] font-semibold sm:text-xs">Seasonal</span>
                    </a>
                </div>
            </div>
            <div class="md:w-1/2 relative">
                <div class="overflow-hidden rounded-2xl border border-white/20 shadow-2xl">
                    <img src="{{ asset('Screenshot 2026-07-04 102403.png') }}"
                         alt="Fresh tropical fruits from the farm"
                         class="h-56 w-full object-cover object-center sm:h-72 lg:h-80">
                </div>
            </div>
        </div>
    </div>


    {{-- Expanded to full width based on previous request --}}
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Search Indicator -->
        @if ($searchTerm)
        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-3 rounded-lg my-4 flex items-center justify-between shadow-sm">
            <span class="font-medium text-sm">
                <i class="fas fa-search mr-2"></i>
                Searching for: <span class="font-bold">"{{ $searchTerm }}"</span>
            </span>
            {{-- Link to clear search but keep current filters (if they exist) --}}
            <a href="{{ route('catalog.index', array_filter(['category' => $selectedCategorySlug === 'all' ? null : $selectedCategorySlug, 'seasonal' => $isSeasonal ? 1 : null, 'exotic' => $isExotic ? 1 : null])) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                Clear Search <i class="fas fa-times ml-1"></i>
            </a>
        </div>
        @endif

        <!-- Category & Filter Bar - CONSOLIDATED to ALL, TROPICAL, SEASONAL, EXOTIC -->
        <div class="flex flex-col gap-3 pt-4 pb-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <span class="mr-1 text-sm font-semibold text-gray-700">Filter By:</span>

                <!-- Helper array to construct URLs, ensuring the search term is always included if set -->
                @php
                    $baseParams = ['search' => $searchTerm ?: null];

                    // Determine which of the two PRIMARY filters (category or seasonal/exotic) is active for highlighting
                    // ALL is active only if NO primary filters are set (seasonal and exotic are secondary flags)
                    $isCategoryFilterActive = $selectedCategorySlug !== 'all';
                    $isAnyPrimaryFilterActive = $isCategoryFilterActive || $isSeasonal || $isExotic;
                    $isAllActive = !$isAnyPrimaryFilterActive;

                    // Current parameters for toggling secondary filters
                    $currentCategory = $selectedCategorySlug === 'all' ? null : $selectedCategorySlug;
                @endphp

                <!-- 1. ALL Button -->
                {{-- ALL link only includes search term, clearing all other primary/secondary filters --}}
                @php
                    $allParams = array_filter($baseParams);
                @endphp
                <a href="{{ route('catalog.index', $allParams) }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition duration-150
                          {{ $isAllActive ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700' }}">
                    ALL
                </a>

                <!-- 2. TROPICAL Category Button (Primary Filter) -->
                {{-- FIX: Sets category=tropical and clears seasonal/exotic if TROPICAL is not already selected.
                     The current assumption is that TROPICAL is meant to clear other primary filters. --}}
                @php
                    $tropicalActive = $selectedCategorySlug == 'tropical' && !$isSeasonal && !$isExotic;

                    // If TROPICAL is selected, we clear the category if clicked again (toggle OFF).
                    // If TROPICAL is NOT selected, we select it, clearing seasonal/exotic.
                    $tropicalParams = array_filter(array_merge($baseParams, [
                        'category' => $tropicalActive ? null : 'tropical',
                        'seasonal' => null, // Clear secondary filters on category change
                        'exotic' => null    // Clear secondary filters on category change
                    ]));

                    $tropicalClass = $selectedCategorySlug == 'tropical' && !$isSeasonal && !$isExotic
                                     ? 'bg-orange-600 text-white shadow-md'
                                     : 'bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700';
                @endphp
                <a href="{{ route('catalog.index', $tropicalParams) }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition duration-150 {{ $tropicalClass }}">
                    TROPICAL
                </a>

                <!-- 3. SEASONAL Filter Button (Secondary Toggle) -->
                {{-- Toggles seasonal status while maintaining current category/exotic status --}}
                @php
                    $seasonalParams = array_filter(array_merge($baseParams, [
                        'category' => $currentCategory,
                        'seasonal' => $isSeasonal ? null : 1, // Toggled state
                        'exotic' => $isExotic ? 1 : null
                    ]));
                @endphp
                <a href="{{ route('catalog.index', $seasonalParams) }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition duration-150
                          {{ $isSeasonal ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-orange-100 hover:text-orange-700' }}">
                    SEASONAL
                </a>

                <!-- 4. EXOTIC Filter Button (Secondary Toggle) -->
                {{-- Toggles exotic status while maintaining current category/seasonal status --}}
                @php
                    $exoticParams = array_filter(array_merge($baseParams, [
                        'category' => $currentCategory,
                        'seasonal' => $isSeasonal ? 1 : null,
                        'exotic' => $isExotic ? null : 1 // Toggled state
                    ]));
                @endphp
                <a href="{{ route('catalog.index', $exoticParams) }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition duration-150
                          {{ $isExotic ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-orange-100 hover:hover:text-orange-700' }}">
                    EXOTIC
                </a>
            </div>

            <!-- Right Side: Product Count -->
            <div class="mt-4 sm:mt-0">
                <p class="text-sm font-medium text-gray-600">
                    <span class="font-bold text-orange-600">{{ $products->count() }}</span> Products Available
                </p>
            </div>
        </div>


        <!-- Product Grid -->
        {{-- Increased grid columns to utilize the full width --}}
        <div class="grid grid-cols-1 gap-4 border-t border-gray-200 pb-12 pt-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 md:gap-6">
            @forelse ($products as $product)
                @include('components.product_card', ['product' => $product])
            @empty
                <div class="col-span-full bg-white rounded-xl shadow-inner p-10 text-center">
                    <p class="text-xl font-semibold text-gray-500">
                        No products found matching your current selection.
                    </p>
                    <p class="text-gray-400 mt-2">Try adjusting your filters, clearing your search, or selecting "ALL".</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
