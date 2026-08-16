@extends('layouts.app')

@section('content')
<div class="py-6 sm:py-8 lg:py-12">
    <h1 class="mb-4 text-2xl font-bold text-gray-800 sm:text-3xl">Checkout</h1>
    <form action="{{ route('checkout.process') }}" method="POST" class="mx-auto max-w-2xl space-y-6 rounded-lg bg-white p-4 shadow sm:p-6 lg:p-8">
        @csrf

        <!-- Shipping Information -->
        <fieldset class="space-y-4">
            <legend class="text-lg font-semibold text-gray-800">Shipping Information</legend>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required class="min-h-[40px] w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Phone Number *</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required class="min-h-[40px] w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="09XXXXXXXXX">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Street Address *</label>
                <input type="text" name="address" value="{{ old('address') }}" required class="min-h-[40px] w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="House/Unit No., Building, Street Name">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Barangay *</label>
                <input type="text" name="barangay" value="{{ old('barangay') }}" required class="min-h-[40px] w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">City/Municipality</label>
                    <input type="text" name="city" value="{{ old('city', 'Victoria') }}" class="min-h-[40px] w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Province</label>
                    <input type="text" name="province" value="{{ old('province', 'Oriental Mindoro') }}" class="min-h-[40px] w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Postal Code *</label>
                <input type="text" name="postal_code" value="{{ old('postal_code', '5205') }}" required class="min-h-[40px] w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
        </fieldset>

        <!-- Payment Method -->
        <fieldset class="space-y-3">
            <legend class="text-lg font-semibold text-gray-800">Payment Method *</legend>
            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-300 p-3 transition hover:bg-gray-50 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                <input type="radio" name="payment_method" value="cod" {{ old('payment_method') === 'cod' || !old('payment_method') ? 'checked' : '' }} required class="">
                <img src="https://png.pngtree.com/png-vector/20210527/ourlarge/pngtree-cash-on-delivery-truck-icon-png-image_3373084.jpg" alt="COD" class="w-10 h-10 object-contain rounded">
                <div>
                    <div class="font-semibold text-gray-900">Cash on Delivery (COD)</div>
                    <div class="text-sm text-gray-600">Pay when your order arrives</div>
                </div>
            </label>
            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-300 p-3 transition hover:bg-gray-50 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                <input type="radio" name="payment_method" value="gcash" {{ old('payment_method') === 'gcash' ? 'checked' : '' }} required class="">
                <img src="https://www.thefastmode.com/media/k2/items/src/03160998318f697230a7e611fb0fa87d.jpg?t=20200629_013741" alt="GCash" class="h-10 w-14 object-contain rounded bg-white p-1 shadow-sm">
                <div>
                    <div class="font-semibold text-gray-900">GCash</div>
                    <div class="text-sm text-gray-600">Pay via GCash (Instant payment)</div>
                </div>
            </label>
        </fieldset>

        <!-- Order Summary -->
        <div class="space-y-3 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:p-6">
            <h2 class="font-semibold text-gray-800">Order Summary</h2>
            <ul class="space-y-2 border-b border-gray-200 pb-3 text-sm">
                @foreach($cartItems as $item)
                    <li class="flex justify-between">
                        <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                        <span>₱{{ number_format($item->subtotal, 2) }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Subtotal:</span><span>₱{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Shipping:</span><span>₱{{ number_format($shipping, 2) }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-200 pt-2 font-bold text-lg">
                    <span>Total Amount:</span>
                    <span>₱{{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
            <a href="{{ route('checkout.cancel') }}" class="min-h-[44px] rounded-lg border border-gray-300 bg-white px-6 py-2 text-center font-semibold text-gray-700 transition hover:bg-gray-100 sm:order-2">
                Cancel
            </a>
            <button type="submit" class="min-h-[44px] rounded-lg bg-orange-600 px-6 py-2 font-semibold text-white shadow-lg transition hover:bg-orange-700 sm:order-3">
                Place Order
            </button>
        </div>
    </form>
</div>

@push('scripts')
    @if(!empty(env('GOOGLE_MAPS_API_KEY')))
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initOrderGeocoding"></script>
        <script>
            function initOrderGeocoding() {
                const geocoder = new google.maps.Geocoder();
                const streetEl = document.querySelector('input[name="address"]');
                const barangayEl = document.querySelector('input[name="barangay"]');
                const cityEl = document.querySelector('input[name="city"]');
                const provinceEl = document.querySelector('input[name="province"]');
                const postalCodeEl = document.querySelector('input[name="postal_code"]');
                const latitudeEl = document.getElementById('latitude');
                const longitudeEl = document.getElementById('longitude');
                const form = document.querySelector('form[action="{{ route('checkout.process') }}"]');

                function buildAddress() {
                    const parts = [streetEl.value, barangayEl.value, cityEl.value, provinceEl.value, postalCodeEl.value];
                    return parts.filter(Boolean).join(', ');
                }

                async function geocodeAddress() {
                    const address = buildAddress();
                    if (!address) {
                        return;
                    }

                    geocoder.geocode({ address }, (results, status) => {
                        if (status === 'OK' && results && results[0]) {
                            const location = results[0].geometry.location;
                            latitudeEl.value = location.lat();
                            longitudeEl.value = location.lng();
                        }
                    });
                }

                [streetEl, barangayEl, cityEl, provinceEl, postalCodeEl].forEach(element => {
                    element.addEventListener('blur', geocodeAddress);
                });

                form.addEventListener('submit', (event) => {
                    if (!latitudeEl.value || !longitudeEl.value) {
                        event.preventDefault();
                        geocodeAddress();
                        setTimeout(() => {
                            if (latitudeEl.value && longitudeEl.value) {
                                form.submit();
                            } else {
                                form.submit();
                            }
                        }, 500);
                    }
                });
            }
        </script>
    @endif
@endpush
@endsection
