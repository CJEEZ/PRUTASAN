@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full p-6 gap-6 min-h-screen">
    <aside class="hidden md:block w-64 bg-white rounded shadow p-4">
        @include('seller._sidebar')
    </aside>
    <div class="md:hidden w-full">
        @include('seller._mobile_nav')
    </div>
    <div class="flex-1">
        <div class="bg-white rounded shadow p-6 mb-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold">My Arindo Properties</h1>
                    <p class="text-gray-600">Manage your land listings, track verification status, and update property terms.</p>
                </div>
                <a href="{{ route('seller.arindo.properties.create') }}" class="px-5 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">Add New Arindo Property</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Total Properties</p>
                <p class="text-3xl font-bold text-gray-900">{{ $summary['total'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Pending Verification</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $summary['pending'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Active for Arindo</p>
                <p class="text-3xl font-bold text-green-600">{{ $summary['active'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Currently Pawned</p>
                <p class="text-3xl font-bold text-red-600">{{ $summary['pawned'] ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Property Listings</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Property</th>
                            <th class="px-5 py-3">Loan</th>
                            <th class="px-5 py-3">Term</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Expires</th>
                            <th class="px-5 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($properties as $property)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-14 w-20 rounded-xl overflow-hidden bg-slate-100">
                                            <img src="{{ $property->image_url ?? 'https://placehold.co/160x120' }}" alt="{{ $property->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $property->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $property->location ?? 'Location not set' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-orange-600">₱{{ number_format($property->loan_amount, 2) }}</td>
                                <td class="px-5 py-4">{{ $property->term_years }} yrs</td>
                                <td class="px-5 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase {{ $property->arindo_status === 'available_for_arindo' ? 'bg-green-100 text-green-800' : ($property->arindo_status === 'pending_verification' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ str_replace('_', ' ', ucfirst($property->arindo_status)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">{{ $property->expiration_date ? $property->expiration_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-5 py-4 space-x-2">
                                    <a href="{{ route('seller.products.edit', $property->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-gray-500">No Arindo properties yet. Add one to start the verification process.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
