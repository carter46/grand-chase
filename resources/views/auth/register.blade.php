@extends('layouts.guest2')

@section('title', 'Member Registration')
@section('content')

<main class="w-full min-h-screen bg-surface">
<div class="flex flex-col lg:flex-row min-h-screen">
    <div class="hidden lg:flex lg:w-2/5 bg-inverse-surface relative overflow-hidden flex-col">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(rgba(255,255,255,0.15) 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="relative z-10 p-8 md:p-10">
            @include('includes.public-brand', [
                'brandTextClass' => 'font-label-bold text-on-primary uppercase tracking-wider',
                'brandImgClass' => 'h-9 w-auto object-contain',
            ])
        </div>
        <div class="relative flex flex-col justify-center flex-1 w-full text-on-primary p-8 md:p-12 z-10">
            <h1 class="text-headline-lg mb-6">Start Banking with Us</h1>
            <p class="font-body-lg text-on-primary/70 mb-8 max-w-md">Create your {{ $settings->site_name }} account in just a few steps.</p>
            @foreach (['Secure Banking Platform', 'Fast Transfers', '24/7 Account Access'] as $benefit)
            <div class="flex items-start gap-3 mb-4">
                <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
                <p class="font-body-sm text-on-primary/80">{{ $benefit }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="w-full lg:w-3/5 flex flex-col justify-center items-center p-4 sm:p-6 lg:p-12">
        <div class="w-full max-w-2xl">
            <div class="lg:hidden flex justify-center mb-6">
                @include('includes.public-brand', [
                    'brandImgClass' => 'h-9 w-auto object-contain',
                    'brandTextClass' => 'font-label-bold text-on-surface uppercase tracking-wider text-lg',
                ])
            </div>
            <!-- Alerts -->
            @if (Session::has('status'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <p>{{ session('status') }}</p>
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
            
            <!-- Registration Card -->
            <div x-data="{ 
                step: 1,
                totalSteps: 4,
                formData: {
                    name: '{{ old('name') }}',
                    middlename: '',
                    lastname: '',
                    username: '{{ old('username') }}',
                    email: '{{ old('email') }}',
                    phone: '',
                    country: '',
                    accounttype: '',
                    pin: '',
                    password: '',
                    password_confirmation: '',
                    terms: false
                },
                nextStep() {
                    if (this.validateCurrentStep()) {
                        if (this.step < this.totalSteps) {
                            this.step++;
                            window.scrollTo(0, 0);
                        }
                    }
                },
                prevStep() {
                    if (this.step > 1) {
                        this.step--;
                        window.scrollTo(0, 0);
                    }
                },
                validateCurrentStep() {
                    // Basic validation logic based on current step
                    if (this.step === 1) {
                        return this.formData.name && this.formData.lastname && this.formData.username;
                    } else if (this.step === 2) {
                        return this.formData.email && this.formData.phone && this.formData.country;
                    } else if (this.step === 3) {
                        return this.formData.accounttype && this.formData.pin;
                    } else if (this.step === 4) {
                        return this.formData.password && this.formData.password_confirmation && this.formData.terms;
                    }
                    return true;
                },
                get progress() {
                    return (this.step / this.totalSteps) * 100;
                }
            }" class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Progress Header -->
                <div class="bg-gray-50 px-8 py-6 border-b border-gray-200">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-2xl font-bold text-gray-900">Create Your Account</h2>
                        <span class="text-sm font-medium text-gray-500">Step <span x-text="step"></span> of <span x-text="totalSteps"></span></span>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full transition-all duration-300 ease-in-out" :style="'width: ' + progress + '%'"></div>
                    </div>
                    
                    <!-- Step Titles -->
                    <div class="flex justify-between mt-2 text-xs text-gray-500">
                        <div class="text-center" :class="{ 'text-primary font-medium': step >= 1 }">Personal Info</div>
                        <div class="text-center" :class="{ 'text-primary font-medium': step >= 2 }">Contact Details</div>
                        <div class="text-center" :class="{ 'text-primary font-medium': step >= 3 }">Account Setup</div>
                        <div class="text-center" :class="{ 'text-primary font-medium': step >= 4 }">Security</div>
                    </div>
                </div>
                
                <!-- Form Container -->
                <div class="px-8 py-6">
                    <form action="{{ route('register') }}" method="post" id="registration-form">
                        @csrf
                        
                        <!-- Step 1: Personal Information -->
                        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="text-center mb-6">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-4">
                                    <i data-lucide="user" class="h-8 w-8 text-primary"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                                <p class="mt-1 text-sm text-gray-500">Please provide your legal name as it appears on official documents</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- First Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Legal First Name *</label>
                                    <input 
                                        type="text" 
                                        id="name" 
                                        name="name" 
                                        x-model="formData.name"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" 
                                        placeholder="Enter first name"
                                        autocomplete="given-name"
                                        required>
                                    @if ($errors->has('name'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                                
                                <!-- Last Name -->
                                <div>
                                    <label for="lastname" class="block text-sm font-medium text-gray-700 mb-2">Legal Last Name *</label>
                                    <input 
                                        type="text" 
                                        id="lastname" 
                                        name="lastname" 
                                        x-model="formData.lastname"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" 
                                        placeholder="Enter last name"
                                        autocomplete="family-name"
                                        required>
                                </div>
                                
                                <!-- Username -->
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                                    <input 
                                        type="text" 
                                        id="username" 
                                        name="username" 
                                        x-model="formData.username"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" 
                                        placeholder="Choose username"
                                        required>
                                    @if ($errors->has('username'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('username') }}</p>
                                    @endif
                                </div>
                                
                                <!-- Middle Name - Moved to end -->
                                <div>
                                    <label for="middlename" class="block text-sm font-medium text-gray-700 mb-2">Middle Name (Optional)</label>
                                    <input 
                                        type="text" 
                                        id="middlename" 
                                        name="middlename" 
                                        x-model="formData.middlename"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" 
                                        placeholder="Middle name if any">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 2: Contact Information -->
                        <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="text-center mb-6">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-4">
                                    <i data-lucide="mail" class="h-8 w-8 text-primary"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
                                <p class="mt-1 text-sm text-gray-500">We'll use these details to communicate with you about your account</p>
                            </div>
                            
                            <div class="space-y-6 mb-6">
                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <i data-lucide="mail" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <input 
                                            type="email" 
                                            id="email" 
                                            name="email" 
                                            x-model="formData.email"
                                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" 
                                            placeholder="your.email@example.com"
                                            autocomplete="email"
                                            required>
                                    </div>
                                    @if ($errors->has('email'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('email') }}</p>
                                    @endif
                                </div>
                                
                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <i data-lucide="phone" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <input 
                                            type="tel" 
                                            id="phone" 
                                            name="phone" 
                                            x-model="formData.phone"
                                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" 
                                            placeholder="Enter contact number"
                                            autocomplete="tel"
                                            required>
                                    </div>
                                    @if ($errors->has('phone'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('phone') }}</p>
                                    @endif
                                </div>
                                
                                <!-- Country -->
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <i data-lucide="globe" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <select 
                                            id="country" 
                                            name="country" 
                                            x-model="formData.country"
                                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary appearance-none"
                                            autocomplete="country-name"
                                            required>
                                            <option value="" disabled selected>Select your country</option>
                                            @include('auth.countries')
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <i data-lucide="chevron-down" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                    </div>
                                    @if ($errors->has('country'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('country') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 3: Account Setup -->
                        <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="text-center mb-6">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-4">
                                    <i data-lucide="landmark" class="h-8 w-8 text-primary"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Account Setup</h3>
                                <p class="mt-1 text-sm text-gray-500">Choose your account type and set up your transaction PIN</p>
                            </div>
                            
                            <div class="space-y-6 mb-6">
                                <!-- Account Type -->
                                <div>
                                    <label for="accounttype" class="block text-sm font-medium text-gray-700 mb-2">Account Type *</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <label @click="formData.accounttype = 'Checking Account'" class="relative block cursor-pointer">
                                            <input type="radio" name="accounttype" value="Checking Account" x-model="formData.accounttype" class="sr-only">
                                            <div class="border rounded-lg p-4 transition-all" :class="formData.accounttype === 'Checking Account' ? 'border-primary bg-primary/5 ring-2 ring-primary' : 'border-gray-300 hover:border-primary/40'">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                                            <i data-lucide="credit-card" class="h-5 w-5 text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <h4 class="text-sm font-medium text-gray-900">Checking Account</h4>
                                                        <p class="text-xs text-gray-500">Perfect for daily transactions and bill payments</p>
                                                    </div>
                                                </div>
                                                <div x-show="formData.accounttype === 'Checking Account'" class="absolute top-2 right-2 w-5 h-5 bg-primary rounded-full flex items-center justify-center">
                                                    <i data-lucide="check" class="h-3 w-3 text-white"></i>
                                                </div>
                                            </div>
                                        </label>
                                        
                                        <label @click="formData.accounttype = 'Savings Account'" class="relative block cursor-pointer">
                                            <input type="radio" name="accounttype" value="Savings Account" x-model="formData.accounttype" class="sr-only">
                                            <div class="border rounded-lg p-4 transition-all" :class="formData.accounttype === 'Savings Account' ? 'border-primary bg-primary/5 ring-2 ring-primary' : 'border-gray-300 hover:border-primary/40'">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                                            <i data-lucide="piggy-bank" class="h-5 w-5 text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <h4 class="text-sm font-medium text-gray-900">Savings Account</h4>
                                                        <p class="text-xs text-gray-500">Earn interest on your deposits</p>
                                                    </div>
                                                </div>
                                                <div x-show="formData.accounttype === 'Savings Account'" class="absolute top-2 right-2 w-5 h-5 bg-primary rounded-full flex items-center justify-center">
                                                    <i data-lucide="check" class="h-3 w-3 text-white"></i>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    
                                    <!-- Additional account types dropdown for more options -->
                                    <div class="mt-4" x-data="{ open: false }">
                                        <button 
                                            type="button" 
                                            @click="open = !open" 
                                            class="w-full text-left flex items-center justify-between text-sm text-primary hover:text-primary-700 focus:outline-none">
                                            <span>Show more account types</span>
                                            <i data-lucide="chevron-down" class="h-4 w-4" :class="{'transform rotate-180': open}"></i>
                                        </button>
                                        
                                        <div x-show="open" x-transition class="mt-2 space-y-2">
                                            <template x-for="(type, index) in [
                                                {value: 'Fixed Deposit Account', label: 'Fixed Deposit Account', desc: 'Highest interest rates for fixed terms', icon: 'calendar'},
                                                {value: 'Current Account', label: 'Current Account', desc: 'For everyday business transactions', icon: 'briefcase'},
                                                {value: 'Crypto Currency Account', label: 'Crypto Currency Account', desc: 'For digital currency management', icon: 'bitcoin'},
                                                {value: 'Business Account', label: 'Business Account', desc: 'For small to medium businesses', icon: 'building'},
                                                {value: 'Non Resident Account', label: 'Non Resident Account', desc: 'For international customers', icon: 'globe'},
                                                {value: 'Cooperate Business Account', label: 'Cooperate Business Account', desc: 'For large corporations', icon: 'landmark'},
                                                {value: 'Investment Account', label: 'Investment Account', desc: 'For stocks and securities', icon: 'trending-up'}
                                            ]" :key="index">
                                                <label @click="formData.accounttype = type.value" class="relative block cursor-pointer">
                                                    <input type="radio" name="accounttype" :value="type.value" x-model="formData.accounttype" class="sr-only">
                                                    <div class="border rounded-lg p-4 transition-all" :class="formData.accounttype === type.value ? 'border-primary bg-primary/5 ring-2 ring-primary' : 'border-gray-300 hover:border-primary/40'">
                                                        <div class="flex items-start">
                                                            <div class="flex-shrink-0">
                                                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                                                    <i :data-lucide="type.icon" class="h-5 w-5 text-primary"></i>
                                                                </div>
                                                            </div>
                                                            <div class="ml-3">
                                                                <h4 class="text-sm font-medium text-gray-900" x-text="type.label"></h4>
                                                                <p class="text-xs text-gray-500" x-text="type.desc"></p>
                                                            </div>
                                                        </div>
                                                        <div x-show="formData.accounttype === type.value" class="absolute top-2 right-2 w-5 h-5 bg-primary rounded-full flex items-center justify-center">
                                                            <i data-lucide="check" class="h-3 w-3 text-white"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Transaction PIN -->
                                <div>
                                    <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">Transaction PIN (4 digits) *</label>
                                    <div class="relative" x-data="{ showPin: false }">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <i data-lucide="key" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <input 
                                            :type="showPin ? 'text' : 'password'" 
                                            id="pin" 
                                            name="pin" 
                                            x-model="formData.pin"
                                            class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" 
                                            placeholder="••••"
                                            maxlength="4"
                                            pattern="[0-9]{4}"
                                            required>
                                        <button 
                                            type="button"
                                            @click="showPin = !showPin"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <i data-lucide="eye" class="h-5 w-5 text-gray-400" x-show="!showPin"></i>
                                            <i data-lucide="eye-off" class="h-5 w-5 text-gray-400" x-show="showPin"></i>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Your PIN will be required to authorize transactions</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 4: Security -->
                        <div x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="text-center mb-6">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-4">
                                    <i data-lucide="shield" class="h-8 w-8 text-primary"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Secure Your Account</h3>
                                <p class="mt-1 text-sm text-gray-500">Create a strong password to protect your account</p>
                            </div>
                            
                            <div class="space-y-6 mb-6">
                                <!-- Password -->
                                <div x-data="{ showPassword: false }">
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <input 
                                            :type="showPassword ? 'text' : 'password'" 
                                            id="password" 
                                            name="password" 
                                            x-model="formData.password"
                                            class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" 
                                            placeholder="••••••••"
                                            autocomplete="new-password"
                                            required>
                                        <button 
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <i data-lucide="eye" class="h-5 w-5 text-gray-400" x-show="!showPassword"></i>
                                            <i data-lucide="eye-off" class="h-5 w-5 text-gray-400" x-show="showPassword"></i>
                                        </button>
                                    </div>
                                    @if ($errors->has('password'))
                                        <p class="mt-1 text-sm text-red-600">{{ $errors->first('password') }}</p>
                                    @endif
                                    
                                    <!-- Password Strength Meter -->
                                    <div class="mt-2" x-data="{ 
                                        get strength() {
                                            let score = 0;
                                            
                                            // Length check
                                            if (formData.password.length > 7) score += 1;
                                            if (formData.password.length > 10) score += 1;
                                            
                                            // Complexity checks
                                            if (/[A-Z]/.test(formData.password)) score += 1;
                                            if (/[0-9]/.test(formData.password)) score += 1;
                                            if (/[^A-Za-z0-9]/.test(formData.password)) score += 1;
                                            
                                            return score;
                                        },
                                        get strengthLabel() {
                                            const labels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
                                            return labels[this.strength] || 'Very Weak';
                                        },
                                        get strengthColor() {
                                            const colors = [
                                                'bg-red-500', // Very Weak
                                                'bg-red-500', // Weak
                                                'bg-yellow-500', // Fair
                                                'bg-yellow-500', // Good
                                                'bg-green-500', // Strong
                                                'bg-green-500'  // Very Strong
                                            ];
                                            return colors[this.strength] || 'bg-red-500';
                                        }
                                    }" x-show="formData.password.length > 0">
                                        <div class="flex justify-between items-center mb-1">
                                            <p class="text-xs text-gray-500">Password strength: <span x-text="strengthLabel" :class="{
                                                'text-red-600': strength < 2,
                                                'text-yellow-600': strength >= 2 && strength < 4,
                                                'text-green-600': strength >= 4
                                            }"></span></p>
                                        </div>
                                        <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div 
                                                class="h-full transition-all duration-300 ease-in-out" 
                                                :class="strengthColor"
                                                :style="`width: ${(strength / 5) * 100}%`"></div>
                                        </div>
                                        <ul class="mt-2 space-y-1 text-xs text-gray-500">
                                            <li class="flex items-center" :class="{ 'text-green-600': formData.password.length > 7 }">
                                                <i data-lucide="check-circle" class="h-3 w-3 mr-1" x-show="formData.password.length > 7"></i>
                                                <i data-lucide="circle" class="h-3 w-3 mr-1" x-show="formData.password.length <= 7"></i>
                                                At least 8 characters
                                            </li>
                                            <li class="flex items-center" :class="{ 'text-green-600': /[A-Z]/.test(formData.password) }">
                                                <i data-lucide="check-circle" class="h-3 w-3 mr-1" x-show="/[A-Z]/.test(formData.password)"></i>
                                                <i data-lucide="circle" class="h-3 w-3 mr-1" x-show="!/[A-Z]/.test(formData.password)"></i>
                                                At least one uppercase letter
                                            </li>
                                            <li class="flex items-center" :class="{ 'text-green-600': /[0-9]/.test(formData.password) }">
                                                <i data-lucide="check-circle" class="h-3 w-3 mr-1" x-show="/[0-9]/.test(formData.password)"></i>
                                                <i data-lucide="circle" class="h-3 w-3 mr-1" x-show="!/[0-9]/.test(formData.password)"></i>
                                                At least one number
                                            </li>
                                            <li class="flex items-center" :class="{ 'text-green-600': /[^A-Za-z0-9]/.test(formData.password) }">
                                                <i data-lucide="check-circle" class="h-3 w-3 mr-1" x-show="/[^A-Za-z0-9]/.test(formData.password)"></i>
                                                <i data-lucide="circle" class="h-3 w-3 mr-1" x-show="!/[^A-Za-z0-9]/.test(formData.password)"></i>
                                                At least one special character
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <!-- Confirm Password -->
                                <div x-data="{ showPassword: false }">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                                        </div>
                                        <input 
                                            :type="showPassword ? 'text' : 'password'" 
                                            id="password_confirmation" 
                                            name="password_confirmation" 
                                            x-model="formData.password_confirmation"
                                            class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" 
                                            placeholder="••••••••"
                                            autocomplete="new-password"
                                            required>
                                        <button 
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <i data-lucide="eye" class="h-5 w-5 text-gray-400" x-show="!showPassword"></i>
                                            <i data-lucide="eye-off" class="h-5 w-5 text-gray-400" x-show="showPassword"></i>
                                        </button>
                                    </div>
                                    <p 
                                        class="mt-1 text-sm" 
                                        x-show="formData.password && formData.password_confirmation"
                                        :class="formData.password === formData.password_confirmation ? 'text-green-600' : 'text-red-600'">
                                        <span x-show="formData.password === formData.password_confirmation">
                                            <i data-lucide="check" class="inline h-3 w-3"></i> Passwords match
                                        </span>
                                        <span x-show="formData.password !== formData.password_confirmation">
                                            <i data-lucide="x" class="inline h-3 w-3"></i> Passwords do not match
                                        </span>
                                    </p>
                                </div>
                                
                                <!-- Terms and Conditions -->
                                <div>
                                    <label class="flex items-start">
                                        <input 
                                            type="checkbox" 
                                            id="terms" 
                                            name="terms" 
                                            x-model="formData.terms"
                                            class="rounded border-gray-300 text-primary shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 mt-1" 
                                            required>
                                        <span class="ml-2 text-sm text-gray-600">
                                            I agree to the <a href="/terms" target="_blank" class="text-primary hover:text-primary underline">Terms of Service</a> and <a href="/privacy" target="_blank" class="text-primary hover:text-primary underline">Privacy Policy</a>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step Navigation -->
                        <div class="flex justify-between pt-4 border-t border-gray-200">
                            <button 
                                type="button" 
                                x-show="step > 1" 
                                @click="prevStep"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <i data-lucide="chevron-left" class="h-4 w-4 mr-2"></i>
                                Previous
                            </button>
                            <div x-show="step === 1"></div>
                            
                            <button 
                                type="button" 
                                x-show="step < totalSteps" 
                                @click="nextStep"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-medium text-white bg-primary hover:bg-primary-container focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Next
                                <i data-lucide="chevron-right" class="h-4 w-4 ml-2"></i>
                            </button>
                            
                            <button 
                                type="submit" 
                                x-show="step === totalSteps"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-medium text-white bg-primary hover:bg-primary-container focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <i data-lucide="check" class="h-4 w-4 mr-2"></i>
                                Create Account
                            </button>
                        </div>
                        
                        <!-- Hidden form fields to ensure data is submitted even if user doesn't visit every step -->
                        <input type="hidden" name="name" :value="formData.name">
                        <input type="hidden" name="middlename" :value="formData.middlename">
                        <input type="hidden" name="lastname" :value="formData.lastname">
                        <input type="hidden" name="username" :value="formData.username">
                        <input type="hidden" name="email" :value="formData.email">
                        <input type="hidden" name="phone" :value="formData.phone">
                        <input type="hidden" name="country" :value="formData.country">
                        <input type="hidden" name="accounttype" :value="formData.accounttype">
                        <input type="hidden" name="pin" :value="formData.pin">
                        <input type="hidden" name="password" :value="formData.password">
                        <input type="hidden" name="password_confirmation" :value="formData.password_confirmation">
                    </form>
                </div>
                
                <!-- Login Link -->
                <div class="text-center mt-4 pb-4">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-primary hover:text-primary font-medium">
                            Sign in instead
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

@endsection

@section('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    function restrictSpaces(event) {
        if (event.keyCode === 32) {
            return false;
        }
    }
    
    // When the DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Prevent spaces in username field
        const usernameInput = document.getElementById('username');
        if (usernameInput) {
            usernameInput.addEventListener('keypress', restrictSpaces);
        }
        
        // Restrict PIN to numbers only
        const pinInput = document.getElementById('pin');
        if (pinInput) {
            pinInput.addEventListener('keypress', function(event) {
                const charCode = (event.which) ? event.which : event.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    event.preventDefault();
                    return false;
                }
                return true;
            });
        }
    });
</script>
@endsection