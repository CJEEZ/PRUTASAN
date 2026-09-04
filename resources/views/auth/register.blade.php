<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        :root{ --primary:#065f46; --primary-2:#16a34a; --muted:#bbf7d0; --muted-2:#dcfce7; }
        .login-page input:focus { border-color: var(--primary) !important; box-shadow: none !important; }
        .login-page .btn-primary { background-color: var(--primary); }
        .login-page .btn-primary:hover { background-color: var(--primary-2); }
        .login-page .link-primary { color: var(--primary); }
        .login-page .link-primary:hover { color: var(--primary-2); }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 login-page relative overflow-hidden" style="background-image: linear-gradient(rgba(255,255,255,0.58), rgba(255,255,255,0.66)), url('{{ asset('Screenshot 2026-07-04 102403.png') }}'); background-size: cover; background-position: center;">
        <div class="w-full max-w-sm">
            <div class="text-center mb-6">
                <img src="{{ asset('ORNOSFARM_LOGOS.png') }}" alt="FruitExpress" class="h-28 md:h-40 lg:h-48 w-auto object-contain mb-2 mx-auto block">

                <p class="text-sm text-emerald-500">Create your account</p>
            </div>

            <div class="bg-white rounded-2xl border-2 border-emerald-900 shadow-2xl p-6">
                @if(session('signup_success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm" role="alert">
                        {{ session('signup_success') }}
                    </div>
                @endif

                @if(session('account_not_found'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-4 text-sm" role="alert">
                        {{ session('account_not_found') }}
                    </div>
                @endif

                <div class="mb-6">
                    <div id="role-tabs" class="flex bg-gray-100 rounded-full p-1">
                        <button type="button" data-role="customer" class="flex-1 py-2 rounded-full text-sm font-semibold text-emerald-600 bg-white shadow-sm">Customer</button>
                        <button type="button" data-role="driver" class="flex-1 py-2 rounded-full text-sm font-semibold text-emerald-600/70">Driver</button>
                    </div>
                </div>

                <form method="POST" action="{{ route('register') }}" id="register-form">
                    @csrf
                    <input type="hidden" name="role" id="role-input" value="customer">

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}" autofocus
                               class="mt-1 block w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                               class="mt-1 block w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                               class="mt-1 block w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <p id="driver-note" class="text-xs text-gray-400 mb-4 hidden">Minimum 8 characters for driver accounts</p>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="mt-1 block w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <button type="submit" id="submit-btn" class="w-full py-2.5 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700">
                        Create Customer Account
                    </button>
                </form>

                <p class="mt-4 text-center text-sm text-gray-500">Already have an account? <a href="{{ route('login') }}" class="text-emerald-600 font-medium">Login</a></p>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const tabs = document.querySelectorAll('#role-tabs button');
            const roleInput = document.getElementById('role-input');
            const submitBtn = document.getElementById('submit-btn');
            const driverNote = document.getElementById('driver-note');

            function setRole(role){
                roleInput.value = role;
                tabs.forEach(btn => {
                    if(btn.dataset.role === role){
                        btn.classList.add('bg-white','shadow-sm');
                        btn.classList.remove('text-emerald-600/70');
                        btn.classList.add('text-emerald-700');
                    } else {
                        btn.classList.remove('bg-white','shadow-sm','text-emerald-700');
                        btn.classList.add('text-emerald-600/70');
                    }
                });
                if(role === 'driver'){
                    submitBtn.textContent = 'Create Driver Account';
                    driverNote.classList.remove('hidden');
                } else {
                    submitBtn.textContent = 'Create Customer Account';
                    driverNote.classList.add('hidden');
                }
            }

            tabs.forEach(btn => btn.addEventListener('click', (e) => setRole(e.currentTarget.dataset.role)));
            // initialize
            setRole('customer');
        })();
    </script>
</body>
</html>
