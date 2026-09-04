@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <!-- Admin Panel Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('ORNOSFARM_LOGOS.png') }}" alt="FruitExpress" class="h-28 w-auto object-contain">
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">FruitWeb Admin</h1>
            <p class="text-orange-100">Administrative Portal</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-xl border-2 border-emerald-900 shadow-2xl p-8">
            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf

                <!-- Email Input -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 @error('email') border-red-500 @enderror"
                           value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" id="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 @error('password') border-red-500 @enderror"
                           required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Error Message -->
                @if ($errors->has('admin'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                        {{ $errors->first('admin') }}
                    </div>
                @endif

                <!-- Login Button -->
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition duration-200 mb-4">
                    Sign In to Admin Panel
                </button>
            </form>

            <!-- Back to User Login -->
            <div class="text-center pt-6 border-t border-gray-200">
                <p class="text-gray-600 text-sm mb-3">Not an admin?</p>
                <a href="{{ route('login') }}" class="text-orange-500 hover:text-orange-600 font-semibold text-sm">
                    Back to User Login
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-white text-center mt-8 text-sm">
            Admin Portal • Protected Access Only
        </p>
    </div>
</div>

@endsection
