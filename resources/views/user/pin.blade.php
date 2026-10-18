@extends('layouts.auth')
@section('title', 'Login Verification')
@section('content')

<div class="flex items-center justify-center min-h-screen w-full bg-gradient-to-br from-gray-50 to-gray-100 p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden">

        <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 opacity-95"></div>
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden">
                <div class="absolute -top-20 -left-20 w-64 h-64 rounded-full bg-white opacity-10"></div>
                <div class="absolute bottom-0 right-0 w-40 h-40 rounded-full bg-white opacity-10"></div>
            </div>
            <div class="relative py-10 px-6 flex flex-col items-center">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mb-6 shadow-lg">
                    <i data-lucide="mail-check" class="h-10 w-10 text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-white text-center mb-1">Verify It's You</h1>
                <p class="text-white/90 text-center max-w-xs">
                    We sent a 6-digit code to <span class="font-semibold">{{ $maskedEmail }}</span>
                </p>
            </div>
        </div>

        <div class="flex flex-col items-center pt-8 pb-4 px-6">
            <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-white shadow-lg">
                <img src="{{ profile_photo_url(Auth::user()->profile_photo_path, Auth::user()->name) }}"
                    alt="{{ Auth::user()->name }}"
                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF';"
                    class="w-full h-full object-cover">
            </div>
            <h2 class="mt-4 text-xl font-bold text-gray-800 text-center">
                {{ Auth::user()->name }} {{ Auth::user()->middlename }} {{ Auth::user()->lastname }}
            </h2>

            @if (Auth::user()->status == 'blocked')
                <div class="mt-6 w-full bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <h3 class="text-sm font-medium text-red-800">Account Blocked</h3>
                    <p class="mt-1 text-sm text-red-700">Your {{ $settings->site_name }} account has been blocked for security reasons.</p>
                    <a href="mailto:{{ $settings->contact_email }}" class="inline-flex items-center mt-3 text-sm font-medium text-red-700 hover:text-red-600">
                        <i data-lucide="mail" class="h-4 w-4 mr-1"></i> Contact Support
                    </a>
                </div>
            @else
                @if ($sendFailed)
                    <div class="mt-6 w-full bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3">
                        We could not send your code right now. Tap "Resend code" or contact support.
                    </div>
                @endif
                @if (Session::has('message'))
                    <div class="mt-6 w-full bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3">
                        {{ Session::get('message') }}
                    </div>
                @endif
                @if (Session::has('success'))
                    <div class="mt-6 w-full bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg p-3">
                        {{ Session::get('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('pinstatus') }}" class="w-full mt-6" x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    <label for="login-otp" class="block text-sm font-medium text-gray-700 mb-2">Enter the code from your email</label>
                    <input id="login-otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                        autocomplete="one-time-code" required autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-center text-2xl tracking-[0.5em]"
                        placeholder="••••••">
                    <p class="mt-2 text-xs text-gray-500">The code expires in 10 minutes. Check your spam folder if you don't see it.</p>

                    <button type="submit" :disabled="submitting"
                        class="w-full mt-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg flex items-center justify-center shadow-md"
                        :class="{ 'opacity-50 cursor-not-allowed': submitting }">
                        <i data-lucide="shield-check" class="h-5 w-5 mr-2"></i>
                        <span x-text="submitting ? 'Verifying...' : 'Verify & Continue'">Verify &amp; Continue</span>
                    </button>
                </form>

                <div class="w-full mt-4 flex items-center justify-between text-sm">
                    <form method="POST" action="{{ route('pin.resend') }}">
                        @csrf
                        <button type="submit" class="text-primary-600 hover:text-primary-700 font-medium">Resend code</button>
                    </form>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700">Log out</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="px-6 pb-8 pt-4">
            <div class="flex items-center justify-center p-3 bg-gray-50 rounded-xl">
                <i data-lucide="lock" class="h-4 w-4 text-primary-500 mr-2"></i>
                <p class="text-xs text-gray-600">This extra step protects your account if someone else learns your password.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('login-otp');
        if (input) {
            input.addEventListener('input', function () {
                input.value = input.value.replace(/\D/g, '').slice(0, 6);
            });
        }
    });
</script>
@endsection
