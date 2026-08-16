@extends('layouts.app')

@section('content')
<div class="bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Seller Approval Requests</h1>
            <p class="text-gray-600 mt-2">Review and manage seller account requests</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Pending Requests</p>
                        <p class="text-3xl font-bold text-orange-600 mt-1">{{ $pendingRequests->total() }}</p>
                    </div>
                    <div class="text-4xl text-orange-100">⏳</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Approved Sellers</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">{{ $approvedSellers }}</p>
                    </div>
                    <div class="text-4xl text-green-100">✅</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Rejected Requests</p>
                        <p class="text-3xl font-bold text-red-600 mt-1">{{ $rejectedRequests }}</p>
                    </div>
                    <div class="text-4xl text-red-100">❌</div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- Pending Requests Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Pending Requests</h2>
            </div>

            @if ($pendingRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Request Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($pendingRequests as $seller)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $seller->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $seller->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $seller->phone_number ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $seller->seller_request_date?->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('admin.seller-approvals.show', $seller) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pendingRequests->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="text-gray-500">No pending seller requests at the moment.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
