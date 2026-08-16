@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#07170f] text-slate-100">
    <div class="max-w-6xl mx-auto px-3 sm:px-4">
        @if(session('success'))
            <div class="mb-4 rounded-2xl border border-emerald-600/30 bg-emerald-600/10 p-3 text-sm text-emerald-200 shadow-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="md:hidden mb-4">
            <details class="bg-[#0d2f1b]/90 rounded-2xl shadow-xl p-3 border border-slate-800">
                <summary class="font-medium text-white text-sm">Menu</summary>
                <nav class="mt-3">
                    <ul class="space-y-2">
                        <li><a href="{{ route('profile.show') }}" class="block text-slate-200 text-sm hover:text-white">My Purchase</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="block text-slate-200 text-sm hover:text-white">My Account</a></li>
                        <li><a href="{{ route('profile.notifications') }}" class="block text-slate-200 text-sm hover:text-white">Messages</a></li>
                    </ul>
                </nav>
            </details>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 xl:gap-8">
            <!-- Sidebar -->
            <aside class="col-span-1 hidden xl:block w-72">
                <div class="bg-[#0d2f1b]/90 rounded-[1.5rem] border border-slate-800 shadow-2xl p-4">
                    <div class="flex items-center gap-3">
                        @if(!empty($user->profile_photo_path))
                            <div class="w-16 h-16 rounded-full overflow-hidden border border-emerald-500 ring-2 ring-emerald-500/30">
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-full" onerror="this.onerror=null;this.src='https://placehold.co/200x200/FF7F00/ffffff?text=No+Photo';">
                            </div>
                        @else
                            <div class="w-16 h-16 rounded-full bg-emerald-700 text-white flex items-center justify-center text-2xl font-semibold ring-2 ring-emerald-500/30">{{ strtoupper(substr($user->name,0,1)) }}</div>
                        @endif
                        <div>
                            <p class="font-semibold text-white text-lg">{{ $user->name }}</p>
                            <p class="text-xs text-slate-400 truncate max-w-[11rem]">{{ $user->email }}</p>
                        </div>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="mt-4 inline-flex items-center gap-2 px-3 py-2 rounded-2xl bg-white text-slate-900 text-xs font-semibold shadow-md shadow-emerald-800/20 hover:bg-emerald-100">
                        <i class="fas fa-edit text-xs"></i>
                        Edit Profile
                    </a>

                    <nav class="mt-4 divide-y divide-slate-800">
                        <ul class="space-y-2 py-2">
                            <li><a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-3 py-2 rounded-2xl text-sm {{ ($activeSection ?? 'account') === 'account' ? 'bg-emerald-600/15 text-emerald-200' : 'text-slate-300 hover:bg-slate-800' }}"><i class="fas fa-shopping-bag w-4"></i> My Purchase</a></li>
                            <li><a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-2xl text-sm {{ ($activeSection ?? '') === 'edit' ? 'bg-emerald-600/15 text-emerald-200' : 'text-slate-300 hover:bg-slate-800' }}"><i class="fas fa-user-circle w-4"></i> My Account</a></li>
                            <li><a href="{{ route('profile.notifications') }}" class="flex items-center gap-3 px-3 py-2 rounded-2xl text-sm text-slate-300 hover:bg-slate-800"><i class="fas fa-comment-dots w-4"></i> Messages</a></li>
                            <li><a href="{{ route('profile.banks') }}" class="flex items-center gap-3 px-3 py-2 rounded-2xl text-sm text-slate-300 hover:bg-slate-800"><i class="fas fa-credit-card w-4"></i> Banks &amp; Cards</a></li>
                            <li><a href="{{ route('profile.change_password') }}" class="flex items-center gap-3 px-3 py-2 rounded-2xl text-sm text-slate-300 hover:bg-slate-800"><i class="fas fa-lock w-4"></i> Change Password</a></li>
                        </ul>
                    </nav>
                </div>
            </aside>

            <!-- Main -->
            <main class="col-span-1 xl:col-span-3">
                <div class="rounded-[1.5rem] border border-slate-800 bg-[#0d2f1b] p-4 md:p-5 min-h-[40vh] shadow-2xl">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-semibold text-white">Customer Dashboard</h1>
                            <p class="max-w-2xl text-xs leading-5 text-emerald-100/80">Track orders, manage your profile, and access account tools in one place.</p>
                        </div>
                        <div>
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-emerald-600 text-white text-sm font-semibold shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-300" aria-label="Home">
                                <i class="fas fa-home text-sm"></i>
                                <span class="ml-1">Home</span>
                            </a>
                        </div>
                    </div>

                    @if(($activeSection ?? 'account') === 'account')
                        @isset($orderStats)
                            <div class="mb-4 overflow-x-auto">
                                <div class="flex min-w-max gap-3">
                                    @php
                                        $labels = [
                                            'all' => 'All',
                                            'pending' => 'To Pay',
                                            'packed' => 'To Ship',
                                            'shipped' => 'To Receive',
                                            'delivered' => 'Completed',
                                        ];
                                    @endphp
                                    @php $currentFilter = $activeOrderFilter ?? request()->query('status', 'all'); @endphp
                                    @foreach($labels as $key => $label)
                                        <a href="{{ route('profile.show', ['status' => $key]) }}" data-status="{{ $key }}" class="purchase-filter flex min-w-[160px] flex-1 flex-col justify-center rounded-[1.5rem] border border-emerald-300/60 bg-[#112e20] px-4 py-3 text-left transition hover:bg-[#16362b] {{ $currentFilter === $key ? 'ring-2 ring-emerald-200' : '' }}">
                                            <p class="text-[10px] uppercase tracking-[0.3em] text-slate-300">{{ $label }}</p>
                                            <div class="mt-2 flex items-end justify-between gap-3">
                                                <span class="text-3xl font-semibold leading-none text-white">{{ $orderStats[$key]['count'] ?? 0 }}</span>
                                                <span class="text-xs font-semibold text-emerald-200">₱{{ number_format($orderStats[$key]['total'] ?? 0, 2) }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endisset

                        {{-- recent orders list (if available) --}}
                        @includeWhen(View::exists('profile.orders-list'), 'profile.orders-list')

                        <!-- Server-side filtering via links; no client interception to ensure reliability -->
                    @else
                        @switch($activeSection ?? '')
                            @case('edit')
                                @includeWhen(View::exists('profile.edit'), 'profile.edit')
                                @break
                            @case('messages')
                                @includeWhen(View::exists('profile.notifications-section'), 'profile.notifications-section')
                                @break
                            @case('banks')
                                @includeWhen(View::exists('profile.banks-and-cards'), 'profile.banks-and-cards')
                                @break
                            @case('change-password')
                                @includeWhen(View::exists('profile.change-password'), 'profile.change-password')
                                @break
                            @default
                                <div class="text-gray-600">Select a section from the left to view details.</div>
                        @endswitch
                    @endif
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
