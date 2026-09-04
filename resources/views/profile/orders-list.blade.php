<div class="mt-4 rounded-[1.5rem] border border-slate-800 bg-[#0f291f] p-4 shadow-2xl">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
        <div>
            <h3 class="text-2xl font-semibold text-white">My Orders</h3>
            <p class="text-xs text-slate-400">Review your latest purchases and order status.</p>
        </div>
        <div class="flex items-center space-x-2 text-xs text-slate-300">
            <span class="px-2.5 py-1 rounded-full bg-emerald-600/10 text-emerald-200">All</span>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($orders as $order)
            <div class="rounded-[1.25rem] border border-slate-800 bg-[#112e20] p-4 shadow-xl">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-16 h-16 rounded-2xl bg-slate-800 overflow-hidden flex items-center justify-center">
                            @if($order->items->first() && $order->items->first()->product && $order->items->first()->product->image_url)
                                <img src="{{ $order->items->first()->product->image_url }}" alt="Product image for {{ $order->items->first()->product->name ?? 'order item' }}" class="object-cover w-full h-full" onerror="this.onerror=null;this.src='https://placehold.co/100x100/FF7F00/ffffff?text=No+Image';">
                            @else
                                <i class="fas fa-box-open text-xl text-slate-400"></i>
                            @endif
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="font-semibold text-white text-base">Order #{{ $order->order_number }}</h4>
                                @php
                                    $customerStatusLabels = ['pending' => 'Pending', 'preparing' => 'Preparing', 'ready_for_pickup' => 'To Ship', 'cancelled' => 'Cancelled', 'in_transit' => 'In Transit', 'out_for_delivery' => 'Out for Delivery', 'to_receive' => 'To Receive', 'delivered' => 'Delivered'];
                                @endphp
                                <span class="text-[10px] px-2 py-1 rounded-full {{ $order->status === 'pending' ? 'bg-amber-500/20 text-amber-200' : ($order->status === 'cancelled' ? 'bg-red-500/20 text-red-200' : 'bg-slate-700 text-slate-200') }}">{{ $customerStatusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                            @if($order->items->count())
                                <p class="text-xs text-slate-300 mt-2">{{ $order->items->first()->product->name ?? 'Item' }} <span class="text-slate-500">x{{ $order->items->first()->quantity ?? 1 }}</span></p>
                                <p class="text-[10px] text-slate-500 mt-1">{{ $order->items->count() - 1 }} more item(s)</p>
                            @endif
                            @if($order->shipment?->driver)
                                <p class="mt-2 text-xs text-emerald-300"><i class="fas fa-truck mr-1"></i> Driver assigned: {{ $order->shipment->driver->name }}</p>
                            @elseif($order->shipment && in_array($order->status, ['ready_for_pickup', 'shipped'], true))
                                <p class="mt-2 text-xs text-amber-300"><i class="fas fa-clock mr-1"></i> Waiting for driver assignment</p>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400">Total</p>
                        <p class="text-2xl font-semibold text-white">₱{{ number_format($order->total ?? $order->total_amount ?? 0, 2) }}</p>
                        @php
                            $trackableStatuses = \App\Models\Order::TRACKABLE_STATUSES;
                            $isToShip = in_array($order->status, ['preparing', 'ready_for_pickup', 'shipped','to_ship','packed','confirmed']);
                            $canCustomerTrack = in_array($order->status, $trackableStatuses, true)
                                && ! in_array($order->status, ['preparing', 'packed', 'confirmed', 'delivered', 'completed'], true);
                            $actionButtonClass = 'inline-flex min-h-[30px] w-[88px] items-center justify-center rounded-full px-2.5 py-1.5 text-center text-[10px] font-semibold leading-tight';
                        @endphp
                        <div class="mt-3 flex flex-wrap items-center justify-end gap-2">
                            <button data-order-id="{{ $order->id }}" class="order-details {{ $actionButtonClass }} bg-[#0f291f] text-slate-100 border border-slate-700 hover:bg-[#16362b]">Details</button>
                            @if($canCustomerTrack)
                                <a href="{{ route('tracking.show', $order) }}" class="{{ $actionButtonClass }} bg-emerald-600 hover:bg-emerald-700 text-white">
                                    <span class="inline-flex items-center justify-center gap-1"><i class="fas fa-truck text-[9px]"></i> Track</span>
                                </a>
                            @endif

                            @if(!$isToShip)
                                @if($order->status === 'pending')
                                    <form method="POST" action="{{ route('order.cancel', $order) }}" onsubmit="return confirm('Cancel order {{ $order->order_number }}?');">
                                        @csrf
                                        <button type="submit" class="{{ $actionButtonClass }} bg-red-600 text-white">Cancel</button>
                                    </form>
                                @endif

                                {{-- Only allow buy again and return for completed/delivered orders --}}
                                @if(in_array($order->status, ['delivered', 'completed']))
                                    <form method="POST" action="{{ route('order.buy_again', $order) }}">
                                        @csrf
                                        <button type="submit" class="{{ $actionButtonClass }} bg-blue-600 text-white">Buy again</button>
                                    </form>

                                    <form method="POST" action="{{ route('order.request_return', $order) }}" onsubmit="return confirm('Request a return for {{ $order->order_number }}?');">
                                        @csrf
                                        <button type="submit" class="{{ $actionButtonClass }} w-[110px] bg-yellow-500 text-black">Request Return</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-slate-400">
                <i class="fas fa-shopping-bag text-5xl mb-4 text-slate-500"></i>
                <p class="text-lg font-semibold text-white">No orders yet</p>
                <p class="mt-2 text-slate-400">Your recent purchases will appear here.</p>
            </div>
        @endforelse
    </div>

    <!-- Order details modal -->
    <div id="orderModal" class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 items-center justify-center">
        <div class="bg-white rounded-lg max-w-2xl w-full p-6 mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalOrderNumber" class="font-semibold">Order Details</h3>
                <button id="closeModal" class="text-gray-500">Close</button>
            </div>
            <div id="modalContent">
                <p class="text-sm text-gray-600">Loading...</p>
            </div>
        </div>
    </div>

    <script>
        // Expose a global function to (re)attach handlers to order elements in a root node
        window.attachOrderHandlers = function(root){
            root = root || document;
            function qs(selector, r=document){ return r.querySelector(selector); }
            root.querySelectorAll('.order-details').forEach(function(btn){
                // remove previous listener if exists
                if (btn._listener) btn.removeEventListener('click', btn._listener);
                    const listener = function(){
                    const id = this.dataset.orderId;
                    const url = '/orders/' + id + '/details';
                    const modal = document.getElementById('orderModal');
                    const content = document.getElementById('modalContent');
                    const title = document.getElementById('modalOrderNumber');
                    modal.classList.remove('hidden');
                    content.innerHTML = '<p class="text-sm text-gray-600">Loading...</p>';
                    // helper: fetch and render details, optionally initialize map and polling
                    let mapIntervalId = null;
                    let leafletState = { map: null, driverMarker: null, destMarker: null };

                    function haversineDistance(lat1, lon1, lat2, lon2){
                        function toRad(v){ return v * Math.PI / 180; }
                        const R = 6371; // km
                        const dLat = toRad(lat2 - lat1);
                        const dLon = toRad(lon2 - lon1);
                        const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2);
                        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                        return R * c;
                    }

                    function formatDurationMinutes(minutes){
                        if (minutes <= 1) return '1 min';
                        if (minutes < 60) return Math.round(minutes) + ' mins';
                        const hrs = Math.floor(minutes/60);
                        const mins = Math.round(minutes % 60);
                        return hrs + 'h ' + (mins>0? mins + 'm':'');
                    }

                    function relativeTime(iso){
                        if (!iso) return 'unknown';
                        const then = new Date(iso).getTime();
                        const diff = Math.floor((Date.now() - then)/1000);
                        if (diff < 60) return diff + 's ago';
                        if (diff < 3600) return Math.floor(diff/60) + 'm ago';
                        return Math.floor(diff/3600) + 'h ago';
                    }

                    function loadLeaflet(cb){
                        if (window.L) return cb();
                        const css = document.createElement('link');
                        css.rel = 'stylesheet'; css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                        document.head.appendChild(css);
                        const s = document.createElement('script');
                        s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        s.onload = cb;
                        document.body.appendChild(s);
                    }

                    function renderData(data){
                        title.textContent = 'Order #' + (data.order_number || '');
                        let html = '';
                        html += '<p class="text-sm text-gray-600">Status: <strong>' + (data.status || '') + '</strong></p>';
                        html += '<p class="text-sm text-gray-600 mt-2">Shipping: ' + (data.order_address || data.street_address || '') + '</p>';
                        html += '<div class="mt-3">';
                        html += '<h4 class="font-semibold">Items</h4>';
                        html += '<ul class="mt-2 space-y-1 text-sm text-gray-700">';
                        (data.items || []).forEach(function(it){
                            html += '<li>' + (it.product_name || 'Item') + ' x' + (it.quantity || 1) + ' — ₱' + (Number(it.subtotal || 0).toFixed(2)) + '</li>';
                        });
                        html += '</ul></div>';
                        html += '<p id="orderTotal" class="mt-4 text-right font-bold">Total: ₱' + (Number(data.total || 0).toFixed(2)) + '</p>';

                        // Map container & legend
                        if (data.driver_latitude && data.driver_longitude && data.latitude && data.longitude) {
                            html += '<div class="mt-4">';
                            html += '<div id="orderMap" style="height:300px;width:100%;" class="rounded"></div>';
                            html += '<div id="mapLegend" class="mt-2 text-sm text-gray-600"></div>';
                            html += '</div>';
                        }

                        content.innerHTML = html;

                        // If at least one coordinate exists, initialize or update map
                        if ((data.driver_latitude && data.driver_longitude) || (data.latitude && data.longitude)) {
                            // Helper: request OSRM route and draw on leaflet map
                            function requestLeafletRouteModal(originLatLng, destinationLatLng) {
                                if (!originLatLng || !destinationLatLng || !leafletState.map) return;
                                const src = originLatLng.lng + ',' + originLatLng.lat;
                                const dst = destinationLatLng.lng + ',' + destinationLatLng.lat;
                                const url = `https://router.project-osrm.org/route/v1/driving/${src};${dst}?overview=full&geometries=geojson`;
                                fetch(url).then(r => r.json()).then(d => {
                                    if (!d || d.code !== 'Ok' || !d.routes || !d.routes.length) return;
                                    const route = d.routes[0];
                                    if (leafletState.routeLayer) { try { leafletState.map.removeLayer(leafletState.routeLayer); } catch(e){} leafletState.routeLayer = null; }
                                    leafletState.routeLayer = L.geoJSON(route.geometry, { style: { color: '#2563eb', weight: 5, opacity: 0.9 } }).addTo(leafletState.map);
                                    try { const b = leafletState.routeLayer.getBounds(); if (b && b.isValid()) leafletState.map.fitBounds(b.pad(0.12)); } catch(e){}
                                }).catch(()=>{});
                            }

                            // Setup Echo subscription for live updates (if Echo is configured)
                            function setupModalRealtime(orderId) {
                                try {
                                    if (window.Echo && window.Echo.private) {
                                        const ch = window.Echo.private(`orders.${orderId}`);
                                        ch.stopListening('RiderLocationUpdated');
                                        ch.listen('RiderLocationUpdated', function(payload){
                                            try {
                                                if (payload && payload.driver_latitude && payload.driver_longitude) {
                                                    const dLat2 = Number(payload.driver_latitude);
                                                    const dLon2 = Number(payload.driver_longitude);
                                                    if (leafletState.driverMarker) leafletState.driverMarker.setLatLng([dLat2, dLon2]);
                                                    else leafletState.driverMarker = L.marker([dLat2, dLon2], {title:'Driver'}).addTo(leafletState.map);
                                                    if (leafletState.destMarker) {
                                                        // refresh route
                                                        requestLeafletRouteModal(leafletState.driverMarker.getLatLng(), leafletState.destMarker.getLatLng());
                                                    }
                                                    const legendNode2 = document.getElementById('mapLegend');
                                                    if (legendNode2 && leafletState.destMarker) {
                                                        const to = leafletState.destMarker.getLatLng();
                                                        const km2 = haversineDistance(dLat2, dLon2, to.lat, to.lng);
                                                        const eta2 = (km2 / 40) * 60;
                                                        legendNode2.innerHTML = '<strong>Distance:</strong> ' + km2.toFixed(2) + ' km &nbsp; • &nbsp; <strong>ETA:</strong> ' + formatDurationMinutes(eta2) + ' &nbsp; • &nbsp; <strong>Last update:</strong> just now';
                                                    }
                                                }
                                            } catch (e) { console.warn('Modal Echo handler error', e); }
                                        });
                                    }
                                } catch (e) { /* ignore */ }
                            }

                            loadLeaflet(function(){
                                try {
                                    const hasDriver = (data.driver_latitude && data.driver_longitude);
                                    const hasDest = (data.latitude && data.longitude);
                                    const dLat = hasDriver ? Number(data.driver_latitude) : null;
                                    const dLon = hasDriver ? Number(data.driver_longitude) : null;
                                    const toLat = hasDest ? Number(data.latitude) : null;
                                    const toLon = hasDest ? Number(data.longitude) : null;

                                    if (!leafletState.map) {
                                        const center = hasDriver ? [dLat, dLon] : (hasDest ? [toLat, toLon] : [14.5995, 120.9842]);
                                        leafletState.map = L.map('orderMap').setView(center, 13);
                                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19}).addTo(leafletState.map);

                                        if (hasDriver) {
                                            leafletState.driverMarker = L.marker([dLat, dLon], {title: 'Driver'}).addTo(leafletState.map);
                                        }
                                        if (hasDest) {
                                            leafletState.destMarker = L.marker([toLat, toLon], {title: 'Destination', icon: L.icon({iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png', iconSize: [25,41], iconAnchor:[12,41]})}).addTo(leafletState.map);
                                        }

                                        // Fit to available markers
                                        const markers = [];
                                        if (leafletState.driverMarker) markers.push(leafletState.driverMarker.getLatLng());
                                        if (leafletState.destMarker) markers.push(leafletState.destMarker.getLatLng());
                                        if (markers.length) {
                                            const bounds = L.latLngBounds(markers);
                                            leafletState.map.fitBounds(bounds.pad(0.3));
                                        }
                                        window.leafletState = leafletState;
                                    } else {
                                        if (hasDriver) {
                                            if (leafletState.driverMarker) leafletState.driverMarker.setLatLng([dLat, dLon]);
                                            else leafletState.driverMarker = L.marker([dLat, dLon], {title:'Driver'}).addTo(leafletState.map);
                                        }
                                        if (hasDest) {
                                            if (leafletState.destMarker) leafletState.destMarker.setLatLng([toLat, toLon]);
                                            else leafletState.destMarker = L.marker([toLat, toLon], {title:'Destination', icon: L.icon({iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png', iconSize:[25,41], iconAnchor:[12,41]})}).addTo(leafletState.map);
                                        }

                                        const markers = [];
                                        if (leafletState.driverMarker) markers.push(leafletState.driverMarker.getLatLng());
                                        if (leafletState.destMarker) markers.push(leafletState.destMarker.getLatLng());
                                        if (markers.length) {
                                            leafletState.map.fitBounds(L.latLngBounds(markers).pad(0.3));
                                        }
                                        window.leafletState = leafletState;
                                    }

                                    // compute distance and ETA if both exist
                                    const legendNode = document.getElementById('mapLegend');
                                    if (legendNode) {
                                        if (hasDriver && hasDest) {
                                            const km = haversineDistance(dLat, dLon, toLat, toLon);
                                            const avgSpeedKmh = 40;
                                            const etaMins = (km / avgSpeedKmh) * 60;
                                            const last = data.driver_updated_at || data.order_updated_at || null;
                                            const legend = '<strong>Distance:</strong> ' + km.toFixed(2) + ' km &nbsp; • &nbsp; <strong>ETA:</strong> ' + formatDurationMinutes(etaMins) + ' &nbsp; • &nbsp; <strong>Last update:</strong> ' + relativeTime(last);
                                            legendNode.innerHTML = legend;
                                        } else if (hasDriver) {
                                            const last = data.driver_updated_at || data.order_updated_at || null;
                                            legendNode.innerHTML = '<strong>Driver at:</strong> ' + dLat.toFixed(5) + ', ' + dLon.toFixed(5) + ' &nbsp; • &nbsp; <strong>Last update:</strong> ' + relativeTime(last);
                                        } else if (hasDest) {
                                            legendNode.innerHTML = '<strong>Destination:</strong> ' + toLat.toFixed(5) + ', ' + toLon.toFixed(5);
                                        }
                                    }

                                } catch(err){ console.error('Map init error', err); }
                            });

                            // start polling if not already started for this modal
                            if (modal._mapPoll) { clearInterval(modal._mapPoll); modal._mapPoll = null; }
                            modal._mapPoll = setInterval(function(){
                                fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                                    .then(r => r.json())
                                    .then(newData => {
                                        if (newData && newData.driver_latitude && newData.driver_longitude) {
                                            // update markers and legend
                                            const dLat2 = Number(newData.driver_latitude);
                                            const dLon2 = Number(newData.driver_longitude);
                                            const toLat2 = Number(newData.latitude);
                                            const toLon2 = Number(newData.longitude);
                                            if (leafletState.driverMarker) leafletState.driverMarker.setLatLng([dLat2, dLon2]);
                                            if (leafletState.destMarker) leafletState.destMarker.setLatLng([toLat2, toLon2]);
                                            // update route using OSRM
                                            try { requestLeafletRouteModal(leafletState.driverMarker.getLatLng(), leafletState.destMarker.getLatLng()); } catch(e){}
                                            const km2 = haversineDistance(dLat2, dLon2, toLat2, toLon2);
                                            const eta2 = (km2 / 40) * 60;
                                            const last2 = newData.driver_updated_at || newData.order_updated_at || null;
                                            const legendNode2 = document.getElementById('mapLegend');
                                            if (legendNode2) legendNode2.innerHTML = '<strong>Distance:</strong> ' + km2.toFixed(2) + ' km &nbsp; • &nbsp; <strong>ETA:</strong> ' + formatDurationMinutes(eta2) + ' &nbsp; • &nbsp; <strong>Last update:</strong> ' + relativeTime(last2);
                                        }
                                        // also refresh items/total if changed
                                        if (newData && typeof newData.total !== 'undefined'){
                                            const tot = document.getElementById('orderTotal');
                                            if (tot) tot.textContent = 'Total: ₱' + (Number(newData.total || 0).toFixed(2));
                                        }
                                    }).catch(()=>{});
                            }, 8000);
                        }
                    }

                    // initial fetch
                    fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.json())
                        .then(data => {
                            if (data.error) {
                                content.innerHTML = '<p class="text-red-600">'+data.error+'</p>';
                                return;
                            }
                                    renderData(data);
                                    // setup real-time after rendering
                                    try { setupModalRealtime(data.id || {{ $order->id ?? 'null' }}); } catch(e){}
                        }).catch(e => {
                            content.innerHTML = '<p class="text-red-600">Unable to load details.</p>';
                        });
                };
                btn.addEventListener('click', listener);
                btn._listener = listener;
            });

            // Close handlers (only attach once to document-level elements)
            const closeBtn = document.getElementById('closeModal');
            if (closeBtn && !closeBtn._closeAttached) {
                closeBtn.addEventListener('click', function(){
                    const modalEl = document.getElementById('orderModal');
                    // stop polling and cleanup map
                    if (modalEl && modalEl._mapPoll) { clearInterval(modalEl._mapPoll); modalEl._mapPoll = null; }
                    if (window.leafletState && window.leafletState.map) { try{ window.leafletState.map.remove(); }catch(e){} window.leafletState = null; }
                    modalEl.classList.add('hidden');
                });
                closeBtn._closeAttached = true;
            }
            const modal = document.getElementById('orderModal');
            if (modal && !modal._outsideAttached) {
                modal.addEventListener('click', function(e){ if (e.target === this) { if (this._mapPoll) { clearInterval(this._mapPoll); this._mapPoll = null; } this.classList.add('hidden'); } });
                modal._outsideAttached = true;
            }
        };

        // Attach on initial load
        document.addEventListener('DOMContentLoaded', function () { window.attachOrderHandlers(document); });
    </script>
</div>
