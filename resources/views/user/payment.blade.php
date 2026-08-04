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
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Make Deposit</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <a href="{{ route('deposits') }}" class="hover:text-primary-600">Deposits</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <span class="font-medium text-gray-700">Make Payment</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Content Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center">
                    @if($title != "Complete Payment")
                        <i data-lucide="credit-card" class="h-5 w-5 mr-2 text-primary-600"></i>
                        <h2 class="text-xl font-semibold text-gray-900">Payment Method: {{$payment_mode->name}}</h2>
                    @else
                        <i data-lucide="qr-code" class="h-5 w-5 mr-2 text-primary-600"></i>
                        <h2 class="text-xl font-semibold text-gray-900">Complete Crypto Payment</h2>
                    @endif
                </div>
                <div class="mt-2 md:mt-0 md:ml-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-gray-800">
                        Amount: {{ $settings->currency }}{{ number_format($amount) }} {{ $settings->s_currency }}
                    </span>
                </div>
            </div>
                    </div>
        
        <!-- Form Content -->
        <div class="p-6">
                            @if($title !="Complete Payment")
                                @php
                                    if($payment_mode->name == "Bitcoin"){
                                        $coin = 'BTC';
                                    }elseif ($payment_mode->name == "Litecoin") {
                                        $coin = 'LTC';
                                    }else {
                                        $coin = 'ETH';
                                    }
                                @endphp
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i data-lucide="info" class="h-5 w-5 text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Payment Instructions</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>You are to make payment of <strong>{{$settings->currency}}{{number_format($amount)}}</strong> using your selected payment method. Screenshot and upload the proof of payment.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Barcode/QR Code (if available) -->
                @if ($payment_mode->methodtype != 'currency')
                <div class="flex justify-center mb-6">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 flex flex-col items-center w-full max-w-xs">
                        <div class="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center mb-3">
                            <i data-lucide="qr-code" class="h-6 w-6 text-gray-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Scan QR Code</h3>
                        <div class="bg-white p-2 rounded-lg border border-gray-200 mb-3 w-full flex justify-center">
                            @php
                                // Generate appropriate QR code data based on payment method
                                $qrContent = $payment_mode->wallet_address;
                                
                                // For cryptocurrencies, create a payment URI when possible
                                if ($payment_mode->name == "Bitcoin") {
                                    $qrContent = "bitcoin:" . $payment_mode->wallet_address . "?amount=" . $amount;
                                } elseif ($payment_mode->name == "Ethereum") {
                                    $qrContent = "ethereum:" . $payment_mode->wallet_address . "?value=" . $amount;
                                } elseif ($payment_mode->name == "Litecoin") {
                                    $qrContent = "litecoin:" . $payment_mode->wallet_address . "?amount=" . $amount;
                                }
                            @endphp
                            
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{urlencode($qrContent)}}" alt="Payment QR Code" class="w-48 h-48">
                        </div>
                        <p class="text-sm text-gray-500 text-center">Scan the QR code with your payment app</p>
                        <div class="mt-2 text-center w-full">
                            <p class="text-xs text-gray-700 font-medium">{{$payment_mode->name}} Address:</p>
                            <p class="text-xs text-gray-600 break-all">{{$payment_mode->wallet_address}}</p>
                            <p class="text-xs text-gray-700 font-medium mt-1">Amount:</p>
                            <p class="text-xs text-gray-600">{{$settings->currency}}{{$amount}}</p>
                        </div>
                    </div>
                </div>
                @endif
                            
                <!-- Payment Method Type Logic Begins -->
                <div class="space-y-6">
                                    @if($settings->deposit_option != "manual")
                                        @if ($payment_mode->name == "Bitcoin" or $payment_mode->name == "Litecoin" or $payment_mode->name == "Ethereum")
                        <div class="flex justify-center mb-4">
                            <a href="{{ url('dashboard/cpay') }}/{{$amount}}/{{$coin}}/{{ Auth::user()->id }}/new" 
                               class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i data-lucide="currency-bitcoin" class="h-5 w-5 mr-2"></i>
                                Pay Via Coinpayment
                            </a>
                                            </div>
                                            @endif
                                        @endif

                    <!-- Method Type: Non-Currency (Crypto etc) -->
                                    @if ($payment_mode->methodtype != 'currency')
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-5">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i data-lucide="wallet" class="h-5 w-5 mr-2 text-primary-600"></i>
                                {{$payment_mode->name}} Address
                                        </h3>
                            
                            <div class="relative mb-4">
                                <div class="flex">
                                    <input type="text" id="myInput" value="{{$payment_mode->wallet_address}}" 
                                           class="block w-full py-3 px-4 border border-gray-200 rounded-l-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all" 
                                           readonly />
                                    <button onclick="myFunction()" type="button" 
                                            class="inline-flex items-center justify-center px-4 py-2 border border-l-0 border-gray-200 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                        <i data-lucide="copy" class="h-5 w-5"></i>
                                    </button>
                                </div>
                                <p class="mt-2 text-sm text-gray-500 flex items-center">
                                    <i data-lucide="info" class="h-4 w-4 mr-1 text-blue-500"></i>
                                    <strong>Network Type:</strong> {{$payment_mode->network}}
                                </p>
                                            </div>
                                        </div>
                                        @else
                        <!-- Method Type: Currency (Bank Transfer, etc) -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i data-lucide="building-bank" class="h-5 w-5 mr-2 text-primary-600"></i>
                                {{$payment_mode->name}} Details
                                        </h3>
                            
                                            @if ($payment_mode->defaultpay == 'yes')
                                <!-- Default Payment Methods Integration -->
                                                @if ($payment_mode->name == "Paystack")
                                    <div id="paystack" class="bg-gray-50 rounded-lg border border-gray-200 p-5">
                                        <div class="flex flex-col items-center">
                                            <div class="mb-4">
                                                <img src="{{ asset('img/paystack-logo.png') }}" alt="Paystack" class="h-10">
                                            </div>
                                            <form method="POST" action="{{ route('pay.paystack') }}" accept-charset="UTF-8" class="w-full max-w-md">
                                                            <input type="hidden" name="email" value="{{auth::user()->email}}">
                                                            <input type="hidden" name="amount" value="{{$payamount}}">
                                                            <input type="hidden" name="currency" value="{{$settings->s_currency}}">
                                                <input type="hidden" name="metadata" value="{{ json_encode($array = ['key_name' => 'value',]) }}"> 
                                                            <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> 
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                                                <button type="submit" class="w-full py-3 px-4 flex items-center justify-center rounded-lg bg-success-600 hover:bg-success-700 text-white font-medium transition-colors">
                                                    <i data-lucide="credit-card" class="h-5 w-5 mr-2"></i>
                                                    Pay with Paystack
                                                            </button>
                                                        </form>
                                        </div>
                                                    </div>
                                                @endif

                                                @if ($payment_mode->name == "Stripe")
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-5">
                                        <div class="flex flex-col items-center mb-4">
                                            <img src="{{ asset('img/stripe-logo.png') }}" alt="Stripe" class="h-10 mb-4">
                                            <form id="payment-form" class="w-full max-w-md">
                                                        @csrf
                                                <div class="sr-combo-inputs-row mb-4">
                                                            <div class="sr-input sr-card-element" id="card-element"></div>
                                                        </div>
                                                <button id="stripesubmit" class="w-full py-3 px-4 flex items-center justify-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-medium transition-colors">
                                                            <div class="spinner d-none" id="spinner"></div>
                                                    <span id="buttontext" class="flex items-center">
                                                        <i data-lucide="credit-card" class="h-5 w-5 mr-2"></i>
                                                        Pay with Stripe
                                                    </span>
                                                        </button>
                                                    </form>
                                        </div>
                                        
                                        <div class="hidden" id="stripesuccess">
                                            <div class="p-4 bg-success-50 border border-success-200 rounded-lg text-success-800 text-center">
                                                <i data-lucide="check-circle" class="h-8 w-8 mx-auto mb-2 text-success-600"></i>
                                                <p>Payment Completed, redirecting...</p>
                                                        </div>
                                                    </div>
                                                    
                                                @endif

                                                @if ($payment_mode->name == "Paypal")
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-5 flex justify-center">
                                                        @include('includes.paypal')
                                                    </div>
                                                @endif

                                                @if ($payment_mode->name == "Bank Transfer")
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-5">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                @if (!empty($payment_mode->bankname))
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                                                <div class="flex">
                                                    <input type="text" value="{{$payment_mode->bankname}}" 
                                                           class="block w-full py-2 px-3 border border-gray-200 rounded-l-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-gray-700" 
                                                           readonly />
                                                    <button type="button" onclick="copyToClipboard(this)" data-value="{{$payment_mode->bankname}}"
                                                            class="inline-flex items-center justify-center px-3 py-2 border border-l-0 border-gray-200 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                                        <i data-lucide="copy" class="h-4 w-4"></i>
                                                    </button>
                                                    </div>
                                                </div>
                                                @endif
                                            
                                                @if (!empty($payment_mode->account_name))
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                                                <div class="flex">
                                                    <input type="text" value="{{$payment_mode->account_name}}" 
                                                           class="block w-full py-2 px-3 border border-gray-200 rounded-l-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-gray-700" 
                                                           readonly />
                                                    <button type="button" onclick="copyToClipboard(this)" data-value="{{$payment_mode->account_name}}"
                                                            class="inline-flex items-center justify-center px-3 py-2 border border-l-0 border-gray-200 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                                        <i data-lucide="copy" class="h-4 w-4"></i>
                                                    </button>
                                                    </div>
                                                </div>
                                                @endif
                                            
                                                @if (!empty($payment_mode->account_number))
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                                                <div class="flex">
                                                    <input type="text" value="{{$payment_mode->account_number}}" 
                                                           class="block w-full py-2 px-3 border border-gray-200 rounded-l-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-gray-700" 
                                                           readonly />
                                                    <button type="button" onclick="copyToClipboard(this)" data-value="{{$payment_mode->account_number}}"
                                                            class="inline-flex items-center justify-center px-3 py-2 border border-l-0 border-gray-200 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                                        <i data-lucide="copy" class="h-4 w-4"></i>
                                                    </button>
                                                    </div>
                                                </div>
                                                @endif
                                            
                                                @if (!empty($payment_mode->swift_code))
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Swift Code</label>
                                                <div class="flex">
                                                    <input type="text" value="{{$payment_mode->swift_code}}" 
                                                           class="block w-full py-2 px-3 border border-gray-200 rounded-l-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-gray-700" 
                                                           readonly />
                                                    <button type="button" onclick="copyToClipboard(this)" data-value="{{$payment_mode->swift_code}}"
                                                            class="inline-flex items-center justify-center px-3 py-2 border border-l-0 border-gray-200 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                                        <i data-lucide="copy" class="h-4 w-4"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endif
                                                    </div>
                                                </div>
                                                @endif
                                            @else
                                <!-- Non-Default Bank Transfer Option -->
                                <div class="bg-gray-50 rounded-lg border border-gray-200 p-5">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                @if (!empty($payment_mode->bankname))
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                                            <div class="flex">
                                                <input type="text" value="{{$payment_mode->bankname}}" 
                                                       class="block w-full py-2 px-3 border border-gray-200 rounded-l-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-gray-700" 
                                                       readonly />
                                                <button type="button" onclick="copyToClipboard(this)" data-value="{{$payment_mode->bankname}}"
                                                        class="inline-flex items-center justify-center px-3 py-2 border border-l-0 border-gray-200 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                                    <i data-lucide="copy" class="h-4 w-4"></i>
                                                </button>
                                                    </div>
                                                </div>
                                                @endif
                                        
                                                @if (!empty($payment_mode->account_name))
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                                            <div class="flex">
                                                <input type="text" value="{{$payment_mode->account_name}}" 
                                                       class="block w-full py-2 px-3 border border-gray-200 rounded-l-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-gray-700" 
                                                       readonly />
                                                <button type="button" onclick="copyToClipboard(this)" data-value="{{$payment_mode->account_name}}"
                                                        class="inline-flex items-center justify-center px-3 py-2 border border-l-0 border-gray-200 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                                        <i data-lucide="copy" class="h-4 w-4"></i>
                                                    </button>
                                                    </div>
                                                </div>
                                                @endif
                                            
                                                @if (!empty($payment_mode->account_number))
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                                                <div class="flex">
                                                    <input type="text" value="{{$payment_mode->account_number}}" 
                                                           class="block w-full py-2 px-3 border border-gray-200 rounded-l-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-gray-700" 
                                                           readonly />
                                                    <button type="button" onclick="copyToClipboard(this)" data-value="{{$payment_mode->account_number}}"
                                                            class="inline-flex items-center justify-center px-3 py-2 border border-l-0 border-gray-200 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                                        <i data-lucide="copy" class="h-4 w-4"></i>
                                                    </button>
                                                    </div>
                                                </div>
                                                @endif
                                            
                                                @if (!empty($payment_mode->swift_code))
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Swift Code</label>
                                                <div class="flex">
                                                    <input type="text" value="{{$payment_mode->swift_code}}" 
                                                           class="block w-full py-2 px-3 border border-gray-200 rounded-l-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-gray-700" 
                                                           readonly />
                                                    <button type="button" onclick="copyToClipboard(this)" data-value="{{$payment_mode->swift_code}}"
                                                            class="inline-flex items-center justify-center px-3 py-2 border border-l-0 border-gray-200 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                                        <i data-lucide="copy" class="h-4 w-4"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                                    @endif
                                </div>
                                @if ($settings->deposit_option == "auto" and $payment_mode->defaultpay != 'yes')
                    <div class="mt-8">
                                        <form method="post" action="{{route('savedeposit')}}" enctype="multipart/form-data">
                                            @csrf
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Payment Proof</label>
                                <div id="auto-upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-500 transition-all relative cursor-pointer">
                                    <div id="auto-upload-placeholder" class="flex flex-col items-center justify-center">
                                        <i data-lucide="upload-cloud" class="h-10 w-10 text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-500 mb-2">
                                            <span class="font-medium text-primary-600">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG or PDF (max. 5MB)</p>
                                    </div>
                                    <div id="auto-preview-container" class="hidden flex flex-col items-center justify-center">
                                        <img id="auto-image-preview" src="#" alt="Preview" class="max-h-48 max-w-full mb-3 rounded-lg shadow-sm">
                                        <p class="text-sm font-medium text-gray-700 flex items-center">
                                            <span id="auto-file-name">filename.jpg</span>
                                            <button type="button" id="auto-remove-file" class="ml-2 text-red-500 hover:text-red-700">
                                                <i data-lucide="x-circle" class="h-5 w-5"></i>
                                            </button>
                                        </p>
                                    </div>
                                    <input id="proof-upload-auto" class="hidden" name="proof" type="file" required accept="image/*,.pdf">
                                </div>
                                                                                    </div>
                                            
                                            <input type="hidden" name="amount" value="{{$amount}}">
                                            <input type="hidden" name="paymethd_method" value="{{$payment_mode->name}}">

                            <div class="flex justify-center">
                                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                    <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>
                                    Submit Payment
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                @if($settings->deposit_option == "manual" and $payment_mode->name != "Paystack" and $payment_mode->name != "Stripe" and $payment_mode->name != "Paypal" and $title !="Complete Payment")
                    <div class="mt-8">
                                        <form method="post" action="{{route('savedeposit')}}" enctype="multipart/form-data">
                                            @csrf
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Payment Proof</label>
                                <div id="manual-upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-500 transition-all relative cursor-pointer">
                                    <div id="manual-upload-placeholder" class="flex flex-col items-center justify-center">
                                        <i data-lucide="upload-cloud" class="h-10 w-10 text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-500 mb-2">
                                            <span class="font-medium text-primary-600">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG or PDF (max. 5MB)</p>
                                    </div>
                                    <div id="manual-preview-container" class="hidden flex flex-col items-center justify-center">
                                        <img id="manual-image-preview" src="#" alt="Preview" class="max-h-48 max-w-full mb-3 rounded-lg shadow-sm">
                                        <p class="text-sm font-medium text-gray-700 flex items-center">
                                            <span id="manual-file-name">filename.jpg</span>
                                            <button type="button" id="manual-remove-file" class="ml-2 text-red-500 hover:text-red-700">
                                                <i data-lucide="x-circle" class="h-5 w-5"></i>
                                            </button>
                                        </p>
                                    </div>
                                    <input id="proof-upload-manual" class="hidden" name="proof" type="file" required accept="image/*,.pdf">
                                </div>
                                                                            </div>
                                            <input type="hidden" name="amount" value="{{$amount}}">
                                            <input type="hidden" name="paymethd_method" value="{{$payment_mode->name}}">

                            <div class="flex justify-center">
                                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                    <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>
                                    Submit Payment
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif
                            {{-- Automatic Cryptopayment qrcode --}}
                            @if($title=="Complete Payment")
                <div class="flex flex-col items-center justify-center p-6">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 flex flex-col items-center max-w-md">
                        <div class="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center mb-4">
                            <i data-lucide="qr-code" class="h-6 w-6 text-primary-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">Scan QR Code to Complete Payment</h3>
                        
                        <div class="bg-white p-3 rounded-lg border border-gray-200 mb-4">
                            <img src="{{$p_qrcode}}" alt="Payment QR code" class="w-48 h-48">
                        </div>
                        
                        <div class="mb-4 w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{$coin}} Address</label>
                            <div class="flex">
                                <input type="text" value="{{$p_address}}" 
                                       class="block w-full py-2 px-3 border border-gray-200 rounded-l-lg bg-white text-gray-700" 
                                       readonly />
                                <button type="button" onclick="copyToClipboard(this)" data-value="{{$p_address}}"
                                        class="inline-flex items-center justify-center px-3 py-2 border border-l-0 border-gray-200 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500">
                                    <i data-lucide="copy" class="h-4 w-4"></i>
                                </button>
                            </div>
                                    </div>	
                        
                        <div class="w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                            <div class="flex">
                                <input type="text" value="{{$amount}} {{$coin}}" 
                                       class="block w-full py-2 px-3 border border-gray-200 rounded-l-lg bg-white text-gray-700" 
                                       readonly />
                                <button type="button" onclick="copyToClipboard(this)" data-value="{{$amount}} {{$coin}}"
                                        class="inline-flex items-center justify-center px-3 py-2 border border-l-0 border-gray-200 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500">
                                    <i data-lucide="copy" class="h-4 w-4"></i>
                                </button>
                                    </div>
                                </div>
                            
                        <div class="mt-4 text-xs text-gray-500 text-center">
                            <p>You can exit this page after scanning and completing payment.</p>
                            <p>The system will track your payment and update your account.</p>
                        </div>
                    </div>
                </div>
            @endif
            
        </div>
    </div>
</div>
    <script>
        function myFunction() {
            /* Get the text field */
            var copyText = document.getElementById("myInput");
            
            /* Select the text field */
            copyText.select();
            copyText.setSelectionRange(0, 99999); /* For mobile devices */
            
            /* Copy the text inside the text field */
            document.execCommand("copy");
            
            /* Alert the copied text */
            alert("Copied: " + copyText.value);
        }

        function copyToClipboard(button) {
            const value = button.getAttribute('data-value');
            const tempInput = document.createElement('input');
            tempInput.value = value;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            // Optional - show feedback
            const originalInnerHTML = button.innerHTML;
            button.innerHTML = '<i data-lucide="check" class="h-4 w-4"></i>';
            setTimeout(() => {
                button.innerHTML = originalInnerHTML;
                // Re-initialize Lucide icon
                lucide.createIcons();
            }, 1500);
        }

        // Initialize file upload with drag and drop
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Lucide icons
            lucide.createIcons();
            
            // Setup file upload with preview and drag-drop for both upload areas
            const setupFileUpload = (dropAreaId, fileInputId, placeholderId, previewContainerId, imagePreviewId, fileNameId, removeButtonId) => {
                const dropArea = document.getElementById(dropAreaId);
                const fileInput = document.getElementById(fileInputId);
                const placeholder = document.getElementById(placeholderId);
                const previewContainer = document.getElementById(previewContainerId);
                const imagePreview = document.getElementById(imagePreviewId);
                const fileName = document.getElementById(fileNameId);
                const removeButton = document.getElementById(removeButtonId);
                
                if (!dropArea || !fileInput || !placeholder || !previewContainer || !imagePreview || !fileName || !removeButton) return;
                
                // Prevent default behavior for all drag events
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, preventDefaults, false);
                });
                
                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                // Highlight drop area when dragging over it
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropArea.addEventListener(eventName, highlight, false);
                });
                
                ['dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, unhighlight, false);
                });
                
                function highlight() {
                    dropArea.classList.add('border-primary-500');
                    dropArea.classList.add('bg-primary-50');
                }
                
                function unhighlight() {
                    dropArea.classList.remove('border-primary-500');
                    dropArea.classList.remove('bg-primary-50');
                }
                
                // Handle file selection via drag & drop
                dropArea.addEventListener('drop', handleDrop, false);
                
                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    
                    if (files.length) {
                        fileInput.files = files;
                        updateFilePreview(files[0]);
                    }
                }
                
                // Handle file selection via click
                dropArea.addEventListener('click', () => {
                    fileInput.click();
                });
                
                // Handle file selection changes
                fileInput.addEventListener('change', function() {
                    if (this.files.length) {
                        updateFilePreview(this.files[0]);
                    }
                });
                
                // Handle removing the selected file
                removeButton.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent triggering dropArea click
                    resetFileInput();
                });
                
                // Update the preview with the selected file
                function updateFilePreview(file) {
                    // Display file name
                    fileName.textContent = file.name;
                    
                    // Handle image preview
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            showPreview();
                        };
                        reader.readAsDataURL(file);
                    } else if (file.type === 'application/pdf') {
                        // Show a PDF icon for PDF files
                        imagePreview.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLWZpbGUiPjxwYXRoIGQ9Ik0xNCAySDZhMiAyIDAgMCAwLTIgMnYxNmEyIDIgMCAwIDAgMiAyaDEyYTIgMiAwIDAgMCAyLTJWOHoiLz48cGF0aCBkPSJNMTQgMnY2aDYiLz48L3N2Zz4=';
                        imagePreview.classList.add('h-24', 'w-24', 'object-contain');
                        showPreview();
                } else {
                        // Show a generic file icon for other files
                        imagePreview.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLWZpbGUiPjxwYXRoIGQ9Ik0xNCAySDZhMiAyIDAgMCAwLTIgMnYxNmEyIDIgMCAwIDAgMiAyaDEyYTIgMiAwIDAgMCAyLTJWOHoiLz48cGF0aCBkPSJNMTQgMnY2aDYiLz48L3N2Zz4=';
                        imagePreview.classList.add('h-24', 'w-24', 'object-contain');
                        showPreview();
                    }
                }
                
                // Show the preview and hide the placeholder
                function showPreview() {
                    placeholder.classList.add('hidden');
                    previewContainer.classList.remove('hidden');
                }
                
                // Reset the file input and show the placeholder again
                function resetFileInput() {
                    fileInput.value = '';
                    placeholder.classList.remove('hidden');
                    previewContainer.classList.add('hidden');
                    // Remove any added classes to the image preview
                    imagePreview.classList.remove('h-24', 'w-24', 'object-contain');
                }
            };
            
            // Setup file upload for both upload areas
            setupFileUpload(
                'auto-upload-area', 
                'proof-upload-auto', 
                'auto-upload-placeholder', 
                'auto-preview-container', 
                'auto-image-preview', 
                'auto-file-name', 
                'auto-remove-file'
            );
            
            setupFileUpload(
                'manual-upload-area', 
                'proof-upload-manual', 
                'manual-upload-placeholder', 
                'manual-preview-container', 
                'manual-image-preview', 
                'manual-file-name', 
                'manual-remove-file'
            );
        });
    </script>
@endsection