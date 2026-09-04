@extends('layouts.admin')

@section('page_title', 'Admin Profile')
@section('page_subtitle', 'Manage your account and monitor platform activity')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-[1.15fr_0.85fr] gap-6">
        <div class="space-y-6">
            <div class="stat-card">
                <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-4">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-full object-cover ring-4 ring-green-100 shadow-lg shadow-green-600/20">
                        @else
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-green-600 to-emerald-400 text-2xl font-bold text-white shadow-lg shadow-green-600/20">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-green-600">Administrator</p>
                            <h3 class="mt-1 text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">{{ ucfirst($user->role ?? 'admin') }}</span>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Verified</span>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Platform Overview</p>
                        <h3 class="mt-2 text-xl font-bold text-gray-900">Admin Profile</h3>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Customers</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $platformStats['customers'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Sellers</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $platformStats['sellers'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Orders</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $platformStats['orders'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Revenue</p>
                        <p class="mt-2 text-xl font-bold text-gray-900">₱{{ number_format($platformStats['revenue'], 2) }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Messages</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $platformStats['messages'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Pending Sellers</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $platformStats['pending_sellers'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="stat-card">
                <h3 class="text-lg font-bold text-gray-900">Account Details</h3>
                <dl class="mt-4 space-y-4 text-sm">
                    <div class="flex justify-between gap-3 border-b border-gray-100 pb-3">
                        <dt class="text-gray-500">Full name</dt>
                        <dd class="font-semibold text-gray-900 text-right">{{ $user->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-3 border-b border-gray-100 pb-3">
                        <dt class="text-gray-500">Email</dt>
                        <dd class="font-semibold text-gray-900 text-right break-all">{{ $user->email }}</dd>
                    </div>
                    <div class="flex justify-between gap-3 border-b border-gray-100 pb-3">
                        <dt class="text-gray-500">Role</dt>
                        <dd class="font-semibold text-gray-900 text-right">{{ ucfirst($user->role ?? 'admin') }}</dd>
                    </div>
                    <div class="flex justify-between gap-3 pb-1">
                        <dt class="text-gray-500">Last updated</dt>
                        <dd class="font-semibold text-gray-900 text-right">{{ $user->updated_at ? $user->updated_at->format('M d, Y') : 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="stat-card">
                <h3 class="text-lg font-bold text-gray-900">Quick Actions</h3>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                        <span><i class="fas fa-chart-line mr-2"></i>Dashboard</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                    <a href="{{ route('admin.messages') }}" class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                        <span><i class="fas fa-comment-dots mr-2"></i>Messages</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                        <span><i class="fas fa-cogs mr-2"></i>Settings</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <div class="stat-card">
                <h3 class="text-lg font-bold text-gray-900">Profile photo</h3>
                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf

                    <div class="flex items-center gap-4">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-green-200">
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-600 text-lg font-bold text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <label for="profile_photo" class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                <i class="fas fa-camera"></i>
                                {{ $user->profile_photo_path ? 'Change photo' : 'Attach profile photo' }}
                            </label>
                            <input id="profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/jpg" class="hidden">
                            <p class="mt-2 text-xs text-gray-500">JPG or PNG, maximum 1MB</p>
                        </div>
                    </div>

                    <div>
                        <label for="admin_name" class="mb-2 block text-sm font-medium text-gray-700">Name</label>
                        <input id="admin_name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2.5 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200">
                    </div>

                    <div>
                        <label for="admin_email" class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                        <input id="admin_email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2.5 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200">
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700">
                        <i class="fas fa-save mr-2"></i>
                        Save profile
                    </button>
                </form>
            </div>

            <div class="stat-card">
                <h3 class="text-lg font-bold text-gray-900">Change password</h3>
                <form method="POST" action="{{ route('admin.profile.change_password') }}" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label for="current_password" class="mb-2 block text-sm font-medium text-gray-700">Current password</label>
                        <input id="current_password" type="password" name="current_password" required class="w-full rounded-xl border border-gray-300 px-3 py-2.5 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200">
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-gray-700">New password</label>
                        <input id="password" type="password" name="password" required class="w-full rounded-xl border border-gray-300 px-3 py-2.5 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200">
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700">Confirm new password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full rounded-xl border border-gray-300 px-3 py-2.5 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-200">
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700">
                        <i class="fas fa-lock mr-2"></i>
                        Update password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
