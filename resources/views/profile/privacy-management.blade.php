<!-- Privacy Management Section (Hidden by default) -->
<div id="privacy-management" class="hidden mt-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Privacy Management</h3>
            <p class="text-sm text-gray-600 mt-1">Control your privacy settings and data</p>
        </div>

        <div class="p-6 space-y-6">
            <!-- Data Sharing -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-3">Data Sharing</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-700">Marketing Communications</p>
                            <p class="text-sm text-gray-600">Receive promotional emails and offers</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-700">Analytics Tracking</p>
                            <p class="text-sm text-gray-600">Help improve our services with usage data</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Account Data -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-3">Account Data</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Download My Data</span>
                        <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Download
                        </button>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">Delete My Account</span>
                        <button onclick="confirmDeleteAccount()" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Privacy Policy -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-3">Privacy Policy</h4>
                <p class="text-sm text-gray-600 mb-3">
                    Review our privacy policy to understand how we collect, use, and protect your data.
                </p>
                <a href="#" class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                    View Privacy Policy →
                </a>
            </div>
        </div>
    </div>
</div>