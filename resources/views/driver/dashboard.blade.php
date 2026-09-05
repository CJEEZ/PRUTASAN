<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Driver Center | {{ config('app.name', 'FruitExpress') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-orange-50 text-gray-900">
<main class="relative min-h-screen px-3 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
        @if(session('success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        @if($application && in_array($application->status, ['approved', 'hired'], true))
        <section class="space-y-6">
            <div class="flex flex-col gap-4 rounded-2xl bg-emerald-800 p-5 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-white p-2 shadow-sm sm:h-20 sm:w-20">
                        <img src="{{ asset('ORNOSFARM_LOGOS.png') }}" alt="FruitExpress" class="max-h-full w-auto object-contain">
                    </div>
                    <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-200">Driver Hub</p>
                    <h2 class="mt-1 text-2xl font-bold">Good day, {{ $driverName }}</h2>
                    <p class="mt-1 text-sm text-emerald-100">You are ready to accept deliveries.</p>
                    </div>
                </div>
                <div class="flex self-end items-center gap-2 sm:self-auto">
                    <form method="POST" action="{{ route('driver.availability') }}" class="flex min-h-[44px] shrink-0 cursor-pointer items-center justify-between gap-2 rounded-lg bg-white/10 px-3 py-2">
                        @csrf @method('PATCH')
                        <span><span class="block text-[10px] text-emerald-100">Availability</span><span id="availability-label" class="text-sm font-bold">{{ $driverAvailable ? 'Online' : 'Offline' }}</span></span>
                        <input id="availability-toggle" name="available" value="1" type="checkbox" {{ $driverAvailable ? 'checked' : '' }} onchange="this.form.submit()" class="peer sr-only">
                        <label for="availability-toggle" class="relative h-6 w-10 cursor-pointer rounded-full bg-emerald-300 transition peer-checked:bg-white after:absolute after:left-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-emerald-700 after:transition peer-checked:after:translate-x-4"></label>
                    </form>
                    <a href="{{ route('driver.profile') }}" class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white text-emerald-800 shadow-sm transition hover:bg-emerald-50" aria-label="Open driver profile" title="Profile">
                        @if(auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Profile photo of {{ $driverName }}" class="h-full w-full object-cover">
                        @else
                            <i class="fas fa-user text-sm" aria-hidden="true"></i>
                        @endif
                    </a>
                </div>
            </div>

            <div class="driver-metrics flex gap-2 overflow-x-auto pb-1 lg:grid lg:grid-cols-1 lg:overflow-visible">
                <div class="min-w-[150px] shrink-0 rounded-xl bg-white p-3 shadow-sm ring-1 ring-gray-100 lg:min-w-0"><div class="flex items-center justify-between"><p class="text-xs text-gray-500">Today's earnings</p><i class="fas fa-peso-sign text-sm text-emerald-600"></i></div><p class="mt-2 text-xl font-bold text-gray-900">₱0.00</p><p class="mt-0.5 text-[10px] text-gray-500">Start a delivery to earn</p></div>
                <div class="min-w-[150px] shrink-0 rounded-xl bg-white p-3 shadow-sm ring-1 ring-gray-100 lg:min-w-0"><div class="flex items-center justify-between"><p class="text-xs text-gray-500">Weekly total</p><i class="fas fa-chart-line text-sm text-blue-600"></i></div><p class="mt-2 text-xl font-bold text-gray-900">₱0.00</p><p class="mt-0.5 text-[10px] text-gray-500">This week's earnings</p></div>
                <div class="min-w-[150px] shrink-0 rounded-xl bg-white p-3 shadow-sm ring-1 ring-gray-100 lg:min-w-0"><div class="flex items-center justify-between"><p class="text-xs text-gray-500">On-time rate</p><i class="fas fa-stopwatch text-sm text-orange-600"></i></div><p class="mt-2 text-xl font-bold text-gray-900">100%</p><p class="mt-0.5 text-[10px] text-gray-500">Keep up the good work</p></div>
                <div class="min-w-[150px] shrink-0 rounded-xl bg-white p-3 shadow-sm ring-1 ring-gray-100 lg:min-w-0"><div class="flex items-center justify-between"><p class="text-xs text-gray-500">Customer rating</p><i class="fas fa-star text-sm text-amber-500"></i></div><p class="mt-2 text-xl font-bold text-gray-900">New</p><p class="mt-0.5 text-[10px] text-gray-500">Build your rating</p></div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
                <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-6">
                    <div class="flex items-center justify-between"><div><h2 class="text-lg font-bold text-gray-900">Active deliveries queue</h2><p class="mt-1 text-sm text-gray-500">Your next delivery assignments will appear here.</p></div><span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $activeDeliveries->count() }} active</span></div>
                    @forelse($activeDeliveries as $delivery)<div class="mt-5 rounded-xl border border-emerald-100 bg-emerald-50 p-4"><div class="flex items-center justify-between"><p class="font-semibold text-gray-900">Order #{{ $delivery->order->order_number ?? $delivery->order_id }}</p><span class="text-xs font-bold uppercase text-emerald-700">{{ str_replace('_', ' ', $delivery->status) }}</span></div><p class="mt-2 text-sm text-gray-600">{{ $delivery->order->full_name ?? 'Customer' }}<span class="mx-1">to</span>{{ $delivery->order->city ?? 'Delivery address pending' }}</p><p data-location-status class="mt-2 text-xs text-emerald-700">Live location sharing is ready</p><form method="POST" action="{{ route('driver.shipments.update', $delivery) }}" data-location-form data-shipment-id="{{ $delivery->id }}" class="mt-4 grid gap-2 sm:grid-cols-2">@csrf @method('PATCH')<select name="status" class="min-h-[42px] rounded-lg border border-gray-200 px-3 text-sm"><option value="in_transit" {{ $delivery->status === 'in_transit' ? 'selected' : '' }}>In transit</option><option value="out_for_delivery" {{ $delivery->status === 'out_for_delivery' ? 'selected' : '' }}>Out for delivery</option><option value="delivered">Delivered</option></select><input name="location" placeholder="Current area (optional)" class="min-h-[42px] rounded-lg border border-gray-200 px-3 text-sm"><input type="hidden" name="latitude"><input type="hidden" name="longitude"><button type="button" data-location class="min-h-[42px] rounded-lg border border-emerald-200 bg-white px-3 text-sm font-semibold text-emerald-700"><i class="fas fa-location-crosshairs mr-1"></i>Use my location</button><button type="submit" data-status-submit class="min-h-[42px] rounded-lg bg-emerald-700 px-3 text-sm font-semibold text-white hover:bg-emerald-800"><i class="fas fa-share mr-1"></i>Share update</button></form></div>@empty<div class="mt-5 rounded-xl border border-dashed border-gray-200 bg-gray-50 px-5 py-10 text-center"><i class="fas fa-route text-3xl text-emerald-600"></i><p class="mt-3 font-semibold text-gray-800">No active deliveries yet</p><p class="mt-1 text-sm text-gray-500">Stay online to receive your first assignment.</p></div>@endforelse
                </section>
                <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-6"><h2 class="text-lg font-bold text-gray-900">Trip payout summary</h2><div class="mt-5 space-y-4"><div class="flex items-center justify-between border-b border-gray-100 pb-4"><span class="text-sm text-gray-500">Completed trips</span><span class="font-bold text-gray-900">0</span></div><div class="flex items-center justify-between border-b border-gray-100 pb-4"><span class="text-sm text-gray-500">Pending payout</span><span class="font-bold text-gray-900">₱0.00</span></div><div class="flex items-center justify-between"><span class="text-sm text-gray-500">Total volume</span><span class="font-bold text-gray-900">0 kg</span></div></div></section>
            </div>

            <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-6"><div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-lg font-bold text-gray-900">Available for pickup</h2><p class="mt-1 text-sm text-gray-500">Claim a prepared shipment to become its assigned rider.</p></div><span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">{{ $availableDeliveries->count() }} ready</span></div><div class="mt-4 grid gap-3 sm:grid-cols-2">@forelse($availableDeliveries as $delivery)<div class="rounded-xl border border-gray-100 bg-gray-50 p-4"><div class="flex items-center justify-between"><p class="font-semibold">Order #{{ $delivery->order->order_number ?? $delivery->order_id }}</p><span class="text-xs font-semibold uppercase text-orange-700">Ready</span></div><p class="mt-2 text-sm text-gray-500">{{ $delivery->order->full_name ?? 'Customer' }}<span class="mx-1">from</span>{{ $delivery->order->city ?? 'Pickup location pending' }}</p><form method="POST" action="{{ route('driver.shipments.claim', $delivery) }}" class="mt-3">@csrf<button class="min-h-[42px] w-full rounded-lg bg-orange-600 px-4 text-sm font-semibold text-white hover:bg-orange-700"><i class="fas fa-hand-pointer mr-1"></i>Accept and pick up</button></form></div>@empty<div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 px-5 py-8 text-center text-sm text-gray-500 sm:col-span-2">No shipments are waiting for pickup.</div>@endforelse</div></section>

            <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-6"><div class="flex items-center justify-between"><div><h2 class="text-lg font-bold text-gray-900">Performance metrics</h2><p class="mt-1 text-sm text-gray-500">Your delivery performance will update after your first trip.</p></div><i class="fas fa-chart-bar text-xl text-emerald-600"></i></div><div class="driver-performance-metrics mt-4 flex gap-2 overflow-x-auto pb-1 lg:grid lg:grid-cols-1 lg:overflow-visible"><div class="min-w-[130px] shrink-0 rounded-lg bg-emerald-50 p-3 lg:min-w-0"><p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-700">Deliveries</p><p class="mt-1 text-xl font-bold text-emerald-900">0</p></div><div class="min-w-[130px] shrink-0 rounded-lg bg-blue-50 p-3 lg:min-w-0"><p class="text-[10px] font-semibold uppercase tracking-wide text-blue-700">Distance</p><p class="mt-1 text-xl font-bold text-blue-900">0 km</p></div><div class="min-w-[130px] shrink-0 rounded-lg bg-orange-50 p-3 lg:min-w-0"><p class="text-[10px] font-semibold uppercase tracking-wide text-orange-700">Volume transported</p><p class="mt-1 text-xl font-bold text-orange-900">0 kg</p></div></div></section>

            <nav class="driver-navigation sticky bottom-0 z-30 flex gap-1.5 overflow-x-auto rounded-xl bg-white/95 p-1.5 shadow-[0_-6px_20px_rgba(15,23,42,0.08)] ring-1 ring-gray-100 backdrop-blur lg:static lg:grid lg:grid-cols-6 lg:overflow-visible lg:bg-white lg:shadow-sm"><a href="{{ route('driver.dashboard') }}" class="min-w-[76px] shrink-0 rounded-lg bg-emerald-50 px-2 py-2 text-center text-[10px] font-semibold text-emerald-700 lg:min-w-0"><i class="fas fa-truck mb-0.5 block text-sm"></i>Deliveries</a><a href="{{ route('driver.analytics') }}" class="min-w-[76px] shrink-0 rounded-lg px-2 py-2 text-center text-[10px] font-semibold text-gray-500 hover:bg-gray-50 lg:min-w-0"><i class="fas fa-chart-column mb-0.5 block text-sm"></i>Analytics</a><a href="{{ route('driver.schedule') }}" class="min-w-[76px] shrink-0 rounded-lg px-2 py-2 text-center text-[10px] font-semibold text-gray-500 hover:bg-gray-50 lg:min-w-0"><i class="fas fa-calendar mb-0.5 block text-sm"></i>Schedule</a><a href="{{ route('driver.history') }}" class="min-w-[76px] shrink-0 rounded-lg px-2 py-2 text-center text-[10px] font-semibold text-gray-500 hover:bg-gray-50 lg:min-w-0"><i class="fas fa-clock-rotate-left mb-0.5 block text-sm"></i>History</a><a href="{{ route('driver.messages') }}" class="relative min-w-[76px] shrink-0 rounded-lg px-2 py-2 text-center text-[10px] font-semibold text-gray-500 hover:bg-gray-50 lg:min-w-0"><i class="fas fa-message mb-0.5 block text-sm"></i>Messages @if(auth()->user()->notifications()->where('is_read', false)->exists())<span class="absolute right-1 top-1 h-1.5 w-1.5 rounded-full bg-orange-500" aria-label="Unread messages"></span>@endif</a><form method="POST" action="{{ route('logout') }}" class="contents"><button type="submit" class="min-h-[44px] min-w-[76px] shrink-0 rounded-lg px-2 py-2 text-center text-[10px] font-semibold text-gray-500 transition hover:bg-red-50 hover:text-red-600 lg:min-w-0"><i class="fas fa-sign-out-alt mb-0.5 block text-sm"></i>Logout</button></form></nav>
        </section>
        <script>
            document.getElementById('availability-toggle')?.addEventListener('change', function () {
                document.getElementById('availability-label').textContent = this.checked ? 'Online' : 'Offline';
            });
            document.querySelectorAll('[data-location]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const form = button.closest('form');
                    if (!navigator.geolocation) return;
                    navigator.geolocation.getCurrentPosition(function (position) {
                        form.querySelector('[name="latitude"]').value = position.coords.latitude;
                        form.querySelector('[name="longitude"]').value = position.coords.longitude;
                        button.innerHTML = '<i class="fas fa-check mr-1"></i>Location ready';
                    });
                });
            });
            document.querySelectorAll('[data-location-form]').forEach(function (form) {
                const status = form.closest('.rounded-xl')?.querySelector('[data-location-status]');
                const shipmentId = form.dataset.shipmentId;
                let lastSent = 0;
                let latestPosition = null;

                form.addEventListener('submit', function (event) {
                    const submitButton = form.querySelector('[data-status-submit]');
                    const latitude = form.querySelector('[name="latitude"]');
                    const longitude = form.querySelector('[name="longitude"]');

                    if (latestPosition) {
                        latitude.value = latestPosition.coords.latitude;
                        longitude.value = latestPosition.coords.longitude;
                    }

                    if (latitude.value && longitude.value) {
                        if (submitButton) {
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Sharing...';
                        }
                        return;
                    }

                    if (!navigator.geolocation) return;
                    event.preventDefault();
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Getting location...';
                    }
                    navigator.geolocation.getCurrentPosition(function (position) {
                        latitude.value = position.coords.latitude;
                        longitude.value = position.coords.longitude;
                        form.submit();
                    }, function () {
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = '<i class="fas fa-share mr-1"></i>Share update';
                        }
                        if (status) status.textContent = 'Location unavailable; allow access or use the update without GPS';
                    }, { enableHighAccuracy: true, maximumAge: 10000, timeout: 15000 });
                });

                if (!navigator.geolocation || !shipmentId) {
                    if (status) status.textContent = 'Location sharing is unavailable on this device';
                    return;
                }

                navigator.geolocation.watchPosition(function (position) {
                    latestPosition = position;
                    form.querySelector('[name="latitude"]').value = position.coords.latitude;
                    form.querySelector('[name="longitude"]').value = position.coords.longitude;
                    const now = Date.now();
                    if (now - lastSent < 15000) return;
                    lastSent = now;
                    fetch('{{ route('driver.shipments.location', ['shipment' => '__SHIPMENT__']) }}'.replace('__SHIPMENT__', shipmentId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                        }),
                    }).then(function (response) {
                        if (response.ok && status) status.textContent = 'Sharing live location';
                    }).catch(function () {
                        if (status) status.textContent = 'Location update failed; retrying';
                    });
                }, function () {
                    if (status) status.textContent = 'Allow location access to share your live pin';
                }, { enableHighAccuracy: true, maximumAge: 10000, timeout: 15000 });
            });
        </script>
        @else
        <div class="mx-auto max-w-3xl space-y-6">
            <aside class="space-y-4">
                <div class="relative rounded-2xl bg-emerald-800 p-6 text-white shadow-lg">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-2xl"><i class="fas fa-id-card"></i></div>
                    <h2 class="mt-5 text-xl font-bold">What you need</h2>
                    <ul class="mt-4 space-y-3 text-sm text-emerald-50">
                        <li><i class="fas fa-check mr-2 text-emerald-300"></i>Valid driver's license</li>
                        <li><i class="fas fa-check mr-2 text-emerald-300"></i>Readable OR and CR photos</li>
                        <li><i class="fas fa-check mr-2 text-emerald-300"></i>Clear, well-lit images</li>
                        <li class="absolute right-[-0.5rem] top-1/2 flex -translate-y-1/2 list-none justify-end sm:right-[-1rem] lg:right-[-2rem]">
                            <img src="{{ asset('ORNOSFARM_LOGOS.png') }}" alt="FruitExpress" class="h-24 w-32 object-contain sm:h-40 sm:w-56 lg:h-60 lg:w-[21.5rem]">
                        </li>
                    </ul>
                </div>
                @if($application && $application->status === 'rejected')
                    <div class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-bold text-red-700">Admin feedback</p>
                        <p class="mt-2 text-sm leading-6 text-gray-600">{{ $application->rejection_reason ?: 'Please review your documents and submit clearer photos.' }}</p>
                    </div>
                @endif
            </aside>

            <section class="rounded-2xl bg-white p-5 shadow-lg ring-1 ring-gray-100 sm:p-8">
                <div class="mb-6 border-b border-gray-100 pb-5">
                    <h2 class="text-xl font-bold text-gray-900">Driver application</h2>
                    <p class="mt-1 text-sm text-gray-500">All fields are required for verification.</p>
                </div>
                <form id="driver-application-form" method="POST" action="{{ route('driver.application.submit') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div>
                        <label for="license_serial_number" class="block text-sm font-semibold text-gray-700">License serial number</label>
                        <input id="license_serial_number" name="license_serial_number" required value="{{ old('license_serial_number', $application->license_serial_number ?? '') }}" class="mt-2 min-h-[46px] w-full rounded-xl border border-gray-200 px-4 text-base outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                    </div>
                    <div class="grid gap-5 sm:grid-cols-3">
                        @foreach([['license_photo', 'Driver license'], ['or_photo', 'OR photo'], ['cr_photo', 'CR photo']] as [$field, $label])
                            <div>
                                @php $existingDocument = $application && $application->{$field . '_path'} ? Storage::disk('public')->url($application->{$field . '_path'}) : ''; @endphp
                                @php $applicationPending = $application && $application->status === 'pending'; @endphp
                                <label for="{{ $field }}" class="block text-sm font-semibold text-gray-700">{{ $label }}</label>
                                <label for="{{ $field }}" class="relative mt-2 flex min-h-[150px] flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 px-3 text-center transition {{ $applicationPending ? 'cursor-not-allowed opacity-90' : 'cursor-pointer hover:border-emerald-400 hover:bg-emerald-50' }}">
                                    <img data-preview="{{ $field }}" src="{{ $existingDocument }}" alt="{{ $label }} preview" class="absolute inset-0 {{ $existingDocument ? '' : 'hidden' }} h-full w-full object-cover">
                                    <span data-placeholder="{{ $field }}" class="relative {{ $existingDocument ? 'hidden' : '' }}"><i class="fas fa-camera mb-2 block text-xl text-emerald-600"></i><span class="text-xs text-gray-500">{{ $applicationPending ? 'Pending admin review' : 'Tap to upload' }}<br>JPG or PNG, max 5MB</span></span>
                                    <input id="{{ $field }}" name="{{ $field }}" type="file" accept="image/jpeg,image/png" {{ $application && $application->{$field . '_path'} ? '' : 'required' }} {{ $applicationPending ? 'disabled' : '' }} class="sr-only">
                                </label>
                            </div>
                        @endforeach
                    </div>
                </form>
                <div class="mt-4 flex flex-row flex-wrap items-center justify-between gap-1.5 border-t border-gray-100 pt-4 sm:gap-3">
                    @if($application && $application->status === 'pending')
                        <button type="button" disabled class="min-h-[44px] w-full cursor-not-allowed rounded-lg bg-amber-100 px-4 py-2 text-sm font-bold text-amber-800 sm:w-auto"><i class="fas fa-clock mr-2"></i>Pending admin review</button>
                    @else
                        <button type="submit" form="driver-application-form" class="min-h-[44px] rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-100">{{ $application && $application->status === 'rejected' ? 'Resubmit for admin review' : 'Submit for admin review' }} <i class="fas fa-arrow-right ml-1"></i></button>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                        @csrf
                        <button type="submit" class="flex min-h-[44px] items-center justify-center rounded-lg px-2 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-red-50 hover:text-red-600"><i class="fas fa-sign-out-alt mr-1"></i>Logout</button>
                    </form>
                </div>
            </section>
        </div>
        @endif
    </div>
</main>
<script>
    document.querySelectorAll('input[type="file"]').forEach(function (input) {
        input.addEventListener('change', function () {
            const file = input.files && input.files[0];
            const preview = document.querySelector('[data-preview="' + input.id + '"]');
            const placeholder = document.querySelector('[data-placeholder="' + input.id + '"]');
            if (!file || !preview) return;
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        });
    });
    document.querySelectorAll('[data-preview]').forEach(function (preview) {
        if (preview.getAttribute('src')) {
            preview.classList.remove('hidden');
            const placeholder = document.querySelector('[data-placeholder="' + preview.dataset.preview + '"]');
            if (placeholder) placeholder.classList.add('hidden');
        }
    });
</script>
</body>
</html>
