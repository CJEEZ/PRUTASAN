<div class="flex h-full flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
    <a href="{{ route('products.show', $product) }}" class="group block flex-1">
        <div class="relative aspect-[4/3] overflow-hidden sm:h-36 sm:aspect-auto md:h-40 lg:h-44">
            <img src="{{ $product->image_url ?? 'https://placehold.co/600x400/FF7F00/ffffff?text=' . urlencode($product->name) }}"
                 alt="{{ $product->name }}"
                 onerror="this.onerror=null;this.src='https://placehold.co/600x400/FF7F00/ffffff?text=Image+Unavailable';"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">

            <!-- Top Left Status Badge (In Season / All Year) -->
            <span class="absolute top-2 left-2 bg-green-600 text-white text-xs font-semibold px-2 py-0.5 rounded-md shadow-md">
                 {{ (isset($product->is_seasonal) && $product->is_seasonal) ? 'In Season' : 'All Year' }}
            </span>

            <!-- Top Right Discount Badge (Mock) -->
            <span class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-md shadow-md">
                 -18%
            </span>
        </div>

        <div class="flex flex-1 flex-col p-3 sm:p-4">
            <h3 class="product-card-title line-clamp-2 text-sm font-semibold text-gray-800 sm:text-base">
                {{ $product->name }} ({{ $product->unit ?? '1kg' }})
            </h3>

            <!-- Rating and sales count -->
            <div class="mt-2 flex min-w-0 items-center text-xs text-gray-500">
                <span class="flex flex-row text-amber-400" dir="ltr" aria-label="{{ number_format($product->average_rating ?? 0, 1) }} out of 5 stars">
                    @for($star = 1; $star <= 5; $star++){!! $star <= round($product->average_rating ?? 0) ? '&#9733;' : '&#9734;' !!}@endfor
                </span>
                <span class="font-bold mr-2 ml-1">{{ number_format($product->average_rating ?? 0, 1) }}</span>
                <span class="text-gray-400">|</span>
                <span class="ml-2 truncate">{{ number_format($product->sold_quantity ?? 0, 0) }} sold</span>
            </div>

            <!-- Price -->
            <div class="mt-2 flex flex-wrap items-center gap-2">
                <span class="text-lg font-extrabold text-emerald-600">
                    ₱{{ number_format($product->price, 2) }}
                </span>
                <!-- Mock Old Price -->
                <span class="text-xs font-light text-gray-400 line-through">
                    ₱{{ number_format($product->price * 1.25, 2) }}
                </span>
            </div>

            <!-- Stock Info -->
            <div class="mt-2 text-xs text-gray-600">
                @if(isset($product->stock) && $product->stock > 0)
                    <span class="font-medium text-green-600">In stock: {{ $product->stock }}</span>
                @else
                    <span class="font-medium text-red-600">Out of stock</span>
                @endif
            </div>
        </div>
    </a>

    <div class="mt-3 px-3 pb-3 sm:px-4 sm:pb-4">
        <!-- Call to Action -->
        @auth
            @if(isset($product->stock) && $product->stock > 0)
                <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="w-full">
                        @csrf
                        <button type="submit" class="flex min-h-[44px] w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 py-2 text-sm font-semibold text-white shadow-md transition duration-150 hover:bg-emerald-700 active:scale-[0.98]">
                            <i class="fas fa-cart-plus"></i>
                            <span>Add to Cart</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('checkout.direct-buy', $product) }}" class="w-full">
                        @csrf
                        <button type="submit" class="flex min-h-[44px] w-full items-center justify-center gap-2 rounded-lg bg-orange-600 py-2 text-sm font-semibold text-white shadow-md transition duration-150 hover:bg-orange-700 active:scale-[0.98]">
                            <i class="fas fa-bolt"></i>
                            <span>Buy Now</span>
                        </button>
                    </form>
                </div>
            @else
                <button type="button" disabled class="mt-4 flex min-h-[44px] w-full items-center justify-center gap-2 rounded-lg bg-gray-300 py-2 text-sm font-semibold text-gray-600 cursor-not-allowed">
                    <i class="fas fa-ban"></i>
                    <span>Out of stock</span>
                </button>
            @endif
        @else
            <a href="{{ route('login') }}" class="mt-4 flex min-h-[44px] w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 py-2 text-sm font-semibold text-white shadow-md transition duration-150 hover:bg-emerald-700 active:scale-[0.98]">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login to Continue</span>
            </a>
        @endauth
    </div>
</div>
