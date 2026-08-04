@extends('layouts.dash2')
@section('title', 'Track IRS Tax Refund')
@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header with Icon -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary-100 mb-4">
                <i data-lucide="activity" class="h-10 w-10 text-gray-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Track Your IRS Tax Refund</h1>
            <p class="text-gray-600">Monitor the status of your refund request in real-time</p>
        </div>

        <!-- Alerts -->
        <x-danger-alert />
        <x-success-alert />

        @if($refund)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <!-- Status Timeline -->
                <div class="p-6">
                    <div class="relative">
                        <!-- Timeline Line -->
                        <div class="absolute left-4 top-0 h-full w-0.5 bg-gray-200"></div>

                        <!-- Status Steps -->
                        <div class="space-y-8">
                            <!-- Submitted Step -->
                            <div class="relative">
                                <div class="absolute left-0 w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                    <i data-lucide="check" class="h-4 w-4 text-gray-600"></i>
                                </div>
                                <div class="ml-12">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-gray-900">Request Submitted</h3>
                                        <span class="text-sm text-gray-500">{{ $refund->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600">Your refund request has been successfully submitted</p>
                                </div>
                            </div>

                            <!-- Under Review Step -->
                            <div class="relative">
                                <div class="absolute left-0 w-8 h-8 rounded-full {{ in_array($refund->status, ['pending', 'approved', 'rejected']) ? 'bg-primary-100' : 'bg-gray-100' }} flex items-center justify-center">
                                    <i data-lucide="search" class="h-4 w-4 {{ in_array($refund->status, ['pending', 'approved', 'rejected']) ? 'text-gray-600' : 'text-gray-400' }}"></i>
                                </div>
                                <div class="ml-12">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-gray-900">Under Review</h3>
                                        <span class="text-sm text-gray-500">{{ $refund->status === 'pending' ? 'In Progress' : ($refund->status === 'approved' ? 'Completed' : 'Completed') }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600">Our team is reviewing your request and verifying your information</p>
                                </div>
                            </div>

                            <!-- Decision Step -->
                            <div class="relative">
                                <div class="absolute left-0 w-8 h-8 rounded-full {{ in_array($refund->status, ['approved', 'rejected']) ? 'bg-primary-100' : 'bg-gray-100' }} flex items-center justify-center">
                                    <i data-lucide="{{ $refund->status === 'approved' ? 'check-circle' : ($refund->status === 'rejected' ? 'x-circle' : 'clock') }}" class="h-4 w-4 {{ in_array($refund->status, ['approved', 'rejected']) ? 'text-primary-600' : 'text-gray-400' }}"></i>
                                </div>
                                <div class="ml-12">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-gray-900">Decision</h3>
                                        <span class="text-sm text-gray-500">{{ $refund->status === 'pending' ? 'Pending' : ($refund->status === 'approved' ? 'Approved' : 'Rejected') }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600">
                                        @if($refund->status === 'pending')
                                            Waiting for review completion
                                        @elseif($refund->status === 'approved')
                                            Your refund request has been approved
                                        @else
                                            Your refund request has been rejected
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Processing Step -->
                            @if($refund->status === 'approved')
                            <div class="relative">
                                <div class="absolute left-0 w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                    <i data-lucide="loader" class="h-4 w-4 text-primary-600 animate-spin"></i>
                                </div>
                                <div class="ml-12">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-gray-900">Processing Refund</h3>
                                        <span class="text-sm text-gray-500">In Progress</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600">Your refund is being processed and will be credited to your account soon</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Refund Details -->
                <div class="border-t border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Refund Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Request ID</h4>
                            <p class="text-lg font-semibold text-gray-900">{{ $refund->id }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Filing ID</h4>
                            <p class="text-lg font-semibold text-gray-900">{{ $refund->filing_id }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Submission Date</h4>
                            <p class="text-lg font-semibold text-gray-900">{{ $refund->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Status</h4>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $refund->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($refund->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                                    'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($refund->status) }}
                            </span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Last Updated</h4>
                            <p class="text-lg font-semibold text-gray-900">{{ $refund->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="border-t border-gray-200 p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-600">
                            <i data-lucide="info" class="h-4 w-4 inline-block mr-1"></i>
                            Need help? Contact our support team
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('support') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i data-lucide="message-circle" class="h-4 w-4 mr-2"></i> Contact Support
                            </a>
                            <a href="{{ route('irs-refund') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i data-lucide="refresh-cw" class="h-4 w-4 mr-2"></i> Refresh Status
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                        <i data-lucide="search" class="h-8 w-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Refund Request Found</h3>
                    <p class="text-gray-600 mb-6">You haven't submitted any refund requests yet.</p>
                    <a href="{{ route('irs-refund') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i data-lucide="plus" class="h-4 w-4 mr-2"></i> Submit New Request
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Animation for the loading spinner */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>

@endsection 