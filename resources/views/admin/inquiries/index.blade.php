@extends('layouts.app')

@section('content')
<style>header{display:none;}</style>
<div class="w-full mx-auto px-4 sm:px-8 lg:px-12 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Customer Inquiries</h1>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-green-800 text-white rounded-lg hover:bg-green-900">Dashboard</a>
    </div>

    <!-- Current Admin User Info -->
    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg shadow p-4 mb-6 border border-green-200">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-green-600 rounded-full flex items-center justify-center">
                <i class="fas fa-user-shield text-white text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-green-700 font-semibold">Logged in as:</p>
                <p class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</p>
                <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-3 items-end">
            <div class="flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or email..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" />
            </div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                <option value="">All</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="resolved" {{ request('status')=='resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
            <button class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">Filter</button>
            <a href="{{ route('admin.inquiries.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Reset</a>
        </form>
    </div>

    <!-- Inquiries Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($inquiries as $inquiry)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $inquiry->id }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($inquiry->user)
                                <a href="{{ route('admin.customers.show', $inquiry->user->id) }}" class="font-semibold text-green-700 hover:underline">{{ $inquiry->user->name }}</a>
                            @else
                                {{ $inquiry->name }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <a href="mailto:{{ $inquiry->user ? $inquiry->user->email : $inquiry->email }}" class="hover:underline">
                                {{ $inquiry->user ? $inquiry->user->email : $inquiry->email }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($inquiry->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($inquiry->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $inquiry->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm flex gap-2">
                            <a href="{{ route('admin.inquiries.show', $inquiry->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">View</a>
                            @if($inquiry->status === 'pending')
                                <form method="POST" action="{{ route('admin.inquiries.update', $inquiry->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="resolved">
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Resolve</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry->id) }}" class="inline" onsubmit="return confirm('Delete this inquiry?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">No inquiries found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">{{ $inquiries->links() }}</div>
</div>
@endsection
