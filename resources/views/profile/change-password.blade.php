<!-- Change Password Section -->
<div id="change-password" class="mt-4 sm:mt-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-3 py-2 border-b border-gray-200 bg-gray-50 sm:px-6 sm:py-4">
            <h3 class="text-base font-bold text-gray-800 sm:text-lg">Change Password</h3>
            <p class="text-xs text-gray-600 mt-0.5 sm:mt-1 sm:text-sm">Update your account password</p>
        </div>

        <div class="p-3 sm:p-6">
            <form action="{{ route('profile.updatePassword') }}" method="POST">
                @csrf

                <!-- Current Password -->
                <div class="mb-3 sm:mb-4">
                    <label for="current_password" class="block text-xs font-medium text-gray-700 mb-1 sm:text-sm sm:mb-2">
                        Current Password
                    </label>
                    <input type="password" id="current_password" name="current_password" required
                           class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent sm:px-3 sm:py-2">
                </div>

                <!-- New Password -->
                <div class="mb-3 sm:mb-4">
                    <label for="password" class="block text-xs font-medium text-gray-700 mb-1 sm:text-sm sm:mb-2">
                        New Password
                    </label>
                    <input type="password" id="password" name="password" required
                              class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent sm:px-3 sm:py-2">
                          <p class="text-[10px] text-gray-500 mt-1 sm:text-xs">Password must be at least 8 characters long</p>
                </div>

                <!-- Confirm New Password -->
                <div class="mb-4 sm:mb-6">
                    <label for="password_confirmation" class="block text-xs font-medium text-gray-700 mb-1 sm:text-sm sm:mb-2">
                        Confirm New Password
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent sm:px-3 sm:py-2">
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-2 sm:space-x-3">
                    <button type="button" onclick="toggleChangePassword()" class="px-3 py-1.5 text-sm text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition sm:px-4 sm:py-2">
                        Cancel
                    </button>
                    <button type="submit" class="px-3 py-1.5 text-sm bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition sm:px-4 sm:py-2">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
