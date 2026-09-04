<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FruitExpress">
    <meta name="theme-color" content="#558467">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FruitExpress') }}</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">



<script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .two-level-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
        }
        /* Brand color override: map existing orange utility classes to new brand color (#558467)
           This keeps templates unchanged while updating button and accent colors globally. */
        .bg-orange-600 { background-color: #558467 !important; }
        .hover\:bg-orange-700:hover { background-color: #476d58 !important; }
        .bg-orange-500 { background-color: #6da07b !important; }
        .bg-orange-100 { background-color: rgba(85,132,103,0.08) !important; }
        .text-orange-600 { color: #558467 !important; }
        .border-orange-200 { border-color: rgba(85,132,103,0.2) !important; }
    </style>
</head>
<body class="bg-gray-50 antialiased min-h-screen flex flex-col overflow-x-hidden text-gray-900">

    @php
        $isAdminLayout = auth()->check() && auth()->user()->role === 'admin' && request()->is('admin*');
        $isSellerApproval = request()->routeIs('seller.approval.*');
    @endphp

    @unless(View::hasSection('hideHeader'))
    <header class="sticky top-0 z-40 two-level-shadow bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
        @if($isAdminLayout)
            <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between h-14">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600">Seller</span>
                    </a>
                    <div class="flex items-center space-x-5">
                        <a href="{{ route('profile.show') }}" class="flex items-center text-gray-700 hover:text-emerald-600 transition">
                            <i class="fas fa-user-circle text-2xl mr-1"></i>
                            <span class="hidden sm:inline text-base">Profile</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                            @csrf
                            <button type="submit" class="flex items-center text-base text-gray-700 hover:text-red-600 transition font-semibold">
                                <i class="fas fa-sign-out-alt text-xl mr-1"></i>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <!-- Top Info Bar (Full Width) - hidden on seller routes -->
            @unless(request()->routeIs('seller.*'))
            <div class="bg-emerald-800 text-white text-base pt-6 pb-4 rounded-t-2xl hidden sm:block">
                <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 flex justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-map-marker-alt text-sm"></i>
                        <span>Bagong Silang, Victoria, Oriental Mindoro</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('seller.start') }}" class="hover:text-emerald-200">Start Selling</a>

                        <div class="relative">
                            <button id="header-notification-toggle"
                                    type="button"
                                    class="relative rounded-full p-2 text-white/90 transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/60"
                                    aria-label="Notifications"
                                    aria-expanded="false">
                                <i class="fas fa-bell text-base"></i>
                                <span id="header-notification-badge" class="absolute -right-1 -top-1 hidden min-w-[18px] rounded-full bg-amber-400 px-1.5 py-0.5 text-[10px] font-bold text-emerald-950"></span>
                            </button>

                            <div id="header-notification-panel" class="absolute right-0 top-12 z-50 hidden w-[min(92vw,22rem)] overflow-hidden rounded-xl border border-emerald-700 bg-white text-gray-800 shadow-2xl">
                                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                                    <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                    <button id="header-notification-mark-all" type="button" class="text-xs font-medium text-emerald-700 hover:text-emerald-800">Mark all read</button>
                                </div>
                                <div id="header-notification-list" class="max-h-80 overflow-y-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endunless

            <!-- Main Navigation Bar (Full Width) -->
            <div class="bg-white">
                <div class="w-full mx-auto px-3 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 sm:h-20 items-center">

                        <!-- Left: Logo -->
                        <div class="flex-shrink-0 flex items-center h-full">
                            <a href="{{ route('catalog.index') }}" class="flex items-center gap-2 sm:gap-3 min-h-touch-target">
                                <img src="{{ asset('ORNOSFARM_LOGOS.png') }}" alt="FruitExpress" class="h-14 w-auto object-contain sm:h-16">
                                <span class="text-[11px] leading-tight text-gray-500 sm:text-xs">Oriental Mindoro</span>
                            </a>
                        </div>


                        <!-- Middle: Search Bar -->
                        @unless($isSellerApproval || request()->routeIs('profile.*') || request()->routeIs('tracking.show') || request()->routeIs('checkout.*') || request()->routeIs('seller.messages'))
                            <div class="hidden md:flex flex-grow max-w-2xl mx-4">
                                <form action="{{ route('catalog.index') }}" method="GET" class="relative w-full">
                                    <input type="search" name="search" placeholder="Search for fresh seasonal fruits..."
                                           class="w-full py-2.5 pl-4 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                           value="{{ request('search') }}">
                                    <button type="submit" class="absolute right-0 top-0 h-full px-4 bg-emerald-600 text-white rounded-r-lg hover:bg-emerald-700 transition">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        @endunless
                        <!-- Right: Cart/Auth Icons -->
                        <div class="flex items-center gap-2 sm:gap-4 lg:gap-6 flex-shrink-0">

                            <!-- Profile Link (New) -->
                            @auth
                                @unless($isSellerApproval)
                                    <a href="{{ route('profile.show') }}" class="hidden sm:flex items-center space-x-1 text-gray-700 hover:text-emerald-600 transition duration-150 font-medium group">
                                        <i class="fas fa-user-circle text-2xl text-gray-600 group-hover:text-emerald-600"></i>
                                        <span class="text-base">Profile</span>
                                    </a>
                                @endunless
                            @endauth

                            <!-- Cart Button -->
                            @unless($isSellerApproval)
                                <button id="open-cart-sidebar" class="relative p-2 min-h-touch-target min-w-touch-target text-gray-600 hover:text-emerald-600 transition duration-150 focus:outline-none rounded-full">
                                    <i class="fas fa-shopping-cart text-2xl"></i>
                                    <span id="cart-count" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                                        {{ $cartService->getTotalQuantity() }}
                                    </span>
                                </button>
                            @endunless

                            <!-- Auth/Logout Button -->
                            @auth
                                @if($isSellerApproval)
                                    <a href="{{ route('home') }}" class="hidden sm:flex items-center space-x-1 text-base font-medium text-gray-700 transition duration-150 hover:text-emerald-600">
                                        <i class="fas fa-home text-xl mr-1"></i>Back to Home
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:flex items-center space-x-1">
                                        @csrf
                                        <button type="submit" class="text-base text-gray-700 hover:text-emerald-600 transition duration-150 font-medium">
                                            <i class="fas fa-sign-out-alt text-xl mr-1"></i>Logout
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="hidden sm:flex items-center space-x-1 text-gray-700 hover:text-emerald-600 transition duration-150 font-medium">
                                    <i class="fas fa-sign-in-alt text-2xl"></i>
                                    <span class="text-base">Login</span>
                                </a>
                            @endauth

                            <!-- Mobile Menu Toggle -->
                            <button id="mobile-menu-toggle" class="md:hidden p-2 min-h-touch-target min-w-touch-target text-gray-600 hover:text-emerald-600 transition duration-150 focus:outline-none rounded-md">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200 p-4">
                <div class="space-y-3">

                    @unless($isSellerApproval || request()->routeIs('profile.*') || request()->routeIs('tracking.show') || request()->routeIs('checkout.*'))
                        <form action="{{ route('catalog.index') }}" method="GET" class="relative w-full">
                            <input type="search" name="search" placeholder="Search..."
                                   class="w-full py-2 pl-4 pr-12 border border-gray-300 rounded-lg text-sm"
                                   value="{{ request('search') }}">
                            <button type="submit" class="absolute right-0 top-1/2 transform -translate-y-1/2 text-gray-400 p-2">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    @endunless

                    @auth
                        @unless($isSellerApproval)
                            <!-- Profile Link in Mobile Menu (New) -->
                            <a href="{{ route('profile.show') }}" class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                Profile
                            </a>
                        @endunless
                        @if(auth()->user()->role === 'seller')
                            <a href="{{ route('seller.dashboard') }}" class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                Seller Dashboard
                            </a>
                        @endif
                        @if($isSellerApproval)
                            <a href="{{ route('home') }}" class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                Back to Home
                            </a>
                        @else
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-red-50 hover:text-red-600">
                                    Logout ({{ Auth::user()->name }})
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">Login</a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-emerald-600 hover:bg-emerald-700 mt-2 text-center">Register</a>
                    @endauth
                </div>
            </div>
        @endif
    </header>
    @endunless



<main class="flex-grow pb-24 md:pb-0">
        @yield('content')
    </main>

    @unless($isAdminLayout)
    <nav class="mobile-bottom-nav md:hidden">
        <div class="mx-auto flex max-w-5xl items-center justify-around px-2 py-2">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }} flex-1">
                <i class="fas fa-home text-lg"></i>
                <span>Home</span>
            </a>
            @unless($isSellerApproval)
                <a href="{{ route('cart.show') }}" class="{{ request()->routeIs('cart.show') ? 'active' : '' }} flex-1">
                    <i class="fas fa-shopping-cart text-lg"></i>
                    <span>Cart</span>
                </a>
            @endunless
            @auth
                @if(auth()->user()->role === 'seller')
                    <a href="{{ route('seller.dashboard') }}" class="{{ request()->routeIs('seller.dashboard') ? 'active' : '' }} flex-1">
                        <i class="fas fa-store text-lg"></i>
                        <span>Seller</span>
                    </a>
                @endif
                @unless($isSellerApproval)
                    <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }} flex-1">
                        <i class="fas fa-user-circle text-lg"></i>
                        <span>Account</span>
                    </a>
                @endunless
            @else
                <a href="{{ route('login') }}" class="flex-1">
                    <i class="fas fa-sign-in-alt text-lg"></i>
                    <span>Login</span>
                </a>
            @endauth
        </div>
    </nav>
    @endunless

    <!-- Footer remains unchanged -->
    <footer class="bg-gray-100 text-gray-600 mt-auto border-t border-gray-200">
        <div class="w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-gray-500 hidden">
                &copy; {{ date('Y') }}. All rights reserved.
                </div>
        </div>
    </footer>

    @stack('scripts')

    @unless($isAdminLayout)
    <!-- Cart Sidebar -->
    <div id="cart-sidebar" class="fixed inset-y-0 right-0 w-full sm:w-96 bg-white z-50 transform translate-x-full transition-transform duration-300 shadow-2xl flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-emerald-600 to-emerald-800">
            <h2 class="text-xl font-bold text-white">Shopping Cart</h2>
            <button id="close-cart-sidebar" class="text-white hover:text-gray-200 transition">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div id="cart-items-container" class="p-4 space-y-4 overflow-y-auto flex-grow">
            <div class="text-center text-gray-500 py-16">
                <i class="fas fa-shopping-cart text-5xl mb-4 text-gray-300"></i>
                <p>Your cart is currently empty.</p>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 bg-gray-50">
            <div class="mb-4 space-y-2">
                <div class="flex justify-between text-gray-700">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal" class="font-semibold text-gray-800">₱0.00</span>
                </div>
                <div class="flex justify-between text-gray-700 pb-4 border-b border-gray-200">
                    <span>Shipping:</span>
                    <span id="cart-shipping" class="font-semibold text-gray-800">₱0.00</span>
                </div>
                <div class="flex justify-between text-lg font-bold">
                    <span class="text-gray-800">Total:</span>
                    <span id="cart-total" class="text-emerald-600">₱0.00</span>
                </div>
            </div>
            @auth
            <a href="{{ route('checkout.show') }}" class="block w-full text-center px-4 py-3 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition shadow-lg mb-2">
                Proceed to Checkout
            </a>
            @else
            <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 bg-emerald-400 text-white rounded-lg font-semibold transition shadow-lg mb-2">
                Login to Proceed
            </a>
            @endauth
            <a href="{{ route('cart.show') }}" class="block w-full text-center px-4 py-3 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition">
                View Full Cart
            </a>
        </div>
    </div>
    @endunless

    @unless($isAdminLayout)
    <script>
        const notificationToggle = document.getElementById('header-notification-toggle');
        const notificationPanel = document.getElementById('header-notification-panel');
        const notificationBadge = document.getElementById('header-notification-badge');
        const notificationList = document.getElementById('header-notification-list');
        const markAllReadButton = document.getElementById('header-notification-mark-all');

        let headerNotifications = [];
        let headerUnreadCount = 0;

        function escapeNotificationText(value) {
            return String(value ?? '').replace(/[&<>'"]/g, character => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
            }[character]));
        }

        function renderNotifications() {
            if (!notificationList || !notificationBadge) {
                return;
            }

            const notifications = headerNotifications;
            const unreadCount = headerUnreadCount;

            notificationBadge.textContent = unreadCount > 9 ? '9+' : unreadCount;
            notificationBadge.classList.toggle('hidden', unreadCount === 0);

            if (notifications.length === 0) {
                notificationList.innerHTML = '<div class="px-4 py-8 text-center text-sm text-gray-500">All caught up. No new notifications.</div>';
                return;
            }

            notificationList.innerHTML = notifications.map(notification => `
                <button type="button" data-notification-id="${notification.id}" data-order-id="${notification.order_id || ''}" class="flex w-full items-start gap-3 border-b border-gray-100 px-4 py-3 text-left transition hover:bg-gray-50 ${notification.read ? 'bg-white' : 'bg-emerald-50/60'}">
                    <span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full ${notification.read ? 'bg-gray-100 text-gray-500' : 'bg-emerald-100 text-emerald-700'}">
                        <i class="fas ${notification.read ? 'fa-check' : 'fa-bell'} text-xs"></i>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-semibold text-gray-900">${escapeNotificationText(notification.title)}</span>
                        <span class="mt-1 block text-xs leading-5 text-gray-600">${escapeNotificationText(notification.message)}</span>
                        <span class="mt-1 block text-[11px] text-gray-400">${escapeNotificationText(notification.time)}</span>
                    </span>
                    ${notification.read ? '' : '<span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-600"></span>'}
                </button>
            `).join('');

            notificationList.querySelectorAll('[data-notification-id]').forEach(button => {
                button.addEventListener('click', () => {
                    const item = headerNotifications.find(entry => String(entry.id) === button.dataset.notificationId);
                    if (!item) return;
                    fetch('/notifications/' + item.id + '/read', {
                        method: 'PATCH',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' },
                    }).then(() => loadNotifications());
                    if (item.order_id) window.location.href = '/tracking/' + item.order_id;
                });
            });
        }

        function markAllNotificationsRead() {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' },
            }).then(() => loadNotifications());
        }

        function loadNotifications() {
            fetch('{{ route('notifications.index') }}', { headers: { 'Accept': 'application/json' } })
                .then(response => response.ok ? response.json() : null)
                .then(data => {
                    if (!data) return;
                    headerNotifications = data.notifications || [];
                    headerUnreadCount = Number(data.unread_count || 0);
                    renderNotifications();
                })
                .catch(() => {});
        }

        if (notificationToggle && notificationPanel) {
            notificationToggle.addEventListener('click', () => {
                const isHidden = notificationPanel.classList.toggle('hidden');
                notificationToggle.setAttribute('aria-expanded', String(!isHidden));
            });

            document.addEventListener('click', (event) => {
                const target = event.target;

                if (!notificationPanel.contains(target) && !notificationToggle.contains(target)) {
                    notificationPanel.classList.add('hidden');
                    notificationToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }

        if (markAllReadButton) {
            markAllReadButton.addEventListener('click', markAllNotificationsRead);
        }

        renderNotifications();
        loadNotifications();
        setInterval(loadNotifications, 30000);

        // Load cart items from AJAX endpoint
        function loadCartItems() {
            fetch('/cart/api/items')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('cart-items-container');
                    const subtotalEl = document.getElementById('cart-subtotal');
                    const shippingEl = document.getElementById('cart-shipping');
                    const totalEl = document.getElementById('cart-total');
                    const cartCount = document.getElementById('cart-count');
                    const shipping = data.items.length > 0 ? 50 : 0;

                    if (data.items.length === 0) {
                        container.innerHTML = '<div class="text-center text-gray-500 py-16"><i class="fas fa-shopping-cart text-5xl mb-4 text-gray-300"></i><p>Your cart is currently empty.</p></div>';
                        subtotalEl.textContent = '₱0.00';
                        shippingEl.textContent = '₱0.00';
                        totalEl.textContent = '₱0.00';
                        cartCount.textContent = '0';
                        return;
                    }

                    // Build cart items HTML
                    let html = '';
                    data.items.forEach(item => {
                        html += `
                            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-orange-300 transition">
                                <img src="${item.image_url}" alt="${item.name}" class="w-16 h-16 object-cover rounded-lg">
                                <div class="flex-grow">
                                    <h4 class="font-semibold text-sm text-gray-800">${item.name}</h4>
                                    <p class="text-xs text-gray-500">₱${parseFloat(item.price).toFixed(2)} / ${item.unit}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="flex items-center border border-gray-300 rounded">
                                            <button onclick="updateQty(${item.id}, ${item.quantity - 1})" class="px-2 py-1 text-gray-600 hover:bg-gray-200 font-bold">−</button>
                                            <span class="px-3 py-1 text-sm font-semibold">${item.quantity}</span>
                                            <button onclick="updateQty(${item.id}, ${item.quantity + 1})" class="px-2 py-1 text-gray-600 hover:bg-gray-200 font-bold">+</button>
                                        </div>
                                        <button onclick="removeItem(${item.id})" class="ml-auto text-red-500 hover:text-red-700 font-bold">✕</button>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-emerald-600">₱${parseFloat(item.subtotal).toFixed(2)}</p>
                                </div>
                            </div>
                        `;
                    });

                    container.innerHTML = html;
                    subtotalEl.textContent = '₱' + parseFloat(data.total).toFixed(2);
                    shippingEl.textContent = '₱' + shipping.toFixed(2);
                    totalEl.textContent = '₱' + (parseFloat(data.total) + shipping).toFixed(2);
                    cartCount.textContent = data.count;
                })
                .catch(error => console.error('Error loading cart items:', error));
        }

        function updateQty(productId, quantity) {
            if (quantity < 1) {
                removeItem(productId);
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/cart/update/${productId}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="quantity" value="${quantity}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function removeItem(productId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/cart/remove/${productId}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('cart-sidebar');
            const openButton = document.getElementById('open-cart-sidebar');
            const closeButton = document.getElementById('close-cart-sidebar');
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            if (openButton && sidebar) {
                openButton.addEventListener('click', () => {
                    loadCartItems();
                    sidebar.classList.remove('translate-x-full');
                });
            }

            if (closeButton && sidebar) {
                closeButton.addEventListener('click', () => {
                    sidebar.classList.add('translate-x-full');
                });
            }

            if (mobileMenuToggle && mobileMenu) {
                mobileMenuToggle.addEventListener('click', () => {
                    const isHidden = mobileMenu.classList.contains('hidden');
                    mobileMenu.classList.toggle('hidden', !isHidden);
                    mobileMenuToggle.setAttribute('aria-expanded', String(isHidden));
                });

                document.addEventListener('click', (event) => {
                    if (!mobileMenu.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                        mobileMenu.classList.add('hidden');
                        mobileMenuToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            document.querySelectorAll('form[action*="/cart/add/"]').forEach(form => {
                form.addEventListener('submit', () => {
                    setTimeout(loadCartItems, 300);
                });
            });

            if (sidebar) {
                document.querySelectorAll('a[href="{{ route('checkout.show') }}"], a[href="{{ route('login') }}"]').forEach(link => {
                    link.addEventListener('click', () => {
                        sidebar.classList.add('translate-x-full');
                    });
                });
            }

            loadCartItems();
        });
    </script>
    @endunless
</body>
</html>
