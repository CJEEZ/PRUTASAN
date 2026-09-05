@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Customer Management</h1>
            <p class="text-sm text-gray-600">View and manage all customers</p>
        </div>

        <!-- Search and Filter -->
        <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <!-- Search -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" placeholder="Name, email, phone..."
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Role Filter -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Role</label>
                    <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">All Roles</option>
                        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="seller" {{ request('role') == 'seller' ? 'selected' : '' }}>Seller</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full px-6 py-2 font-medium text-white transition bg-orange-600 rounded-lg hover:bg-orange-700">
                        Filter
                    </button>
                    <a href="{{ route('admin.customers.index') }}" class="w-full px-6 py-2 font-medium text-center text-gray-700 transition bg-gray-300 rounded-lg hover:bg-gray-400">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Export Button -->
        <div class="mb-6 flex justify-end">
            <!-- Export to CSV functionality can be added later -->
        </div>

        <!-- Customers Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($customers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">ID</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Phone</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Role</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Registered</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($customers as $customer)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-900">#{{ $customer->id }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $customer->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->phone_number ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $customer->role === 'seller' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($customer->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('admin.customers.show', $customer) }}"
                                               class="inline-block px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition">
                                                View Details
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $customers->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-users text-5xl text-gray-300 mb-4 block"></i>
                    <p class="text-gray-500 text-lg">No customers found</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
