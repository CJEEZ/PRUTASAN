@extends('layouts.app')

@section('content')
<div class="bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="{{ route('admin.seller-approvals.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                ← Back to Requests
            </a>
        </div>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-600 mt-2">{{ $user->email }}</p>
                </div>
                <div>
                    @if ($user->seller_status === 'pending')
                        <span class="px-4 py-2 bg-orange-100 text-orange-800 rounded-full font-medium text-sm">
                            🟡 Pending Review
                        </span>
                    @elseif ($user->seller_status === 'approved')
                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full font-medium text-sm">
                            ✅ Approved
                        </span>
                    @elseif ($user->seller_status === 'rejected')
                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full font-medium text-sm">
                            ❌ Rejected
                        </span>
                    @endif
                </div>
            </div>

            <!-- User Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="text-gray-900 mt-1">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <p class="text-gray-900 mt-1">{{ $user->phone_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Request Date</label>
                    <p class="text-gray-900 mt-1">{{ $user->seller_request_date?->format('M d, Y H:i A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Account Created</label>
                    <p class="text-gray-900 mt-1">{{ $user->created_at->format('M d, Y H:i A') }}</p>
                </div>
            </div>

            <!-- Shop Information -->
            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Shop Information</h2>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Shop Name</label>
                        <p class="text-gray-900 mt-1">{{ $user->shop_name ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Business Type</label>
                        <p class="text-gray-900 mt-1">{{ $user->business_type ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Shop Description</label>
                        <p class="text-gray-900 mt-1 whitespace-pre-wrap">{{ $user->shop_description ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Rejection Reason (if rejected) -->
            @if ($user->seller_status === 'rejected' && $user->seller_rejection_reason)
                <div class="border-t border-gray-200 pt-6 mt-6 bg-red-50 border border-red-200 rounded p-4">
                    <h3 class="font-semibold text-red-900 mb-2">Rejection Reason</h3>
                    <p class="text-red-800">{{ $user->seller_rejection_reason }}</p>
                </div>
            @endif
        </div>

        <!-- Actions -->
        @if ($user->seller_status === 'pending')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Approve Button -->
                <form action="{{ route('admin.seller-approvals.approve', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition">
                        ✅ Approve Seller
                    </button>
                </form>

                <!-- Reject Button/Form -->
                <div id="reject-container">
                    <button type="button" onclick="toggleRejectForm()" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition">
                        ❌ Reject Seller
                    </button>
                </div>
            </div>

            <!-- Reject Form (Hidden by default) -->
            <form id="reject-form" action="{{ route('admin.seller-approvals.reject', $user) }}" method="POST" class="hidden mt-4 bg-red-50 border border-red-200 rounded-lg p-6">
                @csrf
                <h3 class="text-lg font-semibold text-red-900 mb-4">Reject Seller Request</h3>
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason <span class="text-red-600">*</span></label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="4" required placeholder="Explain why this seller is being rejected..." class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"></textarea>
                    @error('rejection_reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        Confirm Rejection
                    </button>
                    <button type="button" onclick="toggleRejectForm()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-medium py-2 px-4 rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        @else
            <div class="text-center">
                <p class="text-gray-600">This seller request has already been {{ strtolower($user->seller_status) }}.</p>
            </div>
        @endif
    </div>
</div>

<script>
function toggleRejectForm() {
    const rejectForm = document.getElementById('reject-form');
    const rejectContainer = document.getElementById('reject-container');
    rejectForm.classList.toggle('hidden');
    if (!rejectForm.classList.contains('hidden')) {
        rejectForm.querySelector('textarea').focus();
    }
}
</script>
@endsection
