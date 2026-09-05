@extends('layouts.admin')

@section('page_title', 'Stock Monitoring')
@section('page_subtitle', 'Real-time Inventory Management')

@section('content')
<!-- Stats Cards -->
<div class="mb-6 grid grid-cols-2 gap-2 sm:gap-3 md:grid-cols-4">
    <div class="stat-card p-3 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Products</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 sm:text-2xl">{{ $totalProducts }}</p>
            </div>
                <div class="rounded-lg bg-blue-100 p-2">
                    <i class="fas fa-boxes text-lg text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-3 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Stock Value</p>
                <p class="mt-1 text-xl font-bold text-gray-900 sm:text-2xl">₱{{ number_format($totalStockValue, 0) }}</p>
            </div>
                <div class="rounded-lg bg-green-100 p-2">
                    <i class="fas fa-coins text-lg text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-3 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Low Stock</p>
                <p class="mt-1 text-xl font-bold text-yellow-600 sm:text-2xl">{{ $lowStock }}</p>
                <p class="text-xs text-yellow-600 mt-1">≤ 5 items</p>
            </div>
                <div class="rounded-lg bg-yellow-100 p-2">
                    <i class="fas fa-exclamation-triangle text-lg text-yellow-600"></i>
            </div>
        </div>
    </div>

    <div class="stat-card p-3 sm:p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Out of Stock</p>
                <p class="mt-1 text-xl font-bold text-red-600 sm:text-2xl">{{ $outOfStock }}</p>
                <p class="text-xs text-red-600 mt-1">No stock</p>
            </div>
                <div class="rounded-lg bg-red-100 p-2">
                    <i class="fas fa-times-circle text-lg text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Product Stock Table -->
<div class="stat-card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Product Inventory</h3>
        <div class="flex gap-2">
            <input type="search" id="stockSearch" placeholder="Search products..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Product Name</th>
                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Category</th>
                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Price</th>
                    <th class="text-center py-4 px-6 text-gray-600 font-semibold">Current Stock</th>
                    <th class="text-center py-4 px-6 text-gray-600 font-semibold">Stock Value</th>
                    <th class="text-center py-4 px-6 text-gray-600 font-semibold">Status</th>
                    <th class="text-center py-4 px-6 text-gray-600 font-semibold">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="py-4 px-6 font-semibold text-gray-900">{{ $product->name }}</td>
                    <td class="py-4 px-6 text-gray-600">{{ $product->category->name ?? 'N/A' }}</td>
                    <td class="py-4 px-6 text-gray-600 font-semibold">₱{{ number_format($product->price, 2) }}</td>
                    <td class="py-4 px-6 text-center">
                        <span class="inline-block px-3 py-1 rounded-lg font-semibold
                            @if($product->stock == 0) bg-red-100 text-red-700
                            @elseif($product->stock <= 5) bg-yellow-100 text-yellow-700
                            @else bg-green-100 text-green-700 @endif">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-center text-gray-900 font-semibold">
                        ₱{{ number_format($product->stock * $product->price, 2) }}
                    </td>
                    <td class="py-4 px-6 text-center">
                        @if($product->stock == 0)
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded">
                                <i class="fas fa-times-circle"></i> Out
                            </span>
                        @elseif($product->stock <= 5)
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded">
                                <i class="fas fa-exclamation-circle"></i> Low
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">
                                <i class="fas fa-check-circle"></i> Good
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center">
                        <button onclick="editStock({{ $product->id }}, '{{ $product->name }}', {{ $product->stock }})" class="text-green-600 hover:text-green-700 font-semibold text-sm">
                            <i class="fas fa-edit mr-1"></i>Update
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-3 block opacity-50"></i>
                        No products found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>

<!-- Edit Stock Modal -->
<div id="stockModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">Update Stock Level</h3>

        <form id="stockForm" method="POST" action="">
            @csrf
            @method('POST')

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Product: <span id="productName"></span></label>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">New Stock Quantity</label>
                <input type="number" name="stock" id="stockInput" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeStock()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                    Update Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editStock(productId, productName, currentStock) {
    document.getElementById('stockModal').classList.remove('hidden');
    document.getElementById('productName').textContent = productName;
    document.getElementById('stockInput').value = currentStock;
    document.getElementById('stockForm').action = `/admin/stock/${productId}/update`;
}

function closeStock() {
    document.getElementById('stockModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('stockModal').addEventListener('click', function(e) {
    if (e.target === this) closeStock();
});
</script>
@endsection
