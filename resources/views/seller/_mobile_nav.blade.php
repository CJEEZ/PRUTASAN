<div class="mb-4 md:hidden">
    <label for="seller-nav" class="sr-only">Seller navigation</label>
    <select id="seller-nav" class="w-full min-h-touch-target rounded-md border border-gray-300 bg-white px-3 py-2 text-base" onchange="if(this.value) window.location=this.value">
        <option value="{{ route('seller.dashboard') }}" {{ request()->routeIs('seller.dashboard') ? 'selected' : '' }}>Home</option>
        <option value="{{ route('seller.messages') }}" {{ request()->routeIs('seller.messages') ? 'selected' : '' }}>Messages</option>
        <option value="{{ route('seller.shipments') }}" {{ request()->routeIs('seller.shipments') ? 'selected' : '' }}>Shipment</option>
        <option value="{{ route('seller.orders') }}" {{ request()->routeIs('seller.orders') ? 'selected' : '' }}>Order</option>
        <option value="{{ route('seller.products') }}" {{ request()->routeIs('seller.products') ? 'selected' : '' }}>Product</option>
        <option value="{{ route('seller.marketing') }}" {{ request()->routeIs('seller.marketing') ? 'selected' : '' }}>Marketing Centre</option>
        <option value="{{ route('seller.income') }}" {{ request()->routeIs('seller.income') ? 'selected' : '' }}>My Income</option>
        <option value="{{ route('seller.bank_accounts') }}" {{ request()->routeIs('seller.bank_accounts') ? 'selected' : '' }}>Bank Accounts</option>
    </select>
</div>
