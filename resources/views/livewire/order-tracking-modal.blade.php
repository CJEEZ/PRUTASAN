<div wire:poll.5s="refreshOrder">
    @if($showModal && $order)
        <div class="fixed inset-0 z-50 bg-black bg-opacity-40 flex items-center justify-center px-4 py-6">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Order #{{ $order->order_number }}</h2>
                        <p class="text-sm text-gray-500">{{ ucfirst($order->status) }} • {{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <button wire:click="close" class="text-gray-500 hover:text-orange-600 text-2xl leading-none">×</button>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="text-sm text-gray-500">Shipping Address</div>
                            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50 text-sm text-gray-700">
                                {{ $order->street_address }}, {{ $order->barangay }},<br>
                                {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}
                            </div>

                            <div class="text-sm text-gray-500">Phone</div>
                            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50 text-sm text-gray-700">{{ $order->phone }}</div>

                            <div class="text-sm text-gray-500">Shipment</div>
                            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50 text-sm text-gray-700">
                                @if($order->shipment)
                                    Tracking #: <span class="font-semibold">{{ $order->shipment->tracking_number }}</span><br>
                                    Carrier: {{ $order->shipment->carrier ?? 'N/A' }}<br>
                                    Status: {{ ucfirst($order->shipment->status) }}<br>
                                    Shipped At: {{ $order->shipment->shipped_at?->format('M d, Y H:i') ?? 'N/A' }}
                                @else
                                    <span class="text-gray-500">No shipment data yet.</span>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-gray-500">Order Total</div>
                                    <div class="text-2xl font-semibold text-orange-600">₱{{ number_format($order->total, 2) }}</div>
                                </div>
                                @if($order->driver_latitude && $order->driver_longitude)
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold bg-green-100 text-green-800">Driver Active</span>
                                @else
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-700">Driver location not set</span>
                                @endif
                            </div>

                            <div class="rounded-lg overflow-hidden border border-gray-200">
                                <div id="order-tracking-map" class="w-full h-72"></div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900">Items</h3>
                        <div class="grid gap-3">
                            @foreach($order->items as $item)
                                <div class="rounded-xl border border-gray-200 p-4 flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-lg bg-gray-100 overflow-hidden flex items-center justify-center">
                                        @if($item->product && $item->product->image_url)
                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-box text-gray-400"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 text-sm text-gray-700">
                                        <div class="font-semibold text-gray-900">{{ $item->product?->name ?? 'Product' }}</div>
                                        <div>{{ $item->quantity }} × ₱{{ number_format($item->price, 2) }}</div>
                                    </div>
                                    <div class="text-right text-sm font-semibold text-orange-600">₱{{ number_format($item->subtotal, 2) }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    function initOrderTrackingMap() {
        const mapContainer = document.getElementById('order-tracking-map');
        if (!mapContainer) return;

        if (window.orderTrackingMap) {
            window.orderTrackingMap.remove();
            window.orderTrackingMap = null;
        }

        const orderLat = {{ $order?->latitude ?? 'null' }};
        const orderLng = {{ $order?->longitude ?? 'null' }};
        const driverLat = {{ $order?->driver_latitude ?? 'null' }};
        const driverLng = {{ $order?->driver_longitude ?? 'null' }};

        if (orderLat === null || orderLng === null) {
            mapContainer.innerHTML = '<div class="flex h-full items-center justify-center text-gray-500">Delivery location not set.</div>';
            return;
        }

        const map = L.map('order-tracking-map').setView([orderLat, orderLng], driverLat && driverLng ? 11 : 14);
        window.orderTrackingMap = map;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([orderLat, orderLng]).addTo(map).bindPopup('Delivery Location').openPopup();

        if (driverLat && driverLng) {
            L.marker([driverLat, driverLng], {icon: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/1946/1946429.png',
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            })}).addTo(map).bindPopup('Driver Location');

            const poly = L.polyline([[driverLat, driverLng], [orderLat, orderLng]], {color: 'blue', weight: 4, opacity: 0.7}).addTo(map);
            map.fitBounds(poly.getBounds(), {padding: [40, 40]});
        }
    }

    document.addEventListener('livewire:load', function () {
        initOrderTrackingMap();

        Livewire.hook('message.processed', (message, component) => {
            initOrderTrackingMap();
        });
    });
</script>
@endpush
