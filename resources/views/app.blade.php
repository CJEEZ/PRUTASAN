<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name','Fruit2Web') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="font-bold text-orange-600">Fruit2Web</a>
                <form action="{{ route('products.index') }}" method="GET" class="flex">
                    <input type="text" name="q" placeholder="Search for fresh seasonal fruits..." class="border rounded px-3 py-2 w-96" value="{{ request('q') }}">
                    <button class="bg-orange-500 text-white px-3 ml-2 rounded">Search</button>
                </form>
            </div>

            <div class="flex items-center space-x-4">
                <a href="{{ route('cart.index') }}" class="flex items-center">
                    <svg class="w-6 h-6 mr-1" ...>...</svg> Cart
                </a>
                @auth
                    <a href="#" class="px-3">{{ auth()->user()->name }}</a>
                    <form action="{{ route('logout') }}" method="POST">@csrf<button class="px-3">Logout</button></form>
                @else
                    <a href="{{ route('login') }}" class="px-3">Login</a>
                    <a href="{{ route('register') }}" class="px-3">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
