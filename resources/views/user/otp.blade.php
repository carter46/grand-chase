@extends('layouts.dash2')

@section('title', 'OTP Verification')
@section('content')

<div x-data="{ 
    otp: '',
    isSubmitting: false,
    countdown: 30,
    canResend: false,
    
    startCountdown() {
        this.countdown = 30;
        this.canResend = false;
        
        const timer = setInterval(() => {
            this.countdown--;
            
            if (this.countdown <= 0) {
                clearInterval(timer);
                this.canResend = true;
            }
        }, 1000);
    }
}" x-init="startCountdown()">
    <!-- Alerts -->
    <x-danger-alert />
    <x-success-alert />

    <!-- Container -->
    <div class="max-w-md mx-auto my-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6 md:p-8">
            <!-- OTP Header -->
            <div class="text-center mb-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary-100 mb-4">
                    <i data-lucide="lock" class="h-8 w-8 text-primary-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Enter OTP</h2>
                <p class="mt-1 text-sm text-gray-500">Input the OTP we sent to</p>
                <p class="font-medium text-gray-700">{{ Auth::user()->email }}</p>
            </div>

            <!-- OTP Form -->
            <form action="{{ route('codecomfirm') }}" method="post" @submit="isSubmitting = true">
                @csrf
                
                <!-- OTP Input Field -->
                <div class="mb-6">
                    <label for="otp_input" class="block text-sm font-medium text-gray-700 mb-1">OTP Code</label>
                    <div class="relative rounded-lg shadow-sm">
                        <input 
                            type="text" 
                            name="otp" 
                            id="otp_input" 
                            x-model="otp"
                            class="block w-full px-4 py-3 text-center border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-xl font-bold tracking-widest"
                            placeholder="Enter OTP code"
                            required
                            autocomplete="one-time-code"
                            maxlength="10"
                        />
                    </div>
                </div>
                
                <!-- Resend OTP Link -->
                <div class="text-center mb-6">
                    <template x-if="!canResend">
                        <p class="text-sm text-gray-500">
                            Resend code in <span class="font-medium text-primary-600" x-text="countdown"></span> seconds
                        </p>
                    </template>
                    
                    <template x-if="canResend">
                        <a href="{{ route('getotp') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                            Resend verification code
                        </a>
                    </template>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:space-x-4">
                    <button 
                        type="submit" 
                        class="w-full mb-3 sm:mb-0 inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                        :disabled="isSubmitting"
                    >
                        <i data-lucide="check-circle" class="h-5 w-5 mr-2" :class="{ 'animate-pulse': isSubmitting }"></i>
                        <span x-text="isSubmitting ? 'Verifying...' : 'Confirm OTP'"></span>
                    </button>
                    <a 
                        href="{{ route('dashboard') }}" 
                        class="w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                    >
                        <i data-lucide="arrow-left" class="h-5 w-5 mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Security Tips -->
        <div class="mt-6 p-4 border border-gray-200 rounded-lg bg-white">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-0.5">
                    <i data-lucide="shield" class="h-5 w-5 text-primary-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-900">Security Notice</h3>
                    <p class="text-xs text-gray-500 mt-1">Never share your OTP with anyone, including bank staff. This code is only used to verify your transaction.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();
    });
</script>
@endpush

@endsection