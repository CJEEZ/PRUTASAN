@php
    $sellerMobileLinks = [
        ['route' => 'seller.dashboard', 'label' => 'Home', 'icon' => 'fa-house'],
        ['route' => 'seller.messages', 'label' => 'Messages', 'icon' => 'fa-message'],
        ['route' => 'seller.shipments', 'label' => 'Shipment', 'icon' => 'fa-truck'],
        ['route' => 'seller.orders', 'label' => 'Order', 'icon' => 'fa-box'],
        ['route' => 'seller.products', 'label' => 'Product', 'icon' => 'fa-leaf'],
        ['route' => 'seller.income', 'label' => 'My Income', 'icon' => 'fa-chart-line'],
        ['route' => 'seller.bank_accounts', 'label' => 'Bank Accounts', 'icon' => 'fa-credit-card'],
    ];
    $activeSellerLink = collect($sellerMobileLinks)->first(fn ($link) => request()->routeIs($link['route'])) ?? $sellerMobileLinks[0];
@endphp

<details class="seller-mobile-navigation sticky top-0 z-30 mb-4 rounded-xl border border-gray-200 bg-white p-2 shadow-sm md:hidden">
    <summary class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 [&::-webkit-details-marker]:hidden">
        <span class="flex items-center gap-3">
            <i class="fas {{ $activeSellerLink['icon'] }} w-4 text-center text-emerald-600" aria-hidden="true"></i>
            <span>{{ $activeSellerLink['label'] }}</span>
        </span>
        <i class="fas fa-chevron-down text-xs text-emerald-600" aria-hidden="true"></i>
    </summary>

    <nav class="mt-2 space-y-1" aria-label="Seller navigation">
    @foreach($sellerMobileLinks as $link)
        <a href="{{ route($link['route']) }}" class="flex min-h-[44px] items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs($link['route']) ? 'bg-emerald-100 text-emerald-700' : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
            <i class="fas {{ $link['icon'] }} w-4 text-center" aria-hidden="true"></i>
            <span class="whitespace-nowrap">{{ $link['label'] }}</span>
        </a>
    @endforeach
    </nav>
</details>
