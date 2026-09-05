@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-3 bg-gray-100">
    <div class="mx-auto max-w-7xl">
        <!-- Header -->
        <div class="mb-5">
            <h1 class="mb-1 text-2xl font-bold text-gray-900">Seller Management</h1>
            <p class="text-sm text-gray-600">View and manage all sellers</p>
        </div>

        <!-- Search and Filter -->
        <div class="p-3 mb-4 bg-white rounded-lg shadow-md">
            <form method="GET" action="{{ route('admin.sellers.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <!-- Search -->
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Search</label>
                    <input type="text" name="search" placeholder="Name, email, phone..."
                           value="{{ request('search') }}"
                           class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Date From -->
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Status -->
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Status</label>
                    <select name="status" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">All statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full px-4 py-1.5 text-sm font-medium text-white transition bg-orange-600 rounded-lg hover:bg-orange-700">
                        Filter
                    </button>
                    <a href="{{ route('admin.sellers.index') }}" class="w-full px-4 py-1.5 text-sm font-medium text-center text-gray-700 transition bg-gray-300 rounded-lg hover:bg-gray-400">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats -->
        <div class="scrollbar-on-interaction mb-3 overflow-x-auto pb-1">
            <div class="flex min-w-max gap-2">
            <div class="w-40 flex-none bg-white rounded-lg shadow-md p-2">
                <p class="text-xs font-medium text-gray-500">Total Sellers</p>
                <p class="mt-1 text-xl font-bold text-gray-900">{{ $totalSellers }}</p>
            </div>
            <div class="w-40 flex-none bg-white rounded-lg shadow-md p-2">
                <p class="text-xs font-medium text-gray-500">Approved</p>
                <p class="mt-1 text-xl font-bold text-green-700">{{ $approvedSellers }}</p>
            </div>
            <div class="w-40 flex-none bg-white rounded-lg shadow-md p-2">
                <p class="text-xs font-medium text-gray-500">Pending</p>
                <p class="mt-1 text-xl font-bold text-yellow-700">{{ $pendingSellers }}</p>
            </div>
            <div class="w-40 flex-none bg-white rounded-lg shadow-md p-2">
                <p class="text-xs font-medium text-gray-500">Rejected</p>
                <p class="mt-1 text-xl font-bold text-red-700">{{ $rejectedSellers }}</p>
            </div>
            <div class="w-40 flex-none bg-white rounded-lg shadow-md p-2">
                <p class="text-xs font-medium text-gray-500">Deleted</p>
                <p class="mt-1 text-xl font-bold text-gray-900">{{ $deletedSellers }}</p>
            </div>
            </div>
        </div>

        <div class="flex flex-col gap-2 mb-4 md:flex-row md:justify-between">
            <a href="{{ route('admin.sellers.history') }}" class="px-4 py-1.5 text-sm font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-800 transition">
                View History
            </a>
            <div class="flex gap-2">
                <a href="{{ route('admin.sellers.export') }}" class="px-4 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    Export CSV
                </a>
                <a href="{{ route('admin.sellers.index') }}" class="px-4 py-1.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    Refresh
                </a>
            </div>
        </div>

        <!-- Sellers Table -->
        <div class="overflow-hidden bg-white rounded-lg shadow-md">
            @if($sellers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-xs font-semibold text-left text-gray-900">ID</th>
                                <th class="px-3 py-2 text-xs font-semibold text-left text-gray-900">Name</th>
                                <th class="px-3 py-2 text-xs font-semibold text-left text-gray-900">Email</th>
                                <th class="px-3 py-2 text-xs font-semibold text-left text-gray-900">Phone</th>
                                <th class="px-3 py-2 text-xs font-semibold text-left text-gray-900">Products</th>
                                <th class="px-3 py-2 text-xs font-semibold text-left text-gray-900">Status</th>
                                <th class="px-3 py-2 text-xs font-semibold text-left text-gray-900">Registered</th>
                                <th class="px-3 py-2 text-xs font-semibold text-center text-gray-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($sellers as $seller)
                                @php
                                    $sellerStatus = $seller->computed_seller_status ?? 'pending';
                                @endphp
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-3 py-2 text-xs text-gray-900">#{{ $seller->id }}</td>
                                    <td class="px-3 py-2 text-xs font-medium text-gray-900">{{ $seller->name }}</td>
                                    <td class="px-3 py-2 text-xs text-gray-600">{{ $seller->email }}</td>
                                    <td class="px-3 py-2 text-xs text-gray-600">{{ $seller->phone_number ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 text-xs">
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-semibold text-blue-800 bg-blue-100 rounded-full">
                                            {{ $seller->products_count }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-xs">
                                        @if($sellerStatus === 'approved')
                                            <span class="inline-block px-2 py-0.5 text-[10px] font-semibold text-green-800 bg-green-100 rounded-full">
                                                Approved
                                            </span>
                                        @elseif($sellerStatus === 'rejected')
                                            <span class="inline-block px-2 py-0.5 text-[10px] font-semibold text-red-800 bg-red-100 rounded-full">
                                                Rejected
                                            </span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 text-[10px] font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-600">{{ $seller->created_at->format('M d, Y') }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('admin.sellers.show', $seller) }}"
                                               class="inline-block px-2 py-0.5 text-[10px] text-white transition bg-blue-500 rounded hover:bg-blue-600">
                                                View
                                            </a>
                                            @if($sellerStatus === 'approved')
                                                <form action="{{ route('admin.sellers.reject', $seller) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-2 py-0.5 text-[10px] text-white transition bg-red-500 rounded hover:bg-red-600">
                                                        Deactivate
                                                    </button>
                                                </form>
                                            @elseif($sellerStatus === 'rejected')
                                                <form action="{{ route('admin.sellers.approve', $seller) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-2 py-0.5 text-[10px] text-white transition bg-green-500 rounded hover:bg-green-600">
                                                        Approve
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.sellers.approve', $seller) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-2 py-0.5 text-[10px] text-white transition bg-green-500 rounded hover:bg-green-600">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.sellers.reject', $seller) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-2 py-0.5 text-[10px] text-white transition bg-red-500 rounded hover:bg-red-600">
                                                        Reject
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-3 py-2 border-t border-gray-200">
                    {{ $sellers->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="block mb-4 text-5xl text-gray-300 fas fa-store"></i>
                    <p class="text-lg text-gray-500">No sellers found</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
