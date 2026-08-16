<!-- Cart Sidebar and Checkout Modal -->
<div id="cart-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 hidden transition-opacity duration-300"></div>

<div id="cart-sidebar" class="fixed top-0 right-0 w-full md:w-96 h-full bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
    <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white">
        <h3 class="text-xl font-semibold text-gray-800">Shopping Cart</h3>
        <button id="close-cart-sidebar" class="text-gray-500 hover:text-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Cart Items List -->
    <div class="p-4 space-y-4 h-[calc(100vh-14rem)] overflow-y-auto">
        @forelse ($cartItems as $item)
            <div class="flex items-center justify-between border-b pb-4">
                <div class="flex items-center space-x-3">
                    <img src="{{ $item->product->image_url ?? 'https://placehold.co/60x60/FF7F00/ffffff?text=F' }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $item->product->name }} ({{ $item->product->unit }})</p>
                        <p class="text-xs text-gray-500">₱{{ number_format($item->price_at_addition, 2) }} per unit</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Quantity Controls -->
                    <form action="{{ route('cart.update', $item->product) }}" method="POST" class="flex items-center">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="quantity" id="quantity_{{ $item->id }}" value="{{ $item->quantity }}">

                        <button type="button" onclick="changeQuantity({{ $item->id }}, -1)" class="p-1 border rounded-l-md hover:bg-gray-100 disabled:opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                        </button>
                        <span class="px-3 py-1 border-t border-b text-sm font-medium">{{ $item->quantity }}</span>
                        <button type="button" onclick="changeQuantity({{ $item->id }}, 1)" class="p-1 border rounded-r-md hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m-8-8h16" /></svg>
                        </button>
                    </form>

                    <!-- Remove Button -->
                    <form action="{{ route('cart.remove', $item->product) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.86 12.14A2 2 0 0116.13 21H7.87a2 2 0 01-1.99-1.86L5 7m5 4v6m4-6v6m1-10h-6" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-gray-500">
                Your cart is empty. Add some fresh fruit!
            </div>
        @endforelse
    </div>

    <!-- Cart Summary and Checkout Button -->
    <div class="absolute bottom-0 w-full bg-white shadow-t-xl border-t p-5">
        <div class="space-y-2 mb-4 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-medium">₱{{ number_format($cartTotal, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Shipping</span>
                <span class="font-medium">₱50.00</span>
            </div>
        </div>
        <div class="flex justify-between text-lg font-bold border-t pt-2">
            <span>Total:</span>
            <span>₱{{ number_format($cartTotal + 50, 2) }}</span>
        </div>
        <button id="open-checkout-modal" @disabled($cartTotal === 0) class="w-full mt-4 bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-lg transition duration-150 ease-in-out shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
            Proceed to Checkout
        </button>
    </div>
</div>

<!-- Checkout Modal (Based on address.png) -->
<div x-data="{ showModal: false }" x-show="showModal" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-[60]" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
    <div @click.outside="showModal = false" class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 m-4">
        <div class="flex justify-between items-start border-b pb-3 mb-4">
            <h4 class="text-xl font-semibold text-gray-800">Checkout</h4>
            <button @click="showModal = false" class="text-gray-500 hover:text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <p class="text-sm text-gray-600 mb-4">Please provide your shipping address and contact information</p>

        <form action="{{ route('cart.checkout') }}" method="POST">
            @csrf

            <!-- Form Fields -->
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" required value="{{ Auth::user()->name ?? '' }}" class="mt-1 block w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                    <input type="text" id="phone_number" name="phone_number" required placeholder="09xxxxxxxxx" class="mt-1 block w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="street_address" class="block text-sm font-medium text-gray-700">Street Address *</label>
                    <input type="text" id="street_address" name="street_address" required placeholder="House/Unit No., Building, Street Name" class="mt-1 block w-full border-gray-300 rounded-lg">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay *</label>
                        <input type="text" id="barangay" name="barangay" required placeholder="Enter barangay" class="mt-1 block w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City/Municipality *</label>
                        <input type="text" id="city" name="city" required placeholder="Enter city/municipality" class="mt-1 block w-full border-gray-300 rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700">Province *</label>
                        <input type="text" id="province" name="province" required value="Oriental Mindoro" class="mt-1 block w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code *</label>
                        <input type="text" id="postal_code" name="postal_code" required placeholder="5200" class="mt-1 block w-full border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>

            <!-- Total Amount and Actions -->
            <div class="flex justify-between items-center mt-6 pt-4 border-t">
                <span class="text-sm font-medium text-gray-700">Total Amount:</span>
                <span class="text-xl font-bold text-orange-600">₱{{ number_format($cartTotal + 50, 2) }}</span>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" @click="showModal = false" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg transition shadow-md">
                    Place Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Initialize Alpine.js for modal control if not globally available
    document.addEventListener('alpine:init', () => {
        Alpine.data('checkoutModal', () => ({
            showModal: false,
        }));
    });

    // Event listener to open modal from sidebar button
    document.getElementById('open-checkout-modal').addEventListener('click', () => {
        if (!@json(Auth::check())) {
            // Redirect to login if user is not authenticated
            window.location.href = "{{ route('login') }}";
            return;
        }

        // Use Alpine to show the modal (assuming Alpine is loaded)
        if (window.Alpine) {
            window.Alpine.store('checkoutModal').showModal = true;
        } else {
            // Fallback for modal display if Alpine isn't ready
            const modal = document.querySelector('[x-data="{ showModal: false }"]');
            modal.classList.remove('hidden');
        }
    });

    // Quantity update function for the sidebar
    function changeQuantity(itemId, delta) {
        const input = document.getElementById(`quantity_${itemId}`);
        let currentQuantity = parseInt(input.value);
        let newQuantity = currentQuantity + delta;

        if (newQuantity < 0) newQuantity = 0; // Prevent negative quantity

        input.value = newQuantity;

        // Submit the form containing the updated quantity
        const form = input.closest('form');
        form.submit();
    }
</script>