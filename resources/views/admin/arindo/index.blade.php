@extends('layouts.admin')

@section('page_title', 'Arindo Listings')
@section('page_subtitle', 'Verify land agreements and monitor expiring contracts')

@section('content')
<div class="scrollbar-on-interaction mb-5 overflow-x-auto pb-1">
    <div class="flex min-w-max gap-3">
    <div class="stat-card w-48 flex-none p-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-[10px] font-medium">Pending Verifications</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $pendingCount }}</p>
            </div>
            <div class="bg-yellow-100 p-1.5 rounded-lg">
                <i class="fas fa-hourglass-half text-base text-yellow-600"></i>
            </div>
        </div>
    </div>
    <div class="stat-card w-48 flex-none p-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-[10px] font-medium">Active Arindo Listings</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $activeCount }}</p>
            </div>
            <div class="bg-green-100 p-1.5 rounded-lg">
                <i class="fas fa-check-circle text-base text-green-600"></i>
            </div>
        </div>
    </div>
    <div class="stat-card w-48 flex-none p-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-[10px] font-medium">Expiring Agreements</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $expiringAgreements->count() }}</p>
            </div>
            <div class="bg-blue-100 p-1.5 rounded-lg">
                <i class="fas fa-calendar-alt text-base text-blue-600"></i>
            </div>
        </div>
    </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 mb-5 lg:grid-cols-2">
    <div class="bg-white rounded-lg shadow p-3">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-base font-bold text-gray-900">Pending Listings</h2>
                <p class="text-xs text-gray-500">Review seller documents and approve new Arindo properties.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="py-2 px-3">Property</th>
                        <th class="py-2 px-3">Seller</th>
                        <th class="py-2 px-3">Loan</th>
                        <th class="py-2 px-3">Status</th>
                        <th class="py-2 px-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listings->where('arindo_status', 'pending_verification') as $product)
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="py-2 px-3 text-xs font-medium">{{ $product->name }}</td>
                            <td class="py-2 px-3 text-xs">{{ $product->seller->name ?? 'Unknown' }}</td>
                            <td class="py-2 px-3 text-xs text-orange-600">₱{{ number_format($product->loan_amount, 2) }}</td>
                            <td class="py-2 px-3 text-xs">
                                <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs uppercase">Pending</span>
                            </td>
                            <td class="py-2 px-3 text-xs space-x-2">
                                <a href="{{ route('admin.arindo.show', $product) }}" class="text-blue-600 hover:text-blue-800">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-3 text-center text-gray-500">No listings pending verification.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-3">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-base font-bold text-gray-900">Expiring Agreements</h2>
                <p class="text-xs text-gray-500">Notify owners and investors before the contract end date.</p>
            </div>
        </div>
        <div class="space-y-4">
            @forelse($expiringAgreements as $product)
                <div class="rounded-lg border border-gray-200 p-3 hover:border-blue-300 transition">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $product->name }}</div>
                            <p class="text-xs text-gray-500">Expires on {{ $product->expiration_date->format('M d, Y') }}</p>
                        </div>
                        <a href="{{ route('admin.arindo.show', $product) }}" class="text-blue-600 hover:text-blue-800 text-xs">View</a>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No agreements expiring in the next 90 days.</p>
            @endforelse
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-800">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow p-3">
    <h2 class="text-lg font-semibold mb-3">All Arindo Listings</h2>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="py-2 px-3">Property</th>
                    <th class="py-2 px-3">Seller</th>
                    <th class="py-2 px-3">Loan Amount</th>
                    <th class="py-2 px-3">Status</th>
                    <th class="py-2 px-3">Expires</th>
                    <th class="py-2 px-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($listings as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-2 px-3 text-xs font-medium">{{ $product->name }}</td>
                        <td class="py-2 px-3 text-xs">{{ $product->seller->name ?? 'Unknown' }}</td>
                        <td class="py-2 px-3 text-xs text-orange-600">₱{{ number_format($product->loan_amount, 2) }}</td>
                        <td class="py-2 px-3 text-xs">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold uppercase {{ $product->arindo_status === 'available_for_arindo' ? 'bg-green-100 text-green-800' : ($product->arindo_status === 'pending_verification' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-700') }}">
                                {{ str_replace('_', ' ', ucfirst($product->arindo_status)) }}
                            </span>
                        </td>
                        <td class="py-2 px-3 text-xs">{{ $product->expiration_date ? $product->expiration_date->format('M d, Y') : 'N/A' }}</td>
                        <td class="py-2 px-3 text-xs space-x-2">
                            <a href="{{ route('admin.arindo.show', $product) }}" class="text-blue-600 hover:text-blue-800">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">No Arindo listings available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
