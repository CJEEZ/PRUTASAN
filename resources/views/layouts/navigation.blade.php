<!--
    This file typically handles the main navigation bar, which needs to know
    whether a user is authenticated to show the correct links (Login/Register vs. Profile/Logout).
    Optimized for mobile and touch devices.
-->
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-md sticky top-0 z-50 safe-top">

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-3 xs:px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14 xs:h-16 sm:h-16">
            <div class="flex items-center gap-2 xs:gap-4 sm:gap-8 min-w-0">
                <!-- Logo/Application Name - Touch Target: 44x44 -->
                <div class="shrink-0 flex items-center h-11 w-11 xs:h-auto xs:w-auto">
                    <a href="{{ route('catalog.index') }}" class="flex items-center text-base xs:text-lg font-bold text-green-700 hover:text-green-900 transition duration-150 min-h-touch-target">
                        <svg class="w-5 h-5 xs:w-6 xs:h-6 mr-1 xs:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="hidden xs:inline">Fruit2Web</span>
                    </a>
                </div>

                <!-- Navigation Links - Hidden on Mobile, Visible on SM+ -->
                <div class="hidden space-x-3 xs:space-x-6 sm:space-x-8 sm:-my-px sm:ml-4 sm:flex">
                    <x-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.index')" class="text-sm xs:text-base py-2">
                        {{ __('Shop') }}
                    </x-nav-link>
                    <x-nav-link :href="route('cart.show')" :active="request()->routeIs('cart.show')" class="text-sm xs:text-base py-2">
                        {{ __('Cart') }}
                    </x-nav-link>
                    
                    @if (Auth::check() && Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-sm xs:text-base py-2">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown / Auth Links - Desktop -->
            <div class="hidden sm:flex sm:items-center sm:gap-6">
                @auth
                    <!-- Authenticated User Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 min-h-touch-target">
                                <div class="truncate">{{ Auth::user()->name }}</div>
                                <svg class="fill-current h-4 w-4 ml-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.show')" class="min-h-touch-target">
                                {{ __('My Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('checkout.show')" class=\"min-h-touch-target\">
                                {{ __('Checkout') }}
                            </x-dropdown-link>
                            <form method=\"POST\" action=\"{{ route('logout') }}\">
                                @csrf
                                <x-dropdown-link :href=\"route('logout')\"
                                        onclick=\"event.preventDefault(); this.closest('form').submit();\" class=\"min-h-touch-target\">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-green-600 transition duration-150 min-h-touch-target px-3 py-2">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm font-medium text-gray-700 hover:text-green-600 transition duration-150 min-h-touch-target px-3 py-2">Register</a>
                    @endif
                @endauth
            </div>
            
            <!-- Mobile Menu Icons -->
            <div class=\"flex sm:hidden items-center gap-1\">
                <!-- Mobile Cart/Profile Quick Access -->
                @auth
                    <a href=\"{{ route('cart.show') }}\" class=\"min-h-touch-target min-w-touch-target flex items-center justify-center text-gray-700 hover:text-green-600 transition duration-150\">
                        <i class=\"fas fa-shopping-cart text-lg\"></i>
                    </a>
                    <a href=\"{{ route('profile.show') }}\" class=\"min-h-touch-target min-w-touch-target flex items-center justify-center text-gray-700 hover:text-green-600 transition duration-150\">
                        <i class=\"fas fa-user-circle text-xl\"></i>
                    </a>
                @else
                    <a href=\"{{ route('cart.show') }}\" class=\"min-h-touch-target min-w-touch-target flex items-center justify-center text-gray-700 hover:text-green-600 transition duration-150\">
                        <i class=\"fas fa-shopping-cart text-lg\"></i>
                    </a>
                @endauth
                
                <!-- Hamburger Menu -->
                <button @click=\"open = !open\" class=\"min-h-touch-target min-w-touch-target inline-flex items-center justify-center text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150\">
                    <svg class=\"h-6 w-6\" :class=\"{'hidden': open, 'block': !open }\" stroke=\"currentColor\" fill=\"none\" viewBox=\"0 0 24 24\">
                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 6h16M4 12h16M4 18h16\" />
                    </svg>
                    <svg class=\"h-6 w-6\" :class=\"{'hidden': !open, 'block': open }\" stroke=\"currentColor\" fill=\"none\" viewBox=\"0 0 24 24\">
                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M6 18L18 6M6 6l12 12\" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Responsive Menu -->
    <div :class=\"{'block': open, 'hidden': !open }\" class=\"hidden sm:hidden border-t border-gray-100 bg-white\">
        <div class=\"px-3 pt-2 pb-3 space-y-1\">
            <a href=\"{{ route('catalog.index') }}\" class=\"block px-3 py-3 rounded-md text-base font-medium hover:bg-gray-50 transition duration-150 min-h-touch-target\">
                {{ __('Shop') }}
            </a>
            
            @if (Auth::check() && Auth::user()->role === 'admin')
                <a href=\"{{ route('admin.dashboard') }}\" class=\"block px-3 py-3 rounded-md text-base font-medium hover:bg-gray-50 transition duration-150 min-h-touch-target\">
                    {{ __('Seller Dashboard') }}
                </a>
            @endif
            
            @auth
                <a href=\"{{ route('checkout.show') }}\" class=\"block px-3 py-3 rounded-md text-base font-medium hover:bg-gray-50 transition duration-150 min-h-touch-target\">
                    {{ __('Checkout') }}
                </a>
                <a href=\"{{ route('profile.show') }}\" class=\"block px-3 py-3 rounded-md text-base font-medium hover:bg-gray-50 transition duration-150 min-h-touch-target\">
                    {{ __('Profile') }}
                </a>
                <form method=\"POST\" action=\"{{ route('logout') }}\">
                    @csrf
                    <button type=\"submit\" class=\"w-full text-left block px-3 py-3 rounded-md text-base font-medium hover:bg-gray-50 transition duration-150 min-h-touch-target\">
                        {{ __('Log Out') }}
                    </button>
                </form>
            @else
                <a href=\"{{ route('login') }}\" class=\"block px-3 py-3 rounded-md text-base font-medium hover:bg-gray-50 transition duration-150 min-h-touch-target\">
                    {{ __('Log in') }}
                </a>
                @if (Route::has('register'))
                    <a href=\"{{ route('register') }}\" class=\"block px-3 py-3 rounded-md text-base font-medium hover:bg-gray-50 transition duration-150 min-h-touch-target\">
                        {{ __('Register') }}
                    </a>
                @endif
            @endauth
        </div>
    </div>

</nav>