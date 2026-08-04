@extends('layouts.dash2')
@section('title', 'Fund Transfer')
@section('content')

<div class="container py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-5">
            <div class="flex items-center justify-center">
                <div class="bg-white/20 backdrop-blur-sm p-3 rounded-full">
                    <i data-lucide="alert-triangle" class="h-10 w-10 text-white"></i>
                </div>
            </div>
            <h1 class="text-white text-center font-bold text-2xl mt-4">Before You Proceed!</h1>
            <p class="text-white/80 text-center mt-2">Additional verification is required</p>
        </div>
        
        <!-- Card Content -->
        <div class="p-6 sm:p-8">
            <!-- Alerts -->
            <x-danger-alert />
            <x-success-alert />
            
            <!-- Main Content -->
            <div class="text-center pt-4">
                <div class="inline-flex items-center justify-center h-24 w-24 rounded-full bg-red-50 mb-6">
                    <i data-lucide="shield-alert" class="h-12 w-12 text-red-600"></i>
                </div>
                
                <div class="text-gray-700 mb-8 max-w-lg mx-auto">
                    <p class="text-lg">{{$settings->code1message}}</p>
                </div>
                
                <form action="{{ route('codecomfirm') }}" method="post" class="max-w-md mx-auto">
                    @csrf
                    <div class="mb-6">
                        <label for="code1" class="block text-sm font-medium text-gray-700 mb-2">{{ $settings->code1 }} Code</label>
                        <input 
                            type="text" 
                            name="code1" 
                            id="code1" 
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Enter {{ $settings->code1 }} code"
                            required
                        >
                    </div>
                    
                    <div class="flex flex-col space-y-3">
                        <button 
                            type="submit" 
                            class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                        >
                            <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>
                            Confirm {{$settings->code1}} Code
                        </button>
                        
                        <a 
                            href="{{ route('dashboard') }}" 
                            class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                        >
                            <i data-lucide="arrow-left" class="h-5 w-5 mr-2 text-gray-400"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </form>
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