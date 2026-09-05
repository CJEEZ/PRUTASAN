@extends('layouts.app')

@section('content')
<div class="container mx-auto w-full px-3 py-4 sm:px-4 sm:py-6">
    <div class="mx-auto w-full max-w-5xl">
        <h1 class="mb-4 text-2xl font-bold text-gray-800 sm:mb-6 sm:text-3xl">Edit Profile</h1>

        <div class="rounded-lg bg-white p-4 shadow-md sm:p-6">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-5 grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">
                    <div class="flex flex-col items-center text-center md:text-left">
                        <div class="mb-3 flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-orange-500 text-3xl font-bold text-white sm:h-24 sm:w-24 sm:text-4xl">
                            @if($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile photo of {{ $user->name }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/200x200/FF7F00/ffffff?text=No+Photo';">
                            @else
                                {{ strtoupper(substr($user->name ?? 'User', 0, 1)) }}
                            @endif
                        </div>
                        <label for="profile_photo" class="inline-flex min-h-[44px] items-center rounded-lg bg-orange-600 px-3 py-2 text-sm font-semibold text-white hover:bg-orange-700 cursor-pointer">
                            Change Photo
                        </label>
                        <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/jpeg,image/png,image/jpg">
                        @error('profile_photo')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-3">JPG, PNG, max 1MB</p>
                    </div>

                    <div class="space-y-3 md:col-span-2 sm:space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-orange-500 sm:px-4">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-orange-500 sm:px-4">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                               placeholder="e.g. 09171234567"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-orange-500 sm:px-4">
                        @error('phone_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-orange-500 sm:px-4">
                        @error('date_of_birth')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:mt-6 md:grid-cols-2 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select id="gender" name="gender" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-orange-500 sm:px-4">
                            <option value="" {{ old('gender', $user->gender) === null ? 'selected' : '' }}>Select gender</option>
                            <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Shipping Address</label>
                        <textarea id="shipping_address" name="shipping_address" rows="3"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-orange-500 sm:px-4" placeholder="Enter your shipping address">{{ old('shipping_address', $user->shipping_address) }}</textarea>
                        @error('shipping_address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-2 sm:mt-8 sm:flex-row sm:items-center sm:justify-between sm:gap-3">
                    <button type="submit" class="w-full rounded-lg bg-orange-600 px-5 py-2.5 font-semibold text-white transition hover:bg-orange-700 sm:w-auto">
                        Update Profile
                    </button>
                    <a href="{{ route('profile.show') }}" class="w-full rounded-lg bg-gray-200 px-5 py-2.5 text-center text-gray-700 transition hover:bg-gray-300 sm:w-auto">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
