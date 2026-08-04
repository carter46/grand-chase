@extends('layouts.guest2')

@section('title', 'Reset your password')
@section('content')

<div class="flex flex-col lg:flex-row min-h-screen">
    <!-- Left Side - Branding & Illustration (Desktop Only) -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-600 to-primary-800 relative overflow-hidden">
        <!-- Animated Shapes -->
        <div class="absolute inset-0 overflow-hidden opacity-10">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-white rounded-full mix-blend-overlay floating-slow"></div>
            <div class="absolute bottom-1/3 right-1/4 w-96 h-96 bg-white rounded-full mix-blend-overlay floating"></div>
            <div class="absolute top-2/3 left-1/3 w-40 h-40 bg-white rounded-full mix-blend-overlay floating-slower"></div>
            
            <!-- Grid pattern -->
            <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 20px 20px;"></div>
        </div>
        
        <!-- Content -->
        <div class="relative flex flex-col justify-center items-center w-full h-full text-white p-12 z-10">
            <!-- Logo -->
            <a href="/" class="mb-6">
                <img src="{{ asset('storage/app/public/' . $settings->logo) }}" alt="Logo" class="h-16 filter brightness-0 invert">
            </a>
            
            <!-- Title -->
            <h1 class="text-4xl font-extrabold mb-6 text-center">Account Recovery</h1>
            
            <!-- Description -->
            <p class="text-xl mb-8 max-w-md text-center text-white/80">
                Reset your password to regain access to your {{ $settings->site_name }} account securely.
            </p>
            
            <!-- Features -->
            <div class="grid grid-cols-2 gap-6 w-full max-w-md">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="shield-check" class="h-5 w-5"></i>
                    </div>
                    <span>Secure Recovery</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="key" class="h-5 w-5"></i>
                    </div>
                    <span>Strong Password</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="lock" class="h-5 w-5"></i>
                    </div>
                    <span>Data Protection</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="send" class="h-5 w-5"></i>
                    </div>
                    <span>Instant Access</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Reset Password Form -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 lg:p-12">
        <div class="w-full max-w-md">
            <!-- Mobile Logo -->
            <div class="lg:hidden text-center mb-8">
                <a href="/">
                    <img src="{{ asset('storage/app/public/' . $settings->logo) }}" alt="Logo" class="h-12 mx-auto">
                </a>
            </div>
            
            <!-- Alerts -->
            @if(Session::has('message'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p>{{ Session::get('message') }}</p>
                <button type="button" class="float-right font-bold" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            
            @if (session('status'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                <p>{{ session('status') }}</p>
                <button type="button" class="float-right font-bold" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            
            @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- Reset Password Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Card Header -->
                <div class="px-8 pt-8 pb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Create New Password</h2>
                    <p class="mt-2 text-sm text-gray-600">Enter your email and set a new password for your {{ $settings->site_name }} account.</p>
                </div>
                
                <!-- Reset Password Form -->
                <div class="px-8 pb-8">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        
                        <!-- Email -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <div class="input-wrapper">
                                <div class="relative">
                                    <div class="input-icon">
                                        <i data-lucide="mail" class="h-5 w-5"></i>
                                    </div>
                                    <input 
                                        id="email"
                                        type="email" 
                                        name="email" 
                                        value="{{ $email ?? old('email') }}"
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                        placeholder="name@email.com"
                                        required 
                                        autocomplete="email">
                                </div>
                            </div>
                            @if ($errors->has('email'))
                                <p class="mt-2 text-sm text-red-600">{{ $errors->first('email') }}</p>
                            @endif
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <div class="input-wrapper">
                                <div class="relative">
                                    <div class="input-icon">
                                        <i data-lucide="lock" class="h-5 w-5"></i>
                                    </div>
                                    <input 
                                        id="password"
                                        type="password" 
                                        name="password"
                                        class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                        placeholder="••••••••"
                                        required
                                        autocomplete="new-password">
                                    <button 
                                        type="button"
                                        onclick="togglePasswordVisibility('password', 'show-password', 'hide-password')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 input-toggle">
                                        <i data-lucide="eye" id="show-password" class="h-5 w-5 text-gray-400"></i>
                                        <i data-lucide="eye-off" id="hide-password" class="h-5 w-5 text-gray-400 hidden"></i>
                                    </button>
                                </div>
                            </div>
                            @if ($errors->has('password'))
                                <p class="mt-2 text-sm text-red-600">{{ $errors->first('password') }}</p>
                            @endif
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <div class="input-wrapper">
                                <div class="relative">
                                    <div class="input-icon">
                                        <i data-lucide="check-circle" class="h-5 w-5"></i>
                                    </div>
                                    <input 
                                        id="password_confirmation"
                                        type="password" 
                                        name="password_confirmation"
                                        class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                        placeholder="••••••••"
                                        required
                                        autocomplete="new-password">
                                    <button 
                                        type="button"
                                        onclick="togglePasswordVisibility('password_confirmation', 'show-password-confirm', 'hide-password-confirm')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 input-toggle">
                                        <i data-lucide="eye" id="show-password-confirm" class="h-5 w-5 text-gray-400"></i>
                                        <i data-lucide="eye-off" id="hide-password-confirm" class="h-5 w-5 text-gray-400 hidden"></i>
                                    </button>
                                </div>
                            </div>
                            @if ($errors->has('password_confirmation'))
                                <p class="mt-2 text-sm text-red-600">{{ $errors->first('password_confirmation') }}</p>
                            @endif
                        </div>
                        
                        <!-- Button -->
                        <div class="mb-6">
                            <button 
                                type="submit"
                                class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg shadow transition duration-150 ease-in-out flex items-center justify-center">
                                <i data-lucide="refresh-cw" class="h-5 w-5 mr-2"></i>
                                Reset Password
                            </button>
                        </div>
                        
                        <!-- Back to Login -->
                        <div>
                            <a 
                                href="{{ route('login') }}"
                                class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition duration-150 ease-in-out flex items-center justify-center">
                                <i data-lucide="arrow-left" class="h-5 w-5 mr-2"></i>
                                Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    &copy; Copyright {{date('Y')}} {{$settings->site_name}} &nbsp; All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function togglePasswordVisibility(inputId, showIconId, hideIconId) {
        const passwordInput = document.getElementById(inputId);
        const showPasswordIcon = document.getElementById(showIconId);
        const hidePasswordIcon = document.getElementById(hideIconId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            showPasswordIcon.classList.add('hidden');
            hidePasswordIcon.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            showPasswordIcon.classList.remove('hidden');
            hidePasswordIcon.classList.add('hidden');
        }
    }
</script>
@endsection