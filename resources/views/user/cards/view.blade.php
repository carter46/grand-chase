@extends('layouts.dash2')
@section('title', 'Card Details')

@section('content')
<!-- Breadcrumbs + Page Title -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center flex-wrap">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2 text-gray-400"></i>
                <a href="{{ route('cards') }}" class="text-sm text-gray-500 hover:text-primary-600">Cards</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2 text-gray-400"></i>
                <span class="text-sm font-medium text-gray-700">Details</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Card Details</h1>
        </div>
        <a href="{{ route('cards') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 mt-4 sm:mt-0 w-fit sm:w-auto">
    <i data-lucide="arrow-left" class="h-4 w-4 mr-2"></i> Back to Cards
</a>
    </div>
</div>

<!-- Alert Messages -->
@if(session('message'))
    <div class="rounded-md {{ session('type') == 'success' ? 'bg-green-50 text-green-800 border border-green-100' : 'bg-red-50 text-red-800 border border-red-100' }} p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                @if(session('type') == 'success')
                    <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                @else
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-500"></i>
                @endif
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">{{ session('message') }}</p>
            </div>
        </div>
    </div>
@endif

<!-- Card Details Section -->
<div class="mb-6">
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="p-6">
            <!-- Card Status Badge and Action Buttons - Improved for Mobile -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
                <div>
                    @if($card->status == 'active')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i data-lucide="check-circle" class="h-4 w-4 mr-1.5"></i> Active
                        </span>
                    @elseif($card->status == 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i data-lucide="clock" class="h-4 w-4 mr-1.5"></i> Pending
                        </span>
                    @elseif($card->status == 'inactive')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <i data-lucide="pause" class="h-4 w-4 mr-1.5"></i> Inactive
                        </span>
                    @elseif($card->status == 'blocked')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i data-lucide="lock" class="h-4 w-4 mr-1.5"></i> Blocked
                        </span>
                    @elseif($card->status == 'rejected')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i data-lucide="x-circle" class="h-4 w-4 mr-1.5"></i> Rejected
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            {{ ucfirst($card->status) }}
                        </span>
                    @endif
                </div>
                
                <!-- Card action buttons - Fixed for mobile -->
                <div class="flex flex-wrap gap-2">
                    @if($card->status == 'active')
                        <form action="{{ route('cards.deactivate', $card) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-yellow-300 shadow-sm text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <i data-lucide="pause" class="h-4 w-4 mr-1.5"></i> Deactivate
                            </button>
                        </form>
                    @elseif($card->status == 'inactive')
                        <form action="{{ route('cards.activate', $card) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-green-300 shadow-sm text-sm leading-4 font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i data-lucide="play" class="h-4 w-4 mr-1.5"></i> Activate
                            </button>
                        </form>
                    @endif
                    
                    @if(in_array($card->status, ['active', 'inactive']))
                        <form action="{{ route('cards.block', $card) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to block this card? This action may be irreversible.')">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i data-lucide="lock" class="h-4 w-4 mr-1.5"></i> Block Card
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('cards.transactions', $card) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i data-lucide="list" class="h-4 w-4 mr-1.5"></i> Transactions
                    </a>
                </div>
            </div>
            
            <!-- ENHANCED 3D CARD WITH FLIP ANIMATION -->
            <div class="w-full max-w-md mx-auto mb-8">
                <div class="credit-card-container perspective-1000">
                    <div class="credit-card" id="creditCard">
                        <!-- Front of the card -->
                        <div class="credit-card-front absolute inset-0 flex flex-col justify-between p-6 backface-hidden">
                            @if($card->card_type == 'visa')
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-800 via-blue-600 to-blue-500 rounded-xl"></div>
                            @elseif($card->card_type == 'mastercard')
                                <div class="absolute inset-0 bg-gradient-to-br from-red-700 via-orange-600 to-orange-500 rounded-xl"></div>
                            @elseif($card->card_type == 'american_express')
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-800 via-gray-700 to-gray-600 rounded-xl"></div>
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-800 via-gray-700 to-gray-600 rounded-xl"></div>
                            @endif
                            
                            <!-- Card decorative elements -->
                            <div class="absolute inset-0 overflow-hidden rounded-xl">
                                <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-20 translate-x-20"></div>
                                <div class="absolute bottom-0 left-0 w-60 h-60 bg-white/5 rounded-full translate-y-20 -translate-x-20"></div>
                                <div class="absolute inset-0 backdrop-blur-sm bg-gradient-to-b from-transparent to-black/20"></div>
                            </div>
                            
                            <!-- Bank Logo -->
                            <div class="relative flex justify-between items-start">
                                <div>
                                    <div class="text-white font-semibold text-lg tracking-wider">{{ $settings->site_name }}</div>
                                    <div class="text-white/70 text-xs">Virtual Banking</div>
                                </div>
                                
                                <!-- Card Type Logo positioned at the top right -->
                                <div>
                                    @if($card->card_type == 'visa')
                                        <svg class="h-8 w-16 text-white" viewBox="0 0 1000 324" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M433.334 318.516H348.568L403.315 5.01855H488.081L433.334 318.516Z" fill="white"/>
                                            <path d="M727.98 15.9043C712.039 9.56622 686.227 2.51855 653.355 2.51855C574.054 2.51855 519.055 45.5277 518.802 106.826C518.306 152.343 558.126 177.912 587.98 193.316C618.34 209.233 629.315 219.335 629.315 233.014C629.061 254.008 603.493 263.591 579.702 263.591C547.839 263.591 530.7 258.287 505.123 245.892L493.902 240.335L482.166 313.221C501.069 323.07 536.096 331.667 572.643 332.163C657.14 332.163 711.128 289.897 711.895 224.144C712.394 188.508 689.871 160.445 643.015 136.91C615.2 121.744 598.306 111.638 598.554 97.2356C598.554 84.5987 613.224 70.9167 648.747 70.9167C677.823 70.4206 699.053 77.7188 715.707 85.5144L726.681 90.8186L738.417 19.9214L727.98 15.9043Z" fill="white"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M841.897 5.01855H773.691C755.782 5.01855 742.397 10.0624 734.862 30.0238L630.236 318.516H715.464C715.464 318.516 729.143 280.806 732.429 272.002C742.901 272.002 822.706 272.002 835.844 272.002C838.373 283.215 845.93 318.516 845.93 318.516H922.667L841.897 5.01855ZM762.22 208.49C769.018 189.752 796.85 116.686 796.85 116.686C796.602 117.174 803.16 99.19 806.699 88.0899L812.02 114.188C812.02 114.188 829.775 192.026 833.061 208.49H762.22Z" fill="white"/>
                                            <path d="M251.994 5.01855L171.471 219.335L162.137 174.313C146.216 129.054 102.175 80.0894 52.8662 56.8035L126.462 318.02H212.215L336.832 5.01855H251.994Z" fill="white"/>
                                            <path d="M127.963 5.01855H0.66211L0.167969 10.3101C98.1497 31.7971 163.301 76.5646 190.132 129.298L162.633 13.5747C157.329 -0.60098 144.316 5.51465 127.963 5.01855Z" fill="white"/>
                                        </svg>
                                    @elseif($card->card_type == 'mastercard')
                                        <svg class="h-10 w-16" viewBox="0 0 131.39 86.9" xmlns="http://www.w3.org/2000/svg"><g opacity=".9"><path d="M48.37 15.14h34.66v56.61H48.37z" fill="#fff"/><path d="M52.37 43.45a35.94 35.94 0 0113.75-28.3 36 36 0 100 56.61 35.94 35.94 0 01-13.75-28.31z" fill="#fff"/><path d="M120.39 65.54V64.5h.48v-.24h-1.19v.24h.47v1.04zm2.31 0v-1.29h-.36l-.42.91-.42-.91h-.36v1.29h.26V64.9l.39.89h.27l.39-.89v.89z" fill="#fff"/><path d="M123.94 43.45a36 36 0 01-58 28.3 36 36 0 000-56.61 36 36 0 0158 28.3z" fill="#fff"/></g></svg>
                                   @elseif($card->card_type == 'american_express')
                                    <img src="{{ asset('dash/images/cards/amex.png') }}" class="h-8 w-16" alt="American Express">
                                    @else
                                        <i data-lucide="credit-card" class="h-10 w-10 text-white"></i>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Smart Chip -->
                            <div class="relative mt-2">
                                <div class="w-12 h-10 rounded-md bg-gradient-to-br from-yellow-500 to-yellow-400 flex items-center justify-center overflow-hidden shadow-inner">
                                    <div class="w-full h-full grid grid-cols-2 grid-rows-3 gap-px p-1">
                                        <div class="bg-yellow-600/60 rounded-sm"></div>
                                        <div class="bg-yellow-600/60 rounded-sm"></div>
                                        <div class="bg-yellow-600/60 rounded-sm"></div>
                                        <div class="bg-yellow-600/60 rounded-sm"></div>
                                        <div class="bg-yellow-600/60 rounded-sm"></div>
                                        <div class="bg-yellow-600/60 rounded-sm"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Card Number -->
                            <div class="relative mt-2">
                                <div class="font-mono text-xl text-white tracking-widest drop-shadow-md flex flex-wrap">
                                    @if($card->status == 'active')
                                        <span id="maskedCardNumber">•••• •••• •••• {{ $card->last_four }}</span>
                                        @if(isset($card->card_number))
                                            <span id="fullCardNumber" class="hidden">{{ chunk_split($card->card_number, 4, ' ') }}</span>
                                        @endif
                                    @else
                                        <span>•••• •••• •••• ••••</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Cardholder Info & Expiry Date - Row format -->
                            <div class="relative mt-auto">
                                <div class="flex justify-between items-end">
                                    <div>
                                        <div class="text-xs uppercase text-white/70 mb-1">Card Holder</div>
                                        <div class="text-white font-medium text-sm sm:text-base truncate max-w-[150px]">
                                            {{ $card->card_holder_name }}
                                        </div>
                                    </div>
                                    
                                    <div class="text-right">
                                        <div class="text-xs uppercase text-white/70 mb-1">Valid Thru</div>
                                        <div class="text-white font-medium">
                                            @if($card->status == 'active')
                                                {{ sprintf('%02d', $card->expiry_month) }}/{{ substr($card->expiry_year, -2) }}
                                            @else
                                                --/--
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Back of the card -->
                        <div class="credit-card-back absolute inset-0 flex flex-col justify-between p-6 backface-hidden rotateY-180">
                            @if($card->card_type == 'visa')
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-700 via-blue-500 to-blue-400 rounded-xl"></div>
                            @elseif($card->card_type == 'mastercard')
                                <div class="absolute inset-0 bg-gradient-to-br from-red-600 via-orange-500 to-orange-400 rounded-xl"></div>
                            @elseif($card->card_type == 'american_express')
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 via-blue-400 to-indigo-400 rounded-xl"></div>
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-700 via-gray-600 to-gray-500 rounded-xl"></div>
                            @endif
                            
                            <!-- Card decorative elements -->
                            <div class="absolute inset-0 overflow-hidden rounded-xl">
                                <div class="absolute top-0 left-0 w-40 h-40 bg-white/10 rounded-full -translate-y-20 -translate-x-20"></div>
                                <div class="absolute bottom-0 right-0 w-60 h-60 bg-white/5 rounded-full translate-y-20 translate-x-20"></div>
                                <div class="absolute inset-0 backdrop-blur-sm bg-gradient-to-b from-transparent to-black/20"></div>
                            </div>
                            
                            <!-- Black magnetic strip -->
                            <div class="relative w-full h-12 bg-black/80 -mx-6 my-6"></div>
                            
                            <!-- CVV section -->
                            <div class="relative mt-4">
                                <div class="flex flex-col">
                                    <div class="bg-white/90 h-10 rounded px-2 py-1 flex items-center justify-end">
                                        @if($card->status == 'active')
                                            <div class="font-mono text-gray-800 text-right" id="cvvDisplay">
                                                @if(isset($card->cvv))
                                                    <span id="maskedCVVBack">•••</span>
                                                    <span id="fullCVVBack" class="hidden">{{ $card->cvv }}</span>
                                                @else
                                                    •••
                                                @endif
                                            </div>
                                        @else
                                            <div class="font-mono text-gray-500">•••</div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-white/80 mt-1 text-right">Security Code</div>
                                </div>
                            </div>
                            
                            <!-- Additional information -->
                            <div class="relative mt-auto">
                                <div class="text-xs text-white/70 space-y-1">
                                    <p>This card is property of {{ $settings->site_name }} and licensed for use by the authorized cardholder only.</p>
                                    <p>If found, please contact {{ $settings->site_name }} Virtual Banking.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card controls -->
                <div class="flex justify-center mt-4 space-x-4">
                    <button type="button" id="flipCardBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                        <i data-lucide="refresh-cw" class="h-4 w-4 mr-2"></i> Flip Card
                    </button>
                    
                    @if($card->status == 'active' && (isset($card->card_number) || isset($card->cvv)))
                        <button type="button" id="toggleDetailsBtn" class="inline-flex items-center px-4 py-2 border border-primary-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                            <i data-lucide="eye" class="h-4 w-4 mr-2"></i> <span id="toggleBtnText">Show Details</span>
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Card Details Grid - Improved spacing and responsiveness -->
<!-- Card Details Grid - Fixed toggle functionality -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 transition-all duration-200 hover:shadow-md">
        <div class="text-xs font-medium text-gray-500 uppercase mb-1">Card Number</div>
        @if($card->status == 'active')
            <div class="font-mono text-lg text-gray-800 flex items-center">
                <div class="flex-grow">
                    <span id="maskedCardNumberGrid">•••• •••• •••• {{ $card->last_four }}</span>
                    <span id="fullCardNumberGrid" class="hidden">
                        {{ chunk_split($card->card_number, 4, ' ') }}
                    </span>
                </div>
                @if(isset($card->card_number))
                    <button type="button" class="ml-2 text-primary-600 hover:text-primary-800 toggle-visibility flex-shrink-0" data-target="cardNumberGrid">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                    </button>
                @endif
            </div>
        @else
            <div class="font-mono text-lg text-gray-500">•••• •••• •••• ••••</div>
        @endif
    </div>
    
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 transition-all duration-200 hover:shadow-md">
        <div class="text-xs font-medium text-gray-500 uppercase mb-1">Expiration Date</div>
        @if($card->status == 'active')
            <div class="text-lg text-gray-800">{{ sprintf('%02d', $card->expiry_month) }}/{{ $card->expiry_year }}</div>
        @else
            <div class="text-lg text-gray-500">--/----</div>
        @endif
    </div>
    
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 transition-all duration-200 hover:shadow-md">
        <div class="text-xs font-medium text-gray-500 uppercase mb-1">CVV</div>
        @if($card->status == 'active')
            <div class="font-mono text-lg text-gray-800 flex items-center">
                <div class="flex-grow">
                    <span id="maskedCVVGrid">•••</span>
                    <span id="fullCVVGrid" class="hidden">
                        {{ $card->cvv }}
                    </span>
                </div>
                @if(isset($card->cvv))
                    <button type="button" class="ml-2 text-primary-600 hover:text-primary-800 toggle-visibility flex-shrink-0" data-target="cvvGrid">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                    </button>
                @endif
            </div>
        @else
            <div class="font-mono text-lg text-gray-500">•••</div>
        @endif
    </div>
    
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 transition-all duration-200 hover:shadow-md">
        <div class="text-xs font-medium text-gray-500 uppercase mb-1">Card Type</div>
        <div class="text-lg text-gray-800">{{ ucfirst(str_replace('_', ' ', $card->card_type)) }}</div>
    </div>
    
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 transition-all duration-200 hover:shadow-md">
        <div class="text-xs font-medium text-gray-500 uppercase mb-1">Card Level</div>
        <div class="text-lg text-gray-800">{{ ucfirst($card->card_level) }}</div>
    </div>
    
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 transition-all duration-200 hover:shadow-md">
        <div class="text-xs font-medium text-gray-500 uppercase mb-1">Currency</div>
        <div class="text-lg text-gray-800">{{ $card->currency }}</div>
    </div>
    
    @if($card->status == 'active')
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 transition-all duration-200 hover:shadow-md">
            <div class="text-xs font-medium text-gray-500 uppercase mb-1">Current Balance</div>
            <div class="text-lg text-gray-800">{{ $settings->currency }}{{ number_format($card->balance, 2) }}</div>
        </div>
    @endif
</div>

<!-- Billing Address and Additional Details - Enhanced styling -->
<div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 mb-8 transition-all duration-300 hover:shadow-md">
    <div class="border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-medium text-gray-900">Additional Details</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">Billing Address</h4>
                <p class="text-gray-600 whitespace-pre-line">{{ $card->billing_address }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">Card Limits</h4>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Daily Limit:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $settings->currency }}{{ number_format($card->daily_limit ?? 5000, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Monthly Limit:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $settings->currency }}{{ number_format($card->monthly_limit ?? 20000, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Issued Date:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $card->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($card->approval_date)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Approved Date:</span>
                        <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($card->approval_date)->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions - Enhanced with responsive design -->
<div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 mb-8 transition-all duration-300 hover:shadow-md">
    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
        <a href="{{ route('cards.transactions', $card) }}" class="text-sm text-primary-600 hover:text-primary-800 flex items-center">
            View All
            <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
        </a>
    </div>
    <div>
        @if(count($transactions) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merchant</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->merchant_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $transaction->merchant_category }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($transaction->status == 'completed')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    @elseif($transaction->status == 'pending')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @elseif($transaction->status == 'declined')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Declined
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ucfirst($transaction->transaction_type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                    @if($transaction->transaction_type == 'purchase')
                                        <span class="text-red-600">-{{ $card->currency }} {{ number_format(abs($transaction->amount), 2) }}</span>
                                    @elseif($transaction->transaction_type == 'refund')
                                        <span class="text-green-600">+{{ $card->currency }} {{ number_format(abs($transaction->amount), 2) }}</span>
                                    @else
                                        <span class="text-gray-900">{{ $card->currency }} {{ number_format($transaction->amount, 2) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-8 flex flex-col items-center justify-center text-center px-6">
                <div class="bg-gray-50 rounded-full p-3 mb-4">
                    <i data-lucide="credit-card" class="h-8 w-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No Transactions Yet</h3>
                <p class="text-gray-500 text-sm mt-2 max-w-md">
                    This card has no transaction history yet. Transactions will appear here once you start using the card.
                </p>
            </div>
        @endif
    </div>
</div>

<style>
/* 3D Card Styling */
.perspective-1000 {
    perspective: 1000px;
    transform-style: preserve-3d;
}

.credit-card-container {
    height: 220px;
    position: relative;
    width: 100%;
}

.credit-card {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
    transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border-radius: 1rem;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.credit-card.flipped {
    transform: rotateY(180deg);
}

.backface-hidden {
    backface-visibility: hidden;
    transform-style: preserve-3d;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.rotateY-180 {
    transform: rotateY(180deg);
}

/* Card shine effect */
.credit-card:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 60%);
    border-radius: 1rem;
    z-index: 5;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

/* Mobile optimizations */
@media (max-width: 640px) {
    .credit-card-container {
        height: 200px;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============= 3D Credit Card Flip functionality =============
        const creditCard = document.getElementById('creditCard');
        const flipCardBtn = document.getElementById('flipCardBtn');
        const toggleDetailsBtn = document.getElementById('toggleDetailsBtn');
        const toggleBtnText = document.getElementById('toggleBtnText');
        
        // Initialize variables to track state
        let isDetailsShown = false;
        let isCardFlipped = false;
        
        // Flip card functionality - FIXED to work immediately
        if (flipCardBtn && creditCard) {
            flipCardBtn.addEventListener('click', function() {
                isCardFlipped = !isCardFlipped;
                
                // Force a reflow to make sure transform is applied immediately
                creditCard.style.transform = isCardFlipped ? 'rotateY(180deg)' : 'rotateY(0deg)';
                
                // Add/remove the flipped class for CSS-based animation as well
                if (isCardFlipped) {
                    creditCard.classList.add('flipped');
                } else {
                    creditCard.classList.remove('flipped');
                }
            });
        }
        
        // Toggle card details (front and back)
        if (toggleDetailsBtn) {
            toggleDetailsBtn.addEventListener('click', function() {
                // Elements on the front
                const maskedCardNumber = document.getElementById('maskedCardNumber');
                const fullCardNumber = document.getElementById('fullCardNumber');
                
                // Elements on the back
                const maskedCVVBack = document.getElementById('maskedCVVBack');
                const fullCVVBack = document.getElementById('fullCVVBack');
                
                isDetailsShown = !isDetailsShown;
                
                // Only manipulate elements that exist
                if (isDetailsShown) {
                    // Show details
                    if (maskedCardNumber && fullCardNumber) {
                        maskedCardNumber.classList.add('hidden');
                        fullCardNumber.classList.remove('hidden');
                    }
                    
                    if (maskedCVVBack && fullCVVBack) {
                        maskedCVVBack.classList.add('hidden');
                        fullCVVBack.classList.remove('hidden');
                    }
                    
                    // Update button text if element exists
                    if (toggleBtnText) {
                        toggleBtnText.textContent = 'Hide Details';
                    }
                    
                    // Update icon safely
                    const iconElement = this.querySelector('i');
                    if (iconElement) {
                        iconElement.setAttribute('data-lucide', 'eye-off');
                    }
                } else {
                    // Hide details
                    if (maskedCardNumber && fullCardNumber) {
                        maskedCardNumber.classList.remove('hidden');
                        fullCardNumber.classList.add('hidden');
                    }
                    
                    if (maskedCVVBack && fullCVVBack) {
                        maskedCVVBack.classList.remove('hidden');
                        fullCVVBack.classList.add('hidden');
                    }
                    
                    // Update button text if element exists
                    if (toggleBtnText) {
                        toggleBtnText.textContent = 'Show Details';
                    }
                    
                    // Update icon safely
                    const iconElement = this.querySelector('i');
                    if (iconElement) {
                        iconElement.setAttribute('data-lucide', 'eye');
                    }
                }
                
                // Refresh Lucide icons
                if (typeof lucide !== 'undefined' && lucide.createIcons) {
                    lucide.createIcons();
                }
            });
        }
        
        // ============= Card Details Grid Toggles =============
        // Unified toggle visibility function for the grid card details
        const toggleVisibilityButtons = document.querySelectorAll('.toggle-visibility');
        
        if (toggleVisibilityButtons.length > 0) {
            toggleVisibilityButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');
                    
                    // Handle the case where the target might not exist
                    let showElement, hideElement;
                    
                    if (target === 'cardNumberGrid') {
                        showElement = document.getElementById('fullCardNumberGrid');
                        hideElement = document.getElementById('maskedCardNumberGrid');
                    } else if (target === 'cvvGrid') {
                        showElement = document.getElementById('fullCVVGrid');
                        hideElement = document.getElementById('maskedCVVGrid');
                    } else {
                        // If we don't recognize the target, exit early
                        return;
                    }
                    
                    // Only proceed if both elements exist
                    if (showElement && hideElement) {
                        const isHidden = showElement.classList.contains('hidden');
                        
                        if (isHidden) {
                            showElement.classList.remove('hidden');
                            hideElement.classList.add('hidden');
                            
                            // Safely update icon
                            const iconElement = this.querySelector('i');
                            if (iconElement) {
                                iconElement.setAttribute('data-lucide', 'eye-off');
                            }
                        } else {
                            showElement.classList.add('hidden');
                            hideElement.classList.remove('hidden');
                            
                            // Safely update icon
                            const iconElement = this.querySelector('i');
                            if (iconElement) {
                                iconElement.setAttribute('data-lucide', 'eye');
                            }
                        }
                        
                        // Refresh Lucide icons safely
                        if (typeof lucide !== 'undefined' && lucide.createIcons) {
                            lucide.createIcons();
                        }
                    }
                });
            });
        }
        
        // ============= 3D Hover Effects =============
        // Add subtle 3D hover effect to the card
        if (creditCard) {
            creditCard.addEventListener('mousemove', function(e) {
                if (window.innerWidth > 768) { // Only on desktop
                    const cardRect = this.getBoundingClientRect();
                    const cardCenterX = cardRect.left + cardRect.width / 2;
                    const cardCenterY = cardRect.top + cardRect.height / 2;
                    
                    // Calculate mouse position relative to card center
                    const mouseX = e.clientX - cardCenterX;
                    const mouseY = e.clientY - cardCenterY;
                    
                    // Calculate rotation (limited range)
                    const rotateY = mouseX * 0.05; // Adjust sensitivity
                    const rotateX = -mouseY * 0.05; // Inverted for correct tilt direction
                    
                    // Apply transform (while preserving the flip state if needed)
                    if (isCardFlipped) {
                        this.style.transform = `rotateY(180deg) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
                    } else {
                        this.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
                    }
                }
            });
            
            // Reset position when mouse leaves
            creditCard.addEventListener('mouseleave', function() {
                // Reset to original state (either flipped or not)
                if (isCardFlipped) {
                    this.style.transform = 'rotateY(180deg)';
                } else {
                    this.style.transform = 'rotateY(0deg)';
                }
            });
        }
        
        // ============= Additional UI Enhancements =============
        // Add subtle animations and transitions to other elements
        const cardElements = document.querySelectorAll('.rounded-lg, .rounded-xl');
        cardElements.forEach(el => {
            el.addEventListener('mouseenter', function() {
                this.classList.add('shadow-md');
            });
            el.addEventListener('mouseleave', function() {
                if (!this.classList.contains('hover:shadow-xl')) {
                    this.classList.remove('shadow-md');
                }
            });
        });
    });
</script>
@endsection