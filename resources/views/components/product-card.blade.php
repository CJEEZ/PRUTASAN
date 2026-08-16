<div class="bg-white rounded-xl shadow-lg overflow-hidden transition-shadow duration-300 hover:shadow-xl">
    <div class="relative h-64 w-full overflow-hidden">
        {{-- Season/Availability Label --}}
        <span class="absolute top-3 left-3 px-3 py-1 text-xs font-bold text-white rounded-full
            @if($product->category == 'Seasonal') bg-green-500 @else bg-yellow-500 @endif">
            @if($product->category == 'Seasonal') In Season @else All Year @endif
        </span>

        {{-- Discount Label --}}
        @if($product->discount_percentage > 0)
            <span class="absolute top-3 right-3 px-3 py-1 text-xs font-bold text-white bg-red-500 rounded-full">
                -{{ $product->discount_percentage }}%
            </span>
        @endif

        {{-- Product Image --}}
        <img src="{{ $product->image_path ? asset('storage/' . $product->image_path) : ($product->image_url ?? 'https://placehold.co/600x400/FF7F00/ffffff?text=' . urlencode($product->name)) }}" alt="{{ $product->name }}"
             class="w-full h-full object-cover"
             onerror="this.onerror=null;this.src='https://placehold.co/600x400/FF7F00/ffffff?text=No+Image';">
    </div>

    <div class="p-4">
        <h3 class="text-lg font-semibold h-12">{{ $product->name }} ({{ $product->unit }})</h3>

        {{-- Rating and Sold Count (Static for this example) --}}
        <div class="flex items-center mt-2 text-sm text-gray-500">
            <span class="flex items-center text-yellow-500 mr-2">
                &#9733; {{ number_format(rand(46, 49) / 10, 1) }}
            </span>
            | {{ number_format(rand(500, 2500), 0) }} sold
        </div>

        {{-- Pricing --}}
        <div class="mt-3 flex items-center justify-between">
            <div class="text-xl font-bold text-orange-600">
                @if($product->discount_percentage > 0)
                    {{-- Display discounted price --}}
                    ₱{{ number_format($product->price * (1 - $product->discount_percentage / 100), 2) }}
                @else
                    ₱{{ number_format($product->price, 2) }}
                @endif
            </div>

            @if($product->discount_percentage > 0)
                {{-- Display old price --}}
                <span class="text-sm text-gray-400 line-through">
                    ₱{{ number_format($product->price, 2) }}
                </span>
            @endif
        </div>

        {{-- Add to Cart Form (Requires CartController) --}}
        <form action="{{ route('cart.store') }}" method="POST" class="mt-4">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">

            @auth
                <button type="submit"
                        class="w-full py-2 bg-orange-600 text-white rounded-lg font-bold hover:bg-orange-700 transition">
                    Add to Cart
                </button>
            @else
                 {{-- If not logged in, show 'Login to Add' as per categories.png --}}
                 <a href="{{ route('login') }}" class="w-full block text-center py-2 bg-orange-600 text-white rounded-lg font-bold hover:bg-orange-700 transition">
                    Login to Add
                </a>
            @endauth
        </form>
    </div>
</div>
