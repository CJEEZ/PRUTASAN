<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Driver Profile | {{ config('app.name', 'FruitExpress') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-orange-50 text-gray-900">
<main class="px-3 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-5">
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <header class="flex flex-col gap-5 rounded-2xl bg-emerald-800 p-5 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between sm:p-6">
            <div class="flex min-w-0 items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white/15 text-2xl">
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile photo of {{ $user->name }}" class="h-full w-full object-cover">
                    @else
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="min-w-0"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-200">Driver profile</p><h1 class="mt-1 truncate text-2xl font-bold">{{ $user->name }}</h1><p class="mt-1 text-sm text-emerald-100">Keep your driver information current.</p></div>
            </div>
            <a href="{{ route('driver.dashboard') }}" class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-emerald-800 hover:bg-emerald-50 sm:w-auto"><i class="fas fa-arrow-left"></i> Driver hub</a>
        </header>

        <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100"><p class="text-xs text-gray-500">Active deliveries</p><p class="mt-2 text-2xl font-bold text-emerald-700">{{ $driverStats['active'] }}</p></div>
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100"><p class="text-xs text-gray-500">Completed</p><p class="mt-2 text-2xl font-bold text-blue-700">{{ $driverStats['completed'] }}</p></div>
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100"><p class="text-xs text-gray-500">Total trips</p><p class="mt-2 text-2xl font-bold text-orange-600">{{ $driverStats['total'] }}</p></div>
        </section>

        <form method="POST" action="{{ route('driver.profile.update') }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl bg-white p-5 shadow-lg ring-1 ring-gray-100 sm:p-8">
            @csrf @method('PATCH')
            <section>
                <div class="border-b border-gray-100 pb-4"><h2 class="text-xl font-bold text-gray-900">Personal information</h2><p class="mt-1 text-sm text-gray-500">Update the details customers and the delivery team use to reach you.</p></div>
                <div class="mt-5 flex flex-wrap items-center gap-4">
                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-emerald-100 text-2xl font-bold text-emerald-700">
                        @if($user->profile_photo_path)
                            <img id="profile-photo-preview" src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile photo of {{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            <img id="profile-photo-preview" src="" alt="Profile photo preview" class="hidden h-full w-full object-cover">
                            <span id="profile-photo-fallback">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div><label for="profile_photo" class="inline-flex min-h-[42px] cursor-pointer items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700"><i class="fas fa-camera"></i> {{ $user->profile_photo_path ? 'Change photo' : 'Add profile photo' }}</label><input id="profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/jpg" class="sr-only"><p id="profile-photo-name" class="mt-1 text-xs text-gray-500">JPG or PNG, maximum 1MB</p></div>
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div><label for="name" class="block text-sm font-semibold text-gray-700">Full name</label><input id="name" name="name" required value="{{ old('name', $user->name) }}" class="mt-2 min-h-[44px] w-full rounded-xl border border-gray-200 px-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"></div>
                    <div><label for="email" class="block text-sm font-semibold text-gray-700">Email</label><input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}" class="mt-2 min-h-[44px] w-full rounded-xl border border-gray-200 px-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"></div>
                    <div><label for="phone_number" class="block text-sm font-semibold text-gray-700">Phone number</label><input id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="mt-2 min-h-[44px] w-full rounded-xl border border-gray-200 px-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"></div>
                    <div><label for="date_of_birth" class="block text-sm font-semibold text-gray-700">Date of birth</label><input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth', $user->date_of_birth) }}" class="mt-2 min-h-[44px] w-full rounded-xl border border-gray-200 px-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"></div>
                    <div><label for="gender" class="block text-sm font-semibold text-gray-700">Gender</label><select id="gender" name="gender" class="mt-2 min-h-[44px] w-full rounded-xl border border-gray-200 px-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"><option value="">Select gender</option><option value="male" @selected(old('gender', $user->gender) === 'male')>Male</option><option value="female" @selected(old('gender', $user->gender) === 'female')>Female</option><option value="other" @selected(old('gender', $user->gender) === 'other')>Other</option></select></div>
                    <div><label for="shipping_address" class="block text-sm font-semibold text-gray-700">Address</label><input id="shipping_address" name="shipping_address" value="{{ old('shipping_address', $user->shipping_address) }}" class="mt-2 min-h-[44px] w-full rounded-xl border border-gray-200 px-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"></div>
                </div>
            </section>

            <section class="border-t border-gray-100 pt-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-xl font-bold text-gray-900">Driver verification</h2><p class="mt-1 text-sm text-gray-500">Update your license number or replace a document when needed.</p></div><span class="w-fit rounded-full px-3 py-1 text-xs font-bold {{ $application?->status === 'approved' || $application?->status === 'hired' ? 'bg-emerald-100 text-emerald-800' : ($application?->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ ucfirst($application->status ?? 'Not submitted') }}</span></div>
                @if($application)
                    <div class="mt-5"><label for="license_serial_number" class="block text-sm font-semibold text-gray-700">License serial number</label><input id="license_serial_number" name="license_serial_number" required value="{{ old('license_serial_number', $application->license_serial_number) }}" class="mt-2 min-h-[44px] w-full rounded-xl border border-gray-200 px-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"></div>
                    <div class="mt-5 grid gap-4 sm:grid-cols-3">
                        @foreach([['license_photo', 'License', 'license_photo_path'], ['or_photo', 'OR', 'or_photo_path'], ['cr_photo', 'CR', 'cr_photo_path']] as [$field, $label, $path])
                            <div><label for="{{ $field }}" class="block text-sm font-semibold text-gray-700">{{ $label }} document</label><label for="{{ $field }}" class="mt-2 flex min-h-[110px] cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 p-2 text-center hover:border-emerald-400">@if($application->{$path})<img src="{{ Storage::disk('public')->url($application->{$path}) }}" alt="{{ $label }} document" class="h-20 w-full rounded-lg object-cover">@else<i class="fas fa-camera text-xl text-emerald-600"></i>@endif<input id="{{ $field }}" name="{{ $field }}" type="file" accept="image/jpeg,image/png" class="sr-only"><span class="mt-1 text-xs text-gray-500">Tap to replace</span></label></div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">No driver application has been submitted yet. Start your application from the driver hub.</p>
                @endif
            </section>

            <div class="flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end"><a href="{{ route('driver.dashboard') }}" class="inline-flex min-h-[46px] items-center justify-center rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50">Cancel</a><button class="min-h-[46px] rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-800"><i class="fas fa-save mr-2"></i>Save driver profile</button></div>
        </form>
    </div>
</main>
<script>
    document.getElementById('profile_photo')?.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) return;
        const preview = document.getElementById('profile-photo-preview');
        const fallback = document.getElementById('profile-photo-fallback');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        fallback?.classList.add('hidden');
        document.getElementById('profile-photo-name').textContent = file.name;
    });
</script>
</body>
</html>
