<div x-data="{ showCurrentPassword: false, showNewPassword: false, showConfirmPassword: false }" class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Change Password</h2>

    <form wire:submit="changePassword" class="space-y-4">
        <!-- Current Password -->
        <div>
            <label for="currentPassword" class="block text-sm font-medium text-gray-700">
                Current Password
            </label>
            <div class="relative mt-1">
                <input
                    :type="showCurrentPassword ? 'text' : 'password'"
                    id="currentPassword"
                    wire:model="currentPassword"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 pr-10"
                    placeholder="Enter current password"
                />
                <button
                    type="button"
                    @click="showCurrentPassword = !showCurrentPassword"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                >
                    <template x-if="!showCurrentPassword">
                        <x-icon.eye />
                    </template>
                    <template x-if="showCurrentPassword">
                        <x-icon.eye-off />
                    </template>
                </button>
            </div>
            @error('currentPassword')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- New Password -->
        <div>
            <label for="newPassword" class="block text-sm font-medium text-gray-700">
                New Password
            </label>
            <div class="relative mt-1">
                <input
                    :type="showNewPassword ? 'text' : 'password'"
                    id="newPassword"
                    wire:model="newPassword"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 pr-10"
                    placeholder="Enter new password"
                />
                <button
                    type="button"
                    @click="showNewPassword = !showNewPassword"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                >
                    <template x-if="!showNewPassword">
                        <x-icon.eye />
                    </template>
                    <template x-if="showNewPassword">
                        <x-icon.eye-off />
                    </template>
                </button>
            </div>
            @error('newPassword')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="newPassword_confirmation" class="block text-sm font-medium text-gray-700">
                Confirm New Password
            </label>
            <div class="relative mt-1">
                <input
                    :type="showConfirmPassword ? 'text' : 'password'"
                    id="newPassword_confirmation"
                    wire:model="newPassword_confirmation"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 pr-10"
                    placeholder="Confirm new password"
                />
                <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                >
                    <template x-if="!showConfirmPassword">
                        <x-icon.eye />
                    </template>
                    <template x-if="showConfirmPassword">
                        <x-icon.eye-off />
                    </template>
                </button>
            </div>
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors font-medium"
        >
            Change Password
        </button>
    </form>
</div>
