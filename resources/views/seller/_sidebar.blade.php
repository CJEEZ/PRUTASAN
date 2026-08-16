<nav class="space-y-3">
    <a href="{{ route('seller.dashboard') }}" class="{{ request()->routeIs('seller.dashboard') ? 'block px-4 py-3 text-base text-orange-600 font-semibold rounded-lg' : 'block px-4 py-3 text-base text-gray-700 hover:bg-gray-50 rounded-lg' }}">Home</a>
    <a href="{{ route('seller.messages') }}" class="{{ request()->routeIs('seller.messages') ? 'block px-4 py-3 text-base text-orange-600 font-semibold rounded-lg' : 'block px-4 py-3 text-base text-gray-700 hover:bg-gray-50 rounded-lg' }}">Messages</a>
    <a href="{{ route('seller.shipments') }}" class="{{ request()->routeIs('seller.shipments') ? 'block px-4 py-3 text-base text-orange-600 font-semibold rounded-lg' : 'block px-4 py-3 text-base text-gray-700 hover:bg-gray-50 rounded-lg' }}">Shipment</a>
    <a href="{{ route('seller.orders') }}" class="{{ request()->routeIs('seller.orders') ? 'block px-4 py-3 text-base text-orange-600 font-semibold rounded-lg' : 'block px-4 py-3 text-base text-gray-700 hover:bg-gray-50 rounded-lg' }}">Order</a>
    <a href="{{ route('seller.products') }}" class="{{ request()->routeIs('seller.products') ? 'block px-4 py-3 text-base text-orange-600 font-semibold rounded-lg' : 'block px-4 py-3 text-base text-gray-700 hover:bg-gray-50 rounded-lg' }}">Product</a>

    <div class="mt-4 border-t pt-4">
        <h3 class="text-xs text-gray-500 uppercase mb-2">Finance</h3>
        <a href="{{ route('seller.income') }}" class="{{ request()->routeIs('seller.income') ? 'block px-4 py-3 text-base text-orange-600 font-semibold rounded-lg' : 'block px-4 py-3 text-base text-gray-700 hover:bg-gray-50 rounded-lg' }}">My Income</a>
        <a href="{{ route('seller.bank_accounts') }}" class="{{ request()->routeIs('seller.bank_accounts') ? 'block px-4 py-3 text-base text-orange-600 font-semibold rounded-lg' : 'block px-4 py-3 text-base text-gray-700 hover:bg-gray-50 rounded-lg' }}">Bank Accounts</a>
        <a href="{{ route('seller.arindo.properties') }}" class="{{ request()->routeIs('seller.arindo.properties*') ? 'block px-4 py-3 text-base text-orange-600 font-semibold rounded-lg' : 'block px-4 py-3 text-base text-gray-700 hover:bg-gray-50 rounded-lg' }}">Arindo Properties</a>
    </div>

    <div class="mt-6 border-t pt-4">
        <a href="{{ route('dashboard') }}" class="block w-full rounded-lg border border-orange-500 px-4 py-3 text-center text-base font-semibold text-orange-600 transition hover:bg-orange-50">
            Customer Dashboard
        </a>
    </div>
</nav>
