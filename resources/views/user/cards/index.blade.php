@extends('layouts.dash2')
@section('title', 'Cards')

@section('content')
<!-- Breadcrumbs + Page Title -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2 text-gray-400"></i>
                <span class="text-sm font-medium text-gray-700">Cards</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Virtual Cards</h1>
        </div>
        <a href="{{ route('cards.apply') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <i data-lucide="plus" class="h-4 w-4 mr-2"></i> Apply for Card
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                    <i data-lucide="credit-card" class="h-6 w-6 text-gray-900"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate">Active Cards</p>
                    <h3 class="text-lg font-semibold text-gray-600">{{ $activeCards }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i data-lucide="hourglass" class="h-6 w-6 text-blue-600"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate">Pending Applications</p>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $pendingCards }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i data-lucide="wallet" class="h-6 w-6 text-green-600"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate">Total Card Balance</p>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $settings->currency }} {{ number_format($totalBalance, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Info Box -->
<div class="bg-primary-700 rounded-xl overflow-hidden shadow-md mb-6">
    <div class="md:flex">
        <div class="p-6 md:flex-1">
            <h2 class="text-white text-xl font-bold mb-2">Virtual Cards Made Easy</h2>
            <p class="text-gray-100 mb-4">Create virtual cards for secure online payments, subscription management, and more. Our virtual cards offer enhanced security and control over your spending.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-white bg-opacity-10 rounded-md p-2">
                        <i data-lucide="shield" class="h-5 w-5 text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white text-sm font-medium">Secure Payments</h3>
                        <p class="text-gray-100 text-xs">Protect your main account with separate virtual cards</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-white bg-opacity-10 rounded-md p-2">
                        <i data-lucide="globe" class="h-5 w-5 text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white text-sm font-medium">Global Acceptance</h3>
                        <p class="text-gray-100 text-xs">Use anywhere major cards are accepted online</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-white bg-opacity-10 rounded-md p-2">
                        <i data-lucide="sliders" class="h-5 w-5 text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white text-sm font-medium">Spending Controls</h3>
                        <p class="text-gray-100 text-xs">Set limits and monitor transactions in real-time</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 bg-white bg-opacity-10 rounded-md p-2">
                        <i data-lucide="zap" class="h-5 w-5 text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white text-sm font-medium">Instant Issuance</h3>
                        <p class="text-gray-100 text-xs">Create and use cards within minutes</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <a href="{{ route('cards.apply') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-primary-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white focus:ring-offset-primary-600">
                    Apply Now
                </a>
            </div>
        </div>
        <div class="hidden md:flex md:items-center md:justify-center md:w-1/3 bg-primary-700 bg-opacity-50 p-6">
            <div class="relative w-48 h-32">
                <div class="absolute w-full h-full transform rotate-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 shadow-lg"></div>
                <div class="absolute w-full h-full rounded-xl bg-gradient-to-r from-primary-800 to-primary-600 shadow-lg">
                    <div class="p-4 h-full flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div class="text-xs font-mono text-white opacity-75">Virtual Card</div>
                            <i data-lucide="wifi" class="h-4 w-4 text-white opacity-75 transform rotate-90"></i>
                        </div>
                        <div class="text-xs font-mono text-white mt-4">•••• •••• •••• 1234</div>
                        <div class="flex justify-between items-end">
                            <div>
                                <div class="text-xs font-mono text-white opacity-75">VALID THRU</div>
                                <div class="text-xs font-mono text-white">12/25</div>
                            </div>
                            <div class="h-8 w-8">
                                <i data-lucide="credit-card" class="h-8 w-8 text-white opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Card Listings -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 mb-8">
    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
        <h2 class="text-lg font-medium text-gray-900">Your Cards</h2>
        <a href="{{ route('cards.apply') }}" class="text-sm text-primary-600 hover:text-primary-800 flex items-center">
            <i data-lucide="plus-circle" class="h-4 w-4 mr-1"></i> New Card
        </a>
    </div>
    
    @if(count($cards) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            @foreach($cards as $card)
                <div class="bg-white rounded-xl border border-gray-100 hover:shadow-lg transition-all duration-300 group overflow-hidden">
                    <!-- Card Header with Status -->
                    <div class="px-4 pt-4 pb-2 flex justify-between items-center">
                        <div>
                            @if($card->status == 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i> Active
                                </span>
                            @elseif($card->status == 'pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i data-lucide="clock" class="h-3 w-3 mr-1"></i> Pending
                                </span>
                            @elseif($card->status == 'inactive')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i data-lucide="pause" class="h-3 w-3 mr-1"></i> Inactive
                                </span>
                            @elseif($card->status == 'blocked')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i data-lucide="lock" class="h-3 w-3 mr-1"></i> Blocked
                                </span>
                            @endif
                        </div>
                        
                        <div class="text-xs text-gray-500">
                            {{ ucfirst($card->card_level) }}
                        </div>
                    </div>
                    
                    <!-- Card Representation -->
                    <div class="px-4 py-3">
                        <div class="w-full h-44 backface-hidden relative overflow-hidden">
                            <!-- Card Background -->
                            @if($card->card_type == 'visa')
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-blue-800 via-blue-600 to-blue-500"></div>
                            @elseif($card->card_type == 'mastercard')
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-red-700 via-orange-600 to-orange-500"></div>
                            @elseif($card->card_type == 'american_express')
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-gray-800 via-gray-700 to-gray-600"></div>
                            @else
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-gray-800 via-gray-700 to-gray-600"></div>
                            @endif
                            
                            <!-- Card Content -->
                            <div class="absolute inset-0 p-4 flex flex-col justify-between">
                                <!-- Top Section -->
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="text-white font-semibold text-sm sm:text-base tracking-wider">{{ $settings->site_name }}</div>
                                        <div class="text-white/70 text-xs">Virtual Banking</div>
                                    </div>
                                    
                                    <!-- Card Type Logo -->
                                    <div>
                                        @if($card->card_type == 'visa')
                                            <svg class="h-6 w-12 text-white" viewBox="0 0 1000 324" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M433.334 318.516H348.568L403.315 5.01855H488.081L433.334 318.516Z" fill="white"/>
                                                <path d="M727.98 15.9043C712.039 9.56622 686.227 2.51855 653.355 2.51855C574.054 2.51855 519.055 45.5277 518.802 106.826C518.306 152.343 558.126 177.912 587.98 193.316C618.34 209.233 629.315 219.335 629.315 233.014C629.061 254.008 603.493 263.591 579.702 263.591C547.839 263.591 530.7 258.287 505.123 245.892L493.902 240.335L482.166 313.221C501.069 323.07 536.096 331.667 572.643 332.163C657.14 332.163 711.128 289.897 711.895 224.144C712.394 188.508 689.871 160.445 643.015 136.91C615.2 121.744 598.306 111.638 598.554 97.2356C598.554 84.5987 613.224 70.9167 648.747 70.9167C677.823 70.4206 699.053 77.7188 715.707 85.5144L726.681 90.8186L738.417 19.9214L727.98 15.9043Z" fill="white"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M841.897 5.01855H773.691C755.782 5.01855 742.397 10.0624 734.862 30.0238L630.236 318.516H715.464C715.464 318.516 729.143 280.806 732.429 272.002C742.901 272.002 822.706 272.002 835.844 272.002C838.373 283.215 845.93 318.516 845.93 318.516H922.667L841.897 5.01855ZM762.22 208.49C769.018 189.752 796.85 116.686 796.85 116.686C796.602 117.174 803.16 99.19 806.699 88.0899L812.02 114.188C812.02 114.188 829.775 192.026 833.061 208.49H762.22Z" fill="white"/>
                                                <path d="M251.994 5.01855L171.471 219.335L162.137 174.313C146.216 129.054 102.175 80.0894 52.8662 56.8035L126.462 318.02H212.215L336.832 5.01855H251.994Z" fill="white"/>
                                                <path d="M127.963 5.01855H0.66211L0.167969 10.3101C98.1497 31.7971 163.301 76.5646 190.132 129.298L162.633 13.5747C157.329 -0.60098 144.316 5.51465 127.963 5.01855Z" fill="white"/>
                                            </svg>
                                        @elseif($card->card_type == 'mastercard')
                                            <svg class="h-8 w-12" viewBox="0 0 131.39 86.9" xmlns="http://www.w3.org/2000/svg"><g opacity=".9"><path d="M48.37 15.14h34.66v56.61H48.37z" fill="#fff"/><path d="M52.37 43.45a35.94 35.94 0 0113.75-28.3 36 36 0 100 56.61 35.94 35.94 0 01-13.75-28.31z" fill="#fff"/><path d="M120.39 65.54V64.5h.48v-.24h-1.19v.24h.47v1.04zm2.31 0v-1.29h-.36l-.42.91-.42-.91h-.36v1.29h.26V64.9l.39.89h.27l.39-.89v.89z" fill="#fff"/><path d="M123.94 43.45a36 36 0 01-58 28.3 36 36 0 000-56.61 36 36 0 0158 28.3z" fill="#fff"/></g></svg>
                                        @elseif($card->card_type == 'american_express')
                                             <img src="{{ asset('dash/images/cards/amex.png') }}" class="h-8 w-16" alt="American Express">
                                        @else
                                            <i data-lucide="credit-card" class="h-8 w-8 text-white"></i>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Middle Section - Card Number -->
                                <div class="font-mono text-base text-white tracking-widest drop-shadow-md">
                                    •••• •••• •••• {{ $card->last_four ?? '****' }}
                                </div>
                                
                                <!-- Bottom Section -->
                                <div class="flex justify-between items-end">
                                    <div>
                                        <div class="text-xs uppercase text-white/70 mb-1">Card Holder</div>
                                        <div class="text-white font-medium text-sm truncate max-w-[150px]">
                                            {{ $card->card_holder_name }}
                                        </div>
                                    </div>
                                    
                                    <div class="text-right">
                                        <div class="text-xs uppercase text-white/70 mb-1">Valid Thru</div>
                                        <div class="text-white font-medium text-sm">
                                            {{ $card->expiry_month }}/{{ substr($card->expiry_year, -2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Details -->
                    <div class="px-4 py-3 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $card->card_type)) }}</div>
                            @if($card->status == 'active')
                                <div class="text-sm font-semibold text-gray-900">{{ $card->currency }} {{ number_format($card->balance, 2) }}</div>
                            @endif
                        </div>
                        
                        <!-- Action Button -->
                        <a href="{{ route('cards.view', $card) }}" class="block w-full mt-2 text-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                <i data-lucide="credit-card" class="h-6 w-6 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No cards yet</h3>
            <p class="mt-1 text-sm text-gray-500 max-w-2xl mx-auto">
                You haven't applied for any virtual cards yet. Apply for a new card to get started with secure online payments.
            </p>
            <div class="mt-6">
                <a href="{{ route('cards.apply') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Apply for Card
                </a>
            </div>
        </div>
    @endif
</div>

<!-- How It Works -->
<div class="mb-8">
    <h2 class="text-xl font-bold text-gray-900 mb-6">How Virtual Cards Work</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 mb-4">
                <i data-lucide="file-text" class="h-6 w-6 text-gray-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">1. Apply</h3>
            <p class="text-gray-600">Complete the application form for your virtual card. Select your preferred card type and set your spending limits.</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 mb-4">
                <i data-lucide="check-circle" class="h-6 w-6 text-gray-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">2. Activate</h3>
            <p class="text-gray-600">Once approved, your virtual card will be ready to use. View the card details and activate it from your dashboard.</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 mb-4">
                <i data-lucide="shopping-cart" class="h-6 w-6 text-gray-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">3. Use</h3>
            <p class="text-gray-600">Use your virtual card for online transactions anywhere major credit cards are accepted. Monitor transactions in real-time.</p>
        </div>
    </div>
</div>

<!-- FAQ -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 mb-8">
    <div class="border-b border-gray-200 px-6 py-4">
        <h2 class="text-lg font-medium text-gray-900">Frequently Asked Questions</h2>
    </div>
    
    <div class="p-6">
        <dl class="space-y-6">
            <div>
                <dt class="text-base font-medium text-gray-900">What is a virtual card?</dt>
                <dd class="mt-2 text-sm text-gray-600">A virtual card is a digital payment card that can be used for online transactions. It works just like a physical card but exists only in digital form, providing enhanced security for online purchases.</dd>
            </div>
            
            <div>
                <dt class="text-base font-medium text-gray-900">How secure are virtual cards?</dt>
                <dd class="mt-2 text-sm text-gray-600">Virtual cards offer additional security as they're separate from your primary account. You can create cards with specific spending limits and even create single-use cards for enhanced protection against fraud.</dd>
            </div>
            
            <div>
                <dt class="text-base font-medium text-gray-900">Can I have multiple virtual cards?</dt>
                <dd class="mt-2 text-sm text-gray-600">Yes, you can apply for multiple virtual cards for different purposes - such as one for subscriptions, another for shopping, etc. Each card can have its own limits and settings.</dd>
            </div>
            
            <div>
                <dt class="text-base font-medium text-gray-900">How long does it take to get a virtual card?</dt>
                <dd class="mt-2 text-sm text-gray-600">Virtual cards are typically issued within minutes after approval. Once approved, you can immediately view and use the card details for online transactions.</dd>
            </div>
        </dl>
    </div>
</div>
<style>
/* Card hover effects */
.group-hover\:scale-\[1\.02\] {
    transform: scale(1.02);
}

/* Ensure rounded corners everywhere */
.rounded-xl {
    border-radius: 0.75rem;
}

/* Shadow control */
.shadow-none {
    box-shadow: none !important;
}

.group-hover\:shadow-md {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .grid {
        gap: 1rem;
    }
}
</style>
@endsection 