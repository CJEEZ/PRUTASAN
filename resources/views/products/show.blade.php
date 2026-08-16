@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 pb-6 sm:px-6 lg:px-8">
    <div class="grid gap-6 lg:grid-cols-[1.5fr_0.9fr]">
        <div class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-100">
                <img src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : ($product->image_url ?? 'https://via.placeholder.com/900x500') }}" alt="{{ $product->thumbnail || $product->image_url ? $product->name . ' product image' : 'No image available for ' . $product->name }}" class="h-64 w-full object-cover sm:h-72" onerror="this.onerror=null;this.src='https://placehold.co/900x500/FF7F00/ffffff?text=No+Image';">
            </div>

            <div class="space-y-3">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-900">{{ $product->name }}</h1>
                        <p class="text-sm text-slate-500">{{ $product->description ?? 'No description available.' }}</p>
                    </div>
                    <div class="rounded-full bg-slate-100 px-3 py-1 text-sm uppercase tracking-[0.25em] text-slate-600">{{ $product->category->name ?? 'General' }}</div>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                        <p class="font-semibold text-slate-900">Price</p>
                        <p class="mt-2">₱{{ number_format($product->price, 2) }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                        <p class="font-semibold text-slate-900">Stock</p>
                        <p class="mt-2">{{ isset($product->stock) ? ($product->stock > 0 ? $product->stock . ' pcs' : 'Out of stock') : 'Unknown' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                        <p class="font-semibold text-slate-900">Unit</p>
                        <p class="mt-2">{{ $product->unit ?? '1kg' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 bg-white p-4 text-sm text-slate-700 shadow-sm">
                    <p class="font-semibold text-slate-900">Product features</p>
                    <ul class="mt-3 space-y-2">
                        <li>{{ $product->is_seasonal ? 'Seasonal product' : 'Year-round availability' }}</li>
                        <li>{{ $product->is_exotic ? 'Exotic variety' : 'Local variety' }}</li>
                        @if($product->is_arindo)
                            <li>Arindo status: {{ ucfirst(str_replace('_', ' ', $product->arindo_status ?? 'unknown')) }}</li>
                        @endif
                    </ul>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-4 text-sm text-slate-700 shadow-sm">
                    <p class="font-semibold text-slate-900">Seller details</p>
                    <div class="mt-3 space-y-2">
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

        <aside class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                <div class="flex items-center justify-between gap-4">
                    <span class="font-semibold text-slate-900">Price</span>
                    <span class="text-lg font-semibold text-orange-600">₱{{ number_format($product->price, 2) }}</span>
                </div>
                <div class="mt-4">
                    @auth
                        <form action="{{ route('cart.add', $product) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Quantity</label>
                                <input type="number" name="quantity" min="1" value="1" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                            </div>
                            <button type="submit" class="w-full rounded-2xl bg-orange-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-orange-700">Add to Cart</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-orange-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-orange-700">Login to Add</a>
                    @endauth
                </div>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
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
</div>
@endsection
