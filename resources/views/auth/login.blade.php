@extends('layouts.app')

@section('hideHeader')
@endsection

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center px-3 py-4 sm:p-4 login-page relative overflow-hidden" style="background-image: linear-gradient(rgba(255,255,255,0.58), rgba(255,255,255,0.66)), url('{{ asset('Screenshot 2026-07-04 102403.png') }}'); background-size: cover; background-position: center;">
    <div class="text-center mb-6 sm:mb-8">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('ORNOSFARM_LOGOS.png') }}" alt="FruitExpress" class="h-28 md:h-40 lg:h-48 w-auto object-contain">
        </div>
        <p class="text-gray-600 mt-1">Welcome back! Please login to continue</p>
    </div>

    <div class="w-full max-w-sm bg-white p-4 sm:p-8 rounded-xl border-2 border-emerald-900 shadow-2xl transition duration-500 hover:shadow-2xl">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Login</h2>
        <p class="text-sm text-gray-500 mb-6">Enter your credentials to access your account.</p>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="your@email.com"
                    class="w-full min-h-touch-target px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-base transition duration-150 focus:outline-none focus:border-green-600">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full min-h-touch-target px-4 py-2.5 pr-12 bg-white border border-gray-200 rounded-lg text-base transition duration-150 focus:outline-none focus:border-green-600">
                    <button type="button" data-password-toggle="password" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-green-600" aria-label="Show password" title="Show password">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full min-h-touch-target py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-150 shadow-md">
                Login
            </button>
        </form>

        <div class="mt-6 mb-6 relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Or continue with</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-6">
            <a href="{{ url('auth/google') }}" class="w-full min-h-touch-target py-2.5 px-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 flex items-center justify-center gap-2 shadow-sm">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Google</span>
            </a>

            <a href="{{ url('auth/facebook') }}" class="w-full min-h-touch-target py-2.5 px-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 flex items-center justify-center gap-2 shadow-sm">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#1877F2">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Facebook</span>
            </a>
        </div>

        <div class="mt-6 text-center text-sm">
            <p class="text-gray-500">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-green-600 font-semibold hover:text-green-700 transition">Sign up</a>
            </p>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('[data-password-toggle]').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const password = document.getElementById(toggle.dataset.passwordToggle);
            const isHidden = password.type === 'password';
            password.type = isHidden ? 'text' : 'password';
            toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            toggle.setAttribute('title', isHidden ? 'Hide password' : 'Show password');
            toggle.querySelector('i').className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
        });
    });
</script>

@endsection
