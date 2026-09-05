@extends('layouts.admin')

@section('page_title', 'System Settings')
@section('page_subtitle', 'Configure and manage system-wide settings')

@section('content')
<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
    <!-- Settings Form -->
    <div class="lg:col-span-2">
        <div class="stat-card p-3">
            <h3 class="text-base font-semibold text-gray-900 mb-3">General Settings</h3>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                @csrf
                @method('POST')

                <!-- Commission Rate -->
                <div>
                    <label class="block text-sm text-gray-700 font-semibold mb-1">Seller Commission Rate (%)</label>
                    <input type="number" name="seller_commission" step="0.01" min="0" max="100"
                           value="{{ $settings['seller_commission'] }}"
                              class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                          <p class="text-[10px] text-gray-500 mt-1">Commission percentage charged to sellers per order</p>
                </div>

                <!-- Site Name -->
                <div>
                    <label class="block text-sm text-gray-700 font-semibold mb-1">Site Name</label>
                    <input type="text" name="site_name" value="{{ $settings['site_name'] }}"
                           class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Site URL -->
                <div>
                    <label class="block text-sm text-gray-700 font-semibold mb-1">Site URL</label>
                    <input type="url" name="site_url" value="{{ $settings['site_url'] }}"
                           class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Maintenance Mode -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="maintenance_mode" id="maintenance_mode"
                           @if($settings['maintenance_mode']) checked @endif
                              class="w-3.5 h-3.5 text-green-600 border-gray-300 rounded">
                          <label for="maintenance_mode" class="text-sm text-gray-700 font-semibold">Enable Maintenance Mode</label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Panel -->
    <div>
        <!-- System Info -->
        <div class="stat-card p-3 mb-4">
            <h3 class="text-base font-semibold text-gray-900 mb-3">System Information</h3>
            <div class="space-y-2 text-xs">
                <div>
                    <p class="text-gray-500">Laravel Version</p>
                    <p class="font-semibold text-gray-900">{{ app()->version() }}</p>
                </div>
                <div>
                    <p class="text-gray-500">PHP Version</p>
                    <p class="font-semibold text-gray-900">{{ phpversion() }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Environment</p>
                    <p class="font-semibold text-gray-900">{{ config('app.env') }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="stat-card p-3">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Actions</h3>
            <div class="space-y-2">
                <button class="w-full px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-xs font-semibold text-left">
                    <i class="fas fa-refresh mr-2"></i>Clear Cache
                </button>
                <button class="w-full px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition text-xs font-semibold text-left">
                    <i class="fas fa-database mr-2"></i>Optimize Database
                </button>
                <button class="w-full px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition text-xs font-semibold text-left">
                    <i class="fas fa-download mr-2"></i>Export Database
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
