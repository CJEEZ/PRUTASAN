@extends('layouts.app')

@section('content')
<div class="py-6 sm:py-8 lg:py-12">
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800">Your Shopping Cart</h1>
    </div>

    <!-- Debug Info -->
    <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-3 sm:p-4 text-sm text-blue-700">
        <p class="font-medium"><strong>Items:</strong> {{ $cartItems->count() }} | <strong>Total:</strong> ₱{{ number_format($total, 2) }}</p>
    </div>

    @if($cartItems->isEmpty())
        <div class="rounded-xl bg-white p-8 text-center shadow sm:p-12">
            <div class="mb-4 text-4xl sm:text-5xl lg:text-6xl">🛒</div>
            <h2 class="mb-2 text-xl font-bold text-gray-800 sm:text-2xl">Your cart is empty!</h2>
            <p class="mb-6 text-gray-600">Start shopping and add some fresh fruits to your cart.</p>
            <a href="{{ route('dashboard') }}" class="inline-block rounded-lg bg-orange-600 px-6 py-2 text-sm font-semibold text-white shadow-lg transition hover:bg-orange-700 sm:px-8 sm:py-3">
                Continue Shopping
            </a>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-3 lg:gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="hidden lg:block overflow-hidden rounded-xl bg-white shadow-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-gray-200 bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800 sm:px-6 sm:py-4">Product</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-800 sm:px-6 sm:py-4">Quantity</th>
                                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-800 sm:px-6 sm:py-4">Subtotal</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-800 sm:px-6 sm:py-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                    <tr class="border-b border-gray-200 transition hover:bg-gray-50">
                                        <td class="flex items-center gap-3 px-4 py-3 sm:px-6 sm:py-4">
                                            <img src="{{ $item->product->image_url ?? 'https://placehold.co/100x100/FF7F00/ffffff?text=No+Image' }}" alt="{{ $item->product->image_url ? 'Product image for ' . $item->product->name : 'No image available for ' . $item->product->name }}" class="h-16 w-16 rounded-lg object-cover sm:h-20 sm:w-20" onerror="this.onerror=null;this.src='https://placehold.co/100x100/FF7F00/ffffff?text=No+Image';">
                                            <div class="min-w-0">
                                                <div class="truncate font-semibold text-gray-800">{{ $item->product->name }}</div>
                                                <div class="text-sm text-gray-500">₱{{ number_format($item->product->price, 2) }} / {{ $item->product->unit }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 sm:px-6 sm:py-4">
                                            <form action="{{ route('cart.update', $item->product) }}" method="POST" class="flex justify-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <div class="inline-flex items-center overflow-hidden rounded-lg border border-gray-300">
                                                    <button type="button" onclick="decreaseQuantity(this)" class="min-h-[40px] min-w-[40px] bg-gray-100 px-2 py-2 text-lg font-bold transition hover:bg-gray-200 sm:px-3">−</button>
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-12 border-0 px-2 py-2 text-center focus:ring-0" onchange="validateQuantity(this)">
                                                    <button type="button" onclick="increaseQuantity(this)" class="min-h-[40px] min-w-[40px] bg-gray-100 px-2 py-2 text-lg font-bold transition hover:bg-gray-200 sm:px-3">+</button>
                                                </div>
                                                <button type="submit" class="rounded-lg bg-blue-500 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-600 sm:px-4">Update</button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-3 text-right font-bold text-orange-600 sm:px-6 sm:py-4">₱{{ number_format($item->subtotal, 2) }}</td>
                                        <td class="px-4 py-3 text-center sm:px-6 sm:py-4">
                                            <form action="{{ route('cart.remove', $item->product) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="min-h-[40px] min-w-[40px] rounded-lg bg-red-500 text-sm font-medium text-white transition hover:bg-red-600 sm:px-4 sm:py-2">
                                                    <i class="fas fa-trash mr-0 sm:mr-2"></i><span class="hidden sm:inline">Remove</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="space-y-3 lg:hidden">
                    @foreach($cartItems as $item)
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="flex gap-3">
                                <img src="{{ $item->product->image_url ?? 'https://placehold.co/100x100/FF7F00/ffffff?text=No+Image' }}" alt="{{ $item->product->image_url ? 'Product image for ' . $item->product->name : 'No image available for ' . $item->product->name }}" class="h-20 w-20 rounded-lg object-cover" onerror="this.onerror=null;this.src='https://placehold.co/100x100/FF7F00/ffffff?text=No+Image';">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-800">{{ $item->product->name }}</h3>
                                    <p class="text-sm text-gray-500">₱{{ number_format($item->product->price, 2) }} / {{ $item->product->unit }}</p>
                                    <p class="mt-2 font-bold text-orange-600">₱{{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex gap-2">
                                <form action="{{ route('cart.update', $item->product) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex items-center gap-1">
                                        <button type="button" onclick="decreaseQuantity(this)" class="min-h-[36px] min-w-[36px] rounded bg-gray-100 text-sm font-bold">−</button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-10 border border-gray-300 rounded px-1 py-1 text-center text-sm focus:ring-0">
                                        <button type="button" onclick="increaseQuantity(this)" class="min-h-[36px] min-w-[36px] rounded bg-gray-100 text-sm font-bold">+</button>
                                    </div>
                                </form>
                                <form action="{{ route('cart.remove', $item->product) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="min-h-[36px] min-w-[36px] rounded-lg bg-red-500 text-white">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Continue Shopping -->
                <div class="mt-6">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-orange-600 font-semibold hover:text-orange-700">
                        ← Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Cart Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-20">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Order Summary</h3>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span class="font-semibold">₱{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping:</span>
                            <span class="font-semibold">₱50.00</span>
                        </div>
                        <div class="border-t border-gray-200 pt-4 flex justify-between text-lg font-bold text-gray-800">
                            <span>Total:</span>
                            <span class="text-orange-600">₱{{ number_format($total + 50, 2) }}</span>
                        </div>
                    </div>

                    <form action="{{ route('cart.clear') }}" method="POST" class="mb-4">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold">
                            Clear Cart
                        </button>
                    </form>

                    @auth
                        <a href="{{ route('checkout.show') }}" class="block w-full text-center px-6 py-3 bg-orange-600 text-white rounded-lg font-semibold hover:bg-orange-700 transition shadow-lg">
                            Proceed to Checkout
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center px-6 py-3 bg-orange-400 text-white rounded-lg font-semibold transition shadow-lg">
                            Login to Checkout
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function increaseQuantity(button) {
    const input = button.parentElement.querySelector('input[name="quantity"]');
    input.value = parseInt(input.value) + 1;
}

function decreaseQuantity(button) {
    const input = button.parentElement.querySelector('input[name="quantity"]');
    const newValue = parseInt(input.value) - 1;
    if (newValue >= 1) {
        input.value = newValue;
    } else {
        input.value = 1;
    }
}

function validateQuantity(input) {
    let value = parseInt(input.value);

    // Prevent non-numeric input
    if (isNaN(value)) {
        input.value = 1;
        return;
    }

    // Prevent negative or zero values
    if (value < 1) {
        input.value = 1;
    }
}
</script>
@endsection
