@extends('layouts.dash2')
@section('title', $title)
@section('content')

<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Alerts -->
    <x-danger-alert />
    <x-success-alert />
    <x-error-alert />

    <!-- Page Header with Breadcrumbs -->
    <div class="flex flex-col mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Security Settings</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <a href="{{ route('profile') }}" class="hover:text-primary-600">Settings</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <span class="font-medium text-gray-700">Security</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Content Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                <i data-lucide="shield" class="h-5 w-5 mr-2 text-primary-600"></i>
                Change Password
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Update your account password to maintain security
            </p>
        </div>
                
        <!-- Form Content -->
        <div class="p-6">
            <form action="{{route('updateuserpass')}}" method="post">
                @csrf
                @method('PUT')
                
                <!-- Current Password -->
                <div class="mb-6">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Current Password
                    </label>
                    <div class="relative" x-data="{ showPassword: false }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="current_password" 
                            name="current_password"
                            class="block w-full pl-10 pr-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                            placeholder="Enter your current password"
                            required
                            autocomplete="current-password"
                        />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="text-gray-400 hover:text-primary-600 focus:outline-none"
                            >
                                <i x-show="!showPassword" data-lucide="eye" class="h-5 w-5"></i>
                                <i x-show="showPassword" data-lucide="eye-off" class="h-5 w-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- New Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        New Password
                    </label>
                    <div class="relative" x-data="{ showPassword: false }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="key" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="password" 
                            name="password"
                            class="block w-full pl-10 pr-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                            placeholder="Enter your new password"
                            required
                            autocomplete="new-password"
                        />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="text-gray-400 hover:text-primary-600 focus:outline-none"
                            >
                                <i x-show="!showPassword" data-lucide="eye" class="h-5 w-5"></i>
                                <i x-show="showPassword" data-lucide="eye-off" class="h-5 w-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm Password
                    </label>
                    <div class="relative" x-data="{ showPassword: false }">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="check-circle" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="password_confirmation" 
                            name="password_confirmation"
                            class="block w-full pl-10 pr-10 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                            placeholder="Confirm your new password"
                            required
                            autocomplete="new-password"
                        />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="text-gray-400 hover:text-primary-600 focus:outline-none"
                            >
                                <i x-show="!showPassword" data-lucide="eye" class="h-5 w-5"></i>
                                <i x-show="showPassword" data-lucide="eye-off" class="h-5 w-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Password Requirements Card -->
                <div class="bg-blue-50 rounded-lg p-5 mb-6 border border-blue-100">
                    <h4 class="text-blue-800 font-medium mb-2 flex items-center">
                        <i data-lucide="shield" class="h-5 w-5 mr-2 text-blue-600"></i>
                        Password Requirements
                    </h4>
                    <p class="text-sm text-blue-600 mb-3">Ensure that these requirements are met:</p>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm text-blue-700">
                            <div class="h-5 w-5 mr-3 flex items-center justify-center">
                                <div class="h-2 w-2 bg-blue-500 rounded-full"></div>
                            </div>
                            Minimum 8 characters long - the more, the better
                        </li>
                        <li class="flex items-center text-sm text-blue-700">
                            <div class="h-5 w-5 mr-3 flex items-center justify-center">
                                <div class="h-2 w-2 bg-blue-500 rounded-full"></div>
                            </div>
                            At least one lowercase character
                        </li>
                        <li class="flex items-center text-sm text-blue-700">
                            <div class="h-5 w-5 mr-3 flex items-center justify-center">
                                <div class="h-2 w-2 bg-blue-500 rounded-full"></div>
                            </div>
                            At least one uppercase character
                        </li>
                        <li class="flex items-center text-sm text-blue-700">
                            <div class="h-5 w-5 mr-3 flex items-center justify-center">
                                <div class="h-2 w-2 bg-blue-500 rounded-full"></div>
                            </div>
                            At least one number
                        </li>
                    </ul>
                </div>
                
                <!-- Security Notice -->
                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Security Reminder</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>
                                    After changing your password, you'll be required to log in again with your new credentials.
                                    Make sure to remember your new password or store it in a secure password manager.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit"
                        class="w-full md:w-auto px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors flex items-center justify-center"
                    >
                        <i data-lucide="save" class="h-5 w-5 mr-2"></i>
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();
    });
</script>
@endpush

@endsection