@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-800 font-semibold mb-4 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Orders
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Order Tracking</h1>
            <p class="text-gray-600 mt-2">Order #{{ $order->order_number }}</p>
            {{-- Map and broadcast status will be shown near the map so the page remains friendly. --}}
        </div>

        <!-- Status Banner with LIVE Badge for "To Receive" -->
        <div class="bg-gradient-to-r from-{{ $statusInfo['color'] }}-50 to-{{ $statusInfo['color'] }}-100 border border-{{ $statusInfo['color'] }}-200 rounded-lg p-6 mb-8 {{ in_array($order->status, ['to_receive', 'out_for_delivery']) ? 'shadow-lg' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-{{ $statusInfo['icon'] }} text-{{ $statusInfo['color'] }}-600 text-2xl"></i>
                        <div>
                            <h2 class="text-2xl font-bold text-{{ $statusInfo['color'] }}-900">{{ $statusInfo['label'] }}</h2>
                            @if(in_array($order->status, ['to_receive', 'out_for_delivery']))
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-500 text-white">
                                        <span class="flex h-2 w-2 rounded-full bg-white mr-1.5 animate-pulse"></span>
                                        LIVE TRACKING
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <p class="text-{{ $statusInfo['color'] }}-700">{{ $statusInfo['description'] }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-{{ $statusInfo['color'] }}-600 mb-1">Order Date</p>
                    <p class="text-lg font-semibold text-{{ $statusInfo['color'] }}-900">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Live Map -->
        @if($hasLocation || $hasDriverLocation || !empty($fullAddress))
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-map-location-dot text-red-600"></i>
                        Live Order Location
                    </h3>
                    @if($hasDriverLocation || in_array($order->status, ['to_receive', 'out_for_delivery', 'shipped', 'in_transit']))
                        <span class="px-3 py-1 bg-red-500 text-white text-sm font-semibold rounded-full animate-pulse">
                            Updating in Real-Time
                        </span>
                    @endif
                </div>
                <div id="liveTrackingMap" class="w-full h-96 rounded-lg border border-gray-200"></div>

                <div class="mt-3 text-sm text-gray-600 flex items-center gap-4">
                    <div class="inline-flex items-center px-3 py-1 bg-gray-50 border border-gray-200 rounded">
                        <strong class="mr-2">Map:</strong>
                        @if(!empty(env('GOOGLE_MAPS_API_KEY')))
                            <span class="text-green-700">Google Maps (API key present)</span>
                        @else
                            <span class="text-yellow-700">Leaflet fallback (no Google Maps key)</span>
                        @endif
                    </div>

                    <div class="inline-flex items-center px-3 py-1 bg-gray-50 border border-gray-200 rounded">
                        <strong class="mr-2">Live updates:</strong>
                        @if(env('BROADCAST_CONNECTION') === 'reverb')
                            <span class="text-green-700">Enabled (Reverb)</span>
                        @else
                            <span class="text-yellow-700">Disabled — polling only</span>
                        @endif
                    </div>

                    <a href="/docs/tracking-setup" class="ml-auto text-sm text-blue-600 hover:underline">Setup instructions</a>
                </div>

                <div id="routeInfo" class="mt-3 text-sm text-gray-700 hidden">
                    <strong>ETA:</strong> <span id="routeEta">—</span>
                    <span class="mx-2">•</span>
                    <strong>Distance:</strong> <span id="routeDistance">—</span>
                </div>

                @if($hasDriverLocation && in_array($order->status, ['to_receive', 'out_for_delivery']))
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Driver is on the way!</strong> Your order will arrive shortly. The location updates automatically.
                        </p>
                    </div>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Tracking Timeline -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Tracking Timeline</h3>

                    <div class="relative">
                        <!-- Timeline Line -->
                        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                        <!-- Timeline Items -->
                        <div class="space-y-6">
                            @foreach($timeline as $index => $event)
                                <div class="relative pl-20">
                                    <!-- Timeline Dot -->
                                    <div class="absolute left-0 top-0 w-14 h-14 rounded-full flex items-center justify-center" style="background-color: {{ $event['completed'] ? '#10b981' : '#e5e7eb' }}; left: -1rem;">
                                        <i class="fas fa-{{ $event['icon'] }} text-white text-lg"></i>
                                    </div>

                                    <!-- Content -->
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $event['label'] }}</h4>
                                                <p class="text-sm text-gray-600 mt-1">{{ $event['description'] }}</p>

                                                @if(isset($event['carrier']) && $event['carrier'])
                                                    <p class="text-xs text-gray-500 mt-2">
                                                        <strong>Carrier:</strong> {{ $event['carrier'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        <strong>Tracking:</strong> {{ $event['tracking_number'] }}
                                                    </p>
                                                @endif

                                                @if(isset($event['current_location']) && $event['current_location'])
                                                    <p class="text-xs text-gray-500 mt-2">
                                                        <strong>Current Location:</strong> {{ $event['current_location'] }}
                                                    </p>
                                                @endif
                                            </div>
                                            @if($event['timestamp'])
                                                <div class="text-right flex-shrink-0">
                                                    <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($event['timestamp'])->format('M d') }}</p>
                                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($event['timestamp'])->format('H:i') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Items List -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Order Items</h3>

                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                                <div class="flex items-center gap-3">
                                    @if($item->product && $item->product->image_url)
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-12 h-12 rounded object-cover">
                                    @else
                                        <div class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-box text-gray-300"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $item->product->name ?? 'Item' }}</p>
                                        <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">₱{{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="space-y-2">
                            <div class="flex justify-between text-gray-700">
                                <span>Subtotal</span>
                                <span>₱{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>Shipping</span>
                                <span>₱{{ number_format($order->shipping, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-200">
                                <span>Total</span>
                                <span>₱{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map (if location available or address can be geocoded) -->
                @if($hasLocation || $hasDriverLocation || !empty($fullAddress))
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Delivery Location</h3>
                        <div id="trackingMap" class="w-full h-96 rounded-lg border border-gray-200"></div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Shipment Info -->
                @if($shipment)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Shipment Details</h3>

                        <div class="space-y-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Carrier</p>
                                <p class="text-gray-900 font-medium">{{ $shipment->carrier ?? 'Standard Shipping' }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Tracking Number</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <code class="bg-gray-100 text-gray-900 px-3 py-2 rounded text-sm font-mono">{{ $shipment->tracking_number }}</code>
                                    <button class="text-gray-500 hover:text-gray-700" onclick="copyToClipboard('{{ $shipment->tracking_number }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Status</p>
                                <p class="text-gray-900 font-medium">{{ $shipment->status ?? 'In Transit' }}</p>
                            </div>

                            @if($shipment->shipped_at)
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Shipped Date</p>
                                    <p class="text-gray-900 font-medium">{{ $shipment->shipped_at->format('M d, Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Delivery Address -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Delivery Address</h3>

                    <div class="space-y-2">
                        <p class="font-semibold text-gray-900">{{ $order->full_name }}</p>
                        <p class="text-gray-700">{{ $order->street_address }}</p>
                        <p class="text-gray-700">{{ $order->barangay }}, {{ $order->city }}</p>
                        <p class="text-gray-700">{{ $order->province }} {{ $order->postal_code }}</p>
                        <p class="text-gray-700 mt-3">
                            <i class="fas fa-phone mr-2"></i> {{ $order->phone }}
                        </p>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Payment Information</h3>

                    <div class="space-y-3">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Method</p>
                            <p class="text-gray-900 font-medium capitalize">{{ $order->payment_method ?? 'Not specified' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Status</p>
                            <div class="mt-1">
                                @if($order->payment_status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Paid
                                    </span>
                                @elseif($order->payment_status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($order->payment_confirmed_at)
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Confirmed At</p>
                                <p class="text-gray-900 font-medium">{{ $order->payment_confirmed_at->format('M d, Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    @if($order->status === 'delivered')
                        <form method="POST" action="{{ route('order.request_return', $order) }}" onsubmit="return confirm('Request a return for this order?');">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg transition">
                                <i class="fas fa-undo mr-2"></i> Request Return
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('order.buy_again', $order) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                            <i class="fas fa-shopping-cart mr-2"></i> Buy Again
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet (fallback) CSS & JS - used only when Google Maps is unavailable -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.js"></script>

<script>
    let liveMap = null;
    let driverMarker = null;
    let customerMarker = null;
    let destinationMarker = null;
    let liveRoutePath = null;
    let staticMap = null;
    let staticRoutePath = null;
    let directionsService = null;
    let directionsRenderer = null;
    // Leaflet fallback instances (prefix L_)
    let L_liveMap = null;
    let L_driverMarker = null;
    let L_customerMarker = null;
    let L_destinationMarker = null;
    let L_liveRoute = null;
    const fallbackOrderAddress = @json($fullAddress ?? '');
    const defaultMapCenter = {
        lat: parseFloat('{{ $order->latitude ?? 14.5995 }}'),
        lng: parseFloat('{{ $order->longitude ?? 120.9842 }}'),
    };

    function resolveMapCenter(callback) {
        const hasStoredCoordinates = '{{ $order->latitude ?? null }}' !== 'null' && '{{ $order->longitude ?? null }}' !== 'null';

        if (hasStoredCoordinates) {
            callback({
                lat: parseFloat('{{ $order->latitude ?? 14.5995 }}'),
                lng: parseFloat('{{ $order->longitude ?? 120.9842 }}'),
            });
            return;
        }

        if (!fallbackOrderAddress) {
            callback(defaultMapCenter);
            return;
        }

        fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(fallbackOrderAddress)}`)
            .then(response => response.json())
            .then(results => {
                if (results && results.length > 0) {
                    callback({
                        lat: parseFloat(results[0].lat),
                        lng: parseFloat(results[0].lon),
                    });
                    return;
                }

                callback(defaultMapCenter);
            })
            .catch(() => callback(defaultMapCenter));
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Tracking number copied to clipboard!');
        });
    }

    function createMarker(position, map, title, iconUrl) {
        return new google.maps.Marker({
            position,
            map,
            title,
            icon: {
                url: iconUrl,
                scaledSize: new google.maps.Size(32, 32),
            },
        });
    }

    function getDestinationPosition() {
        return {
            lat: parseFloat('{{ $order->latitude ?? 14.5995 }}'),
            lng: parseFloat('{{ $order->longitude ?? 120.9842 }}'),
        };
    }

    function updateRoutePath(map, routePath, driverMarker, destinationMarker) {
        if (!map || !driverMarker || !destinationMarker) {
            return null;
        }

        const path = [driverMarker.getPosition(), destinationMarker.getPosition()];

        if (routePath) {
            routePath.setPath(path);
            return routePath;
        }

        return new google.maps.Polyline({
            path,
            geodesic: true,
            strokeColor: '#2563eb',
            strokeOpacity: 0.8,
            strokeWeight: 4,
            map,
        });
    }

    function fitMapToMarkers(map, markers) {
        const bounds = new google.maps.LatLngBounds();
        let hasMarker = false;

        markers.forEach(marker => {
            if (marker) {
                bounds.extend(marker.getPosition());
                hasMarker = true;
            }
        });

        if (hasMarker) {
            map.fitBounds(bounds, 50);
        }
    }

    function initializeLiveTrackingMap() {
        const liveMapElement = document.getElementById('liveTrackingMap');
        if (!liveMapElement || !window.google || !window.google.maps) return;

        resolveMapCenter((center) => {
            liveMap = new google.maps.Map(liveMapElement, {
                center,
                zoom: 14,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
            });

            const destinationPosition = getDestinationPosition();
            destinationMarker = createMarker(
                destinationPosition,
                liveMap,
                'Delivery Destination',
                'https://maps.google.com/mapfiles/ms/icons/green-dot.png'
            );

            new google.maps.InfoWindow({
                content: '<strong>Delivery Destination</strong><br>{{ $fullAddress }}'
            }).open(liveMap, destinationMarker);

            @if($hasLocation)
                customerMarker = createMarker(
                    { lat: parseFloat('{{ $order->latitude }}'), lng: parseFloat('{{ $order->longitude }}') },
                    liveMap,
                    'Delivery Address',
                    'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                );

                new google.maps.InfoWindow({
                    content: '<strong>Delivery Address</strong><br>{{ $fullAddress }}'
                }).open(liveMap, customerMarker);
            @endif

            @if($hasDriverLocation)
                driverMarker = createMarker(
                    { lat: parseFloat('{{ $order->driver_latitude }}'), lng: parseFloat('{{ $order->driver_longitude }}') },
                    liveMap,
                    'Driver Location',
                    'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
                );

                new google.maps.InfoWindow({
                    content: '<strong>Driver Location</strong><br>On the way to you'
                }).open(liveMap, driverMarker);
            @endif

            liveRoutePath = updateRoutePath(liveMap, liveRoutePath, driverMarker, destinationMarker);
            fitMapToMarkers(liveMap, [customerMarker, driverMarker, destinationMarker]);

            // Initialize Directions service and renderer for driving route + ETA
            if (window.google && window.google.maps) {
                try {
                    directionsService = new google.maps.DirectionsService();
                    directionsRenderer = new google.maps.DirectionsRenderer({
                        suppressMarkers: true,
                        polylineOptions: { strokeColor: '#2563eb', strokeWeight: 5, strokeOpacity: 0.9 },
                    });
                    directionsRenderer.setMap(liveMap);

                    // If we have a driver position, request initial route
                    if (driverMarker && destinationMarker) {
                        requestRoute(driverMarker.getPosition(), destinationMarker.getPosition());
                    }
                } catch (e) {
                    console.warn('DirectionsService init failed', e);
                }
            }
        });
    }

    function initializeStaticTrackingMap() {
        const trackingMapElement = document.getElementById('trackingMap');
        if (!trackingMapElement || !window.google || !window.google.maps) return;

        resolveMapCenter((center) => {
            staticMap = new google.maps.Map(trackingMapElement, {
                center,
                zoom: 13,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
            });

            const destinationPosition = getDestinationPosition();
            destinationMarker = createMarker(
                destinationPosition,
                staticMap,
                'Delivery Destination',
                'https://maps.google.com/mapfiles/ms/icons/green-dot.png'
            );

            new google.maps.InfoWindow({
                content: '<strong>Delivery Destination</strong><br>{{ $fullAddress }}'
            }).open(staticMap, destinationMarker);

            if ({{ $hasLocation ? 'true' : 'false' }}) {
                customerMarker = createMarker(
                    { lat: parseFloat('{{ $order->latitude ?? 14.5995 }}'), lng: parseFloat('{{ $order->longitude ?? 120.9842 }}') },
                    staticMap,
                    'Delivery Address',
                    'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                );
            }

            @if($hasDriverLocation && in_array($order->status, ['shipped', 'in_transit', 'out_for_delivery']))
                driverMarker = createMarker(
                    { lat: parseFloat('{{ $order->driver_latitude }}'), lng: parseFloat('{{ $order->driver_longitude }}') },
                    staticMap,
                    'Driver Location',
                    'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
                );
            @endif

            staticRoutePath = updateRoutePath(staticMap, staticRoutePath, driverMarker, destinationMarker);
            fitMapToMarkers(staticMap, [customerMarker, driverMarker, destinationMarker]);
        });
    }

    function updateLiveTrackingLocation() {
        fetch('{{ route("tracking.getTrackingData", $order) }}')
            .then(response => response.json())
            .then(data => {
                if (data.driver_latitude && data.driver_longitude) {
                    const newPosition = { lat: parseFloat(data.driver_latitude), lng: parseFloat(data.driver_longitude) };

                    if (liveMap) {
                        updateDriverMarker(newPosition);
                        liveRoutePath = updateRoutePath(liveMap, liveRoutePath, driverMarker, destinationMarker);
                    }

                    if (typeof L !== 'undefined' && L_liveMap) {
                        try {
                            updateLeafletDriverMarker(newPosition);
                        } catch (error) {
                            console.warn('Leaflet live update failed', error);
                        }
                    }
                }

                if (data.status) {
                    console.log('Order status:', data.status);
                }
            })
            .catch(error => console.log('Error fetching tracking data:', error));
    }

        const destinationPosition = getDestinationPosition();
        destinationMarker = createMarker(
            destinationPosition,
            liveMap,
            'Delivery Destination',
            'https://maps.google.com/mapfiles/ms/icons/green-dot.png'
        );

        new google.maps.InfoWindow({
            content: '<strong>Delivery Destination</strong><br>{{ $fullAddress }}'
        }).open(liveMap, destinationMarker);

        @if($hasLocation)
            customerMarker = createMarker(
                { lat: parseFloat('{{ $order->latitude }}'), lng: parseFloat('{{ $order->longitude }}') },
                liveMap,
                'Delivery Address',
                'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
            );

            new google.maps.InfoWindow({
                content: '<strong>Delivery Address</strong><br>{{ $fullAddress }}'
            }).open(liveMap, customerMarker);
        @endif

        @if($hasDriverLocation)
            driverMarker = createMarker(
                { lat: parseFloat('{{ $order->driver_latitude }}'), lng: parseFloat('{{ $order->driver_longitude }}') },
                liveMap,
                'Driver Location',
                'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
            );

            new google.maps.InfoWindow({
                content: '<strong>Driver Location</strong><br>On the way to you'
            }).open(liveMap, driverMarker);
        @endif

        liveRoutePath = updateRoutePath(liveMap, liveRoutePath, driverMarker, destinationMarker);
        fitMapToMarkers(liveMap, [customerMarker, driverMarker, destinationMarker]);

        // Initialize Directions service and renderer for driving route + ETA
        if (window.google && window.google.maps) {
            try {
                directionsService = new google.maps.DirectionsService();
                directionsRenderer = new google.maps.DirectionsRenderer({
                    suppressMarkers: true,
                    polylineOptions: { strokeColor: '#2563eb', strokeWeight: 5, strokeOpacity: 0.9 },
                });
                directionsRenderer.setMap(liveMap);

                // If we have a driver position, request initial route
                if (driverMarker && destinationMarker) {
                    requestRoute(driverMarker.getPosition(), destinationMarker.getPosition());
                }
            } catch (e) {
                console.warn('DirectionsService init failed', e);
            }
        }
    }

    function initializeStaticTrackingMap() {
        const trackingMapElement = document.getElementById('trackingMap');
        if (!trackingMapElement || !window.google || !window.google.maps) return;

        const center = {
            lat: parseFloat('{{ $order->latitude ?? 14.5995 }}'),
            lng: parseFloat('{{ $order->longitude ?? 120.9842 }}'),
        };

        staticMap = new google.maps.Map(trackingMapElement, {
            center,
            zoom: 13,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
        });

        const destinationPosition = getDestinationPosition();
        destinationMarker = createMarker(
            destinationPosition,
            staticMap,
            'Delivery Destination',
            'https://maps.google.com/mapfiles/ms/icons/green-dot.png'
        );

        new google.maps.InfoWindow({
            content: '<strong>Delivery Destination</strong><br>{{ $fullAddress }}'
        }).open(staticMap, destinationMarker);

        if ({{ $hasLocation ? 'true' : 'false' }}) {
            customerMarker = createMarker(
                { lat: parseFloat('{{ $order->latitude ?? 14.5995 }}'), lng: parseFloat('{{ $order->longitude ?? 120.9842 }}') },
                staticMap,
                'Delivery Address',
                'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
            );
        }

        @if($hasDriverLocation && in_array($order->status, ['shipped', 'in_transit', 'out_for_delivery']))
            driverMarker = createMarker(
                { lat: parseFloat('{{ $order->driver_latitude }}'), lng: parseFloat('{{ $order->driver_longitude }}') },
                staticMap,
                'Driver Location',
                'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
            );
        @endif

        staticRoutePath = updateRoutePath(staticMap, staticRoutePath, driverMarker, destinationMarker);
        fitMapToMarkers(staticMap, [customerMarker, driverMarker, destinationMarker]);
    }

    function updateLiveTrackingLocation() {
        fetch('{{ route("tracking.getTrackingData", $order) }}')
            .then(response => response.json())
            .then(data => {
                if (data.driver_latitude && data.driver_longitude) {
                    const newPosition = { lat: parseFloat(data.driver_latitude), lng: parseFloat(data.driver_longitude) };

                    if (liveMap) {
                        updateDriverMarker(newPosition);
                        liveRoutePath = updateRoutePath(liveMap, liveRoutePath, driverMarker, destinationMarker);
                    }

                    if (typeof L !== 'undefined' && L_liveMap) {
                        try {
                            updateLeafletDriverMarker(newPosition);
                        } catch (error) {
                            console.warn('Leaflet live update failed', error);
                        }
                    }
                }

                if (data.status) {
                    console.log('Order status:', data.status);
                }
            })
            .catch(error => console.log('Error fetching tracking data:', error));
    }

    function subscribeToRiderLocationUpdates() {
        if (!window.Echo || !window.Echo.private) {
            console.warn('Echo is not available. Falling back to polling.');
            return;
        }

        try {
            const channel = window.Echo.private(`orders.{{ $order->id }}`);
            channel.listen('RiderLocationUpdated', handleBroadcastLocationUpdate);
        } catch (error) {
            console.warn('Unable to subscribe to broadcast channel:', error);
        }
    }

    function handleBroadcastLocationUpdate(payload) {
        if (!payload || !payload.driver_latitude || !payload.driver_longitude) {
            return;
        }

        updateDriverMarker({
            lat: parseFloat(payload.driver_latitude),
            lng: parseFloat(payload.driver_longitude),
        });

        // Also update Leaflet fallback markers/routes when active
        if (typeof updateLeafletDriverMarker === 'function' && typeof L !== 'undefined' && L_liveMap) {
            try {
                updateLeafletDriverMarker({ lat: parseFloat(payload.driver_latitude), lng: parseFloat(payload.driver_longitude) });
            } catch (e) {
                console.warn('Leaflet update failed', e);
            }
        }
    }

    function animateMarker(marker, targetPosition, duration = 800) {
        if (!marker) return;

        const startPosition = marker.getPosition();
        const startLat = startPosition.lat();
        const startLng = startPosition.lng();
        const latStep = (targetPosition.lat - startLat) / Math.max(1, Math.round(duration / 16));
        const lngStep = (targetPosition.lng - startLng) / Math.max(1, Math.round(duration / 16));
        let frame = 0;
        const frames = Math.max(1, Math.round(duration / 16));

        function step() {
            frame += 1;
            marker.setPosition({
                lat: startLat + latStep * frame,
                lng: startLng + lngStep * frame,
            });

            if (frame < frames) {
                requestAnimationFrame(step);
            }
        }

        requestAnimationFrame(step);
    }

    function updateDriverMarker(position) {
        if (!liveMap) {
            return;
        }

        if (driverMarker) {
            animateMarker(driverMarker, position);
        } else {
            driverMarker = createMarker(position, liveMap, 'Driver Location', 'https://maps.google.com/mapfiles/ms/icons/red-dot.png');
        }

        if (destinationMarker) {
            liveRoutePath = updateRoutePath(liveMap, liveRoutePath, driverMarker, destinationMarker);
            // Update driving route and ETA via Directions API when available
            if (typeof requestRoute === 'function' && directionsService && directionsRenderer) {
                try {
                    requestRoute(driverMarker.getPosition(), destinationMarker.getPosition());
                } catch (e) {
                    console.warn('requestRoute failed', e);
                }
            }
        }

        if (customerMarker || destinationMarker) {
            fitMapToMarkers(liveMap, [customerMarker, driverMarker, destinationMarker]);
        } else {
            liveMap.panTo(position);
        }
    }

    window.initOrderTrackingMap = function() {
        initializeLiveTrackingMap();
        initializeStaticTrackingMap();
        subscribeToRiderLocationUpdates();

        const isToReceive = '{{ $order->status }}' === 'to_receive';
        const updateInterval = isToReceive ? 5000 : 30000;

        setInterval(updateLiveTrackingLocation, updateInterval);
        setTimeout(updateLiveTrackingLocation, 1000);
        // If we already have driver coordinates, immediately ensure marker + route are visible
        @if($hasDriverLocation)
            setTimeout(function() {
                try {
                    const pos = { lat: parseFloat('{{ $order->driver_latitude }}'), lng: parseFloat('{{ $order->driver_longitude }}') };
                    if (typeof updateDriverMarker === 'function') updateDriverMarker(pos);
                    if (typeof updateLeafletDriverMarker === 'function' && typeof L !== 'undefined') {
                        try { updateLeafletDriverMarker(pos); } catch (e) { /* ignore */ }
                    }
                } catch (e) { console.warn('Initial marker update failed', e); }
            }, 600);
        @endif
    };

    // Request a driving route from origin to destination and display ETA/distance
    function requestRoute(originLatLng, destinationLatLng) {
        if (!directionsService || !directionsRenderer || !window.google || !window.google.maps) return;

        const request = {
            origin: originLatLng,
            destination: destinationLatLng,
            travelMode: google.maps.TravelMode.DRIVING,
            provideRouteAlternatives: false,
        };

        directionsService.route(request, function(result, status) {
            if (status === 'OK' && result && result.routes && result.routes.length) {
                directionsRenderer.setDirections(result);
                try {
                    const leg = result.routes[0].legs[0];
                    const durationText = leg.duration ? leg.duration.text : null;
                    const distanceText = leg.distance ? leg.distance.text : null;
                    const routeInfoEl = document.getElementById('routeInfo');
                    const etaEl = document.getElementById('routeEta');
                    const distEl = document.getElementById('routeDistance');

                    if (etaEl) etaEl.textContent = durationText || '—';
                    if (distEl) distEl.textContent = distanceText || '—';
                    if (routeInfoEl) routeInfoEl.classList.remove('hidden');
                } catch (e) {
                    console.warn('Failed to parse directions result', e);
                }
            } else {
                console.warn('Directions request failed:', status);
            }
        });
    }
</script>
<script>
    // Show a visible banner when Google Maps fails to authenticate or load
    function showMapError(message) {
        const existing = document.getElementById('map-error-banner');
        if (existing) {
            existing.textContent = message;
            return;
        }
        const banner = document.createElement('div');
        banner.id = 'map-error-banner';
        banner.className = 'fixed bottom-6 right-6 z-50 p-3 bg-red-50 text-red-800 border border-red-200 rounded shadow';
        banner.textContent = message;
        document.body.appendChild(banner);
    }

    // Called by Google Maps when auth fails
    window.gm_authFailure = function() {
        showMapError('Google Maps authentication failed. Switching to fallback map.');
        // initialize Leaflet fallback
        try { initLeafletFallback(); } catch (e) { console.error('Leaflet fallback failed', e); }
    };

    // If maps library doesn't load within X seconds, show a helpful message
    (function waitForMaps() {
        const checkAfter = 8000; // ms
        setTimeout(() => {
            if (!window.google || !window.google.maps || typeof initOrderTrackingMap !== 'function') {
                showMapError('Google Maps did not load. Switching to fallback map.');
                try { initLeafletFallback(); } catch (e) { console.error('Leaflet fallback failed', e); }
            }
        }, checkAfter);
    })();
</script>
@if(!empty(env('GOOGLE_MAPS_API_KEY')))
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initOrderTrackingMap"></script>
@else
    <script>
        // No Google Maps API key present — initialize Leaflet fallback immediately
        document.addEventListener('DOMContentLoaded', function() {
            try { initLeafletFallback(); } catch (e) { console.error('Leaflet init failed', e); }
        });
    </script>
@endif

<script>
    // Initialize a basic Leaflet fallback for both map containers
    function initLeafletFallback() {
        const liveEl = document.getElementById('liveTrackingMap');
        if (liveEl && typeof L !== 'undefined' && !L_liveMap) {
            resolveMapCenter((center) => {
                L_liveMap = L.map('liveTrackingMap').setView([center.lat, center.lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(L_liveMap);

                @if($hasLocation)
                    L_customerMarker = L.marker([{{ $order->latitude }}, {{ $order->longitude }}]).addTo(L_liveMap).bindPopup('{{ $fullAddress }}');
                @else
                    if (fallbackOrderAddress) {
                        L_customerMarker = L.marker([center.lat, center.lng]).addTo(L_liveMap).bindPopup(fallbackOrderAddress);
                    }
                @endif

                @if($hasDriverLocation)
                    L_driverMarker = L.marker([{{ $order->driver_latitude }}, {{ $order->driver_longitude }}], { icon: L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', iconSize: [25,41], iconAnchor: [12,41] }) }).addTo(L_liveMap).bindPopup('Driver Location');
                @endif

                L_destinationMarker = L.marker([center.lat, center.lng], { icon: L.icon({ iconUrl: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png' }) }).addTo(L_liveMap).bindPopup('Delivery Destination');

                if (L_customerMarker || L_driverMarker || L_destinationMarker) {
                    const group = new L.featureGroup([L_customerMarker, L_driverMarker, L_destinationMarker].filter(Boolean));
                    L_liveMap.fitBounds(group.getBounds().pad(0.12));
                    if (L_driverMarker && L_destinationMarker) {
                        try { requestLeafletRoute(L_driverMarker.getLatLng(), L_destinationMarker.getLatLng()); } catch (e) { console.warn('OSRM initial request failed', e); }
                    }
                }
            });
        }

        const staticEl = document.getElementById('trackingMap');
        if (staticEl && typeof L !== 'undefined' && !staticEl.dataset.leafletReady) {
            resolveMapCenter((center) => {
                const staticMap = L.map('trackingMap').setView([center.lat, center.lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(staticMap);
                L.marker([center.lat, center.lng]).addTo(staticMap).bindPopup(fallbackOrderAddress || 'Delivery Address');

                @if($hasDriverLocation && in_array($order->status, ['shipped', 'in_transit', 'out_for_delivery']))
                    L.marker([{{ $order->driver_latitude }}, {{ $order->driver_longitude }}]).addTo(staticMap).bindPopup('Driver Location');
                @endif

                staticEl.dataset.leafletReady = 'true';
            });
        }
    }

    function requestLeafletRoute(originLatLng, destinationLatLng) {
        if (!originLatLng || !destinationLatLng || typeof L === 'undefined' || !L_liveMap) return;

        const src = originLatLng.lng + ',' + originLatLng.lat;
        const dst = destinationLatLng.lng + ',' + destinationLatLng.lat;
        const url = `https://router.project-osrm.org/route/v1/driving/${src};${dst}?overview=full&geometries=geojson`;

        fetch(url).then(r => r.json()).then(data => {
            if (!data || data.code !== 'Ok' || !data.routes || !data.routes.length) {
                console.warn('OSRM route not available', data);
                return;
            }

            const route = data.routes[0];

            if (L_liveRoute) {
                try { L_liveMap.removeLayer(L_liveRoute); } catch (e) { /* ignore */ }
                L_liveRoute = null;
            }

            L_liveRoute = L.geoJSON(route.geometry, {
                style: { color: '#2563eb', weight: 5, opacity: 0.9 }
            }).addTo(L_liveMap);

            try {
                const bounds = L_liveRoute.getBounds();
                if (bounds && bounds.isValid()) L_liveMap.fitBounds(bounds.pad(0.12));
            } catch (e) { /* ignore */ }

            try {
                const durationSec = route.duration || 0;
                const distanceMeters = route.distance || 0;
                const mins = Math.round(durationSec / 60);
                const km = (distanceMeters / 1000).toFixed(1);
                const etaEl = document.getElementById('routeEta');
                const distEl = document.getElementById('routeDistance');
                const routeInfoEl = document.getElementById('routeInfo');

                if (etaEl) etaEl.textContent = (mins > 0) ? `${mins} min` : `${Math.round(durationSec)} sec`;
                if (distEl) distEl.textContent = `${km} km`;
                if (routeInfoEl) routeInfoEl.classList.remove('hidden');
            } catch (e) {
                console.warn('Failed to update Leaflet route info', e);
            }
        }).catch(err => console.warn('OSRM fetch error', err));
    }

    function updateLeafletDriverMarker(position) {
        if (!position || typeof L === 'undefined' || !L_liveMap) return;

        const lat = position.lat;
        const lng = position.lng;

        if (L_driverMarker) {
            L_driverMarker.setLatLng([lat, lng]);
        } else {
            L_driverMarker = L.marker([lat, lng], { icon: L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', iconSize: [25,41], iconAnchor: [12,41] }) }).addTo(L_liveMap).bindPopup('Driver Location');
        }

        if (L_destinationMarker) {
            try { requestLeafletRoute(L_driverMarker.getLatLng(), L_destinationMarker.getLatLng()); } catch (e) { console.warn('OSRM update failed', e); }
        }
    }
</script>
@endsection
