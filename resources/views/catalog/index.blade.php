@extends('layouts.app')

@section('content')
<div class="pt-0">

    <!-- Hero Banner Section -->
    <div class="catalog-hero rounded-2xl bg-orange-600 py-8 text-white shadow-lg sm:py-10 md:py-12">
        <div class="mx-auto flex w-full flex-col items-center justify-between gap-6 px-4 sm:px-6 md:flex-row md:gap-8 lg:px-8">
            <div class="space-y-4 text-center md:w-1/2 md:text-left">
                <h1 class="text-3xl font-extrabold leading-snug tracking-tight sm:text-4xl md:text-5xl">
                    Fresh Seasonal Fruits
                </h1>
                <p class="mx-auto max-w-lg text-base font-light text-white text-opacity-95 sm:text-lg md:mx-0">
                    Delivered straight from the farms of Victoria, Oriental Mindoro. Experience the sweetest, freshest tropical fruits at your doorstep!
                </p>
                <div class="flex flex-wrap justify-start gap-1 pt-2 sm:gap-2 sm:pt-3">
                    <a href="{{ route('catalog.index', ['search' => $searchTerm ?: null]) }}" class="flex h-12 w-12 flex-col items-center justify-center rounded-lg bg-orange-500 p-1 text-center shadow-md transition duration-200 hover:bg-orange-700 sm:h-16 sm:w-16 md:h-20 md:w-20">
                        <span class="text-lg sm:text-xl">🥭</span><span class="mt-0.5 text-[8px] font-medium sm:text-[9px]">All Fruits</span>
                    </a>
                    <a href="{{ route('catalog.index', ['category' => 'tropical', 'search' => $searchTerm ?: null]) }}" class="flex h-12 w-12 flex-col items-center justify-center rounded-lg bg-orange-500 p-1 text-center shadow-md transition duration-200 hover:bg-orange-700 sm:h-16 sm:w-16 md:h-20 md:w-20">
                        <span class="text-lg sm:text-xl">🍌</span><span class="mt-0.5 text-[8px] font-medium sm:text-[9px]">Tropical</span>
                    </a>
                    <a href="{{ route('catalog.index', ['seasonal' => 1, 'search' => $searchTerm ?: null]) }}" class="flex h-12 w-12 flex-col items-center justify-center rounded-lg bg-orange-500 p-1 text-center shadow-md transition duration-200 hover:bg-orange-700 sm:h-16 sm:w-16 md:h-20 md:w-20">
                        <span class="text-lg sm:text-xl">🥥</span><span class="mt-0.5 text-[8px] font-medium sm:text-[9px]">Seasonal</span>
                    </a>
                </div>
            </div>
            <div class="relative mt-2 w-full md:mt-0 md:w-1/2 md:pl-8">
                <div class="h-36 w-full overflow-hidden rounded-xl shadow-2xl sm:h-48 md:h-64">
                    <img src="{{ asset('Screenshot 2026-07-04 102403.png') }}"
                         alt="Fresh tropical fruits from the farm"
                         class="h-full w-full object-cover object-center">
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
            <div class="category-scroll flex min-w-0 items-center gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:gap-3 sm:overflow-visible sm:pb-0">
                <span class="mr-1 shrink-0 whitespace-nowrap text-sm font-semibold text-gray-700">Filter By:</span>

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
                   class="shrink-0 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition duration-150
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
                   class="shrink-0 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition duration-150 {{ $tropicalClass }}">
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
                   class="shrink-0 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition duration-150
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
                   class="shrink-0 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition duration-150
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
        <div class="product-grid grid grid-cols-3 gap-1 border-t border-gray-200 pb-12 pt-4 sm:gap-2 md:gap-4 lg:grid-cols-4 xl:grid-cols-5">
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
