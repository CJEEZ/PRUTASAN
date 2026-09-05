<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Admin Panel">
    <meta name="theme-color" content="#15803d">
    <title>{{ config('app.name') }} - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sidebar-menu {
            transition: all 0.3s ease;
        }
        .sidebar-menu.active {
            display: block;
        }
        .nav-link.active,
        .nav-link[aria-current='page'] {
            background-color: #16a34a;
            color: #ffffff;
        }
        .stat-card {
            @apply bg-white rounded-lg shadow p-4 hover:shadow-lg transition;
        }
        .scrollbar-on-interaction {
            scrollbar-width: none;
        }
        .scrollbar-on-interaction::-webkit-scrollbar {
            height: 0;
        }
        .scrollbar-on-interaction:hover,
        .scrollbar-on-interaction:focus-within,
        .scrollbar-on-interaction:active {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .scrollbar-on-interaction:hover::-webkit-scrollbar,
        .scrollbar-on-interaction:focus-within::-webkit-scrollbar,
        .scrollbar-on-interaction:active::-webkit-scrollbar {
            height: 6px;
        }
        .mobile-tab {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.2rem;
            min-height: 54px;
            min-width: 54px;
            border-radius: 999px;
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .mobile-tab.active {
            color: #15803d;
            background: rgba(21, 128, 61, 0.1);
        }
        .sidebar-nav a {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Mobile responsive fixes */
        @media (max-width: 768px) {
            body { overflow-x: hidden; }
            .main-content { width: 100%; }
            #sidebar {
                width: min(86vw, 20rem);
                max-height: 100dvh;
                overflow-y: auto;
                padding-bottom: 1rem;
            }
            #sidebar .sidebar-nav {
                margin-top: 1rem;
                padding: 0 0.75rem;
            }
            #sidebar .nav-link {
                padding: 0.7rem 0.8rem;
                font-size: 0.95rem;
            }
            #sidebar .nav-link i {
                font-size: 0.95rem;
            }
            #sidebar .user-card {
                margin: 0.75rem;
                padding: 0.8rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50 overflow-x-hidden">
    <div class="flex min-h-screen overflow-hidden flex-col md:flex-row">
        <div id="mobile-sidebar-overlay" class="fixed inset-0 z-30 hidden bg-black/40 md:hidden"></div>
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 h-dvh w-[min(86vw,20rem)] max-w-[86vw] -translate-x-full transform overflow-y-auto bg-gradient-to-b from-green-800 to-green-900 text-white shadow-lg transition-transform duration-300 md:static md:h-screen md:w-64 md:translate-x-0 md:flex-shrink-0">
            <!-- Logo -->
            <div class="p-6 border-b border-green-700">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('ORNOSFARM_LOGOS.png') }}"
                         alt="FruitExpress"
                         class="h-16 md:h-20 w-auto object-contain">
                    <div class="min-w-0">
                        <h1 class="text-lg font-bold text-white">{{ config('app.name') }}</h1>
                        <p class="text-xs text-green-200">Admin Panel</p>
                    </div>
                </a>
            </div>

            <!-- Navigation Menu -->
            <nav class="sidebar-nav mt-6 px-3 sm:mt-8 sm:px-4">
                <div class="space-y-1.5 sm:space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.dashboard') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Products -->
                    <a href="{{ route('admin.products.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.products.*') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-boxes w-5"></i>
                        <span>Products</span>
                    </a>

                    <!-- Stock Monitoring -->
                    <a href="{{ route('admin.stock.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.stock.*') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>Stock Monitor</span>
                    </a>

                    <!-- Orders -->
                    <a href="{{ route('admin.orders.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.orders.*') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span>Orders</span>
                    </a>

                    <!-- Customers -->
                    <a href="{{ route('admin.customers.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.customers.*') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-users w-5"></i>
                        <span>Customers</span>
                    </a>

                    <!-- Sellers -->
                    <a href="{{ route('admin.sellers.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.sellers.*') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-store w-5"></i>
                        <span>Sellers</span>
                    </a>

                    <a href="{{ route('admin.drivers.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.drivers.*') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-id-card w-5"></i>
                        <span>Driver Applications</span>
                    </a>

                    <!-- Arindo Verification -->
                    <a href="{{ route('admin.arindo.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.arindo.*') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-landmark w-5"></i>
                        <span>Arindo Listings</span>
                    </a>

                    <!-- Financial -->
                    <a href="{{ route('admin.financial.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.financial.*') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-chart-pie w-5"></i>
                        <span>Financial</span>
                    </a>

                    <!-- System Settings -->
                    <a href="{{ route('admin.settings.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.settings.*') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-cogs w-5"></i>
                        <span>Settings</span>
                    </a>

                    <!-- Inquiries -->
                    <a href="{{ route('admin.inquiries.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.inquiries.*') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-envelope w-5"></i>
                        <span>Inquiries</span>
                    </a>

                    <!-- Messages -->
                    <a href="{{ route('admin.messages') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition {{ Route::is('admin.messages') ? 'active' : 'hover:bg-green-700' }}">
                        <i class="fas fa-comment-dots w-5"></i>
                        <span>Messages</span>
                    </a>
                </div>
            </nav>

            <!-- User Info Footer -->
            <div class="user-card relative mx-3 mt-6 mb-3 rounded-xl border border-green-700/60 bg-green-900/40 px-3 py-3 sm:mx-4 sm:px-4 sm:py-4">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-400 text-sm font-bold text-green-900">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-green-200">Admin</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.profile') }}" class="flex items-center justify-center gap-2 rounded-lg border border-green-700/60 bg-white/10 px-2 py-2 text-sm font-medium text-white transition hover:bg-white/20">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-white/10 px-2 py-2 text-sm font-medium text-green-200 transition hover:bg-white/20 hover:text-white">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto bg-gray-50">
            <!-- Top Bar -->
            <div class="sticky top-0 z-10 border-b border-gray-200 bg-white/95 backdrop-blur">
                <div class="flex items-center justify-between gap-2 px-3 py-2 sm:px-6 sm:py-3">
                    <div class="flex min-w-0 items-center gap-2">
                        <button id="mobile-sidebar-toggle" class="order-first rounded-lg p-1.5 text-gray-600 hover:bg-gray-100 md:hidden">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <img src="{{ asset('ORNOSFARM_LOGOS.png') }}" alt="FruitExpress" class="h-10 w-auto object-contain md:hidden">
                        <div class="min-w-0">
                            <p class="text-[9px] font-semibold uppercase tracking-[0.18em] text-green-600 sm:text-[10px]">Operations Hub</p>
                            <h2 class="text-sm font-bold text-gray-900 sm:text-lg">@yield('page_title', 'Dashboard')</h2>
                            <p class="mt-0.5 truncate text-[10px] text-gray-600 sm:mt-1 sm:text-xs">@yield('page_subtitle', '')</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-4">
                        @php
                            $adminNotifications = \App\Models\Notification::where('user_id', Auth::id())
                                ->latest()
                                ->take(10)
                                ->get();
                            $unreadNotificationCount = $adminNotifications->where('is_read', false)->count();
                        @endphp
                        <div class="relative">
                            <button id="admin-notification-toggle" type="button" class="relative rounded-lg p-2 text-gray-600 transition hover:bg-gray-100" aria-label="Notifications" aria-expanded="false">
                                <i class="fas fa-bell text-sm"></i>
                                <span id="admin-notification-badge" class="absolute -right-1 -top-1 hidden min-h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white">
                                    {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
                                </span>
                            </button>
                            <div id="admin-notification-panel" class="absolute right-0 top-12 z-50 hidden w-[min(92vw,24rem)] overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">
                                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                    <h3 class="font-semibold text-gray-900">Notifications</h3>
                                    @if ($unreadNotificationCount > 0)
                                        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                                            @csrf
                                            <button id="admin-notification-mark-all" type="submit" class="text-xs font-semibold text-green-700 hover:text-green-900">Mark all read</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="max-h-[min(65vh,28rem)] overflow-y-auto">
                                    @forelse ($adminNotifications as $notification)
                                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}" class="notification-item-form border-b border-gray-100 last:border-0" data-notification-read="{{ $notification->is_read ? 'true' : 'false' }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="flex w-full gap-3 px-4 py-3 text-left transition hover:bg-gray-50 {{ $notification->is_read ? 'bg-white' : 'bg-green-50/60' }}">
                                                <span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $notification->type === 'order_update' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600' }}">
                                                    <i class="fas {{ $notification->type === 'order_update' ? 'fa-shopping-bag' : 'fa-user-plus' }} text-xs"></i>
                                                </span>
                                                <span class="min-w-0">
                                                    <span class="block text-sm font-semibold text-gray-900">{{ $notification->title }}</span>
                                                    <span class="mt-0.5 block text-xs leading-5 text-gray-600">{{ $notification->message }}</span>
                                                    <span class="mt-1 block text-[11px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                                </span>
                                                @if (! $notification->is_read)
                                                    <span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-green-600"></span>
                                                @endif
                                            </button>
                                        </form>
                                    @empty
                                        <p class="px-4 py-8 text-center text-sm text-gray-500">No notifications yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="p-3 pb-24 sm:p-8 sm:pb-8">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="text-red-800 font-semibold mb-2">Errors:</h3>
                        <ul class="list-disc list-inside text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <nav id="mobile-bottom-nav" class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white/95 px-2 py-2 shadow-[0_-6px_20px_rgba(15,23,42,0.08)] backdrop-blur md:hidden">
        <div class="mx-auto flex max-w-5xl items-center justify-around gap-1">
            <a href="{{ route('admin.dashboard') }}" class="mobile-tab {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line text-base"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('admin.messages') }}" class="mobile-tab {{ Route::is('admin.messages') ? 'active' : '' }}">
                <i class="fas fa-comment-dots text-base"></i>
                <span>Messages</span>
            </a>
            <a href="{{ route('admin.financial.index') }}" class="mobile-tab {{ Route::is('admin.financial.*') ? 'active' : '' }}">
                <i class="fas fa-wallet text-base"></i>
                <span>Financial</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="mobile-tab {{ Route::is('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog text-base"></i>
                <span>Settings</span>
            </a>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarLinks = document.querySelectorAll('aside nav a.nav-link');
            const sidebarToggle = document.getElementById('mobile-sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-sidebar-overlay');
            const notificationToggle = document.getElementById('admin-notification-toggle');
            const notificationPanel = document.getElementById('admin-notification-panel');
            const notificationBadge = document.getElementById('admin-notification-badge');
            const markAllReadButton = document.getElementById('admin-notification-mark-all');

            const updateAdminNotificationBadge = () => {
                if (!notificationBadge) return;

                const unreadCount = Array.from(document.querySelectorAll('.notification-item-form[data-notification-read="false"]')).length;
                const displayValue = unreadCount > 9 ? '9+' : String(unreadCount || 0);

                notificationBadge.textContent = displayValue;
                notificationBadge.classList.toggle('hidden', unreadCount === 0);
                notificationBadge.classList.toggle('inline-flex', unreadCount > 0);
            };

            const mobileBottomNav = document.getElementById('mobile-bottom-nav');

            const openSidebar = () => {
                if (!sidebar || !overlay) return;
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                if (mobileBottomNav) {
                    mobileBottomNav.classList.add('hidden');
                }
            };

            const closeSidebar = () => {
                if (!sidebar || !overlay) return;
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                if (mobileBottomNav) {
                    mobileBottomNav.classList.remove('hidden');
                }
            };

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', () => {
                    if (sidebar?.classList.contains('-translate-x-full')) {
                        openSidebar();
                    } else {
                        closeSidebar();
                    }
                });
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    closeSidebar();
                }
            });

            const setActiveLink = () => {
                const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';

                sidebarLinks.forEach((link) => {
                    const href = link.getAttribute('href');
                    const linkPath = href
                        ? new URL(href, window.location.origin).pathname.replace(/\/+$/, '') || '/'
                        : '';
                    const isActive = Boolean(href) && (
                        currentPath === linkPath ||
                        (currentPath.startsWith(linkPath + '/') && linkPath !== '/admin')
                    );

                    link.classList.toggle('active', isActive);
                    if (isActive) {
                        link.setAttribute('aria-current', 'page');
                    } else {
                        link.removeAttribute('aria-current');
                    }
                });
            };

            setActiveLink();

            if (notificationToggle && notificationPanel) {
                updateAdminNotificationBadge();

                notificationToggle.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const isHidden = notificationPanel.classList.toggle('hidden');
                    notificationToggle.setAttribute('aria-expanded', String(!isHidden));
                });

                document.addEventListener('click', (event) => {
                    if (!notificationPanel.contains(event.target) && !notificationToggle.contains(event.target)) {
                        notificationPanel.classList.add('hidden');
                        notificationToggle.setAttribute('aria-expanded', 'false');
                    }
                });

                document.querySelectorAll('.notification-item-form').forEach((form) => {
                    form.addEventListener('submit', () => {
                        form.dataset.notificationRead = 'true';
                        const button = form.querySelector('button[type="submit"]');
                        if (button) {
                            button.classList.remove('bg-green-50/60');
                            button.classList.add('bg-white');
                        }
                        const unreadDot = form.querySelector('span.rounded-full.bg-green-600');
                        if (unreadDot) unreadDot.remove();
                        updateAdminNotificationBadge();
                    });
                });

                if (markAllReadButton) {
                    markAllReadButton.closest('form')?.addEventListener('submit', () => {
                        document.querySelectorAll('.notification-item-form[data-notification-read="false"]').forEach((form) => {
                            form.dataset.notificationRead = 'true';
                            const button = form.querySelector('button[type="submit"]');
                            if (button) {
                                button.classList.remove('bg-green-50/60');
                                button.classList.add('bg-white');
                            }
                            const unreadDot = form.querySelector('span.rounded-full.bg-green-600');
                            if (unreadDot) unreadDot.remove();
                        });
                        updateAdminNotificationBadge();
                    });
                }
            }

            sidebarLinks.forEach((link) => {
                link.addEventListener('click', () => {
                    sidebarLinks.forEach((item) => {
                        item.classList.remove('active');
                        item.removeAttribute('aria-current');
                    });
                    link.classList.add('active');
                    link.setAttribute('aria-current', 'page');
                    closeSidebar();
                });
            });
        });
    </script>
</body>
</html>
