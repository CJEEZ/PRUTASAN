@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full flex-col gap-6 p-3 sm:p-4 lg:flex-row lg:gap-6 lg:p-6" style="min-height:70vh;">
    <aside class="hidden lg:block w-64 rounded bg-white p-4 shadow">
        @include('seller._sidebar')
    </aside>
    <div class="lg:hidden w-full">
        @include('seller._mobile_nav')
    </div>
    <div class="flex-1">
        <div class="rounded bg-white p-4 shadow sm:p-6">
            <h2 class="mb-4 text-lg font-semibold sm:text-xl">Shipments</h2>
            @if(session('success'))
                <div class="mb-4 rounded bg-green-100 p-2 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 font-semibold text-gray-700 sm:px-4 sm:py-3">Order #</th>
                            <th class="px-3 py-2 font-semibold text-gray-700 sm:px-4 sm:py-3">Tracking #</th>
                            <th class="hidden px-3 py-2 font-semibold text-gray-700 sm:table-cell sm:px-4 sm:py-3">Carrier</th>
                            <th class="px-3 py-2 font-semibold text-gray-700 sm:px-4 sm:py-3">Status</th>
                            <th class="hidden px-3 py-2 font-semibold text-gray-700 lg:table-cell lg:px-4 lg:py-3">Shipped At</th>
                            <th class="px-3 py-2 font-semibold text-gray-700 sm:px-4 sm:py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                        <tr class="border-t transition hover:bg-gray-50">
                            <td class="px-3 py-2 align-middle sm:px-4 sm:py-3">{{ $shipment->order->order_number ?? $shipment->order->id }}</td>
                            <td class="px-3 py-2 align-middle text-xs sm:px-4 sm:py-3 sm:text-sm">{{ $shipment->tracking_number }}</td>
                            <td class="hidden px-3 py-2 align-middle sm:table-cell sm:px-4 sm:py-3">{{ $shipment->carrier }}</td>
                            <td class="px-3 py-2 align-middle sm:px-4 sm:py-3">
                                <span class="inline-block rounded-full px-2 py-1 text-xs font-semibold
                                    @if($shipment->status === 'shipped') bg-blue-100 text-blue-800
                                    @elseif($shipment->status === 'delivered') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($shipment->status) }}
                                </span>
                            </td>
                            <td class="hidden px-3 py-2 align-middle lg:table-cell lg:px-4 lg:py-3">{{ $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d') : '-' }}</td>
                            <td class="px-3 py-2 align-middle sm:px-4 sm:py-3">
                                <form method="POST" action="{{ route('seller.shipments.update', $shipment->id) }}" class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="tracking_number" value="{{ $shipment->tracking_number }}">
                                    <input type="hidden" name="carrier" value="{{ $shipment->carrier }}">
                                    <select name="status" class="min-h-[32px] rounded border border-gray-300 px-2 py-1 text-xs sm:text-sm">
                                        <option value="shipped" {{ $shipment->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                        <option value="delivered" {{ $shipment->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    </select>
                                    <button type="submit" class="min-h-[32px] rounded bg-orange-600 px-3 py-1 text-xs font-medium text-white transition hover:bg-orange-700 sm:text-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">No shipments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $shipments->links() }}</div>
        </div>
    </div>
</div>
@endsection
