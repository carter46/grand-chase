@extends('layouts.dash2')
@section('title', $title)
@section('content')

<div class="container mx-auto px-4 py-6 max-w-5xl">
    <!-- Alerts -->
    <x-danger-alert />
    <x-success-alert />
    <x-error-alert />

    <!-- Page Header with Breadcrumbs -->
    <div class="flex flex-col mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Support Center</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <span class="font-medium text-gray-700">Support</span>
            </div>
        </div>
    </div>

    <!-- Support Header Card -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="px-6 py-8 text-center">
            <div class="flex justify-center mb-4">
                <div class="h-16 w-16 rounded-full bg-white/20 flex items-center justify-center">
                    <i data-lucide="headphones" class="h-8 w-8 text-white"></i>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">How Can We Help You?</h2>
            <p class="text-white/90">Browse our frequently asked questions below or chat with us for instant support</p>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                <i data-lucide="help-circle" class="h-5 w-5 mr-2 text-primary-600"></i>
                Frequently Asked Questions
            </h3>
            <p class="text-sm text-gray-500 mt-1">Find answers to common questions about {{ $settings->site_name }}</p>
        </div>

        <div class="p-6" x-data="{ openFaq: null }">
            <div class="space-y-3">
                
                <!-- FAQ 1 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">How do I create an account?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 1 }"></i>
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">To create an account, click on the "Sign Up" button on our homepage, fill in your personal information including name, email, phone number, and create a secure password. You'll receive a verification email to activate your account.</p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">How do I deposit funds into my account?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 2 }"></i>
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">Navigate to the "Deposit" section in your dashboard, select your preferred payment method (bank transfer, card, or cryptocurrency), enter the amount you wish to deposit, and follow the on-screen instructions. Deposits are typically processed within 24 hours.</p>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">What are the withdrawal methods available?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 3 }"></i>
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">You can withdraw funds via bank transfer, cryptocurrency wallet, or payment processors. Go to the "Withdraw" section, select your preferred method, enter the amount and destination details, then submit your request. Withdrawals are processed within 1-3 business days.</p>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">How long does it take to process a withdrawal?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 4 }"></i>
                    </button>
                    <div x-show="openFaq === 4" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">Standard withdrawal requests are processed within 1-3 business days. However, processing time may vary depending on your withdrawal method and bank processing times. Cryptocurrency withdrawals are typically faster, often completed within 24 hours.</p>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">Is my account secure?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 5 }"></i>
                    </button>
                    <div x-show="openFaq === 5" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">Yes, we use industry-standard security measures including SSL encryption, two-factor authentication (2FA), and secure data storage. We recommend enabling 2FA on your account and using a strong, unique password for maximum security.</p>
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 6 ? null : 6" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">How do I enable two-factor authentication?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 6 }"></i>
                    </button>
                    <div x-show="openFaq === 6" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">Go to your Profile settings, find the "Security" section, and click on "Enable Two-Factor Authentication." Follow the prompts to scan the QR code with your authenticator app (such as Google Authenticator or Authy) and enter the verification code to activate 2FA.</p>
                    </div>
                </div>

                <!-- FAQ 7 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 7 ? null : 7" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">Can I transfer funds to other users?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 7 }"></i>
                    </button>
                    <div x-show="openFaq === 7" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">Yes, you can transfer funds to other {{ $settings->site_name }} users or to external bank accounts. Navigate to the "Transfer" section, select the transfer type (internal or external), enter recipient details and amount, then confirm your transaction.</p>
                    </div>
                </div>

                <!-- FAQ 8 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 8 ? null : 8" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">What should I do if I forget my password?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 8 }"></i>
                    </button>
                    <div x-show="openFaq === 8" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">Click on "Forgot Password" on the login page, enter your registered email address, and you'll receive a password reset link. Follow the link to create a new password. Make sure to choose a strong password that you haven't used before.</p>
                    </div>
                </div>

                <!-- FAQ 9 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 9 ? null : 9" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">Are there any fees for deposits or withdrawals?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 9 }"></i>
                    </button>
                    <div x-show="openFaq === 9" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">{{ $settings->site_name }} does not charge fees for deposits. However, withdrawal fees may apply depending on your chosen method and amount. Please check the specific fee structure in the withdrawal section before submitting your request.</p>
                    </div>
                </div>

                <!-- FAQ 10 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 10 ? null : 10" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">How can I verify my account?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 10 }"></i>
                    </button>
                    <div x-show="openFaq === 10" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">Navigate to the "Verification" or "KYC" section in your profile. Upload the required documents including a valid ID card (passport, driver's license, or national ID) and proof of address (utility bill or bank statement). Verification typically takes 1-2 business days.</p>
                    </div>
                </div>

                <!-- FAQ 11 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 11 ? null : 11" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">Can I have multiple accounts?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 11 }"></i>
                    </button>
                    <div x-show="openFaq === 11" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">No, each user is allowed only one account per email address. Creating multiple accounts is against our terms of service and may result in account suspension. If you need to update your account information, please contact support.</p>
                    </div>
                </div>

                <!-- FAQ 12 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === 12 ? null : 12" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-medium text-gray-900 text-left">How do I contact customer support?</span>
                        <i data-lucide="chevron-down" class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': openFaq === 12 }"></i>
                    </button>
                    <div x-show="openFaq === 12" x-collapse class="px-5 py-4 bg-white border-t border-gray-200">
                        <p class="text-gray-600">You can contact our support team through the live chat widget on this page for instant assistance. Alternatively, you can email us at {{ $settings->contact_email ?? 'support@'.$settings->site_name }} and we'll respond within 24 hours.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Still Need Help Card -->
    <div class="bg-blue-50 rounded-xl border border-blue-100 overflow-hidden">
        <div class="p-6 text-center">
            <div class="flex justify-center mb-4">
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i data-lucide="message-circle" class="h-6 w-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Still Need Help?</h3>
            <p class="text-gray-600 mb-4">Can't find the answer you're looking for? Our support team is ready to assist you.</p>
            <p class="text-sm text-blue-700 font-medium">Use the live chat widget below to speak with a support agent instantly!</p>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Re-initialize when FAQ items are toggled
        document.addEventListener('click', function() {
            setTimeout(() => lucide.createIcons(), 100);
        });
    });
</script>
@endpush

@endsection
