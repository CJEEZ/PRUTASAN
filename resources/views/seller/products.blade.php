@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full flex-col gap-6 p-3 sm:p-4 lg:flex-row lg:gap-6 lg:p-6" style="min-height:70vh;">
    <aside class="hidden lg:block w-64 rounded bg-white p-4 shadow">
        @include('seller._sidebar')
    </aside>
    <div class="lg:hidden w-full">
        @include('seller._mobile_nav')
    </div>
    <div class="flex-1">
        <div class="rounded bg-white p-4 shadow sm:p-6">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold sm:text-xl">Products</h2>
                <a href="{{ route('seller.products.add') }}" class="min-h-[40px] rounded bg-orange-600 px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-orange-700 sm:px-6">
                    + Add Product
                </a>
            </div>
            @if(session('success'))
                <div class="mb-4 rounded bg-green-100 p-2 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 align-middle sm:px-4">Image</th>
                            <th class="px-3 py-3 align-middle sm:px-4">Name</th>
                            <th class="hidden px-3 py-3 align-middle sm:table-cell sm:px-4">Category</th>
                            <th class="px-3 py-3 align-middle sm:px-4">Price</th>
                            <th class="hidden px-3 py-3 align-middle sm:table-cell sm:px-4">Stock</th>
                            <th class="hidden px-3 py-3 align-middle lg:table-cell lg:px-4">Unit</th>
                            <th class="px-3 py-3 align-middle sm:px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr class="border-b transition hover:bg-gray-50">
                            <td class="px-3 py-3 align-middle sm:px-4">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="mx-auto h-10 w-10 rounded-full object-cover">
                                @endif
                            </td>
                            <td class="px-3 py-3 align-middle font-medium sm:px-4">{{ $product->name }}</td>
                            <td class="hidden px-3 py-3 align-middle sm:table-cell sm:px-4">{{ $product->category->name ?? 'N/A' }}</td>
                            <td class="px-3 py-3 align-middle font-semibold text-orange-600 sm:px-4">₱{{ number_format($product->price, 2) }}</td>
                            <td class="hidden px-3 py-3 align-middle sm:table-cell sm:px-4">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold
                                    @if($product->stock > 10) bg-green-100 text-green-800
                                    @elseif($product->stock > 0) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="hidden px-3 py-3 align-middle lg:table-cell lg:px-4">{{ $product->unit }}</td>
                            <td class="px-3 py-3 align-middle sm:px-4">
                                <div class="flex gap-1 flex-col sm:flex-row">
                                    <a href="{{ route('seller.products.edit', $product->id) }}" class="min-h-[32px] rounded bg-blue-600 px-2 py-1 text-center text-xs font-medium text-white transition hover:bg-blue-700 sm:px-3">Edit</a>
                                    <form method="POST" action="{{ route('seller.products.delete', $product->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="min-h-[32px] w-full rounded bg-red-600 px-2 py-1 text-xs font-medium text-white transition hover:bg-red-700 sm:w-auto sm:px-3">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-4 text-center text-gray-500">No products found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
