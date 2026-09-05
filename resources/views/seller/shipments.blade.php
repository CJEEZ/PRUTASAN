@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full min-w-0 flex-col gap-3 p-3 sm:gap-6 sm:p-4 lg:flex-row lg:p-6" style="min-height:70vh;">
    <aside class="hidden lg:block w-64 rounded bg-white p-4 shadow">
        @include('seller._sidebar')
    </aside>
    <div class="w-full min-w-0 lg:hidden">
        @include('seller._mobile_nav')
    </div>
    <div class="min-w-0 flex-1">
        <div class="rounded bg-white p-3 shadow sm:p-6">
            <h2 class="mb-3 text-lg font-semibold sm:mb-4 sm:text-xl">Shipments</h2>
            @if(session('success'))
                <div class="mb-4 rounded bg-green-100 p-2 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            <div class="w-full max-w-full overflow-x-auto overscroll-x-contain">
                <table class="min-w-[560px] text-left text-[11px] sm:w-full sm:min-w-0 sm:text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="whitespace-nowrap px-1.5 py-1 text-[9px] font-semibold text-gray-700 sm:px-4 sm:py-3 sm:text-sm">Order #</th>
                            <th class="whitespace-nowrap px-1.5 py-1 text-[9px] font-semibold text-gray-700 sm:px-4 sm:py-3 sm:text-sm">Tracking #</th>
                            <th class="whitespace-nowrap px-1.5 py-1 text-[9px] font-semibold text-gray-700 sm:px-4 sm:py-3 sm:text-sm">Carrier</th>
                            <th class="whitespace-nowrap px-1.5 py-1 text-[9px] font-semibold text-gray-700 sm:px-4 sm:py-3 sm:text-sm">Status</th>
                            <th class="whitespace-nowrap px-1.5 py-1 text-[9px] font-semibold text-gray-700 sm:px-4 sm:py-3 sm:text-sm">Shipped At</th>
                            <th class="whitespace-nowrap px-1.5 py-1 text-[9px] font-semibold text-gray-700 sm:px-4 sm:py-3 sm:text-sm">Assigned rider</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                        <tr class="border-t transition hover:bg-gray-50">
                            <td class="whitespace-nowrap px-1.5 py-1 align-middle sm:px-4 sm:py-3">{{ $shipment->order->order_number ?? $shipment->order->id }}</td>
                            <td class="whitespace-nowrap px-1.5 py-1 align-middle sm:px-4 sm:py-3">{{ $shipment->tracking_number }}</td>
                            <td class="whitespace-nowrap px-1.5 py-1 align-middle sm:px-4 sm:py-3">{{ $shipment->carrier }}</td>
                            <td class="whitespace-nowrap px-1.5 py-1 align-middle sm:px-4 sm:py-3">
                                <span class="inline-block rounded-full px-1 py-0.5 text-[9px] font-semibold sm:px-2 sm:py-1 sm:text-xs
                                    @if($shipment->status === 'shipped') bg-blue-100 text-blue-800
                                    @elseif($shipment->status === 'delivered') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $shipment->status === 'ready_for_pickup' ? 'To Ship' : ucfirst(str_replace('_', ' ', $shipment->status)) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-1.5 py-1 align-middle sm:px-4 sm:py-3">{{ $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d') : '-' }}</td>
                            <td class="whitespace-nowrap px-1.5 py-1 align-middle sm:px-4 sm:py-3">
                                @if($shipment->driver)
                                    <span class="text-[11px] text-gray-700 sm:text-sm">{{ $shipment->driver->name }}</span>
                                @else
                                    <span class="text-xs text-gray-500">Waiting for rider</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-2 py-3 text-center text-xs text-gray-500 sm:py-4 sm:text-left sm:text-sm">No shipments found.</td>
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
