@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full p-6 gap-6" style="min-height:70vh;">
    <aside class="hidden md:block w-64 bg-white rounded shadow p-4">
        @include('seller._sidebar')
    </aside>
    <div class="md:hidden w-full">
        @include('seller._mobile_nav')
    </div>
    <div class="flex-1">
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Shipment Tracking</h2>

            <div class="mb-4 text-sm text-gray-500">
                Order #{{ $order->order_number ?? $order->id }} • {{ $order->created_at->format('Y-m-d') }}
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="bg-gray-50 rounded p-4 border">
                    <h3 class="font-semibold mb-2">Order Details</h3>
                    <div>Customer: {{ $order->full_name ?? 'Customer' }}</div>
                    <div>Total: ₱{{ number_format($order->total, 2) }}</div>
                    <div>Status: {{ ucfirst($order->status) }}</div>
                </div>
                <div class="bg-gray-50 rounded p-4 border">
                    <h3 class="font-semibold mb-2">Delivery Address</h3>
                    <div>{{ $order->street_address }}, {{ $order->barangay }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</div>
                    <div class="mt-2">Phone: {{ $order->phone }}</div>
                </div>
            </div>

            <div class="mt-6 bg-gray-50 rounded p-4 border">
                <h3 class="font-semibold mb-3">Shipment Information</h3>
                @if($shipment)
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div><span class="font-semibold">Tracking #:</span> {{ $shipment->tracking_number }}</div>
                        <div><span class="font-semibold">Carrier:</span> {{ $shipment->carrier ?? '-' }}</div>
                        <div><span class="font-semibold">Status:</span> {{ ucfirst($shipment->status) }}</div>
                        <div><span class="font-semibold">Shipped At:</span> {{ $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d') : '-' }}</div>
                    </div>
                @else
                    <div class="text-gray-600">No shipment record was found for this order.</div>
                @endif
            </div>

            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold mb-4">Rider Simulator</h3>
                <p class="text-sm text-gray-600 mb-4">Use this simulator to publish live rider updates while testing customer tracking. It will send driver coordinates to the order update endpoint.</p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <button id="start-simulator" type="button" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition">
                        Start Simulation
                    </button>
                    <button id="stop-simulator" type="button" disabled class="inline-flex items-center justify-center px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                        Stop Simulation
                    </button>
                </div>
                <div id="simulator-status" class="mt-4 text-sm text-gray-700">Simulator is inactive.</div>
            </div>

            <div class="mt-6">
                <a href="{{ route('seller.orders.detail', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Back to Order</a>
                <a href="{{ route('seller.shipments') }}" class="inline-flex items-center px-4 py-2 ml-2 bg-orange-600 text-white rounded hover:bg-orange-700">View All Shipments</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const simulatorStatusEl = document.getElementById('simulator-status');
    const startSimulatorButton = document.getElementById('start-simulator');
    const stopSimulatorButton = document.getElementById('stop-simulator');
    const updateLocationUrl = '{{ route('seller.orders.update_tracking_location', $order) }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let simulatorInterval = null;
    let simulatorStep = 0;
    const destination = {
        lat: {{ $order->latitude ?? 'null' }},
        lng: {{ $order->longitude ?? 'null' }},
    };
    const startPoint = {
        lat: {{ $order->driver_latitude ?? ($order->latitude ? ($order->latitude + 0.02) : '14.5995') }},
        lng: {{ $order->driver_longitude ?? ($order->longitude ? ($order->longitude - 0.02) : '120.9842') }},
    };
    const routePoints = [];

    function createSimulationRoute() {
        routePoints.length = 0;
        if (!destination.lat || !destination.lng) {
            return;
        }

        const steps = 6;
        for (let i = 0; i <= steps; i++) {
            const ratio = i / steps;
            const lat = startPoint.lat + (destination.lat - startPoint.lat) * ratio;
            const lng = startPoint.lng + (destination.lng - startPoint.lng) * ratio;
            routePoints.push({ lat, lng });
        }
    }

    async function sendSimulatorUpdate(position, step) {
        const status = step < routePoints.length - 1 ? 'out_for_delivery' : 'to_receive';
        const response = await fetch(updateLocationUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                latitude: position.lat,
                longitude: position.lng,
                status,
                location: `Simulator update ${step + 1}`,
            }),
        });

        if (!response.ok) {
            throw new Error('Simulator update failed');
        }

        return response.json();
    }

    function updateSimulatorStatus(message) {
        simulatorStatusEl.textContent = message;
    }

    function stopSimulation() {
        if (simulatorInterval) {
            clearInterval(simulatorInterval);
            simulatorInterval = null;
        }
        simulatorStep = 0;
        startSimulatorButton.disabled = false;
        stopSimulatorButton.disabled = true;
        updateSimulatorStatus('Simulator stopped.');
    }

    function startSimulation() {
        if (!destination.lat || !destination.lng) {
            updateSimulatorStatus('Cannot simulate without destination coordinates.');
            return;
        }

        createSimulationRoute();
        if (!routePoints.length) {
            updateSimulatorStatus('No route points available for simulation.');
            return;
        }

        startSimulatorButton.disabled = true;
        stopSimulatorButton.disabled = false;
        updateSimulatorStatus('Simulation started. Sending live updates...');

        simulatorInterval = setInterval(async () => {
            if (simulatorStep >= routePoints.length) {
                stopSimulation();
                return;
            }

            const nextPoint = routePoints[simulatorStep];
            try {
                await sendSimulatorUpdate(nextPoint, simulatorStep);
                updateSimulatorStatus(`Simulator sent update ${simulatorStep + 1} of ${routePoints.length}`);
            } catch (error) {
                updateSimulatorStatus('Simulator error: ' + error.message);
                stopSimulation();
            }

            simulatorStep += 1;
        }, 4000);
    }

    startSimulatorButton.addEventListener('click', startSimulation);
    stopSimulatorButton.addEventListener('click', stopSimulation);
</script>
@endpush
