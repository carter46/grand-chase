@extends('layouts.guest2')

@section('title', 'Verify Email')
@section('content')

<div class="container py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-6 py-5">
            <div class="flex items-center justify-center">
                <div class="bg-white/20 backdrop-blur-sm p-3 rounded-full">
                    <i data-lucide="mail" class="h-10 w-10 text-white"></i>
                </div>
            </div>
            <h1 class="text-white text-center font-bold text-2xl mt-4">Verify Your Email Address</h1>
            <p class="text-white/80 text-center mt-2">Please check your inbox for the verification link</p>
        </div>
        
        <!-- Card Content -->
        <div class="p-6 sm:p-8">
            <!-- Alerts -->
            @if (Session::has('message'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i data-lucide="alert-circle" class="h-5 w-5 text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                            <p class="text-sm text-red-700 mt-1">{{ Session::get('message') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="this.parentElement.parentElement.parentElement.remove()">
                                    <i data-lucide="x" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif (Session::has('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Success</h3>
                            <p class="text-sm text-green-700 mt-1">{{ Session::get('success') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" onclick="this.parentElement.parentElement.parentElement.remove()">
                                    <i data-lucide="x" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Success</h3>
                            <p class="text-sm text-green-700 mt-1">Your registration is successful. A verification link has been sent to your email address, please click on the link to verify your email address.</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" onclick="this.parentElement.parentElement.parentElement.remove()">
                                    <i data-lucide="x" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('status'))
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i data-lucide="info" class="h-5 w-5 text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Information</h3>
                            <p class="text-sm text-blue-700 mt-1">A verification link has been sent to the email address, please click on the link to verify your email address.</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" class="inline-flex rounded-md p-1.5 text-blue-500 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="this.parentElement.parentElement.parentElement.remove()">
                                    <i data-lucide="x" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Main Content -->
            <div class="text-center pt-4">
                <div class="inline-flex items-center justify-center h-24 w-24 rounded-full bg-primary-50 mb-6">
                    <i data-lucide="mail-check" class="h-12 w-12 text-primary-600"></i>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Check your inbox</h2>
                <p class="text-gray-600 mb-6">We've sent you an email with a link to confirm your account</p>
                
                <div class="bg-gray-50 rounded-lg p-5 text-left mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Didn't get the email?</h3>
                    <ul class="list-decimal pl-5 text-gray-600 space-y-2">
                        <li>The email may be in your spam folder</li>
                        <li>The email address you entered might have a typo</li>
                        <li>You may have accidentally entered another email address (Usually happens with auto-complete)</li>
                        <li>We can't deliver the email to this address (Usually because of corporate firewalls or filtering)</li>
                    </ul>
                </div>
                
                <!-- Actions -->
                <div class="space-y-4">
                    <a href="{{ route('verification.send') }}" 
                       onclick="event.preventDefault(); document.getElementById('verification').submit();" 
                       class="inline-flex items-center justify-center w-full px-4 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <i data-lucide="refresh-cw" class="h-5 w-5 mr-2"></i>
                        Resend Verification Email
                    </a>
                    <form id="verification" action="{{ route('verification.send') }}" method="POST" class="hidden">
                        {{ csrf_field() }}
                    </form>
                    
                    <a href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                       class="inline-flex items-center justify-center w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i data-lucide="log-out" class="h-5 w-5 mr-2 text-gray-400"></i>
                        Sign Out
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        {{ csrf_field() }}
                    </form>
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
