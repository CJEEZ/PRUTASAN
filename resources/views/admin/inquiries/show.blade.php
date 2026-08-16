@extends('layouts.app')

@section('content')
<style>header{display:none;}</style>
<div class="w-full mx-auto px-4 sm:px-8 lg:px-12 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Inquiry #{{ $inquiry->id }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-green-800 text-white rounded-lg hover:bg-green-900">Dashboard</a>
            <a href="{{ route('admin.inquiries.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Back</a>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Inquiry Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold">FROM</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($inquiry->user)
                                <a href="{{ route('admin.customers.show', $inquiry->user->id) }}" class="text-green-700 hover:underline">{{ $inquiry->user->name }}</a>
                            @else
                                {{ $inquiry->name }}
                            @endif
                        </p>
                        <p class="text-sm text-gray-600">
                            @if($inquiry->user)
                                <a href="mailto:{{ $inquiry->user->email }}" class="hover:underline">{{ $inquiry->user->email }}</a>
                            @else
                                {{ $inquiry->email }}
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 font-semibold">SUBJECT</p>
                        <p class="text-base font-medium text-gray-900">{{ $inquiry->subject ?? '(No subject)' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 font-semibold">MESSAGE</p>
                        <div class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 whitespace-pre-wrap">{{ $inquiry->message }}</div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 font-semibold">SUBMITTED</p>
                        <p class="text-sm text-gray-600">{{ $inquiry->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">STATUS</h3>
                <div class="mb-4">
                    <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold
                        @if($inquiry->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800 @endif">
                        {{ ucfirst($inquiry->status) }}
                    </span>
                </div>
                @if($inquiry->status === 'pending')
                    <form method="POST" action="{{ route('admin.inquiries.update', $inquiry->id) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="resolved">
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-sm">Mark as Resolved</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.inquiries.update', $inquiry->id) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="pending">
                        <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-semibold text-sm">Mark as Pending</button>
                    </form>
                @endif
            </div>

            <!-- Customer Account -->
            @if($inquiry->user)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">CUSTOMER ACCOUNT</h3>
                <a href="{{ route('admin.customers.show', $inquiry->user->id) }}" class="inline-block">
                    <div class="p-3 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100 transition">
                        <p class="text-sm font-semibold text-green-900">{{ $inquiry->user->name }}</p>
                        <p class="text-xs text-green-700">{{ $inquiry->user->email }}</p>
                        <p class="text-xs text-green-700 mt-1">User #{{ $inquiry->user->id }}</p>
                    </div>
                </a>
            </div>
            @endif

            <!-- Delete Button -->
            <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry->id) }}" onsubmit="return confirm('Delete this inquiry?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold text-sm">Delete Inquiry</button>
            </form>
        </div>
    </div>
</div>
@endsection
