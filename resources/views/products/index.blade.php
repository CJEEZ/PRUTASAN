@extends('layouts.app')

@section('content')
    <div class="px-4 pb-6 sm:px-6 lg:px-8">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Fresh Seasonal Fruits</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $products->total() }} products available</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('home') }}" class="rounded-full bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-700">All</a>
                <a href="{{ route('home', ['category' => 'tropical']) }}" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">Tropical</a>
                <a href="{{ route('home', ['category' => 'arindo']) }}" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">Arindo</a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($products as $product)
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <a href="{{ route('products.show', $product) }}" class="block overflow-hidden">
                        <img src="{{ $product->thumbnail ? asset('storage/'.$product->thumbnail) : ($product->image_url ?? 'https://via.placeholder.com/400x300') }}" alt="{{ $product->name ?? $product->title }}" class="h-44 w-full object-cover transition duration-300 hover:scale-105">
                    </a>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-base font-semibold text-slate-900">{{ $product->name ?? $product->title }}</h3>
                                @if(optional($product->category)->name)
                                    <p class="mt-1 text-xs uppercase tracking-[0.3em] text-slate-500">{{ optional($product->category)->name }}</p>
                                @endif
                            </div>
                            <span class="text-sm font-semibold text-orange-600">₱{{ number_format($product->price,2) }}</span>
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-3 text-xs text-slate-500">
                            @if(isset($product->stock))
                                <span>{{ $product->stock > 0 ? 'In stock' : 'Out of stock' }}</span>
                            @endif
                            <span>{{ $product->unit ?? '1kg' }}</span>
                        </div>

                        <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            @auth
                                <button class="w-full rounded-2xl bg-orange-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-orange-700">Add</button>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-orange-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-orange-700">Login</a>
                            @endauth
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
@endsection
