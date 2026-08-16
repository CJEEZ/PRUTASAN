@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $seller->name }}</h1>
                <p class="text-gray-600">Seller Details</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.sellers.edit', $seller) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Edit
                </a>
                <a href="{{ route('admin.sellers.index') }}" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition">
                    Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 mx-auto rounded-full bg-blue-100 flex items-center justify-center text-4xl font-bold text-blue-600">
                            {{ strtoupper(substr($seller->name, 0, 1)) }}
                        </div>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase">Email</label>
                            <p class="text-gray-900">{{ $seller->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase">Phone</label>
                            <p class="text-gray-900">{{ $seller->phone_number ?? 'N/A' }}</p>
                        </div>
                        @php
                            $sellerStatus = $seller->computed_seller_status ?? 'pending';
                            $statusClasses = [
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                            ];
                        @endphp
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
                            <p>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$sellerStatus] ?? $statusClasses['pending'] }}">
                                    {{ ucfirst($sellerStatus) }}
                                </span>
                            </p>
                            @if($sellerStatus === 'rejected' && $seller->seller_rejection_reason)
                                <p class="mt-2 text-sm text-red-600">Rejection reason: {{ $seller->seller_rejection_reason }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase">Member Since</label>
                            <p class="text-gray-900">{{ $seller->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Status Actions -->
                    <div class="flex flex-col gap-2">
                        @if($sellerStatus === 'approved')
                            <form action="{{ route('admin.sellers.reject', $seller) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-medium text-sm">
                                    Deactivate Seller
                                </button>
                            </form>
                        @elseif($sellerStatus === 'rejected')
                            <form action="{{ route('admin.sellers.approve', $seller) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-medium text-sm">
                                    Approve Seller
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.sellers.approve', $seller) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-medium text-sm">
                                    Approve Seller
                                </button>
                            </form>
                            <form action="{{ route('admin.sellers.reject', $seller) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition font-medium text-sm">
                                    Reject Seller
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.sellers.destroy', $seller) }}" method="POST" onsubmit="return confirm('Delete this seller and all their products?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition font-medium text-sm">
                                Delete Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stats and Products -->
            <div class="lg:col-span-2">
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Products</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $totalProducts }}</p>
                            </div>
                            <i class="fas fa-box text-4xl text-blue-400 opacity-20"></i>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Active Products</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $activeProducts }}</p>
                            </div>
                            <i class="fas fa-check-circle text-4xl text-green-400 opacity-20"></i>
                        </div>
                    </div>
                </div>

                <!-- Sales Stats -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Orders</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $totalOrders }}</p>
                            </div>
                            <i class="fas fa-shopping-cart text-4xl text-purple-400 opacity-20"></i>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Sales</p>
                                <p class="text-3xl font-bold text-gray-900">₱{{ number_format($totalSales, 2) }}</p>
                            </div>
                            <i class="fas fa-credit-card text-4xl text-green-400 opacity-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Recent Products</h3>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($products->take(6) as $product)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                            <div class="h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-image text-3xl text-gray-300"></i>
                                @endif
                            </div>
                            <div class="p-4">
                                <h4 class="font-medium text-gray-900 truncate">{{ $product->name }}</h4>
                                <p class="text-sm text-gray-600 mb-2">₱{{ number_format($product->price, 2) }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                        Stock: {{ $product->stock }}
                                    </span>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($products->count() > 6)
                    <p class="text-center text-gray-500 mt-4">+{{ $products->count() - 6 }} more products</p>
                @endif
            @else
                <p class="text-center text-gray-500 py-8">No products yet</p>
            @endif
        </div>
    </div>
</div>
@endsection
