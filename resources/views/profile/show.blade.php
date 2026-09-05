@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#07170f] text-slate-100">
    <div class="max-w-6xl mx-auto px-3 sm:px-4">
        @if(session('success'))
            <div class="mb-4 rounded-2xl border border-emerald-600/30 bg-emerald-600/10 p-3 text-sm text-emerald-200 shadow-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-3 md:hidden">
            <details class="rounded-2xl border border-slate-800 bg-[#0d2f1b]/90 p-2 shadow-xl">
                @php $mobileProfileSection = $activeSection ?? 'account'; @endphp
                <summary class="flex cursor-pointer list-none items-center justify-between rounded-xl px-2 py-1.5 text-xs font-semibold text-white [&::-webkit-details-marker]:hidden">
                    <span class="flex items-center gap-2">
                        <i class="fas {{ $mobileProfileSection === 'edit' ? 'fa-user-circle' : ($mobileProfileSection === 'messages' ? 'fa-comment-dots' : ($mobileProfileSection === 'banks' ? 'fa-credit-card' : ($mobileProfileSection === 'change-password' ? 'fa-lock' : 'fa-shopping-bag'))) }} w-4 text-center text-emerald-300"></i>
                        <span>{{ $mobileProfileSection === 'edit' ? 'My Account' : ($mobileProfileSection === 'messages' ? 'Messages' : ($mobileProfileSection === 'banks' ? 'Banks & Cards' : ($mobileProfileSection === 'change-password' ? 'Change Password' : 'My Purchase'))) }}</span>
                    </span>
                    <i class="fas fa-chevron-down text-xs text-emerald-300"></i>
                </summary>
                <nav class="mt-2">
                    <ul class="space-y-1">
                        <li><a href="{{ route('profile.show') }}" @if($mobileProfileSection === 'account') aria-current="page" @endif class="flex items-center gap-2 rounded-xl px-2 py-1.5 text-xs {{ $mobileProfileSection === 'account' ? 'bg-emerald-600/20 font-semibold text-emerald-200' : 'text-slate-200 hover:bg-white/5 hover:text-white' }}"><i class="fas fa-shopping-bag w-4 text-center"></i><span>My Purchase</span></a></li>
                        <li><a href="{{ route('profile.edit') }}" @if($mobileProfileSection === 'edit') aria-current="page" @endif class="flex items-center gap-2 rounded-xl px-2 py-1.5 text-xs {{ $mobileProfileSection === 'edit' ? 'bg-emerald-600/20 font-semibold text-emerald-200' : 'text-slate-200 hover:bg-white/5 hover:text-white' }}"><i class="fas fa-user-circle w-4 text-center"></i><span>My Account</span></a></li>
                        <li><a href="{{ route('profile.notifications') }}" @if($mobileProfileSection === 'messages') aria-current="page" @endif class="flex items-center gap-2 rounded-xl px-2 py-1.5 text-xs {{ $mobileProfileSection === 'messages' ? 'bg-emerald-600/20 font-semibold text-emerald-200' : 'text-slate-200 hover:bg-white/5 hover:text-white' }}"><i class="fas fa-comment-dots w-4 text-center"></i><span>Messages</span></a></li>
                        <li><a href="{{ route('profile.banks') }}" @if($mobileProfileSection === 'banks') aria-current="page" @endif class="flex items-center gap-2 rounded-xl px-2 py-1.5 text-xs {{ $mobileProfileSection === 'banks' ? 'bg-emerald-600/20 font-semibold text-emerald-200' : 'text-slate-200 hover:bg-white/5 hover:text-white' }}"><i class="fas fa-credit-card w-4 text-center"></i><span>Banks &amp; Cards</span></a></li>
                        <li><a href="{{ route('profile.change_password') }}" @if($mobileProfileSection === 'change-password') aria-current="page" @endif class="flex items-center gap-2 rounded-xl px-2 py-1.5 text-xs {{ $mobileProfileSection === 'change-password' ? 'bg-emerald-600/20 font-semibold text-emerald-200' : 'text-slate-200 hover:bg-white/5 hover:text-white' }}"><i class="fas fa-lock w-4 text-center"></i><span>Change Password</span></a></li>
                    </ul>
                </nav>
            </details>
        </div>

        <div class="grid grid-cols-1 gap-3 xl:grid-cols-4 xl:gap-6">
            <!-- Sidebar -->
            <aside class="col-span-1 hidden xl:block w-72">
                <div class="rounded-[1.5rem] border border-slate-800 bg-[#0d2f1b]/90 p-3 shadow-2xl xl:p-6">
                    <div class="flex items-center gap-2 xl:gap-3">
                        @if(!empty($user->profile_photo_path))
                            <div class="h-12 w-12 rounded-full overflow-hidden border border-emerald-500 ring-2 ring-emerald-500/30 xl:h-16 xl:w-16">
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-full" onerror="this.onerror=null;this.src='https://placehold.co/200x200/FF7F00/ffffff?text=No+Photo';">
                            </div>
                        @else
                            <div class="h-12 w-12 shrink-0 aspect-square rounded-full bg-emerald-700 text-white flex items-center justify-center text-xl font-semibold ring-2 ring-emerald-500/30 xl:h-16 xl:w-16 xl:text-2xl">{{ strtoupper(substr($user->name,0,1)) }}</div>
                        @endif
                        <div>
                            <p class="font-semibold text-white text-base xl:text-xl">{{ $user->name }}</p>
                            <p class="text-xs text-slate-400 truncate max-w-[11rem] xl:max-w-[14rem]">{{ $user->email }}</p>
                        </div>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="mt-3 inline-flex items-center gap-2 rounded-2xl bg-white px-2.5 py-1.5 text-[10px] font-semibold text-slate-900 shadow-md shadow-emerald-800/20 hover:bg-emerald-100 xl:mt-5 xl:px-4 xl:py-2.5 xl:text-sm">
                        <i class="fas fa-edit text-xs"></i>
                        Edit Profile
                    </a>
                    <nav class="mt-3 divide-y divide-slate-800 xl:mt-5">
                        <ul class="space-y-1 py-1 xl:space-y-3 xl:py-3">
                            <li><a href="{{ route('home') }}" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm text-slate-300 hover:bg-slate-800 xl:px-4 xl:py-3 xl:text-base"><i class="fas fa-home w-4 xl:w-5"></i> Home</a></li>
                            <li><a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-3 py-2 rounded-2xl text-sm xl:px-4 xl:py-3 xl:text-base {{ ($activeSection ?? 'account') === 'account' ? 'bg-emerald-600/15 text-emerald-200' : 'text-slate-300 hover:bg-slate-800' }}"><i class="fas fa-shopping-bag w-4 xl:w-5"></i> My Purchase</a></li>
                            <li><a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-2xl text-sm xl:px-4 xl:py-3 xl:text-base {{ ($activeSection ?? '') === 'edit' ? 'bg-emerald-600/15 text-emerald-200' : 'text-slate-300 hover:bg-slate-800' }}"><i class="fas fa-user-circle w-4 xl:w-5"></i> My Account</a></li>
                            <li><a href="{{ route('profile.notifications') }}" class="flex items-center gap-3 px-3 py-2 rounded-2xl text-sm xl:px-4 xl:py-3 xl:text-base {{ ($activeSection ?? '') === 'messages' ? 'bg-emerald-600/15 text-emerald-200' : 'text-slate-300 hover:bg-slate-800' }}"><i class="fas fa-comment-dots w-4 xl:w-5"></i> Messages</a></li>
                            <li><a href="{{ route('profile.banks') }}" class="flex items-center gap-3 px-3 py-2 rounded-2xl text-sm xl:px-4 xl:py-3 xl:text-base {{ ($activeSection ?? '') === 'banks' ? 'bg-emerald-600/15 text-emerald-200' : 'text-slate-300 hover:bg-slate-800' }}"><i class="fas fa-credit-card w-4 xl:w-5"></i> Banks &amp; Cards</a></li>
                            <li><a href="{{ route('profile.change_password') }}" class="flex items-center gap-3 px-3 py-2 rounded-2xl text-sm xl:px-4 xl:py-3 xl:text-base {{ ($activeSection ?? '') === 'change-password' ? 'bg-emerald-600/15 text-emerald-200' : 'text-slate-300 hover:bg-slate-800' }}"><i class="fas fa-lock w-4 xl:w-5"></i> Change Password</a></li>
                        </ul>
                    </nav>
                </div>
            </aside>

            <!-- Main -->
            <main class="col-span-1 xl:col-span-3">
                <div class="min-h-[28vh] rounded-2xl border border-slate-800 bg-[#0d2f1b] p-2 shadow-2xl sm:p-4 md:min-h-[32vh] md:p-5">
                    <div class="mb-3 flex flex-col items-start justify-between gap-2 md:mb-4 md:flex-row md:items-center">
                        <div>
                            <h1 class="text-xl font-semibold text-white sm:text-3xl">Customer Dashboard</h1>
                            <p class="max-w-2xl text-xs leading-5 text-emerald-100/80">Track orders, manage your profile, and access account tools in one place.</p>
                        </div>
                    </div>

                    @if(($activeSection ?? 'account') === 'account')
                        @isset($orderStats)
                            <div class="mb-3 overflow-x-auto sm:mb-4">
                                <div class="flex min-w-max gap-1 sm:gap-2">
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
                                        <a href="{{ route('profile.show', ['status' => $key]) }}" data-status="{{ $key }}" class="purchase-filter flex min-w-[88px] flex-1 flex-col justify-center rounded-xl border border-emerald-300/60 bg-[#112e20] px-1.5 py-1.5 text-left transition hover:bg-[#16362b] sm:min-w-[132px] sm:rounded-2xl sm:px-3 sm:py-2.5 {{ $currentFilter === $key ? 'ring-2 ring-emerald-200' : '' }}">
                                            <p class="text-[8px] uppercase tracking-[0.15em] text-slate-300 sm:text-[9px] sm:tracking-[0.2em]">{{ $label }}</p>
                                            <div class="mt-1 flex items-end justify-between gap-1 sm:mt-1.5 sm:gap-2">
                                                <span class="text-lg font-semibold leading-none text-white sm:text-2xl">{{ $orderStats[$key]['count'] ?? 0 }}</span>
                                                <span class="text-[10px] font-semibold text-emerald-200 sm:text-xs">₱{{ number_format($orderStats[$key]['total'] ?? 0, 2) }}</span>
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
