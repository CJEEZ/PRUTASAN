@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-6 bg-gray-100">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="mb-2 text-4xl font-bold text-gray-900">Seller History</h1>
                <p class="text-gray-600">Deleted seller accounts and restore actions.</p>
            </div>
            <a href="{{ route('admin.sellers.index') }}" class="px-6 py-2 font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                Back to Sellers
            </a>
        </div>

        <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
            <form method="GET" action="{{ route('admin.sellers.history') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" placeholder="Name, email, phone..."
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                <div class="md:col-span-3 flex items-end gap-2">
                    <button type="submit" class="px-6 py-2 font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition">
                        Search
                    </button>
                    <a href="{{ route('admin.sellers.history') }}" class="px-6 py-2 font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden bg-white rounded-lg shadow-md">
            @if($deletedSellers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-sm font-semibold text-left text-gray-900">ID</th>
                                <th class="px-6 py-3 text-sm font-semibold text-left text-gray-900">Name</th>
                                <th class="px-6 py-3 text-sm font-semibold text-left text-gray-900">Email</th>
                                <th class="px-6 py-3 text-sm font-semibold text-left text-gray-900">Deleted At</th>
                                <th class="px-6 py-3 text-sm font-semibold text-left text-gray-900">Status</th>
                                <th class="px-6 py-3 text-sm font-semibold text-center text-gray-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($deletedSellers as $seller)
                                @php
                                    $sellerStatus = $seller->computed_seller_status ?? 'pending';
                                @endphp
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">#{{ $seller->id }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $seller->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $seller->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $seller->deleted_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-block px-3 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">
                                            {{ ucfirst($sellerStatus) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('admin.sellers.restore', $seller->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1 text-xs text-white transition bg-green-500 rounded hover:bg-green-600">
                                                Restore
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $deletedSellers->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="text-lg text-gray-500">No deleted seller accounts found.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
