@extends('layouts.dash2')
@section('title', $title)
@section('content')

<div x-data="{ 
    showBankAccount: false, 
    showSendMoney: false,
    currentTime: '',
    greeting: '',
    currentDate: '',
    balanceVisible: true,
    toggleBalance() {
        this.balanceVisible = !this.balanceVisible;
    },
    updateTime() {
        const now = new Date();
        
        // Format the time (HH:MM:SS)
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        this.currentTime = `${hours}:${minutes}:${seconds}`;
        
        // Set greeting based on hours
        if (now.getHours() < 12) {
            this.greeting = 'Good Morning';
        } else if (now.getHours() < 18) {
            this.greeting = 'Good Afternoon';
        } else {
            this.greeting = 'Good Evening';
        }
        
        // Format the date (Day, Month Date, Year)
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        this.currentDate = now.toLocaleDateString(undefined, options);
    }
}" x-init="
    updateTime();
    setInterval(() => updateTime(), 1000);
">
    <!-- Alerts -->
    <x-danger-alert />
    <x-success-alert />

    <!-- Top Stats Summary Bar -->
    <div class="hidden lg:grid grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-primary-50 to-white rounded-xl p-4 border border-primary-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-800">Current Balance</p>
                <p class="text-lg font-bold text-gray-800">{{ $settings->currency }}{{ number_format(Auth::user()->account_bal,0, '.', ',') }}</p>
            </div>
            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                <i data-lucide="wallet" class="h-5 w-5 text-gray-800"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-50 to-white rounded-xl p-4 border border-green-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Monthly Income</p>
                <p class="text-lg font-bold text-green-700">{{ $settings->currency }}{{ number_format($monthly_deposits ?? 0,0, '.', ',') }}</p>
            </div>
            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                <i data-lucide="trending-up" class="h-5 w-5 text-green-600"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-red-50 to-white rounded-xl p-4 border border-red-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Monthly Outgoing</p>
                <p class="text-lg font-bold text-red-700">{{ $settings->currency }}{{ number_format($monthly_expenses ?? 0,0, '.', ',') }}</p>
            </div>
            <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                <i data-lucide="trending-down" class="h-5 w-5 text-red-600"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-50 to-white rounded-xl p-4 border border-purple-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500">Transaction Limit</p>
                <p class="text-lg font-bold text-purple-700">{{ $settings->currency }}{{ number_format(Auth::user()->limit, 2, '.', ',') }}</p>
            </div>
            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                <i data-lucide="gauge" class="h-5 w-5 text-purple-600"></i>
            </div>
        </div>
    </div>
    

    <!-- Main Dashboard Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Balance and Quick Actions -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Balance Card with Interactive Elements -->
            <div class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 rounded-2xl shadow-lg text-white relative overflow-hidden">
                <!-- Day/Night Decoration -->
                <div class="absolute inset-0 w-full h-full overflow-hidden">
                    <div class="absolute {{ now()->hour >= 6 && now()->hour < 18 ? 'opacity-20' : 'opacity-5' }} right-0 top-0">
                        <div class="{{ now()->hour >= 6 && now()->hour < 18 ? 'bg-yellow-300' : 'bg-blue-900' }} rounded-full h-32 w-32 -mt-10 -mr-10 blur-xl"></div>
                    </div>
                    <div class="absolute {{ now()->hour >= 6 && now()->hour < 18 ? 'opacity-10' : 'opacity-5' }} left-1/2 top-1/2">
                        <div class="{{ now()->hour >= 6 && now()->hour < 18 ? 'bg-yellow-200' : 'bg-indigo-900' }} rounded-full h-40 w-40 blur-xl"></div>
                    </div>
                    @if(now()->hour >= 6 && now()->hour < 18)
                        <!-- Daytime clouds -->
                        <div class="absolute opacity-10 left-0 bottom-0">
                            <i data-lucide="cloud" class="h-16 w-16 text-white"></i>
                        </div>
                    @else
                        <!-- Nighttime stars -->
                        @for($i = 0; $i < 8; $i++)
                            <div class="absolute opacity-20 rounded-full bg-white h-1 w-1" style="left: {{ rand(5, 95) }}%; top: {{ rand(5, 95) }}%"></div>
                        @endfor
                    @endif
                </div>
                
                <!-- Card Content -->
                <div class="relative z-10 p-6">
                    <!-- Header with time and user -->
                    <div class="flex items-center justify-between mb-6">
                       <div class="flex items-center space-x-3">
    @if(!empty(Auth::user()->profile_photo_path))
        <img 
            alt="{{ Auth::user()->name }}" 
            src="{{ $settings->site_address }}/storage/app/public/photos/{{ Auth::user()->profile_photo_path }}" 
            class="h-12 w-12 rounded-full object-cover border-2 border-white/20">
    @else
        @php
            $initials = strtoupper(substr(Auth::user()->name, 0, 1) . substr(Auth::user()->lastname, 0, 1));
        @endphp
        <div class="h-12 w-12 rounded-full bg-white/20 text-white flex items-center justify-center font-bold border-2 border-white/20">
            {{ $initials }}
        </div>
    @endif
    <div>
        <div class="text-sm text-white/80" x-text="greeting"></div>
        <div class="font-medium text-white">{{ Auth::user()->name }}</div>
    </div>
</div>

                        <div class="text-right">
                            <div class="text-lg font-bold" x-text="currentTime"></div>
                            <div class="text-xs text-white/70" x-text="currentDate"></div>
                        </div>
                    </div>
                    
                    <!-- Balance with hide/show toggle -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-medium mb-1">Available Balance</h2>
                            <button @click="toggleBalance()" class="text-white/80 hover:text-white focus:outline-none transition-all">
                                <i x-show="balanceVisible" data-lucide="eye-off" class="h-5 w-5"></i>
                                <i x-show="!balanceVisible" data-lucide="eye" class="h-5 w-5"></i>
                            </button>
                        </div>
                        <div x-show="balanceVisible" x-transition class="text-3xl font-bold">
                            {{ $settings->currency }}{{ number_format(Auth::user()->account_bal,0, '.', ',') }} {{ $settings->s_currency }}
                        </div>
                        <div x-show="!balanceVisible" x-transition class="text-3xl font-bold">
                            *******
                        </div>
                    </div>
                    
                    <!-- Account Info Bar -->
<div class="relative z-10 p-4 bg-white/10 rounded-lg backdrop-blur-sm">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <!-- Mobile layout (side-by-side) -->
        <div class="sm:hidden flex items-center justify-between w-full">
            <div class="flex items-center flex-1 min-w-0">
                <div class="flex-shrink-0 mr-3">
                    <div class="h-10 w-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i data-lucide="shield" class="h-5 w-5 text-white"></i>
                    </div>
                </div>
                <div class="truncate">
                    <div class="text-sm font-medium">Your Account Number</div>
                    <div class="flex items-center">
                        <div class="text-lg font-bold truncate mr-2">{{ Auth::user()->usernumber }}</div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ Auth::user()->account_status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ Auth::user()->account_status == 'active' ? 'bg-green-600' : 'bg-red-600' }} mr-1"></span>
                                {{ ucfirst(Auth::user()->account_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col gap-2 ml-2">
                <a href="{{ route('accounthistory') }}" class="inline-flex items-center justify-center px-2 py-1 bg-white text-primary-600 text-xs font-medium rounded-md hover:bg-gray-50">
                    <i data-lucide="activity" class="h-3 w-3 mr-1"></i> Transactions
                </a>
                <a href="{{ route('deposits') }}" class="inline-flex items-center justify-center px-2 py-1 bg-primary-700 text-white text-xs font-medium rounded-md hover:bg-primary-800 border border-white/10">
                    <i data-lucide="wallet" class="h-3 w-3 mr-1"></i> Top up
                </a>
            </div>
        </div>
        
        <!-- Desktop layout - hidden on mobile -->
        <div class="hidden sm:flex sm:items-center sm:flex-1">
            <div class="flex-shrink-0 mr-4">
                <div class="h-10 w-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i data-lucide="shield" class="h-5 w-5 text-white"></i>
                </div>
            </div>
            <div>
                <div class="flex items-center">
                    <div class="text-sm font-medium mr-2">Your Account Number</div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ Auth::user()->account_status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ Auth::user()->account_status == 'active' ? 'bg-green-600' : 'bg-red-600' }} mr-1"></span>
                        {{ ucfirst(Auth::user()->account_status) }}
                    </span>
                </div>
                <div class="text-lg font-bold">{{ Auth::user()->usernumber }}</div>
            </div>
        </div>
        
        <!-- Original desktop buttons - hidden on mobile -->
        <div class="hidden sm:flex sm:flex-row gap-2">
            <a href="{{ route('accounthistory') }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-white text-primary-600 text-sm font-medium rounded-md hover:bg-gray-50">
                <i data-lucide="activity" class="h-4 w-4 mr-1"></i> Transactions
            </a>
            <a href="{{ route('deposits') }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-primary-700 text-white text-sm font-medium rounded-md hover:bg-primary-800 border border-white/10">
                <i data-lucide="wallet" class="h-4 w-4 mr-1"></i> Top up
            </a>
        </div>
    </div>
</div>
                </div>
            </div>
            
            <!-- Welcome and Quick Actions Card -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div>
                        <h1 class="text-xl font-bold mb-1">What would you like to do today?</h1>
                        <p class="text-gray-600">Choose from our popular actions below</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <button 
                        @click="showBankAccount = true"
                        class="flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 border border-gray-200 transition-all">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mb-3">
                            <i data-lucide="building-2" class="h-6 w-6 text-gray-600"></i>
                        </div>
                        <span class="font-medium text-gray-800">Account Info</span>
                    </button>
                    
                    <button 
                        @click="showSendMoney = true"
                        class="flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 hover:from-primary-100 hover:to-primary-200 border border-primary-200 transition-all">
                        <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center mb-3">
                            <i data-lucide="send" class="h-6 w-6 text-gray-600"></i>
                        </div>
                        <span class="font-medium text-gray-800">Send Money</span>
                    </button>
                    
                    <a href="{{ route('deposits') }}" 
                        class="flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 border border-green-200 transition-all">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-3">
                            <i data-lucide="plus" class="h-6 w-6 text-green-600"></i>
                        </div>
                        <span class="font-medium text-gray-800">Deposit</span>
                    </a>
                    
                    <a href="{{ route('accounthistory') }}"
                        class="flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 border border-purple-200 transition-all">
                        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mb-3">
                            <i data-lucide="history" class="h-6 w-6 text-purple-600"></i>
                        </div>
                        <span class="font-medium text-gray-800">History</span>
                    </a>
                </div>
            </div>
           <!-- Cards Section to add to the Dashboard -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 mb-6">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <div class="flex items-center">
            <i data-lucide="credit-card" class="h-5 w-5 text-gray-500 mr-2"></i>
            <h3 class="text-lg font-medium text-gray-900">Your Cards</h3>
        </div>
        <a href="{{ route('cards') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500 flex items-center">
            View all <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
        </a>
    </div>
    
    <div class="p-6">
        @if(count($cards) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($cards->take(2) as $card)
                    <div class="bg-white rounded-xl border border-gray-100 hover:shadow-lg transition-all duration-300 group overflow-hidden">
                        <!-- Card Representation -->
                        <div class="p-4">
                            <div class="w-full h-44 rounded-xl relative overflow-hidden shadow-none group-hover:shadow-md transition-all duration-300 transform group-hover:scale-[1.02]">
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
                                        <!-- Bank Name and Status Badge -->
                                        <div>
                                            <div class="text-white font-semibold text-sm sm:text-base tracking-wider">{{ $settings->site_name }}</div>
                                            <div class="text-white/70 text-xs">Virtual Banking</div>
                                            <div class="mt-1">
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
                        
                        <!-- Card Details and Actions -->
                        <div class="px-4 py-3 border-t border-gray-100">
                            <div class="flex justify-between items-center mb-2">
                                <div class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $card->card_type)) }} {{ ucfirst($card->card_level) }}</div>
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
            
            @if(count($cards) > 2)
                <div class="mt-4 text-center">
                    <a href="{{ route('cards') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        View all {{ count($cards) }} cards
                    </a>
                </div>
            @endif
        @else
            <div class="py-8 flex flex-col items-center justify-center text-center">
                <div class="bg-gray-50 rounded-full p-3 mb-4">
                    <i data-lucide="credit-card" class="h-8 w-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No cards yet</h3>
                <p class="text-gray-500 text-sm mt-2 mb-4 max-w-md">
                    You haven't applied for any virtual cards yet. Apply for a new card to get started with secure online payments.
                </p>
                <a href="{{ route('cards.apply') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i data-lucide="plus" class="h-4 w-4 mr-2"></i> Apply for Card
                </a>
            </div>
        @endif
    </div>
</div>
            
            <!-- Recent Transactions Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div class="flex items-center">
                        <i data-lucide="list" class="h-5 w-5 text-gray-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
                    </div>
                    <a href="{{ route('accounthistory') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500 flex items-center">
                        View all <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    @if(count($withdrawals) > 0 || count($deposits) > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    // Combine and sort transactions by date
                                    $allTransactions = collect()
                                        ->merge($withdrawals)
                                        ->merge($deposits)
                                        ->sortByDesc('created_at')
                                        ->take(5);
                                @endphp
                                
                                @forelse($allTransactions as $transaction)
                                    <tr class="hover:bg-gray-50 cursor-pointer" id="{{ $transaction->id }}{{ $transaction->txn_id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $isCredit = isset($transaction->type) ? $transaction->type == 'Credit' : true;
                                            @endphp
                                            <div class="h-10 w-10 {{ $isCredit ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center">
                                                <i data-lucide="{{ $isCredit ? 'plus' : 'minus' }}" class="h-5 w-5 {{ $isCredit ? 'text-green-600' : 'text-red-600' }}"></i>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $settings->currency }}{{ number_format($transaction->amount, 2, '.', ',') }} {{ $settings->s_currency }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isCredit ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $isCredit ? 'Credit' : 'Debit' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($transaction->status == 'Pending')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                            @elseif($transaction->status == 'On-hold')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                On-hold
                                            </span>
                                            @elseif($transaction->status == 'Rejected')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Rejected
                                            </span>
                                            @elseif($transaction->status == 'Processed' || $transaction->status == 'Completed')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $transaction->status }}
                                            </span>
                                            @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $transaction->status }}
                                            </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->txn_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($transaction->created_at)->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <i data-lucide="inbox" class="h-12 w-12 text-gray-300 mb-4"></i>
                                                <p class="text-lg font-medium text-gray-600">No transactions yet</p>
                                                <p class="text-sm text-gray-500 mt-1">Your transaction history will appear here</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <div class="py-12 flex flex-col items-center justify-center">
                            <i data-lucide="inbox" class="h-16 w-16 text-gray-300 mb-4"></i>
                            <p class="text-lg font-medium text-gray-600">No transactions yet</p>
                            <p class="text-sm text-gray-500 mt-1 mb-4">Your transaction history will appear here</p>
                            <a href="{{ route('deposits') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                                Make your first deposit
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Right Column - Stats and Notices -->
        <div class="space-y-6">
            <!-- Account Stats Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Account Statistics</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <!-- Transaction Limit -->
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center mr-4">
                            <i data-lucide="credit-card" class="h-5 w-5 text-gray-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500">Transaction Limit</p>
                            <p class="text-lg font-bold text-gray-900 truncate">{{ $settings->currency }}{{ number_format(Auth::user()->limit, 2, '.', ',') }}</p>
                        </div>
                    </div>
                    
                    <!-- Pending Transactions -->
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center mr-4">
                            <i data-lucide="clock" class="h-5 w-5 text-yellow-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500">Pending Transactions</p>
                            <p class="text-lg font-bold text-gray-900 truncate">{{ $settings->currency }}{{ number_format($total_deposited_pending + $total_withdrawal_pending, 2, '.', ',') }}</p>
                        </div>
                    </div>
                    
                    <!-- Total Transaction Volume -->
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                            <i data-lucide="bar-chart-2" class="h-5 w-5 text-green-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500">Transaction Volume</p>
                            <p class="text-lg font-bold text-gray-900 truncate">{{ $settings->currency }}{{ number_format($total_withdrawal+$deposited, 2, '.', ',') }}</p>
                        </div>
                    </div>
                    
                    <!-- Account Age -->
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                            <i data-lucide="calendar" class="h-5 w-5 text-purple-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500">Account Age</p>
                            <p class="text-lg font-bold text-gray-900 truncate">N/A</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Transfer Links Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Transfer</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <a href="{{ route('localtransfer') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 mr-4">
                                <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i data-lucide="user" class="h-5 w-5 text-gray-600"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Local Transfer</h4>
                                <p class="text-sm text-gray-600">0% Handling charges</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="h-5 w-5 text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('internationaltransfer') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 mr-4">
                                <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i data-lucide="globe" class="h-5 w-5 text-gray-600"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">International Transfer</h4>
                                <p class="text-sm text-gray-600">Global reach, 0% fee</p>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="h-5 w-5 text-gray-400"></i>
                    </a>
                </div>
            </div>
            
            <!-- Help & Support Card -->
            <div class="bg-gradient-to-br from-primary-50 via-primary-100 to-primary-50 rounded-xl shadow-sm overflow-hidden border border-primary-200">
                <div class="p-6">
                    <div class="flex items-center justify-center mb-4">
                        <div class="h-16 w-16 rounded-full bg-white flex items-center justify-center">
                            <i data-lucide="help-circle" class="h-10 w-10 text-primary-600"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Need Help?</h3>
                    <p class="text-sm text-gray-600 text-center mb-4">Our support team is here to assist you 24/7</p>
                    <div class="flex justify-center">
                        <a href="{{ route('support') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            <i data-lucide="message-circle" class="h-4 w-4 mr-2"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
   <!-- Bank Account Modal -->
    <div 
        x-show="showBankAccount" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="bank-account-title" 
        role="dialog" 
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="showBankAccount" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" 
                @click="showBankAccount = false" 
                aria-hidden="true">
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div 
                x-show="showBankAccount" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button
                        @click="showBankAccount = false"
                        type="button"
                        class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <i data-lucide="x" class="h-6 w-6"></i>
                    </button>
                </div>
                
                <div class="text-center mb-5">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary-100 mb-4">
                        <i data-lucide="building-2" class="h-8 w-8 text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900" id="bank-account-title">Bank Account Details</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $settings->site_name }}</p>
                    <p class="text-xs text-gray-500">{{ $settings->address }}</p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <p class="font-medium mb-3 flex items-center"><i data-lucide="info" class="h-4 w-4 mr-2 text-primary-500"></i> Account Details</p>
                    <ul class="space-y-3">
                        <li class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <div class="h-2 w-2 bg-primary-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Account Name</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium">{{ Auth::user()->name }} {{ Auth::user()->lastname }}</span>
                                <button class="ml-2 text-primary-500 hover:text-primary-700 focus:outline-none" @click="navigator.clipboard.writeText('{{ Auth::user()->name }} {{ Auth::user()->lastname }}'); $el.querySelector('i').classList.add('text-green-500')">
                                    <i data-lucide="copy" class="h-4 w-4 transition-colors duration-300"></i>
                                </button>
                            </div>
                        </li>
                        <li class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <div class="h-2 w-2 bg-primary-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Account Number</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium">{{ Auth::user()->usernumber }}</span>
                                <button class="ml-2 text-primary-500 hover:text-primary-700 focus:outline-none" @click="navigator.clipboard.writeText('{{ Auth::user()->usernumber }}'); $el.querySelector('i').classList.add('text-green-500')">
                                    <i data-lucide="copy" class="h-4 w-4 transition-colors duration-300"></i>
                                </button>
                            </div>
                        </li>
                        <li class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <div class="h-2 w-2 bg-primary-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Sort Code</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium">388130</span>
                                <button class="ml-2 text-primary-500 hover:text-primary-700 focus:outline-none" @click="navigator.clipboard.writeText('388130'); $el.querySelector('i').classList.add('text-green-500')">
                                    <i data-lucide="copy" class="h-4 w-4 transition-colors duration-300"></i>
                                </button>
                            </div>
                        </li>
                        <li class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <div class="h-2 w-2 bg-primary-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Payment Reference</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium">1234567890</span>
                                <button class="ml-2 text-primary-500 hover:text-primary-700 focus:outline-none" @click="navigator.clipboard.writeText('1234567890'); $el.querySelector('i').classList.add('text-green-500')">
                                    <i data-lucide="copy" class="h-4 w-4 transition-colors duration-300"></i>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="flex items-start p-4 bg-primary-50 rounded-lg">
                    <i data-lucide="info" class="h-5 w-5 text-primary-500 mt-0.5 mr-3 flex-shrink-0"></i>
                    <p class="text-sm text-gray-700">
                        Payment reference helps {{$settings->site_name}} track payments faster. Please include it in wire transfer description.
                    </p>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button 
                        @click="showBankAccount = false"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Send Money Modal -->
    <div 
        x-show="showSendMoney" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="send-money-title" 
        role="dialog" 
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="showSendMoney" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" 
                @click="showSendMoney = false" 
                aria-hidden="true">
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div 
                x-show="showSendMoney" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button
                        @click="showSendMoney = false"
                        type="button"
                        class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <i data-lucide="x" class="h-6 w-6"></i>
                    </button>
                </div>
                
                <div class="text-center mb-5">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary-100 mb-4">
                        <i data-lucide="send" class="h-8 w-8 text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900" id="send-money-title">Send Money</h3>
                    <p class="mt-1 text-sm text-gray-500">Swift and Secure Money Transfer</p>
                </div>
                
                <div class="mt-6 space-y-4">
                    <a href="{{ route('localtransfer') }}" class="block group">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg group-hover:bg-gray-100 transition-colors border border-gray-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center group-hover:bg-primary-200 transition-colors">
                                        <i data-lucide="user" class="h-5 w-5 text-primary-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Local Transfer</h4>
                                    <p class="text-sm text-gray-600">Easily send money locally</p>
                                    <p class="text-xs text-gray-500">0% Handling charges</p>
                                </div>
                            </div>
                            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center group-hover:bg-primary-100 transition-colors">
                                <i data-lucide="chevron-right" class="h-5 w-5 text-gray-400 group-hover:text-primary-600 transition-colors"></i>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('internationaltransfer') }}" class="block group">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg group-hover:bg-gray-100 transition-colors border border-gray-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center group-hover:bg-primary-200 transition-colors">
                                        <i data-lucide="globe" class="h-5 w-5 text-primary-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">International Wire Transfer</h4>
                                    <p class="text-sm text-gray-600">Wire transfer is executed under 72 hours</p>
                                    <p class="text-xs text-gray-500">IBAN & SWIFT code required</p>
                                </div>
                            </div>
                            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center group-hover:bg-primary-100 transition-colors">
                                <i data-lucide="chevron-right" class="h-5 w-5 text-gray-400 group-hover:text-primary-600 transition-colors"></i>
                            </div>
                        </div>
                    </a>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button 
                        @click="showSendMoney = false"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Close
                    </button>
                </div>
            </div>
        </div>
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