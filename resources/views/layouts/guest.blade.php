<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Fruit2Web">
    <meta name="theme-color" content="#558467">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Fruit2Web') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased overflow-x-hidden" style="background-color: #fef4e7;">
    <div class="min-h-screen flex flex-col sm:justify-center items-center px-3 py-4 sm:px-6 sm:py-6">
        <div>
            <a href="/" class="text-4xl font-bold text-orange-600">
                Fruit2Web
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-4 sm:mt-6 px-4 py-6 sm:px-6 sm:py-8 bg-white shadow-xl overflow-hidden rounded-xl">
            @yield('content')
        </div>
    </div>
</body>
</html>
