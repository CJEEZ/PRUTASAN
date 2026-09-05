@extends('layouts.app')

@section('content')
<style>
    header { display: none; }
</style>

<div class="min-h-screen bg-gray-100">
    <div class="mx-auto w-full px-3 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-8">
        <div class="mb-4">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-orange-600 hover:text-orange-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
        <div class="mb-8">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Product Management</h1>
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center justify-center rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-orange-700 sm:px-6 sm:py-3">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Product
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 flex items-center rounded-lg bg-green-100 p-3 text-sm text-green-800 sm:p-4">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
                <table class="w-full divide-y divide-gray-200 text-xs sm:text-sm">
                    <thead class="divide-y border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left text-[10px] font-medium text-gray-600 uppercase sm:px-4 sm:py-3">Product Name</th>
                            <th class="hidden px-2 py-2 text-left text-[10px] font-medium text-gray-600 uppercase sm:table-cell sm:px-4 sm:py-3">Category</th>
                            <th class="px-2 py-2 text-left text-[10px] font-medium text-gray-600 uppercase sm:px-4 sm:py-3">Price</th>
                            <th class="hidden px-2 py-2 text-left text-[10px] font-medium text-gray-600 uppercase sm:table-cell sm:px-4 sm:py-3">Stock</th>
                            <th class="hidden px-2 py-2 text-left text-[10px] font-medium text-gray-600 uppercase lg:table-cell lg:px-4 lg:py-3">Unit</th>
                            <th class="px-2 py-2 text-left text-[10px] font-medium text-gray-600 uppercase sm:px-4 sm:py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-2 py-2 sm:px-4 sm:py-3">
                                    <div class="flex items-center gap-2">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-8 w-8 rounded-full object-cover">
                                        @endif
                                        <span class="text-xs font-medium text-gray-900 sm:text-sm">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="hidden px-2 py-2 text-[10px] text-gray-600 sm:table-cell sm:px-4 sm:py-3">
                                    {{ $product->category->name ?? 'N/A' }}
                                </td>
                                <td class="px-2 py-2 text-[10px] font-semibold text-orange-600 sm:px-4 sm:py-3">
                                    ₱{{ number_format($product->price, 2) }}
                                </td>
                                <td class="hidden px-2 py-2 text-[10px] sm:table-cell sm:px-4 sm:py-3">
                                    <span class="inline-block rounded-full px-2 py-1 text-xs font-semibold
                                        @if($product->stock > 10) bg-green-100 text-green-800
                                        @elseif($product->stock > 0) bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="hidden px-2 py-2 text-[10px] text-gray-600 lg:table-cell lg:px-4 lg:py-3">
                                    {{ $product->unit }}
                                </td>
                                <td class="space-x-1 px-2 py-2 text-[10px] sm:px-4 sm:py-3 sm:space-x-2">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium" onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                    <p>No products found. <a href="{{ route('admin.products.create') }}" class="text-orange-600 hover:text-orange-700 font-medium">Add one now</a></p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
