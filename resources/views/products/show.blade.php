@extends('layouts.app')

@section('content')
<div class="mx-auto min-w-0 max-w-6xl px-2 pb-4 sm:px-6 sm:pb-6 lg:px-8">
    <div class="grid min-w-0 gap-3 sm:gap-6 lg:grid-cols-[1.5fr_0.9fr]">
        <div class="min-w-0 space-y-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:space-y-4 sm:rounded-3xl sm:p-5">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-100">
                <img src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : ($product->image_url ?? 'https://via.placeholder.com/900x500') }}" alt="{{ $product->thumbnail || $product->image_url ? $product->name . ' product image' : 'No image available for ' . $product->name }}" class="h-48 w-full object-cover sm:h-72" onerror="this.onerror=null;this.src='https://placehold.co/900x500/FF7F00/ffffff?text=No+Image';">
            </div>

            <div class="space-y-3">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-900 sm:text-2xl">{{ $product->name }}</h1>
                        <p class="text-sm text-slate-500">{{ $product->description ?? 'No description available.' }}</p>
                    </div>
                    <div class="rounded-full bg-slate-100 px-2.5 py-1 text-xs uppercase tracking-[0.2em] text-slate-600 sm:px-3 sm:text-sm">{{ $product->category->name ?? 'General' }}</div>
                </div>

                <div class="grid grid-cols-3 gap-2 sm:gap-3">
                    <div class="min-w-0 rounded-xl bg-slate-50 p-2 text-[11px] text-slate-700 sm:rounded-2xl sm:p-4 sm:text-sm">
                        <p class="font-semibold text-slate-900">Price</p>
                        <p class="mt-1 break-words sm:mt-2">₱{{ number_format($product->price, 2) }}</p>
                    </div>
                    <div class="min-w-0 rounded-xl bg-slate-50 p-2 text-[11px] text-slate-700 sm:rounded-2xl sm:p-4 sm:text-sm">
                        <p class="font-semibold text-slate-900">Stock</p>
                        <p class="mt-1 break-words sm:mt-2">{{ isset($product->stock) ? ($product->stock > 0 ? $product->stock . ' pcs' : 'Out of stock') : 'Unknown' }}</p>
                    </div>
                    <div class="min-w-0 rounded-xl bg-slate-50 p-2 text-[11px] text-slate-700 sm:rounded-2xl sm:p-4 sm:text-sm">
                        <p class="font-semibold text-slate-900">Unit</p>
                        <p class="mt-1 break-words sm:mt-2">{{ $product->unit ?? '1kg' }}</p>
                    </div>
                </div>

                <div class="rounded-2xl bg-amber-50 p-4 text-sm text-slate-700">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="flex flex-row text-lg tracking-wide text-amber-500" dir="ltr">
                            @for($star = 1; $star <= 5; $star++){!! $star <= round($product->reviews_avg_rating ?? 0) ? '&#9733;' : '&#9734;' !!}@endfor
                        </span>
                        <span class="text-xl font-bold text-slate-900">{{ number_format($product->reviews_avg_rating ?? 0, 1) }}</span>
                        <span class="text-slate-500">{{ $product->reviews->count() }} {{ $product->reviews->count() === 1 ? 'review' : 'reviews' }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 sm:gap-4">
                <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-2 text-[11px] text-slate-700 shadow-sm sm:rounded-3xl sm:p-4 sm:text-sm">
                    <p class="font-semibold text-slate-900">Product features</p>
                    <ul class="mt-2 space-y-1.5 sm:mt-3 sm:space-y-2">
                        <li>{{ $product->is_seasonal ? 'Seasonal product' : 'Year-round availability' }}</li>
                        <li>{{ $product->is_exotic ? 'Exotic variety' : 'Local variety' }}</li>
                        @if($product->is_arindo)
                            <li>Arindo status: {{ ucfirst(str_replace('_', ' ', $product->arindo_status ?? 'unknown')) }}</li>
                        @endif
                    </ul>
                </div>

                <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-2 text-[11px] text-slate-700 shadow-sm sm:rounded-3xl sm:p-4 sm:text-sm">
                    <p class="font-semibold text-slate-900">Seller details</p>
                    <div class="mt-2 space-y-1.5 sm:mt-3 sm:space-y-2">
                        @if($product->seller)
                            <p>{{ $product->seller->name }}</p>
                            @if(!empty($product->seller->phone))
                                <p>{{ $product->seller->phone }}</p>
                            @endif
                            @if(!empty($product->seller->email))
                                <p>{{ $product->seller->email }}</p>
                            @endif
                        @else
                            <p>Seller information is not available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <aside class="min-w-0 space-y-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:space-y-4 sm:rounded-3xl sm:p-5">
            <div class="rounded-2xl bg-slate-50 p-3 text-xs text-slate-700 sm:p-4 sm:text-sm">
                <div class="flex items-center justify-between gap-4">
                    <span class="font-semibold text-slate-900">Price</span>
                    <span class="text-base font-semibold text-orange-600 sm:text-lg">₱{{ number_format($product->price, 2) }}</span>
                </div>
                <div class="mt-4">
                    @auth
                        <form action="{{ route('cart.add', $product) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Quantity</label>
                                <input type="number" name="quantity" min="1" value="1" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 sm:rounded-2xl sm:px-4 sm:py-3" />
                            </div>
                            <button type="submit" class="w-full rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700 sm:rounded-2xl sm:py-3">Add to Cart</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700 sm:rounded-2xl sm:py-3">Login to Add</a>
                    @endauth
                </div>
            </div>

            <div class="rounded-2xl bg-slate-50 p-3 text-xs text-slate-700 sm:p-4 sm:text-sm">
                <p class="font-semibold text-slate-900">Quick facts</p>
                <ul class="mt-3 space-y-2">
                    @if($product->expiration_date)
                        <li>Expiration: {{ $product->expiration_date->format('M d, Y') }}</li>
                    @endif
                    @if($product->map_location)
                        <li>Location available</li>
                    @endif
                </ul>
            </div>
        </aside>
    </div>

    <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:mt-6 sm:rounded-3xl sm:p-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 sm:text-xl">Customer ratings and comments</h2>
                <p class="mt-1 text-sm text-slate-500">Share your experience with this product.</p>
            </div>
            @auth
                <span class="text-sm text-slate-500">Signed in as {{ auth()->user()->name }}</span>
            @else
                <a href="{{ route('login') }}" class="text-sm font-semibold text-orange-600 hover:underline">Log in to review</a>
            @endauth
        </div>

        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        @auth
            <form method="POST" action="{{ route('products.reviews.store', $product) }}" class="mt-4 grid gap-3 border-t border-slate-100 pt-4 sm:mt-5 sm:gap-4 sm:pt-5 sm:grid-cols-[12rem_1fr_auto] sm:items-start">
                @csrf
                <div>
                    <label for="rating" class="block text-sm font-medium text-slate-700">Rating</label>
                    <div class="-mt-1 flex flex-row items-center gap-1" role="radiogroup" aria-label="Product rating" dir="ltr">
                        @for($rating = 1; $rating <= 5; $rating++)
                            <input id="rating-{{ $rating }}" type="radio" name="rating" value="{{ $rating }}" required class="peer sr-only" {{ old('rating') == $rating ? 'checked' : '' }}>
                            <label for="rating-{{ $rating }}" class="cursor-pointer text-2xl leading-8 text-gray-300 transition hover:text-amber-400 peer-checked:text-amber-500 sm:text-3xl sm:leading-10" title="{{ $rating }} out of 5 stars">&#9733;</label>
                        @endfor
                    </div>
                    @error('rating')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="comment" class="block text-sm font-medium text-slate-700">Comment</label>
                    <textarea id="comment" name="comment" rows="3" maxlength="2000" placeholder="Tell other customers what you think..." class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm sm:px-4 sm:py-3">{{ old('comment') }}</textarea>
                    @error('comment')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700 sm:w-auto sm:px-5 sm:py-3">Save review</button>
            </form>
        @endauth

        <div class="mt-6 space-y-4 border-t border-slate-100 pt-5">
            @forelse($product->reviews as $review)
                <article class="rounded-2xl bg-slate-50 p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $review->user->name ?? 'Customer' }}</p>
                            <p class="text-xs text-slate-500">{{ $review->created_at->format('M d, Y') }}</p>
                        </div>
                        <span class="flex flex-row text-xl tracking-wide text-amber-500" dir="ltr">
                            @for($star = 1; $star <= 5; $star++){!! $star <= $review->rating ? '&#9733;' : '&#9734;' !!}@endfor
                            <span class="ml-1 text-sm text-slate-500">({{ $review->rating }}/5)</span>
                        </span>
                    </div>
                    @if($review->comment)
                        <p class="mt-3 text-sm leading-6 text-slate-700">{{ $review->comment }}</p>
                    @endif
                </article>
            @empty
                <p class="py-4 text-sm text-slate-500">No ratings or comments yet.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
