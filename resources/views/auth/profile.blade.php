@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8 mt-10 mb-20">
    <div class="bg-white p-6 sm:p-8 rounded-xl shadow-2xl border border-gray-100">
        
        <div class="flex items-center space-x-6 mb-8 border-b pb-4">
            <div class="flex-shrink-0">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-orange-100 text-orange-600 flex items-center justify-center rounded-full text-3xl font-bold">
                    J
                </div>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800">Juan Dela Cruz's Profile</h1>
                <p class="text-lg text-orange-600 font-medium capitalize">User Account</p>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="flex space-x-4 border-b mb-6 text-sm sm:text-base" id="profile-tabs">
            <button data-tab="details" class="py-2 px-4 border-b-2 border-orange-600 text-orange-600 font-semibold transition duration-150">
                Account Details
            </button>
            <button data-tab="orders" class="py-2 px-4 border-b-2 border-transparent text-gray-600 hover:border-gray-300 transition duration-150">
                Order History
            </button>
            <button data-tab="security" class="py-2 px-4 border-b-2 border-transparent text-gray-600 hover:border-gray-300 transition duration-150">
                Security
            </button>
        </div>

        
        <!-- Tab Content: Account Details -->
        <div id="tab-details" class="tab-content space-y-6">
            <h2 class="text-2xl font-semibold text-gray-800">Personal Information</h2>
            
            <div class="space-y-4">
                <!-- Name -->
                <div class="p-4 bg-gray-50 rounded-lg flex justify-between items-center border border-gray-200">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Full Name</p>
                        <p class="text-lg font-semibold text-gray-800">Juan Dela Cruz</p>
                    </div>
                    <button class="text-sm text-orange-600 hover:text-orange-700 font-medium">Edit</button>
                </div>
                
                <!-- Email -->
                <div class="p-4 bg-gray-50 rounded-lg flex justify-between items-center border border-gray-200">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email Address</p>
                        <p class="text-lg font-semibold text-gray-800">juan.dela.cruz@example.com</p>
                    </div>
                    <button class="text-sm text-orange-600 hover:text-orange-700 font-medium">Edit</button>
                </div>

                <!-- Phone Number -->
                <div class="p-4 bg-gray-50 rounded-lg flex justify-between items-center border border-gray-200">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Phone Number</p>
                        <p class="text-lg font-semibold text-gray-800">09123456789</p>
                    </div>
                    <button class="text-sm text-orange-600 hover:text-orange-700 font-medium">Edit</button>
                </div>

                <!-- Default Address -->
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Default Shipping Address</h3>
                        <button class="text-sm text-orange-600 hover:text-orange-700 font-medium">Manage Addresses</button>
                    </div>
                    <p class="text-gray-600 text-sm">Unit 1, Sampaguita St., Brgy. 10, Victoria, Oriental Mindoro, 5205</p>
                </div>
            </div>
        </div>
        
        <!-- Tab Content: Order History (Hidden by default) -->
        <div id="tab-orders" class="tab-content hidden space-y-6">
            <h2 class="text-2xl font-semibold text-gray-800">Recent Orders</h2>
            
            <!-- Order Card 1 -->
            <div class="p-4 border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-500">Order #F2W-20241001</span>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Delivered</span>
                </div>
                <p class="text-xl font-bold text-gray-800 mb-2">₱450.00 Total</p>
                <p class="text-sm text-gray-600">3 items ordered. Placed on Oct 1, 2024.</p>
                <div class="mt-3 flex justify-end">
                    <button class="text-sm text-orange-600 hover:text-orange-700 font-medium">View Details</button>
                </div>
            </div>

            <!-- Order Card 2 -->
            <div class="p-4 border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-500">Order #F2W-20240920</span>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Processing</span>
                </div>
                <p class="text-xl font-bold text-gray-800 mb-2">₱1,200.00 Total</p>
                <p class="text-sm text-gray-600">5 items ordered. Placed on Sep 20, 2024.</p>
                <div class="mt-3 flex justify-end">
                    <button class="text-sm text-orange-600 hover:text-orange-700 font-medium">View Details</button>
                </div>
            </div>
        </div>
        
        <!-- Tab Content: Security (Hidden by default) -->
        <div id="tab-security" class="tab-content hidden space-y-6">
            <h2 class="text-2xl font-semibold text-gray-800">Update Password</h2>
            
            <form action="{{ route('profile.update_password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required
                           class="mt-1 w-full max-w-sm px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                
                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" id="new_password" name="new_password" required
                           class="mt-1 w-full max-w-sm px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                
                <!-- Confirm New Password -->
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                           class="mt-1 w-full max-w-sm px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                
                <button type="submit" class="px-6 py-2 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition shadow-md">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabsContainer = document.getElementById('profile-tabs');
        const tabButtons = tabsContainer.querySelectorAll('button');
        const tabContents = document.querySelectorAll('.tab-content');

        const switchTab = (activeTab) => {
            tabButtons.forEach(button => {
                if (button.dataset.tab === activeTab) {
                    button.classList.add('border-orange-600', 'text-orange-600', 'font-semibold');
                    button.classList.remove('border-transparent', 'text-gray-600', 'hover:border-gray-300');
                } else {
                    button.classList.remove('border-orange-600', 'text-orange-600', 'font-semibold');
                    button.classList.add('border-transparent', 'text-gray-600', 'hover:border-gray-300');
                }
            });

            tabContents.forEach(content => {
                if (content.id === `tab-${activeTab}`) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
        };

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                switchTab(button.dataset.tab);
            });
        });
        
        // Initialize: Ensure the first tab is active on load
        switchTab('details');
    });
</script>
@endsection