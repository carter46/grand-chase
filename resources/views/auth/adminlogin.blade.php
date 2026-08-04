@extends('layouts.guest2')

@section('title', 'Administration Access')
@section('content')
<div class="flex items-center justify-center min-h-screen p-4 bg-gray-50">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Logo and Header -->
            <div class="p-6 bg-primary-600 text-white text-center">
                <a href="/" class="inline-block mb-4">
                    <img src="{{ public_storage_url($settings->logo) }}" alt="Logo" class="h-16 mx-auto">
                </a>
                <h1 class="text-2xl font-bold">Administration Portal</h1>
                <p class="text-sm text-gray-100 mt-1">Secure access to management dashboard</p>
            </div>
            
            <!-- Form Section -->
            <div class="p-6">
                @if (Session::has('status'))
                    <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-700">
                        {{ session('status') }}
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-700">
                        <ul class="list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('adminlogin') }}" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i data-lucide="mail" class="w-5 h-5"></i>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" 
                                class="pl-10 w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 transition-all duration-300" 
                                placeholder="administrator@example.com" autocomplete="email" required>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <a href="{{ route('admin.forgetpassword') }}" class="text-sm text-primary-600 hover:text-primary-500 font-medium transition-colors">
                                Forgot password?
                            </a>
                        </div>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i data-lucide="lock" class="w-5 h-5"></i>
                            </span>
                            <input type="password" id="password" name="password" 
                                class="pl-10 w-full h-12 px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 transition-all duration-300" 
                                placeholder="••••••••" autocomplete="current-password" required>
                        </div>
                    </div>
                    
                    <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300">
                        Sign in
                    </button>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="p-6 bg-gray-50 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} {{ $settings->site_name }}. All Rights Reserved.
                </p>
            </div>
        </div>
        
        <!-- Security Note -->
        <div class="mt-6 flex items-center justify-center">
            <div class="flex items-center text-gray-500 text-sm">
                <i data-lucide="shield" class="w-4 h-4 mr-2 text-primary-500"></i>
                <span>Secure Banking Administration Portal</span>
            </div>
        </div>
    </div>
</div>
@endsection