@extends('layouts.admin')

@section('page_title', 'Review Arindo Submission')
@section('page_subtitle', 'Verify documentation and confirm the listing')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.arindo.index') }}" class="text-orange-600 hover:text-orange-700 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Arindo Listings
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <div class="flex flex-wrap items-center gap-4 mb-6">
            <div class="w-20 h-20 rounded-3xl overflow-hidden shadow-sm bg-gray-100">
                <img src="{{ $product->image_url ?? 'https://placehold.co/320x320?text=No+Image' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-[0.2em]">Arindo Property</p>
                <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="mt-2 text-sm text-gray-600">{{ $product->description }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                <p class="text-sm text-gray-500">Loan Amount</p>
                <p class="text-2xl font-bold text-orange-600">₱{{ number_format($product->loan_amount, 2) }}</p>
            </div>
            <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                <p class="text-sm text-gray-500">Term</p>
                <p class="text-2xl font-bold text-gray-900">{{ $product->term_years ?? 0 }} years</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Property & Harvest Details</h2>
                <div class="grid grid-cols-1 gap-3 text-sm text-gray-700">
                    <div><span class="font-medium">Location:</span> {{ $product->location ?? 'N/A' }}</div>
                    <div><span class="font-medium">Coordinate / Map Pin:</span> {{ $product->map_location ?? 'N/A' }}</div>
                    <div><span class="font-medium">Yield Description:</span> {{ $product->crop_yield_description ?? 'N/A' }}</div>
                    <div><span class="font-medium">Expiration Date:</span> {{ $product->expiration_date ? $product->expiration_date->format('M d, Y') : 'N/A' }}</div>
                </div>
            </div>

            <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Documentation</h2>
                <div class="space-y-3 text-sm text-gray-700">
                    <div><span class="font-medium">Land Photos:</span></div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($product->land_photo_urls ?? [] as $photoUrl)
                            <a href="{{ $photoUrl }}" target="_blank" class="block rounded-2xl border border-gray-200 overflow-hidden hover:border-orange-400 transition">
                                <img src="{{ $photoUrl }}" alt="Land photo" class="w-full h-32 object-cover">
                            </a>
                        @endforeach
                        @if(empty($product->land_photo_urls))
                            <div class="rounded-2xl border border-gray-200 p-4 text-gray-500">No land photos submitted.</div>
                        @endif
                    </div>
                    <div><span class="font-medium">Soil Report:</span> <a href="{{ $product->soil_report_url }}" target="_blank" class="text-blue-600">View document</a></div>
                    <div><span class="font-medium">Legal Document:</span> <a href="{{ $product->legal_document_url }}" target="_blank" class="text-blue-600">View document</a></div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-5">
        <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
            <p class="text-sm text-gray-500">Current Listing Status</p>
            <div class="mt-3 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $product->arindo_status === 'available_for_arindo' ? 'bg-green-100 text-green-800' : ($product->arindo_status === 'pending_verification' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-700') }}">
                {{ str_replace('_', ' ', ucfirst($product->arindo_status)) }}
            </div>
        </div>

        <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Audit Trail</h2>
            <div class="space-y-4 text-sm text-gray-700">
                <div>
                    <p class="font-medium">Created</p>
                    <p>{{ $product->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="font-medium">Last Updated</p>
                    <p>{{ $product->updated_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="font-medium">Verified</p>
                    <p>{{ $product->arindo_verified_at ? $product->arindo_verified_at->format('M d, Y H:i') : 'Not verified yet' }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.arindo.verify', $product) }}" method="POST" class="space-y-3">
            @csrf
            @method('PATCH')
            <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Verify Listing</button>
        </form>

        <form action="{{ route('admin.arindo.reject', $product) }}" method="POST" class="space-y-3">
            @csrf
            @method('PATCH')
            <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Reject Listing</button>
        </form>
    </div>
</div>
@endsection
